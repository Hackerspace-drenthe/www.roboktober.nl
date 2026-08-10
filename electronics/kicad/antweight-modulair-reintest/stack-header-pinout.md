# Stack Header Pinout

Voorgestelde stapelheader tussen onderste power-board en middelste CPU/DRV-board: `Conn_02x04_Odd_Even`

## Pin mapping

1. `VCPU_3V5`
2. `GND`
3. `VMOTOR_6V`
4. `GND`
5. `VCPU_3V5`
6. `GND`
7. `VMOTOR_6V`
8. `GND`

## Rationale

- De stack draagt alleen voedingsrails tussen power-board en CPU/DRV-board.
- `DRV8833` zit op de CPU-board, zodat een andere power-board in theorie verwisselbaar blijft.
- Dubbele power- en ground-pinnen geven meer stroomreserve en mechanische robuustheid.
- Besturingssignalen `AIN/BIN` blijven lokaal tussen ESP32 en DRV8833 op de middelste board.
- Motoruitgangen `M1A/M1B/M2A/M2B` blijven lokaal op de middelste board.

## Addon board later

De addon/experiment board hoort bovenop de CPU-board te komen via een aparte uitbreidingsheader met bijvoorbeeld:

- `VCPU_3V5`
- `GND`
- `GPIO0_SPARE`
- `GPIO1_SPARE`
- `GPIO2_SPARE`
- `GPIO3_SPARE`
- `GPIO4_SPARE`
- `GPIO20_SPARE`
- `GPIO21_SPARE`
- `GPIO8_LED_EXPANSION`
- `GPIO5_SPARE`
