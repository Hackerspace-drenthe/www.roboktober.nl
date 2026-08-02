# KiCad antweight controller v2

This KiCad 9 project implements the ESP32-C3/DRV8833 v2 net contract.

## Status

The schematic is electrically documented and the PCB is **partially routed**. The compact 30.48 x 63.50 mm strip layout uses two M2 mounting holes and machined socket-strip placeholders for the ESP32-C3 and DRV8833 modules. The DRV8833 breakout is rotated 90 degrees clockwise, with its input pins facing the ESP32 and its output pins facing the motors. A 5.08 mm length increase provides additional routing space between both modules. The GPIO breakout rows sit one 2.54 mm step outside the matching ESP32 pins. Both motor connectors sit directly below the DRV8833, accessory power is centered between them, and the battery, switch, regulator and driver capacitors form a short power-path cluster on the right. All footprints remain abstract until exact supplier parts are selected. Motor-current sizing assumes 1.5 A maximum per motor and 3.0 A combined on shared power paths.

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
- PCB DRC reports zero violations;
- 20 unconnected routing items remain;
- legacy schematic ERC reports abstract-header and label warnings.

Before completing routing, convert the schematic to native KiCad format, replace all `VERIFY` footprints, rerun ERC/DRC and inspect antenna clearance.

The U1/U2 socket pads currently retain conservative 1.0 mm drills. Update pad and annular-ring dimensions from the selected machined socket strip datasheet before manufacturing.