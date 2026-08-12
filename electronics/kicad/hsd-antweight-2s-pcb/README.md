# KiCad antweight controller v2 - hsd-antweight-2s-pcb

This KiCad 9 project is the current 2S-oriented board variant with
externalized power interfaces.

## Status

The schematic is documented and the PCB is now proto-ready for
PCBWay 2-layer fabrication.

This folder diverges from the archived 1S board layout by externalizing
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
  electronics/kicad/hsd-antweight-2s-pcb/rein-2s-controller.kicad_sch
kicad-cli sch export netlist \
  electronics/kicad/hsd-antweight-2s-pcb/rein-2s-controller.sch
kicad-cli pcb drc \
  electronics/kicad/hsd-antweight-2s-pcb/rein-2s-controller.kicad_pcb \
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

- `production/pcbway/rein-2s-controller-pcbway-proto-package-2026-08-12.zip`

Contents:

- `gerbers/`: copper, mask, silkscreen and edge cuts Gerbers
- `drill/`: PTH/NPTH drill files, maps and drill report
- `rein-2s-controller-pos.csv`: placement file
- `rein-2s-controller-bom.csv`: BOM export
- `rein-2s-controller.net`: KiCad netlist export

## 3D render (current revision)

Primary preview (with components, top side):

![Antweight controller v2 3D top render](rein-2s-controller-3d-with-components-top-2026-08-10.png)

Render definitions:

- `with-components` is the assembly view with mounted board parts.
- `without-components` is the PCB-only view.

Motor mapping in assembly view:

- right motor: `M1A` and `M1B` (`MOTOR R`);
- left motor: `M2A` and `M2B` (`MOTOR L`).

Generated artifacts:

- `rein-2s-controller-3d-with-components-top-2026-08-10.png`
- `rein-2s-controller-3d-without-components-top-2026-08-10.png`
- `rein-2s-controller-3d-without-components-bottom-2026-08-10.png`

Render command:

```bash
kicad-cli pcb render \
  --width 2200 \
  --height 1600 \
  --background transparent \
  --quality high \
  --perspective \
  --zoom 1.35 \
  --rotate "-35,0,25" \
  --side top \
  --output \
  electronics/kicad/hsd-antweight-2s-pcb/rein-2s-controller-3d-with-components-top-2026-08-10.png \
  electronics/kicad/hsd-antweight-2s-pcb/rein-2s-controller.kicad_pcb
```

Render both sides without components:

```bash
PCB="electronics/kicad/hsd-antweight-2s-pcb/rein-2s-controller.kicad_pcb"
TMP="electronics/kicad/hsd-antweight-2s-pcb/rein-2s-controller-no-components.tmp.kicad_pcb"

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
    --output \
    "electronics/kicad/hsd-antweight-2s-pcb/rein-2s-controller-3d-without-components-${side}-2026-08-10.png" \
    "$TMP"
done

rm -f "$TMP"
```

## Release checklist

- [ ] `*_VERIFY` footprints replaced by production footprints.
- [ ] Mounting-hole implementation and coordinates verified.
- [ ] Current ratings validated for switch and connectors.
- [x] ERC and DRC rerun and reports archived.
- [ ] Final visual review completed.