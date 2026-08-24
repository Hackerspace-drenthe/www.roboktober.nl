# DRV8833 breakout v2 contract

The purchased module has **no `nSLEEP` pin**. It exposes two rows of six pins at 2.54 mm pitch; the seven-hole breadboard width gives a 15.24 mm center-to-center distance between the rows. Pad order is confirmed against the physical silkscreen below — do not assume another DRV8833 board uses this geometry or pin order.

| Carrier pad | Silkscreen | Signal | Purpose |
| ---: | --- | --- | --- |
| 1 | `IN4` | `BIN2_GPIO10` | Right motor phase input 2 |
| 2 | `IN3` | `BIN1_GPIO9` | Right motor phase input 1 |
| 3 | `GND` | `GND` | Common return |
| 4 | `VCC` | `VBAT_SW` | Switched raw 2S motor supply (6.0-8.4 V) |
| 5 | `IN2` | `AIN2_GPIO6` | Left motor phase input 2 |
| 6 | `IN1` | `AIN1_GPIO7` | Left motor phase input 1 |
| 7 | `EEP` | `GND` | Exposed thermal/enable pad; tie to ground |
| 8 | `OUT1` | `M1A` | Left motor terminal A |
| 9 | `OUT2` | `M1B` | Left motor terminal B |
| 10 | `OUT3` | `M2A` | Right motor terminal A |
| 11 | `OUT4` | `M2B` | Right motor terminal B |
| 12 | `ULT` (nFAULT) | `FLT_NC` | Open-drain fault output; left unconnected/spare |

## Required support circuitry

- Place `C1 = 100 nF` and `C2 = 100 μF` between driver VM and ground near the breakout.
- Do not bias GPIO9. It is an ESP32-C3 strapping pin.
- Verify the breakout's maximum motor current against measured motor stall current.
- Tie `EEP` (pad 7) to `GND` per the module's datasheet recommendation.

## No hardware sleep control (deferred)

This module has no `nSLEEP` pin, so there is no direct hardware sleep input on the breakout. The board-level control signal is therefore implemented via `GPIO5` as a software-controlled driver disable line, while the firmware still zeroes the PWM outputs on reset and link loss. This keeps the pin available without conflicting with the ESP's internal boot/strapping behavior — see [esp32-c3-supermini-v2.md](esp32-c3-supermini-v2.md).

If hardware-level sleep/disable is needed later, consider adding an external MOSFET load switch on `VCC`/`VM` controlled by a spare GPIO, rather than assuming a sleep pin exists on this breakout. Not implemented now; revisit only if needed.
