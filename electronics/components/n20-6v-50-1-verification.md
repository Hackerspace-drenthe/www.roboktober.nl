# N20 6 V 50:1 reference verification

Verified online on 2026-08-03 for the controller's N20-compatible micro-metal gearmotor assumption.

`N20` is a form-factor label, not a complete electrical specification. The exact winding, brushes, gearbox and shaft options must be confirmed from the purchased motor's listing or datasheet before manufacture.

## Verified references

| Reference | Voltage | Gear ratio | No-load performance | Stall extrapolation | Purpose |
| --- | --- | --- | --- | --- | --- |
| [Pololu #998 HP 6 V](https://www.pololu.com/product/998) | 6 V | Approximately 50:1 | 590 RPM, 100 mA | 0.86 kg cm, 1.6 A | Upper current reference for the selected 6 V 50:1 class |
| [Pololu #3063 HPCB 6 V](https://www.pololu.com/product/3063) | 6 V | Approximately 50:1 | 650 RPM, 150 mA | 0.74 kg cm, 1.5 A | Independent brush construction within the same mechanical family |

Both supplier pages state that their stall figures are theoretical and that a stall can damage the motor or gearbox. The controller must therefore not claim continuous-stall capability.

## Mechanical envelope

The [Pololu micro metal gearmotor dimension drawing](https://www.pololu.com/file/0J949/micro-metal-gearmotors-dimensions.pdf) and the #998 product page specify the common N20-compatible gearbox envelope as:

- Gearbox cross section: 10 x 12 mm.
- Output shaft: 3 mm diameter D-shaft, 9 mm exposed length.
- The 1000:1 gearbox is 3.5 mm longer; do not use that exception for the 50:1 footprint.
- HPCB and precious-metal-brush variants have different terminal/end-cap details even though their main gearbox dimensions match.

## Controller design values

Use the conservative design envelope below until the exact purchased motors are measured:

| Parameter | Design value |
| --- | --- |
| Nominal motor voltage | 6 V |
| Gear ratio | Approximately 50:1 |
| No-load speed | 590-650 RPM at 6 V |
| Stall current | 1.5-1.6 A at 6 V |
| 8.4 V direct-2S extrapolated stall current | Approximately 2.10-2.24 A per motor |
| Mechanical footprint basis | 10 x 12 mm gearbox, 3 mm D-shaft |

Before releasing a motor mount or placing N20 footprints, compare the purchased motor's drawing against this envelope and confirm terminal orientation, motor-can length, threaded-face holes and shaft geometry. The controller retains J5 and J6 as the motor connectors; use a detachable two-wire harness and solder its motor-side ends directly to each N20 terminal.

## PCB mounting provision

The KiCad panel includes M1/M2 N20 footprints centered on the left and right wheel axes. Each footprint provides four 3.0 mm plated pads with 1.2 mm drills for two hand-bent, approximately 1.0 mm steel-wire brackets. Bend each wire into a U-shape that sits tightly over the motor body, insert its two legs through one pad pair, then solder both legs on the underside of the outer PCB. The two U-brackets are separated by 5 mm along the motor axis.

This provision uses the verified 10 x 12 mm gearbox envelope and presents the 3 mm D-shaft through its wheel recess. It is a hand-fit retention method: test the actual paperclip/wire diameter and the purchased motor before release. Do not use the mechanical pads as electrical motor connections.