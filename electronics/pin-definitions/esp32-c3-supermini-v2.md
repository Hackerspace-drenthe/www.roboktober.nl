# ESP32-C3 SuperMini v2 pin contract

This contract follows the WORC DRV8833 firmware at upstream commit `933d0f5f65ad239410037045cb9ca3b6130f2128`.

## Logical assignment

| Function | Signal | Destination | Reset requirement |
| --- | --- | --- | --- |
| Spare | `GPIO5` | No carrier connection | This DRV8833 breakout has no `nSLEEP` pin; firmware still toggles `GPIO5` but it is unconnected here |
| Left motor input 2 | `GPIO6` | DRV8833 `AIN2` | - |
| Left motor input 1 | `GPIO7` | DRV8833 `AIN1` | - |
| Module status LED | `GPIO8` | No carrier connection | Strapping pin; do not add an external load |
| Right motor input 1 | `GPIO9` | DRV8833 `BIN1` | Strapping pin; do not add a pull resistor |
| Right motor input 2 | `GPIO10` | DRV8833 `BIN2` | - |
| Logic supply | `3V3` | Regulator output | Regulated 3.3 V only |
| Ground | `GND` | Common ground | Join controller, driver and regulator returns |

## Abstract 2x8 carrier header

The PCB reserves two rows of eight pins at 2.54 mm pitch. The measured row spacing is six pitch intervals, or 15.24 mm center-to-center. Pin order remains vendor-specific and must be checked against the purchased module.

| Pad | Carrier signal | Pad | Carrier signal |
| ---: | --- | ---: | --- |
| 1 | `5V_NC` | 9 | `GPIO5 / spare` |
| 2 | `GND` | 10 | `GPIO6 / AIN2` |
| 3 | `3V3` | 11 | `GPIO7 / AIN1` |
| 4 | `GPIO4_SPARE` | 12 | `GPIO8 / LED, no carrier load` |
| 5 | `GPIO3_SPARE` | 13 | `GPIO9 / BIN1` |
| 6 | `GPIO2_SPARE / strap` | 14 | `GPIO10 / BIN2` |
| 7 | `GPIO1_SPARE` | 15 | `GPIO20_SPARE` |
| 8 | `GPIO0_SPARE` | 16 | `GPIO21_SPARE` |

## Adjacent pin breakout rows J8 and J9

Every ESP32 socket pad now has one matching unpopulated solder pad exactly one 2.54 mm grid step farther from the module. J8 mirrors the full left U1 row including `5V_NC`, `GND` and `3V3`. J9 mirrors the full right U1 row.

| J8 pad | Left signal | J9 pad | Right signal |
| ---: | --- | ---: | --- |
| 1 | `5V_NC` | 1 | `GPIO5 / spare` |
| 2 | `GND` | 2 | `GPIO6 / AIN2` |
| 3 | `3V3` | 3 | `GPIO7 / AIN1` |
| 4 | `GPIO4` | 4 | `GPIO8 / onboard LED / strap` |
| 5 | `GPIO3` | 5 | `GPIO9 / BIN1 / strap` |
| 6 | `GPIO2 / strap` | 6 | `GPIO10 / BIN2` |
| 7 | `GPIO1` | 7 | `GPIO20` |
| 8 | `GPIO0` | 8 | `GPIO21` |

## Accessory power header J10

J10 is a horizontal unpopulated 1x3 header below the DRV8833 and between the left and right motor connectors.

| J10 pad | Signal | Use |
| ---: | --- | --- |
| 1 | `3V3` | Regulated logic and sensor supply |
| 2 | `GND` | Common return |
| 3 | `VBAT_SW` | Switched raw 1S battery; up to 4.2 V |

Motor-control pins are also duplicated for measurement and future reconfiguration, but accessories must not contend with firmware outputs. GPIO2, GPIO8 and GPIO9 are boot-sensitive strapping pins. Expansion loads must not source current into GPIO pins while the controller is unpowered. `VBAT_SW` is not a 3.3 V supply. `5V_NC` remains intentionally unpowered even though it is now mirrored on J8 pad 1.

## Mandatory verification

1. Compare the module silkscreen and continuity measurements with every carrier pad.
2. Confirm the purchased module matches the recorded 15.24 mm row spacing and 2.54 mm pin pitch.
3. Confirm the module accepts regulated 3.3 V on its `3V3` pin.
4. Verify GPIO8 and GPIO9 boot behavior with the DRV8833 module fitted.
5. Confirm the firmware still zeroes motor PWM/digital outputs on reset (`stopMotors()`); this breakout has no `nSLEEP` pin to gate the driver in hardware.
6. Perform the first power-up without motors and with a current limit.
