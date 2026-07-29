# ESP32-C2 SuperMini pin contract

## Logical assignment

| Function | ESP32 signal | Destination | Boot behavior |
| --- | --- | --- | --- |
| Left motor input 1 | `GPIO0` | DRV8833 `AIN1` | Keep motor disabled during boot |
| Left motor input 2 | `GPIO1` | DRV8833 `AIN2` | Keep motor disabled during boot |
| Right motor input 1 | `GPIO2` | DRV8833 `BIN1` | Verify module strapping behavior |
| Right motor input 2 | `GPIO3` | DRV8833 `BIN2` | Verify module strapping behavior |
| Spare | `GPIO4` | Header only | Available for receiver or sensor |
| Spare | `GPIO5` | Header only | Available for receiver or sensor |
| Spare | `GPIO6` | Header only | Available for receiver or sensor |
| Spare | `GPIO7` | Header only | Available for receiver or sensor |
| Boot/strapping | `GPIO8` | Header only | Do not load until module is verified |
| Spare | `GPIO9` | Header only | Verify against purchased module |
| Logic supply | `3V3` | Regulator output | Regulated 3.3 V only |
| Ground | `GND` | Common ground | Two ground header positions preferred |

## Carrier header contract

The KiCad carrier reserves two seven-position, 2.54 mm headers. These names describe the carrier signals, not a guaranteed vendor module order.

| Left header pin | Carrier signal | Right header pin | Carrier signal |
| ---: | --- | ---: | --- |
| 1 | `3V3` | 1 | `GPIO4` |
| 2 | `GND` | 2 | `GPIO5` |
| 3 | `3V3` | 3 | `GPIO6` |
| 4 | Spare | 4 | `GPIO0 / AIN1` |
| 5 | Spare | 5 | `GPIO1 / AIN2` |
| 6 | Spare | 6 | `GPIO2 / BIN1` |
| 7 | Spare | 7 | `GPIO3 / BIN2` |

## Required verification

1. Identify the exact module manufacturer and product revision.
2. Compare every header pin with its schematic or continuity-test the module.
3. Update both KiCad header pad nets if the physical order differs.
4. Confirm that selected motor GPIOs are output-capable and do not prevent boot.
5. Power the module from a current-limited 3,3 V supply before fitting the DRV8833.