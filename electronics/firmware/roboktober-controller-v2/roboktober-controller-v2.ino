#define CONFIG_ESP32_WIFI_AMPDU_RX_ENABLED 0
#define CONFIG_ESP32_WIFI_AMPDU_TX_ENABLED 0

#if !defined(WOKWI)
#include <WiFi.h>
#include <esp_now.h>
#include <esp_wifi.h>
#endif

#include <esp_arduino_version.h>

#include <cstring>
#include <cstdio>

namespace Pins {
constexpr uint8_t DriverSleep = 5;
constexpr uint8_t MotorLeftIn2 = 6;
constexpr uint8_t MotorLeftIn1 = 7;
constexpr uint8_t StatusLed = 8;
constexpr uint8_t MotorRightIn1 = 9;
constexpr uint8_t MotorRightIn2 = 10;
}  // namespace Pins

namespace Pwm {
constexpr uint8_t LeftIn1Channel = 0;
constexpr uint8_t LeftIn2Channel = 1;
constexpr uint8_t RightIn1Channel = 2;
constexpr uint8_t RightIn2Channel = 3;
constexpr uint32_t FrequencyHz = 5000;
constexpr uint8_t ResolutionBits = 8;
constexpr int MaximumDuty = 255;
}  // namespace Pwm

struct RcData {
  uint16_t aileron;
  uint16_t elevator;
  uint16_t throttle;
  uint16_t rudder;
  uint16_t auxiliary1;
  uint16_t auxiliary2;
  uint16_t auxiliary3;
  uint16_t auxiliary4;
};

static_assert(sizeof(RcData) == 16, "ESP-NOW protocol layout changed");

constexpr uint16_t RcMinimum = 960;
constexpr uint16_t RcNeutral = 1500;
constexpr uint16_t RcMaximum = 2040;
constexpr uint16_t RcDeadbandMicroseconds = 40;
constexpr uint32_t LinkTimeoutMilliseconds = 200;

// Replace this placeholder with the transmitter station MAC before driving motors.
constexpr uint8_t AllowedSenderMac[6] = {0x00, 0x00, 0x00, 0x00, 0x00, 0x00};

portMUX_TYPE packetMutex = portMUX_INITIALIZER_UNLOCKED;
RcData receivedPacket{};
volatile bool packetPending = false;
uint32_t lastValidPacketMilliseconds = 0;
bool linkActive = false;
bool radioReady = false;

#if defined(WOKWI)
constexpr bool SimulationMode = true;
#else
constexpr bool SimulationMode = false;
#endif

bool senderMacConfigured() {
  for (uint8_t octet : AllowedSenderMac) {
    if (octet != 0) {
      return true;
    }
  }

  return false;
}

bool senderAllowed(const uint8_t* senderMac) {
  return senderMacConfigured() && std::memcmp(senderMac, AllowedSenderMac, sizeof(AllowedSenderMac)) == 0;
}

#if defined(WOKWI)
bool takeSimulationPacket(RcData& packet) {
  static char lineBuffer[96];
  static size_t lineLength = 0;

  while (Serial.available() > 0) {
    const char incoming = static_cast<char>(Serial.read());
    if (incoming == '\r') {
      continue;
    }

    if (incoming == '\n') {
      lineBuffer[lineLength] = '\0';
      lineLength = 0;

      unsigned int values[8] = {};
      const int matched = std::sscanf(
        lineBuffer,
        "packet %u %u %u %u %u %u %u %u",
        &values[0], &values[1], &values[2], &values[3],
        &values[4], &values[5], &values[6], &values[7]);

      if (matched == 8) {
        packet.aileron = static_cast<uint16_t>(values[0]);
        packet.elevator = static_cast<uint16_t>(values[1]);
        packet.throttle = static_cast<uint16_t>(values[2]);
        packet.rudder = static_cast<uint16_t>(values[3]);
        packet.auxiliary1 = static_cast<uint16_t>(values[4]);
        packet.auxiliary2 = static_cast<uint16_t>(values[5]);
        packet.auxiliary3 = static_cast<uint16_t>(values[6]);
        packet.auxiliary4 = static_cast<uint16_t>(values[7]);
        return true;
      }

      return false;
    }

    if (lineLength + 1 < sizeof(lineBuffer)) {
      lineBuffer[lineLength++] = incoming;
    }
  }

  return false;
}
#endif

bool channelValid(uint16_t value) {
  return value >= RcMinimum && value <= RcMaximum;
}

int channelToMotorCommand(uint16_t value) {
  if (!channelValid(value) || abs(static_cast<int>(value) - RcNeutral) <= RcDeadbandMicroseconds) {
    return 0;
  }

  if (value < RcNeutral) {
    return map(value, RcMinimum, RcNeutral, -Pwm::MaximumDuty, 0);
  }

  return map(value, RcNeutral, RcMaximum, 0, Pwm::MaximumDuty);
}

void writeBridge(uint8_t input1Channel, uint8_t input2Channel, int command) {
  command = constrain(command, -Pwm::MaximumDuty, Pwm::MaximumDuty);

  if (command > 0) {
    writePwm(input1Channel, 0);
    writePwm(input2Channel, command);
  } else if (command < 0) {
    writePwm(input1Channel, abs(command));
    writePwm(input2Channel, 0);
  } else {
    writePwm(input1Channel, 0);
    writePwm(input2Channel, 0);
  }
}

void stopMotors() {
  writeBridge(Pwm::LeftIn1Channel, Pwm::LeftIn2Channel, 0);
  writeBridge(Pwm::RightIn1Channel, Pwm::RightIn2Channel, 0);
}

void setDriverEnabled(bool enabled) {
  if (!enabled) {
#if defined(WOKWI)
  RcData packet{};
  if (takeSimulationPacket(packet)) {
    applyPacket(packet, millis());
  }
#else
  const uint32_t now = millis();
    stopMotors();
  ledcWriteChannel(channel, duty);
#else
  ledcWrite(channel, duty);
  return ledcAttachChannel(pin, Pwm::FrequencyHz, Pwm::ResolutionBits, channel);

  const uint32_t now = millis();
  if (!linkActive || static_cast<uint32_t>(now - lastValidPacketMilliseconds) >= LinkTimeoutMilliseconds) {
    linkActive = false;
    stopMotors();
  }
#else
  ledcSetup(channel, Pwm::FrequencyHz, Pwm::ResolutionBits);
  ledcAttachPin(pin, channel);
  return true;
#endif
}

bool configureMotorOutputs() {
  pinMode(Pins::DriverSleep, OUTPUT);
  digitalWrite(Pins::DriverSleep, LOW);

  pinMode(Pins::MotorLeftIn1, OUTPUT);
  pinMode(Pins::MotorLeftIn2, OUTPUT);
  pinMode(Pins::MotorRightIn1, OUTPUT);
  pinMode(Pins::MotorRightIn2, OUTPUT);
  digitalWrite(Pins::MotorLeftIn1, LOW);
  digitalWrite(Pins::MotorLeftIn2, LOW);
  digitalWrite(Pins::MotorRightIn1, LOW);
  digitalWrite(Pins::MotorRightIn2, LOW);

  const bool attached =
    attachPwm(Pins::MotorLeftIn1, Pwm::LeftIn1Channel) &&
    attachPwm(Pins::MotorLeftIn2, Pwm::LeftIn2Channel) &&
    attachPwm(Pins::MotorRightIn1, Pwm::RightIn1Channel) &&
    attachPwm(Pins::MotorRightIn2, Pwm::RightIn2Channel);
  stopMotors();
  return attached;
}

void handleReceivedData(const uint8_t* senderMac, const uint8_t* incomingData, int length) {
  if (length != static_cast<int>(sizeof(RcData)) || !senderAllowed(senderMac)) {
    return;
  }

  portENTER_CRITICAL(&packetMutex);
  std::memcpy(&receivedPacket, incomingData, sizeof(receivedPacket));
  packetPending = true;
  portEXIT_CRITICAL(&packetMutex);
}

#if ESP_ARDUINO_VERSION_MAJOR >= 3
void onDataReceived(const esp_now_recv_info_t* receiveInfo, const uint8_t* incomingData, int length) {
  if (receiveInfo != nullptr) {
    handleReceivedData(receiveInfo->src_addr, incomingData, length);
  }
}
#else
void onDataReceived(const uint8_t* senderMac, const uint8_t* incomingData, int length) {
  handleReceivedData(senderMac, incomingData, length);
}
#endif

bool takeReceivedPacket(RcData& packet) {
  bool available = false;

  portENTER_CRITICAL(&packetMutex);
  if (packetPending) {
    packet = receivedPacket;
    packetPending = false;
    available = true;
  }
  portEXIT_CRITICAL(&packetMutex);

  return available;
}

bool configureRadio() {
#if defined(WOKWI)
  return true;
#else
  WiFi.mode(WIFI_STA);
  WiFi.setSleep(false);

  if (esp_wifi_set_bandwidth(WIFI_IF_STA, WIFI_BW_HT20) != ESP_OK) {
    return false;
  }

  if (esp_now_init() != ESP_OK) {
    return false;
  }

  if (esp_wifi_config_espnow_rate(WIFI_IF_STA, WIFI_PHY_RATE_24M) != ESP_OK) {
    esp_now_deinit();
    return false;
  }

  return esp_now_register_recv_cb(onDataReceived) == ESP_OK;
#endif
}

void applyPacket(const RcData& packet, uint32_t now) {
  if (!channelValid(packet.elevator) || !channelValid(packet.aileron)) {
    linkActive = false;
    stopMotors();
    return;
  }

  const int throttle = channelToMotorCommand(packet.elevator);
  const int steering = channelToMotorCommand(packet.aileron);
  const int leftMotor = constrain(throttle - steering, -Pwm::MaximumDuty, Pwm::MaximumDuty);
  const int rightMotor = constrain(throttle + steering, -Pwm::MaximumDuty, Pwm::MaximumDuty);

  lastValidPacketMilliseconds = now;
  linkActive = true;
  writeBridge(Pwm::LeftIn1Channel, Pwm::LeftIn2Channel, leftMotor);
  writeBridge(Pwm::RightIn1Channel, Pwm::RightIn2Channel, rightMotor);
}

void setup() {
  Serial.begin(115200);
  if (!configureMotorOutputs()) {
    Serial.println("Driver disabled: PWM initialization failed");
    return;
  }

#if defined(WOKWI)
  Serial.println("Wokwi simulation mode active");
  Serial.println("Send packets as: packet aileron elevator throttle rudder aux1 aux2 aux3 aux4");
  radioReady = true;
  setDriverEnabled(true);
  return;
#endif

  if (!senderMacConfigured()) {
    Serial.println("Driver disabled: configure AllowedSenderMac first");
    return;
  }

  radioReady = configureRadio();
  if (!radioReady) {
    Serial.println("Driver disabled: ESP-NOW initialization failed");
    return;
  }

  setDriverEnabled(true);
  Serial.print("Receiver MAC: ");
  Serial.println(WiFi.macAddress());
}

void loop() {
  if (!radioReady) {
    stopMotors();
    delay(20);
    return;
  }

  const uint32_t now = millis();

#if defined(WOKWI)
  RcData packet{};
  if (takeSimulationPacket(packet)) {
    applyPacket(packet, now);
  }
#else
  RcData packet{};
  if (takeReceivedPacket(packet)) {
    applyPacket(packet, now);
  }
#endif

  if (!linkActive || static_cast<uint32_t>(now - lastValidPacketMilliseconds) >= LinkTimeoutMilliseconds) {
    linkActive = false;
    stopMotors();
  }
}
