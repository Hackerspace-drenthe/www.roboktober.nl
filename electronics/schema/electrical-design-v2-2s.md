# Electrical design v2-2S

## Status

This is the design contract for a separate 2S controller variant. It does not make the existing v2 PCB 2S-compatible. Do not connect a 2S pack to the existing v2 PCB.

## Design targets

| Item | Requirement |
| --- | --- |
| Battery | Protected 2S LiPo/Li-ion, 6.0-8.4 V, external balance charger |
| Motors | Two 6 V N20 50:1 gearmotors, approximately 600 RPM |
| Motor rail | Direct switched 2S, 6.0-8.4 V |
| Motor control | Full 0-100% PWM; no external motor current limiter |
| Logic rail | Regulated 3.3 V, 1 A total |
| Common return | Battery, regulator, driver and ESP32 share GND |

The [N20 6 V 50:1 verification record](../components/n20-6v-50-1-verification.md) cross-checks two published 6 V 50:1 micro-metal gearmotor variants: [Pololu HP #998](https://www.pololu.com/product/998) is specified at 590 RPM and 1.6 A theoretical stall at 6 V, while [HPCB #3063](https://www.pololu.com/product/3063) is specified at 650 RPM and 1.5 A. At 8.4 V, linear extrapolation gives approximately 826/910 RPM and 2.24/2.10 A stall respectively. Stalling is an abnormal short-duration condition and can rapidly damage the motor, gearbox or driver.

## Power architecture

```mermaid
flowchart LR
    BAT[Protected 2S pack] --> F1[5 A fuse]
    F1 --> SW[Main switch, at least 5 A DC]
    SW --> RAW[VBAT_2S_SW]
    RAW --> VM[DRV8833 VM]
    RAW --> LBUCK[3.3 V logic buck]
    LBUCK --> ESP[ESP32-C3 3V3]
    BAT --- GND[Common GND]
    GND --- LBUCK
    GND --- VM
    GND --- ESP
```

The DRV8833 VM input receives switched 2S voltage directly and firmware permits 255/255 duty. At full duty the motors receive the complete pack voltage continuously. The protected pack/BMS and 5 A fuse protect the battery wiring, the DRV8833 internal over-current/thermal shutdown protects the driver, and the 200 ms radio failsafe covers link loss. None of these guarantees a stopped motor during a persistent mechanical stall.

## Logic buck module

Selected reference module: Mini-360 adjustable buck module with MP2307DN controller, adjusted to 3.3 V before installation.

| Property | Design value |
| --- | --- |
| Output | Adjustable; set and verify 3.3 V before connecting ESP32 |
| Input range | 4.75-23 V for MP2307DN; valid for a 6.0-8.4 V 2S pack |
| Design load | 1 A maximum total after thermal and ripple validation |
| Interface | Four wired pads: `IN+`, `IN-`, `OUT-`, `OUT+` |
| Typical module size | Approximately 17 x 11 mm; confirm received module dimensions |

Purchase references: [Mini-360 MP2307DN AliExpress search](https://www.aliexpress.com/wholesale?SearchText=Mini-360+MP2307DN) and [MP2307 product information](https://www.monolithicpower.com/en/mp2307.html). AliExpress Mini-360 boards are mechanically and electrically variant-dependent; verify the board markings and pad labels against the received module before soldering.

Reserve at least 600 mA transient capacity for the ESP32-C3. Until measured otherwise, limit external 3.3 V accessories on J10 to 300 mA continuous so regulator and radio transient margin remain available.

U3 exposes four 2.54 mm through-holes for short, insulated wires to the Mini-360: `IN+` and `IN-` on the input side, `OUT-` and `OUT+` on the output side. The Mini-360 deliberately overhangs the pushout edge. Do not assume an AliExpress module supports a plug-in header pattern.

Place at least 10 uF input bulk capacitance close to VIN/GND and verify the exact module manufacturer's capacitor recommendation. Feed the regulator directly from `VBAT_2S_SW`, not from a motor output or PWM node.

## Board interfaces

The future v2-2S carrier needs these power interfaces:

| Connector | Pins | Rating and purpose |
| --- | --- | --- |
| JBAT | `BAT_2S+`, `GND` | Keyed battery connector, at least 5 A DC |
| ULOGIC | `IN+`, `IN-`, `OUT-`, `OUT+` | Mini-360 wired module interface; `IN-` and `OUT-` both connect to GND |
| J10 | `3V3`, `GND`, `VBAT_2S_SW` | Accessory power; 3.3 V accessories limited to 300 mA continuous pending test |

Label J10 raw power explicitly as `2S RAW 8.4V MAX`; it is no longer the maximum 4.2 V accessory rail of the 1S design.

## Driver constraint

The DRV8833 IC accepts 8.4 V because its operating motor-supply range extends to 10.8 V. The design intentionally uses the existing breakout without an external current limiter. Its approximately 1.5 A RMS and 2 A peak output capability is close to the extrapolated 2.10-2.24 A motor stall current, so simultaneous stall may activate the driver's internal over-current or thermal shutdown.

Release requires bench validation of simultaneous startup, reversal and a brief controlled stall on the exact motors and breakout. The driver protection must operate without PCB, connector or wire overheating. Persistent stall testing must use a current-limited bench supply because the driver may automatically retry after a fault. Do not claim continuous stall capability or increased continuous motor torque.

## Capacitors and wiring

- Fit at least 100 nF ceramic and 470 uF low-ESR, rated at least 16 V, at the motor-driver VM/GND connection.
- Fit 100 nF ceramic directly across each motor terminal pair.
- Twist each motor pair and keep it away from the ESP32 antenna.
- Use 20-22 AWG for battery wiring and keep the switched 2S loop short.
- Size unregulated shared motor paths for at least 4.5 A fault/stall current and individual outputs for at least 2.25 A.

## Validation gates

1. Verify battery connector polarity and fuse operation with no modules fitted.
2. Set the Mini-360 to 3.30 V with a multimeter, then verify the rail at 6.0 V and 8.4 V input before fitting the ESP32.
3. Load-test 3.3 V at 1 A and verify temperature, ripple and switch-on overshoot.
4. Verify the ESP32 radio workload together with the intended 3.3 V accessory load.
5. Power the motor driver from a current-limited 8.4 V bench supply without motors and verify all four inputs.
6. Measure each motor's actual stall current briefly with current limiting and wheels raised.
7. Verify 255/255 duty, simultaneous start/reverse behavior and the DRV8833 fault response during a brief controlled stall.
8. Confirm link loss stops both motors within 200 ms at full battery voltage.
9. Record driver, regulator, switch, connector and capacitor temperatures at 8.4 V and near pack cutoff.

This variant remains blocked from manufacture until the exact Mini-360 variant and pinout, exact battery connector, protected 2S pack and full-load DRV8833 behavior are verified.