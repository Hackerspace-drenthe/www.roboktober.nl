# DRV8833 module pin contract

The carrier uses a ten-pin abstract module interface. Adapt the footprint when the exact breakout supplier is selected.

| Carrier pad | Signal | Purpose |
| ---: | --- | --- |
| 1 | `VBAT_SW` | Switched 1S motor supply |
| 2 | `GND` | Common ground |
| 3 | `AIN1` | Left motor control from `GPIO0` |
| 4 | `AOUT1` | Left motor terminal A |
| 5 | `AIN2` | Left motor control from `GPIO1` |
| 6 | `AOUT2` | Left motor terminal B |
| 7 | `BIN1` | Right motor control from `GPIO2` |
| 8 | `BOUT1` | Right motor terminal A |
| 9 | `BIN2` | Right motor control from `GPIO3` |
| 10 | `BOUT2` | Right motor terminal B |

If a breakout exposes `nSLEEP`, tie it high according to that breakout's documentation or route it to a spare GPIO. Do not assume all DRV8833 modules share this carrier order.