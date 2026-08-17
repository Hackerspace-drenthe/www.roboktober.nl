# KiCad antweight controller v2 - hsd-antweight-2s-v2

This KiCad 9 project is the current 2S-oriented board variant with
externalized power interfaces.

Current board silk details:

- logo area: `HACKERSPACE` above `DRENTHE`
- J10 pad 3 raw rail silk: `VLIPO` (electrically `VBAT_SW`)

Power-mode placement update (component-only, routing deferred):

- Added `JP1` (`ESP PWR SEL: REG-3V3-1S`) near `U3`.
- `JP1` maps: `1=REG_3V3_2S`, `2=ESP_3V3`, `3=VBAT_SW`.
- 2S mode: place `U3`, shunt `JP1` pins `1-2`.
- 1S mode: do not place `U3` (DNP), shunt `JP1` pins `2-3`.
- Motor driver path still uses `VBAT_SW` directly in both modes.
- This revision intentionally focuses on component placement only; routing will be redone later.

## Status

The schematic is documented and the PCB is now proto-ready for
PCBWay 2-layer fabrication.

This folder diverges from the archived legacy board layout by externalizing
the power path:

- `SW1` is a 2-pin external switch header (`VBAT_RAW` <-> `VBAT_SW`).
- Bypass mode uses a 2-pin shunt directly on `SW1`.
- `U3` is a 3-pin external regulator header
  (`VIN`, `GND`, `VOUT=3V3`).

Use this revision for prototype fabrication only.
It is not yet a locked mass-production release.

## Validation

```bash
kicad-cli sch erc --severity-error \
  electronics/kicad/hsd-antweight-2s-v2/hsd-antweight-2s-v2.kicad_sch
kicad-cli sch export netlist \
  electronics/kicad/hsd-antweight-2s-v2/hsd-antweight-2s-v2.sch
kicad-cli pcb drc \
  electronics/kicad/hsd-antweight-2s-v2/hsd-antweight-2s-v2.kicad_pcb \
  --severity-all
```

Latest validation snapshot (2026-08-12):

- ERC errors: 0 (`--severity-error` gate passes)
- DRC violations: 0
- Unconnected pads: 0
- Footprint errors: 0
- Full ERC warning report still contains expected legacy wiring warnings.

Before manufacturing review:

- convert the schematic fully to native KiCad format;
- replace all `*_VERIFY` footprints with production footprints;
- rerun ERC and DRC after every electrical/layout change;
- inspect antenna clearance and high-current routes.

## PCBWay proto package

Generated package root:

- `production/pcbway/`

Upload-ready archive:

- `production/pcbway/hsd-antweight-2s-v2-pcbway-proto-package-2026-08-12.zip`

Contents:

- `gerbers/`: copper, mask, silkscreen and edge cuts Gerbers
- `drill/`: PTH/NPTH drill files, maps and drill report
- `hsd-antweight-2s-v2-pos.csv`: placement file
- `hsd-antweight-2s-v2-bom.csv`: BOM export
- `hsd-antweight-2s-v2.net`: KiCad netlist export

## 3D render (current revision)

Render images are stored in `renders/`.
All renders in this folder are PCB-only (no external connectors, modules or capacitors).

Primary preview (top overview, PCB-only):

![Antweight controller v2 bare PCB top overview](renders/pcbway-bare-top-1-overview.png)

Generated artifacts (6 views):

- Top:
  - `renders/pcbway-bare-top-1-overview.png`
  - `renders/pcbway-bare-top-2-angle-left.png`
  - `renders/pcbway-bare-top-3-angle-right.png`
- Bottom:
  - `renders/pcbway-bare-bottom-1-overview.png`
  - `renders/pcbway-bare-bottom-2-angle-left.png`
  - `renders/pcbway-bare-bottom-3-angle-right.png`

Render command:

Settings below are tuned so the complete PCB (including holes, pads,
traces and vias) stays visible in frame.

```bash
PCB="electronics/kicad/hsd-antweight-2s-v2/hsd-antweight-2s-v2.kicad_pcb"
TMP="electronics/kicad/hsd-antweight-2s-v2/hsd-antweight-2s-v2-no-3dmodels.tmp.kicad_pcb"

perl -ne '
sub balance { my ($s)=@_; my $o=()=$s=~/\(/g; my $c=()=$s=~/\)/g; return $o-$c; }
if (!$skip && /\(model\s+/) { $skip=1; $depth=balance($_); next; }
if ($skip) { $depth += balance($_); if ($depth <= 0) { $skip=0; } next; }
print;
' "$PCB" > "$TMP"

kicad-cli pcb render \
  --width 3200 \
  --height 2400 \
  --background transparent \
  --quality high \
  --zoom 1.00 \
  --rotate "0,0,0" \
  --side top \
  --output \
  electronics/kicad/hsd-antweight-2s-v2/renders/pcbway-bare-top-1-overview.png \
  "$TMP"

kicad-cli pcb render \
  --width 3200 \
  --height 2400 \
  --background transparent \
  --quality high \
  --perspective \
  --zoom 1.04 \
  --rotate "-20,0,18" \
  --side top \
  --output \
  electronics/kicad/hsd-antweight-2s-v2/renders/pcbway-bare-top-2-angle-left.png \
  "$TMP"

kicad-cli pcb render \
  --width 3200 \
  --height 2400 \
  --background transparent \
  --quality high \
  --perspective \
  --zoom 1.04 \
  --rotate "20,0,-18" \
  --side top \
  --output \
  electronics/kicad/hsd-antweight-2s-v2/renders/pcbway-bare-top-3-angle-right.png \
  "$TMP"

rm -f "$TMP"
```

Render bottom views (PCB-only):

```bash
PCB="electronics/kicad/hsd-antweight-2s-v2/hsd-antweight-2s-v2.kicad_pcb"
TMP="electronics/kicad/hsd-antweight-2s-v2/hsd-antweight-2s-v2-no-3dmodels.tmp.kicad_pcb"

perl -ne '
sub balance { my ($s)=@_; my $o=()=$s=~/\(/g; my $c=()=$s=~/\)/g; return $o-$c; }
if (!$skip && /\(model\s+/) { $skip=1; $depth=balance($_); next; }
if ($skip) { $depth += balance($_); if ($depth <= 0) { $skip=0; } next; }
print;
' "$PCB" > "$TMP"

kicad-cli pcb render \
  --width 3200 \
  --height 2400 \
  --background transparent \
  --quality high \
  --zoom 1.00 \
  --rotate "0,0,0" \
  --side bottom \
  --output \
  electronics/kicad/hsd-antweight-2s-v2/renders/pcbway-bare-bottom-1-overview.png \
  "$TMP"

kicad-cli pcb render \
  --width 3200 \
  --height 2400 \
  --background transparent \
  --quality high \
  --perspective \
  --zoom 1.04 \
  --rotate "-20,0,18" \
  --side bottom \
  --output \
  electronics/kicad/hsd-antweight-2s-v2/renders/pcbway-bare-bottom-2-angle-left.png \
  "$TMP"

kicad-cli pcb render \
  --width 3200 \
  --height 2400 \
  --background transparent \
  --quality high \
  --perspective \
  --zoom 1.04 \
  --rotate "20,0,-18" \
  --side bottom \
  --output \
  electronics/kicad/hsd-antweight-2s-v2/renders/pcbway-bare-bottom-3-angle-right.png \
  "$TMP"

rm -f "$TMP"
```

## Release checklist

- [ ] `*_VERIFY` footprints replaced by production footprints.
- [ ] Mounting-hole implementation and coordinates verified.
- [ ] Current ratings validated for switch and connectors.
- [x] ERC and DRC rerun and reports archived.
- [ ] Final visual review completed.