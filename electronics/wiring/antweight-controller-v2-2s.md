# Antweight controller v2-2S wiring

## Power wiring

The 2S rail feeds the motor driver directly. Logic power comes from an external DC-DC converter connected through U3 (`VIN`, `GND`, `VOUT=3V3`).

| Connection | Destination | Suggested color |
| --- | --- | --- |
| `VBAT_RAW` | 5 A fuse, then main switch input (`SW1`) | Red |
| `VBAT_SW` | DRV8833 VM and U3 `VIN` | Red |
| `GND` | DRV8833 GND, U3 `GND`, and ESP32 GND | Black |
| U3 `VOUT` (`3V3`) | ESP32 3V3 and J10 3V3 | Orange |

Route the high-current battery/driver return directly to the battery-ground node. Join the logic-regulator ground at that node rather than carrying motor current through the ESP32 return path.

## Motor connections

Keep the existing two-pin motor connectors on the controller PCB: J5 for the left motor and J6 for the right motor. Each N20 motor uses a detachable two-wire harness with the matching plug at the controller end; solder the other two wire ends directly to the motor terminals.

Before closing the robot, verify forward/reverse direction with the wheels raised. Reverse the two wires at the motor or connector if a side runs opposite to the commanded direction. Add strain relief at the motor terminals and route each harness clear of the wheel, shaft and ESP32 antenna.

Each motor is retained mechanically by two hand-bent steel-wire U-brackets soldered into its four dedicated M1/M2 mounting pads. These pads are mechanical only; do not connect the motor wires to them.

## Complete power path

```mermaid
flowchart LR
    P[2S pack + -> VBAT_RAW] --> F[5 A fuse]
    F --> S[Main switch]
    S --> VB[VBAT_SW]
    VB --> VM[Driver VM 6.0-8.4 V]
    VB --> U[U3 VIN]
    U --> V3[U3 VOUT 3V3]
    N --> DG[Driver GND]
    N --> UG[U3 GND]
```

## Bring-up

1. Disconnect the ESP32, motor driver and motors.
2. Apply a current-limited 7.4 V bench supply and verify 2S polarity.
3. Set the external converter on U3 to 3.30 V with a multimeter. Verify 3.3 V and load-test it at 1 A before fitting the ESP32.
4. Fit the ESP32 and verify radio operation with the intended 3.3 V accessories; keep accessory load at or below 300 mA pending measurement.
5. Fit the driver without motors and verify 2S voltage at VM and all four control inputs.
6. Connect motors with wheels raised and verify the full 255/255 PWM range.
7. Repeat startup, reverse, brief controlled-stall and link-loss tests at 8.4 V while monitoring driver temperature and fault behavior.

Never charge the 2S pack through this carrier. Remove it and use a compatible 2S balance charger.

## Spanningsomvormer (U3)

Use an adjustable external converter only after confirming polarity and effective voltage at U3: `VIN` from `VBAT_SW`, `GND` common return, and `VOUT` to `3V3`. Keep converter interconnects short, insulated and strain-relieved. Never connect the ESP32 until U3 `VOUT` measures 3.30 V relative to GND.

Standard for this design: keep U3 output at `3V3`. Do not run the ESP32 rail at
`3V5` on this board revision.