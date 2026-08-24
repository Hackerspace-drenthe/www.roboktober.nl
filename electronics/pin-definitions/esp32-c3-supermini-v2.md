# ESP32-C3 SuperMini v2 pin contract

This contract follows the WORC DRV8833 firmware at upstream commit `933d0f5f65ad239410037045cb9ca3b6130f2128`.

## Logical assignment

| Function | Signal | Destination | Reset requirement |
| --- | --- | --- | --- |
| Sleep/disable control | `GPIO5` | DRV8833 breakout sleep/disable signal (board-level control) | Use `GPIO5` as the designated board sleep/control line; keep it distinct from the internal ESP boot/strapping functions |
| Left motor input 2 | `GPIO6` | DRV8833 `AIN2` | - |
| Left motor input 1 | `GPIO7` | DRV8833 `AIN1` | - |
| Module status LED | `GPIO8` | No carrier connection | Strapping pin; do not add an external load |
| Right motor input 1 | `GPIO9` | DRV8833 `BIN1` | Strapping pin; do not add a pull resistor |
| Right motor input 2 | `GPIO10` | DRV8833 `BIN2` | - |
| Logic supply | `ESP_5V_IN` | Regulated 5V module input, approx. 3.7-5.0 V valid for the tested module | Do not feed raw battery directly unless the module is explicitly validated |
| Internal logic rail | `3V3` | Generated inside the ESP32-C3 module | The module's onboard regulator creates the logic rail; no separate raw 3.3 V battery feed |
| Ground | `GND` | Common ground | Join controller, driver and regulator returns |

## Abstract 2x8 carrier header

The PCB reserves two rows of eight pins at 2.54 mm pitch. The measured row spacing is six pitch intervals, or 15.24 mm center-to-center. Pin order remains vendor-specific and must be checked against the purchased module.

Pad numbering wraps DIP-style: pad 1 starts the left column top-to-bottom, then pad 9 continues the right column bottom-to-top, so pad 8 and pad 9 sit on the same physical row (nearest the board edge) and pad 1 and pad 16 sit on the opposite row. Each table row below lists a real physical row, confirmed against the module's official pinout diagram (5V/GPIO21 on the row nearest the USB-C connector, GPIO0/GPIO5 on the row nearest the antenna edge).

| Pad | Carrier signal | Pad | Carrier signal |
| ---: | --- | ---: | --- |
| 1 | `ESP_5V_IN` | 16 | `GPIO21_SPARE` |
| 2 | `GND` | 15 | `GPIO20_SPARE` |
| 3 | `3V3` | 14 | `GPIO10 / BIN2` |
| 4 | `GPIO4_SPARE` | 13 | `GPIO9 / BIN1` |
| 5 | `GPIO3_SPARE` | 12 | `GPIO8 / LED, no carrier load` |
| 6 | `GPIO2_SPARE / strap` | 11 | `GPIO7 / AIN1` |
| 7 | `GPIO1_SPARE` | 10 | `GPIO6 / AIN2` |
| 8 | `GPIO0_SPARE` | 9 | `GPIO5 / spare` |

## Adjacent pin breakout rows J8 and J9

Every ESP32 socket pad now has one matching unpopulated solder pad exactly one 2.54 mm grid step farther from the module. J8 mirrors the full left U1 row (pads 1-8) including `ESP_5V_IN`, `GND` and `3V3`. J9 mirrors the full right U1 row (pads 9-16), so J9 pad 1 corresponds to U1 pad 9, J9 pad 2 to U1 pad 10, and so on up to J9 pad 8 corresponding to U1 pad 16.

| J8 pad | Left signal | J9 pad | Right signal |
| ---: | --- | ---: | --- |
| 1 | `ESP_5V_IN` | 1 | `GPIO5 / sleep control` |
| 2 | `GND` | 2 | `GPIO6 / AIN2` |
| 3 | `3V3` | 3 | `GPIO7 / AIN1` |
| 4 | `GPIO4` | 4 | `GPIO8 / onboard LED / strap` |
| 5 | `GPIO3` | 5 | `GPIO9 / BIN1 / strap` |
| 6 | `GPIO2 / strap` | 6 | `GPIO10 / BIN2` |
| 7 | `GPIO1` | 7 | `GPIO20` |
| 8 | `GPIO0` | 8 | `GPIO21` |

## Accessory power header J10

ESP power standard on this controller: `ESP_5V_IN` with the module's onboard regulator creating the 3.3 V domain.

J10 is a horizontal unpopulated 1x3 header below the DRV8833 and between the left and right motor connectors.

| J10 pad | Signal | Use |
| ---: | --- | --- |
| 1 | `ESP_5V_IN (3.7-5V valid input for tested module)` | Regulated 5V feed to the ESP module; direct 1S is allowed only on the validated module |
| 2 | `GND` | Common return |
| 3 | `VBAT_SW` | Switched raw 2S battery rail; up to 8.4 V; silk label on J10 is `VLIPO` |

Motor-control pins are also duplicated for measurement and future reconfiguration, but accessories must not contend with firmware outputs. GPIO2, GPIO8 and GPIO9 are boot-sensitive strapping pins. Expansion loads must not source current into GPIO pins while the controller is unpowered. `VBAT_SW` is not a regulated rail; the ESP must receive a valid 5V rail via `ESP_5V_IN` or the tested direct-1S exception. `5V_NC` is no longer the active design meaning; use the validated `ESP_5V_IN` path and keep all raw battery feed rules behind the v3-fix power policy.

## Mandatory verification

1. Compare the module silkscreen and continuity measurements with every carrier pad.
2. Confirm the purchased module matches the recorded 15.24 mm row spacing and 2.54 mm pin pitch.
3. Confirm the module accepts regulated 3.3 V on its `3V3` pin.
4. Verify GPIO8 and GPIO9 boot behavior with the DRV8833 module fitted.
5. Confirm the firmware still zeroes motor PWM/digital outputs on reset (`stopMotors()`); this breakout has no `nSLEEP` pin to gate the driver in hardware.
6. Perform the first power-up without motors and with a current limit.
