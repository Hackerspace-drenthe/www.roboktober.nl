# KiCad antweight controller v2

This KiCad 9 project implements the ESP32-C3/DRV8833 v2 net contract.

## Status

The schematic is electrically documented and the PCB is **placement-only**. The compact 30.48 x 58.42 mm strip layout uses two M2 mounting holes and machined socket-strip placeholders for the ESP32-C3 and DRV8833 modules. The GPIO breakout rows sit one 2.54 mm step outside the matching ESP32 pins. Both motor connectors sit directly below the DRV8833, accessory power is centered between them, and the battery, switch, regulator and driver capacitors form a short power-path cluster on the right. All footprints remain abstract until exact supplier parts are selected. The PCB intentionally has no copper routing and DRC must report open connections until exact supplier parts and measured motor stall current are recorded.

Do not generate or order manufacturing files from this revision.

## Validation

```bash
kicad-cli sch erc electronics/kicad/antweight-controller-v2/antweight-controller-v2.sch
kicad-cli sch export netlist electronics/kicad/antweight-controller-v2/antweight-controller-v2.sch
kicad-cli pcb drc electronics/kicad/antweight-controller-v2/antweight-controller-v2.kicad_pcb --severity-all
```

Expected at this stage:

- schema and PCB parse successfully in KiCad 9;
- the legacy schematic netlist command completes, but KiCad 9 exports an empty netlist until this hand-written `.sch` is converted to native `.kicad_sch`;
- no PCB footprint or copper-clearance errors;
- 39 unconnected placement ratlines remain, including the unpopulated J8, J9 and J10 headers;
- KiCad 9 reports cosmetic silkscreen warnings around the abstract placement placeholders;
- legacy schematic ERC reports abstract-header and label warnings.

Before routing, convert the schematic to native KiCad format, replace all `VERIFY` footprints, rerun ERC/DRC, inspect antenna clearance and size power traces for measured stall current.

The U1/U2 socket pads currently retain conservative 1.0 mm drills. Update pad and annular-ring dimensions from the selected machined socket strip datasheet before manufacturing.