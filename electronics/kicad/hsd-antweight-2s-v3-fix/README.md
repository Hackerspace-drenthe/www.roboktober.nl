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
| `U1` | ESP32-C3 SuperMini | `1=GPIO5_SPARE`, `2=AIN2_GPIO6`, `3=AIN1_GPIO7`, `4=GPIO8_LED_EXPANSION`, `5=BIN1_GPIO9`, `6=BIN2_GPIO10`, `7=GPIO20_SPARE`, `8=GPIO21_SPARE`, `9=GPIO0_SPARE`, `10=GPIO1_SPARE`, `11=GPIO2_SPARE`, `12=GPIO3_SPARE`, `13=GPIO4_SPARE`, `14=3V3`, `15=GND`, `16=5V_NC` |
| `SW1` | Main power switch | `1=VBAT_RAW`, `2=VBAT_SW` |
| `U3` | External regulator header | `1=VBAT_SW`, `2=GND`, `3=REG_3V3_2S` |
| `JP1` | ESP power select jumper | `1=REG_3V3_2S`, `2=3V3`, `3=VBAT_SW` |
| `J10` | Accessory power header | `1=3V3`, `2=GND`, `3=VBAT_SW` |
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