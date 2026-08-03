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

## Optional 2S battery variant

The released baseline remains a protected 1S LiPo. A 2S LiPo/Li-ion pack (6.0 V discharged, 7.4 V nominal and 8.4 V fully charged) is an optional future variant, not a drop-in battery replacement.

The selected architecture and component requirements are now maintained separately in `electrical-design-v2-2s.md`. The notes below explain the original decision space; they do not change this 1S net contract or PCB.

The ESP32 supply branch requires a 3.3 V step-down (buck) regulator with all of these properties:

- input operating range covering at least 6.0-8.4 V, with a recommended absolute input rating of at least 12 V for switching margin;
- regulated 3.3 V output within the ESP32-C3 module's permitted supply range;
- at least 500 mA continuous output and preferably 1 A rated output for radio transients and thermal margin;
- stable transient response with the selected input/output capacitors and no damaging output overshoot during switch-on or battery connection;
- quiescent current, efficiency, module dimensions, pin order and thermal rise recorded for the selected part.

At 500 mA and 3.3 V the load is 1.65 W. At a 6.0 V battery voltage and 85% efficiency, the regulator input current is approximately 0.32 A. This estimate sizes only the ESP32 branch; it does not include motor current.

The motor branch must use one of these architectures:

1. Feed `VBAT_SW` directly to the DRV8833 only when the exact breakout, capacitors and motors are explicitly rated for 8.4 V and measured stall current.
2. Add a separate high-current buck regulator between `VBAT_SW` and the DRV8833 VM input, set to the selected motor voltage. This regulator must tolerate at least 3.0 A combined stall current plus startup margin; do not use the ESP32 regulator for this branch.

The 2S pack must have cell-level protection and be charged with a 2S balance charger. Battery connector, switch, wiring and protection must be rated above the measured combined motor stall current plus the regulator input current. Recheck the present JST-PH battery connector before using it at this current.

Data required before selecting parts or changing the schematic/PCB:

1. Exact N20 motor rated voltage and left/right stall current at the intended motor-bus voltage.
2. Exact DRV8833 breakout input-voltage and continuous/peak-current ratings, including onboard capacitor voltage ratings.
3. Desired motor-bus voltage: direct 2S, regulated 6 V or another measured value.
4. Battery capacity, continuous and burst discharge ratings, protection/BMS rating, connector and charger.
5. Selected 3.3 V buck datasheet, pinout, footprint, capacitor requirements, efficiency and thermal test at 6.0 V and 8.4 V input.
6. If required, selected motor buck datasheet, footprint, switching frequency, current limit and transient/thermal test at combined stall.

Do not connect a 2S pack to the current 1S PCB until these items are resolved and the affected voltage ratings, labels, clearances and test procedure are updated.

## Release blockers

The v2 PCB remains placement-only until these supplier-specific facts are recorded:

1. Exact ESP32-C3 SuperMini mechanical drawing and header orientation.
2. Exact DRV8833 breakout dimensions, pad order and current rating.
3. Exact buck-boost module dimensions and pin order.
4. Measured left and right motor stall current at fully charged 1S voltage.
5. Selected switch and connector DC-current ratings.

Do not generate manufacturing files while any blocker is unresolved.
