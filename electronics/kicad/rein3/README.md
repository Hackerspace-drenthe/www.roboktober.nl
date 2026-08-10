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

## 3D render (current revision)

Generated artifact:
- `antweight-controller-v2-3d-render-2026-08-10.png`
- `antweight-controller-v2-3d-with-components-top-2026-08-10.png`
- `antweight-controller-v2-3d-with-components-bottom-2026-08-10.png`
- `antweight-controller-v2-3d-without-components-top-2026-08-10.png`
- `antweight-controller-v2-3d-without-components-bottom-2026-08-10.png`

Render command (reproducible):

```bash
kicad-cli pcb render \
	--width 2200 \
	--height 1600 \
	--background transparent \
	--quality high \
	--perspective \
	--zoom 1.35 \
	--rotate "-35,0,25" \
	--output electronics/kicad/rein3/antweight-controller-v2-3d-render-2026-08-10.png \
	electronics/kicad/rein3/antweight-controller-v2.kicad_pcb
```

Render both sides with components:

```bash
for side in top bottom; do
	kicad-cli pcb render \
		--width 2200 \
		--height 1600 \
		--background transparent \
		--quality high \
		--perspective \
		--zoom 1.35 \
		--rotate "-35,0,25" \
		--side "$side" \
		--output "electronics/kicad/rein3/antweight-controller-v2-3d-with-components-${side}-2026-08-10.png" \
		electronics/kicad/rein3/antweight-controller-v2.kicad_pcb
done
```

Render both sides without components (temporary footprint-stripped board):

```bash
PCB="electronics/kicad/rein3/antweight-controller-v2.kicad_pcb"
TMP="electronics/kicad/rein3/antweight-controller-v2-no-components.tmp.kicad_pcb"

perl -ne '
sub balance { my ($s)=@_; my $o=()=$s=~/\(/g; my $c=()=$s=~/\)/g; return $o-$c; }
if (!$skip && /\(footprint /) { $skip=1; $depth=balance($_); next; }
if ($skip) { $depth += balance($_); if ($depth <= 0) { $skip=0; } next; }
print;
' "$PCB" > "$TMP"

for side in top bottom; do
	kicad-cli pcb render \
		--width 2200 \
		--height 1600 \
		--background transparent \
		--quality high \
		--perspective \
		--zoom 1.35 \
		--rotate "-35,0,25" \
		--side "$side" \
		--output "electronics/kicad/rein3/antweight-controller-v2-3d-without-components-${side}-2026-08-10.png" \
		"$TMP"
done

rm -f "$TMP"
```

Notes:
- This render is for design review and communication.
- The board is still development-stage unless explicitly marked manufacturing-ready.
- Re-run DRC after routing changes before generating manufacturing output.

## Routing completion checklist (from latest DRC)

Current blocker count: 0 unconnected items, 0 DRC violations.

Use this as the finishing checklist before a manufacturing-ready release.

### 1. Ground and power backbone

- [ ] Connect all `GND` islands between `U3`, `J4`, `J10`, `C1_C2`, and `U2`.
- [ ] Connect `VBAT_SW` chain between `U3`, `SW1`, `C1_C2`, `U2`, and `J10`.
- [ ] Connect `3V3` from `U3` to `J10` and to the existing `3V3` track near `U2`.

### 2. Driver control signals (ESP32 -> DRV8833)

- [ ] Connect `AIN2_GPIO6` from `U2` control pad to the matching track segment.
- [ ] Connect `AIN1_GPIO7` from `U2` control pad to the matching track segment.
- [ ] Connect `BIN1_GPIO9` from `U2` control pad to the matching track segment.
- [ ] Connect `BIN2_GPIO10` from `U2` control pad to the matching track segment.

### 3. Motor output paths (DRV8833 -> connectors)

- [ ] Connect `M1A` from `U2` to `J5`.
- [ ] Connect `M1B` from `U2` to `J5`.
- [ ] Connect `M2A` from `U2` to `J6`.
- [ ] Connect `M2B` from `U2` to `J6`.

### 4. Release gates (must all pass)

- [ ] `kicad-cli pcb drc ... --severity-all` returns 0 DRC violations.
- [ ] Unconnected items count is `0` (not 20).
- [ ] ERC/DRC rerun after any footprint replacement.
- [ ] Board-level visual review done for antenna clearance and high-current routes.

When all checklist items are complete, the board can move from "partially routed" to "candidate for manufacturing review".