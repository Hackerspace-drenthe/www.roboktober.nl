# Roboktober controller firmware v2

Reviewed derivative of the MIT-licensed WORC ESP32-C3/DRV8833 firmware. The exact upstream snapshot and license are stored in `../upstream/worc-robot-controller-esp32c3/`.

## Hardware contract

| Function | ESP32-C3 GPIO | DRV8833 signal | PWM channel |
| --- | ---: | --- | ---: |
| Driver enable (firmware only) | 5 | Not connected; this DRV8833 breakout has no `nSLEEP` pin | - |
| Left motor input 2 | 6 | `AIN2` | 1 |
| Left motor input 1 | 7 | `AIN1` | 0 |
| Status LED, module only | 8 | Not routed on carrier | - |
| Right motor input 1 | 9 | `BIN1` | 2 |
| Right motor input 2 | 10 | `BIN2` | 3 |

GPIO2, GPIO8 and GPIO9 are ESP32-C3 strapping pins. V2 exposes GPIO2 with a boot-safety warning, does not add an external load to GPIO8 and does not add a pull-up or pull-down to GPIO9. The firmware still toggles GPIO5 (named `DriverSleep` in the sketch) as a defense-in-depth carryover from upstream, but this DRV8833 breakout has no `nSLEEP` pin, so GPIO5 is left unconnected on the carrier. Motor safety on reset/link-loss does not depend on this pin: `stopMotors()` independently zeroes the PWM/digital motor outputs.

GPIO0 through GPIO4, GPIO20 and GPIO21 are reserved for the optional J8 expansion header. GPIO2 is a strapping pin and must remain unbiased during reset. Firmware v2 does not configure or drive these expansion pins. Accessory firmware must initialize only the pins it owns and must leave motor-control and strapping pins unchanged.

## Required configuration

Replace `AllowedSenderMac` in the sketch with the station MAC address of the transmitter. The all-zero placeholder deliberately keeps the driver disabled.

```cpp
constexpr uint8_t AllowedSenderMac[6] = {0x24, 0x6F, 0x28, 0x00, 0x00, 0x01};
```

The packet layout remains compatible with upstream: eight unsigned 16-bit RC channels in this order: aileron, elevator, throttle, rudder, AUX1 through AUX4.

## Behavior

- Elevator controls throttle; aileron controls steering.
- RC range is 960-2040 μs, neutral is 1500 μs and input deadband is ±40 μs.
- Four 5 kHz, 8-bit PWM channels drive the DRV8833 phase inputs.
- The direct-2S variant permits the full 255/255 duty range. Its motors and driver must therefore be validated for continuous 8.4 V operation and abnormal stall conditions.
- A valid packet must be exactly 16 bytes and come from `AllowedSenderMac`.
- Invalid channel values stop both motors immediately.
- No valid packet for 200 ms stops both motors using rollover-safe elapsed-time arithmetic.
- The motor driver's phase inputs are held low until outputs and ESP-NOW are initialized.

## Improvements over upstream

1. Checks packet length before copying data.
2. Rejects senders other than the configured transmitter.
3. Protects callback data shared between the Wi-Fi task and Arduino loop.
4. Resets motor commands on invalid input instead of retaining stale throttle.
5. Uses a rollover-safe link timeout.
6. Zeroes motor PWM/digital outputs when configuration or radio initialization fails, independent of the unconnected `DriverSleep` (GPIO5) pin.
7. Removes the unused EEPROM dependency and avoids manually reinitializing the network stack.

MAC filtering is not cryptographic authentication. For events where intentional radio interference is in scope, enable ESP-NOW encryption and provision peer keys.

## Toolchain

The firmware has compatibility branches for Arduino ESP32 core 2.x and 3.x. It was compiled on 2026-07-27 with Arduino CLI 1.2.2, ESP32 core 3.0.7 and board `esp32:esp32:nologo_esp32c3_super_mini`:

```bash
arduino-cli compile \
	--fqbn esp32:esp32:nologo_esp32c3_super_mini \
	electronics/firmware/roboktober-controller-v2
```

The resulting build used 909632 bytes of flash and 33844 bytes of dynamic memory. The upstream project documents core 2.0.14; compile that compatibility branch in CI or before declaring support for the older toolchain.

Test with wheels raised and a current-limited supply. Verify loss-of-link shutdown before placing the robot in an arena.
