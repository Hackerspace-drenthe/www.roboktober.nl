# ESP32-C3 SuperMini v2 pin contract

Dit contract is geharmoniseerd met de actuele v4 PCB-layout (electronics/kicad/hsd-antweight-2s-v4/hsd-antweight-2s-v4.kicad_pcb).

## Logische toewijzing (firmware -> driver)

| Functie | ESP-signaal | Bestemming |
| --- | --- | --- |
| Sleep/control | GPIO5 | DRV8833 control line |
| Left input 2 | GPIO6 | DRV8833 AIN2 |
| Left input 1 | GPIO7 | DRV8833 AIN1 |
| Right input 1 | GPIO9 | DRV8833 BIN1 |
| Right input 2 | GPIO10 | DRV8833 BIN2 |
| Logic supply | ESP_5V_IN | ESP module voedingsingang |
| Ground | GND | Common ground |

## U1 padmapping op de v4 PCB

U1 staat op F.Cu. De onderstaande mapping is rechtstreeks van de PCB afgeleid.

| U1 pad | Net |
| ---: | --- |
| 1 | GPIO5 |
| 2 | AIN2_GPIO6 |
| 3 | AIN1_GPIO7 |
| 4 | GPIO8_LED_EXPANSION |
| 5 | BIN1_GPIO9 |
| 6 | BIN2_GPIO10 |
| 7 | GPIO20_SPARE |
| 8 | GPIO21_SPARE |
| 9 | GPIO0_SPARE |
| 10 | GPIO1_SPARE |
| 11 | GPIO2_SPARE |
| 12 | GPIO3_SPARE |
| 13 | GPIO4_SPARE |
| 14 | 3V3 |
| 15 | GND |
| 16 | ESP_5V_IN |

## Power selector en accessoireheader

### JP1 (3-pin selector)

| JP1 pad | Net | Betekenis |
| ---: | --- | --- |
| 1 | REG_5V_OUT | Externe regelaar-uitgang |
| 2 | ESP_5V_IN | ESP voedingsingang |
| 3 | VBAT_SW | Geschakelde batterijlijn |

Gebruik een shunt:
- 1-2 voor REG_5V_OUT -> ESP_5V_IN
- 2-3 voor VBAT_SW -> ESP_5V_IN

### J10 (1x3 accessoire/power)

| J10 pad | Net |
| ---: | --- |
| 1 | ESP_5V_IN |
| 2 | GND |
| 3 | VBAT_SW |

## Verificatie-eisen

1. Controleer U1 pinout met continuiteitstest op de fysieke module/socket.
2. Verifieer bootgedrag van strapping pins (met name GPIO8/GPIO9).
3. Start eerste test met stroomlimiet en zonder motorbelasting.
