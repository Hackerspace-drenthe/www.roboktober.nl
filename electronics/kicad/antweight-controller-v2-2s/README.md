# Antweight controller v2-2S KiCad panel

This project is the separate direct-2S controller variant. The original v2 project remains unchanged.

## Panel geometry

- Finished panel: 100.00 x 100.00 mm bounding size, with 20.00 mm 45-degree chamfers on both top corners.
- Outer outline: 20.00 mm 45-degree chamfers at both top corners; the former wheel recesses are removed.
- Controller PCB: centered, approximately 40.48 x 70.75 mm after breakout.
- Breakout: four side tabs with five 0.5 mm mouse-bite holes per tab.
- The former wheel-routing recesses were removed to preserve the full enlarged controller pushout for copper routing.
- Tooling: four 3.0 mm non-plated holes; the upper pair is repositioned inside the chamfered top edge.
- Fiducials: three 1.0 mm copper targets with 2.0 mm mask openings; the upper pair is repositioned inside the chamfered top edge.

The Mini-360 MP2307DN regulator deliberately overhangs the lower pushout edge. Its four wired pads retain a 1.0 mm copper-to-edge margin. The C1_C2 capacitor bank is rotated into the lower-right controller area so the 1.5 mm switched-battery routes use the lower and right-side corridors.

Confirm the board manufacturer's minimum routing width, mouse-bite drill diameter, tab rules and accepted panel format before ordering. The routed slots are represented as closed `Edge.Cuts` contours.

## Electrical status

KiCad DRC reports 17 remaining non-routing panel, mouse-bite, fiducial, silkscreen, and locally overridden crossing warnings. The PCB has 0 unconnected electrical items and 0 footprint errors. Resolve or explicitly accept the remaining DRC items, then run ERC, Gerber, and drill-file inspection before release.

The 2S input expects a protected pack with an external 5 A inline fuse. U3 is a [Mini-360 MP2307DN adjustable buck module](https://www.aliexpress.com/wholesale?SearchText=Mini-360+MP2307DN), wired through `IN+`, `IN-`, `OUT-` and `OUT+`. Confirm the received module's pad labels and set `OUT+` to 3.30 V before connecting the ESP32.

## Preview

- `antweight-controller-v2-2s-3d.png`: perspective component render.
- `antweight-controller-v2-2s-3d-top.png`: top component render.
- `antweight-controller-v2-2s-3d-bottom.png`: bottom copper and pad render.
- `antweight-controller-v2-2s-panel.svg`: front copper, front silkscreen and board-outline preview.
- `antweight-controller-v2-2s.svg`: current electrical schematic export.
