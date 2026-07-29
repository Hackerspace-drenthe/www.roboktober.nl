# Antweight controller wiring

## Power path

```mermaid
flowchart LR
    BAT[1S LiPo] --> SW[Main switch]
    SW --> VM[DRV8833 VM]
    SW --> REG[3.3 V buck-boost]
    REG --> ESP[ESP32-C2 3V3]
    BAT --- GND[Common ground]
    GND --- VM
    GND --- REG
    GND --- ESP
```

## Signal path

```mermaid
flowchart LR
    G0[GPIO0] --> A1[AIN1]
    G1[GPIO1] --> A2[AIN2]
    G2[GPIO2] --> B1[BIN1]
    G3[GPIO3] --> B2[BIN2]
    DRV[DRV8833 AOUT1/AOUT2] --> ML[Left N20]
    DRV2[DRV8833 BOUT1/BOUT2] --> MR[Right N20]
```

## Assembly order

1. Verify connector polarity and both module pinouts with a multimeter.
2. Fit the switch, JST connectors, capacitors and regulator header.
3. With controller and driver removed, verify that switched battery voltage reaches regulator input and driver VM only.
4. Fit the regulator and verify `3V3` before inserting the ESP32 module.
5. Fit the ESP32, flash firmware and verify all four outputs without the driver installed.
6. Fit the DRV8833, connect motors and test with wheels raised from the bench.

## Safety checks

- Never charge the LiPo through this carrierboard.
- Insulate the battery and prevent puncture or crushing.
- The main switch must be reachable without approaching moving parts.
- Disconnect the battery before changing motor wiring.