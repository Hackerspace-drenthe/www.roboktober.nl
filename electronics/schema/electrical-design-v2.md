# Electrical design v2

## Net contract

| Net | Description |
| --- | --- |
| `VBAT_RAW` | Direct 1S LiPo positive before the main switch |
| `VBAT_SW` | Switched driver and regulator input |
| `3V3` | Regulated ESP32-C3 supply |
| `GND` | Shared power and signal return |
| `GPIO5_SPARE` | Spare GPIO; formerly wired to `nSLEEP`, but this DRV8833 breakout has no sleep pin |
| `AIN1_GPIO7` | Left bridge input 1 |
| `AIN2_GPIO6` | Left bridge input 2 |
| `BIN1_GPIO9` | Right bridge input 1; no external bias because GPIO9 is a strapping pin |
| `BIN2_GPIO10` | Right bridge input 2 |
| `M1A`, `M1B` | Left motor outputs |
| `M2A`, `M2B` | Right motor outputs |
| `GPIO0_SPARE` through `GPIO4_SPARE` | Expansion GPIO; GPIO2 is a strapping pin and must remain unbiased during reset |
| `GPIO20_SPARE`, `GPIO21_SPARE` | Expansion GPIO, including optional UART use |

## Improvements over v1

- Matches the reviewed ESP32-C3 firmware instead of the unrelated provisional ESP32-C2 GPIO0-3 mapping.
- Corrects the DRV8833 carrier pinout to the module actually used: `IN1`-`IN4`, `VCC`, `GND`, `OUT1`-`OUT4`, `EEP` (tied to `GND`) and `ULT`/`FLT_NC` (unconnected fault output). This module has no `nSLEEP` pin, so `GPIO5` is left spare/unconnected; motor safety on reset still relies on firmware zeroing PWM/digital outputs (`stopMotors()`).
- Keeps ESP32-C3 strapping pins free from carrier pull resistors.
- Separates the unmodified upstream snapshot from the reviewed local firmware.
- Requires measured stall-current and undervoltage checks before production release.
- Mirrors every ESP32 GPIO/signal pin to an adjacent unpopulated solder pad, while moving accessory `3V3`, `GND` and `VBAT_SW` to a dedicated header below the DRV8833 between both motor connectors.

## Future consideration: hardware sleep/disable

The DRV8833 breakout used in this design has no `nSLEEP` pin, so there is no hardware pull-down/enable circuit for the driver. If hardware-level sleep/disable is needed later, consider an external MOSFET load switch on `VCC`/`VM` controlled by a spare GPIO. Not implemented now; revisit only if needed.

## Power integrity

- `C1`, 100 nF ceramic, belongs at the DRV8833 VM/GND pins.
- `C2`, at least 100 μF low-ESR, belongs next to the driver breakout.
- Each brushed motor needs a 100 nF ceramic capacitor directly across its terminals.
- The 3.3 V regulator must support ESP32-C3 radio current peaks with margin; use at least 500 mA continuous rating and verify transient response.
- Keep motor-current loops short and away from the ESP32 antenna area.

## Release blockers

The v2 PCB remains placement-only until these supplier-specific facts are recorded:

1. Exact ESP32-C3 SuperMini mechanical drawing and header orientation.
2. Exact DRV8833 breakout dimensions, pad order and current rating.
3. Exact buck-boost module dimensions and pin order.
4. Measured left and right motor stall current at fully charged 1S voltage.
5. Selected switch and connector DC-current ratings.

Do not generate manufacturing files while any blocker is unresolved.
