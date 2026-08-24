# KiCad antweight controller v3 - hsd-antweight-2s-v3

This folder is the current v3 project. The PCB is the source of truth:
reference designators, pinout, routing, and mechanical placement all follow
`hsd-antweight-2s-v3.kicad_pcb`.

The schematic in this folder is kept in sync with the PCB for documentation and
tooling, but when there is any mismatch, the PCB wins.

## Current PCB reality

Reference-to-function mapping in the board:

| Ref | Role | Key net mapping |
| --- | --- | --- |
| `U1` | ESP32-C3 SuperMini | `1=GPIO5`, `2=AIN2_GPIO6`, `3=AIN1_GPIO7`, `4=GPIO8_LED_EXPANSION`, `5=BIN1_GPIO9`, `6=BIN2_GPIO10`, `7=GPIO5` (driver sleep/control), `8=GPIO21_SPARE`, `9=GPIO0_SPARE`, `10=GPIO1_SPARE`, `11=GPIO2_SPARE`, `12=GPIO3_SPARE`, `13=GPIO4_SPARE`, `14=3V3`, `15=GND`, `16=ESP_5V_IN` |
| `SW1` | Main power switch | `1=VBAT_RAW`, `2=VBAT_SW` |
| `U3` | External regulator header | `1=VBAT_SW`, `2=GND`, `3=REG_5V_OUT` |
| `JP1` | ESP power select jumper | `1=REG_5V_OUT`, `2=ESP_5V_IN`, `3=VBAT_SW` |
| `J10` | Accessory power header | `1=ESP_5V_IN (3.7-5V valid input)`, `2=GND`, `3=VBAT_SW` |
| `J4` | Battery connector | `1=VBAT_RAW`, `2=GND` |
| `J5` | Motor left | `1=M1A`, `2=M1B` |
| `J6` | Motor right | `1=M2A`, `2=M2B` |
| `U2` | DRV8833 carrier/socket | `1=AIN1_GPIO7`, `3=AIN2_GPIO6`, `4=M2B`, `5=VBAT_SW`, `6=M2A`, `7=GND`, `8=M1B`, `9=BIN1_GPIO9`, `10=M1A`, `11=BIN2_GPIO10` |
| `C1_C2` | Driver caps | `1=VBAT_SW`, `2=GND` |

## Notes

- The top/bottom copper, renders, and silkscreen overlay files in this folder
  are derived from the PCB.
- If a text label, schematic symbol, or render disagrees with the PCB, the PCB
  definition is the correct one.
- This revision intentionally powers the ESP32 via the board's `5V` input and uses
  the module's onboard regulator to create the 3.3V domain. There is no direct
  battery-to-3.3V bypass path on the logic rail.
- Power rule: `2S = VBAT -> REG_5V_OUT -> ESP_5V_IN`; `1S` direct to `ESP_5V_IN`
  requires an explicit shunt on `JP1` between `2` and `3` (`VBAT_SW -> ESP_5V_IN`),
  and is allowed only on the specifically validated, purchased ESP32-C3 module used
  in this build.
  Validation note: direct 1S feed at about 4.0V on the ESP 5V input (valid range
  approximately 3.7-5.0V for this tested module) was stable in test for a 5-minute
  race profile without reset or brownout.
- The current v3 artwork includes separate visualization outputs for the
  copper-track overlay and size/dimension previews.

## Current validation snapshot

Latest check on the v3 PCB:

- DRC violations: 1
- Unconnected pads: 0
- Remaining violation: the known `lib_footprint_mismatch` warning for `U1`

## Files of interest

- PCB: `hsd-antweight-2s-v3.kicad_pcb`
- Schematic: `hsd-antweight-2s-v3.kicad_sch`
- Project: `hsd-antweight-2s-v3.kicad_pro`
- Rules: `hsd-antweight-2s-v3.kicad_dru`
- Custom footprints: `Library.pretty/` and `Roboktober_Custom.pretty/`
- Visualizations: `renders/` and `artwork/`

## Render outputs

The v3 render sets currently used in this folder are:

- `renders/pcbway-assembled-top-*.png`
- `renders/pcbway-bare-top-*.png`
- `renders/pcbway-bare-bottom-*.png`
- `renders/v3-light/*.png`
- `renders/v3-light-size/*.png`
- `renders/v3-light-dimlines/*.png`
- `renders/v3-light-nocomponents/*.png`

The copper-overlay visualization is:

- `artwork/front-silkscreen-copper-overlay-cyan.svg`
- `artwork/front-silkscreen-copper-overlay-cyan.png`