# Electrical design

## Nets

| Net | Description |
| --- | --- |
| `VBAT_RAW` | Direct 1S LiPo positive, before the main switch |
| `VBAT_SW` | Switched motor and regulator input supply |
| `3V3` | Regulated controller supply |
| `GND` | Shared battery, regulator, controller and driver return |
| `AIN1_GPIO0` | Left motor H-bridge input 1 |
| `AIN2_GPIO1` | Left motor H-bridge input 2 |
| `BIN1_GPIO2` | Right motor H-bridge input 1 |
| `BIN2_GPIO3` | Right motor H-bridge input 2 |
| `M1A`, `M1B` | Left motor outputs |
| `M2A`, `M2B` | Right motor outputs |

## Design decisions

- A buck-boost module separates the ESP32 supply from motor noise and the varying 1S LiPo voltage.
- The DRV8833 motor supply is taken directly from switched battery voltage.
- `C1` provides high-frequency decoupling and `C2` absorbs motor-current transients.
- Motor traces on the PCB use at least 1.0 mm width; logic traces use 0.35 mm.
- Connectors are placed at board edges and use explicit polarity/signal labels.

## Open supplier-specific items

- Exact ESP32-C2 SuperMini physical pin order and spacing.
- Exact DRV8833 breakout dimensions and header order.
- Exact 3,3 V buck-boost module dimensions and header order.

These are intentionally isolated behind module headers. Update footprints and pad-net assignments before ordering a production PCB.