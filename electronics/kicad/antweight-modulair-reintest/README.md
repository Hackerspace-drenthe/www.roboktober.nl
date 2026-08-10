# Antweight modulair reintest

Dit ontwerp splitst de huidige controller op in twee stapelbare boards:

- `power-board/`: onderste board, met alleen power-conversie en power-distributie.
- `cpu-board/`: middelste board, met ESP32-C3, DRV8833, motorconnectoren en addon-header.

Een optionele derde board kan later als experiment/addon bovenop de CPU-board komen.

## Board split

CPU board verantwoordelijkheden:
- ESP32-C3 module/sockets
- DRV8833 driver
- motorconnectoren
- lokale driver-ontkoppeling
- stack-header naar power board
- addon-header voor spare GPIO en logic power
- logische netnamen voor motorbesturing

Power board verantwoordelijkheden:
- 2S LiPo ingang
- hoofdschakelaar
- buck 6V voor motoren
- buck 3.5V voor CPU-board
- stack-header omhoog naar CPU/DRV-board
- optionele aux power header

## Belangrijke nets

- `VBAT_2S_RAW`: ruwe 2S LiPo
- `VBAT_2S_SW`: geschakelde 2S LiPo
- `VMOTOR_6V`: gereguleerde motorvoeding
- `VCPU_3V5`: gereguleerde CPU-voeding
- `GND`
- `AIN1_GPIO7`, `AIN2_GPIO6`, `BIN1_GPIO9`, `BIN2_GPIO10`
- `M1A`, `M1B`, `M2A`, `M2B`
- `GPIO8_LED_EXPANSION`, `GPIO5_SPARE`

## Stack order

1. `power-board` onderaan
2. `cpu-board` in het midden
3. optionele addon/experiment board bovenop de CPU-board

De stack-header tussen power-board en CPU-board draagt alleen voedingsrails. Daardoor kan de power-board in theorie door een andere variant worden vervangen, bijvoorbeeld een 1S power-board, zonder DRV8833 of CPU-board te wisselen.

## Opmerking

De CPU-rail is hier expliciet als `VCPU_3V5` vastgelegd omdat dat gevraagd is. Controleer vóór hardwarebouw of de gekozen ESP32-C3 module direct 3.5V op zijn voedingspin mag hebben. Als dat niet kan, zet deze rail terug naar 3.3V in zowel schema als layout.

## Files

- `cpu-board/cpu-board.sch`: legacy schema bron voor CPU board
- `cpu-board/cpu-board.net`: geëxporteerde netlist
- `cpu-board/cpu-board.kicad_pcb`: eerste PCB met ESP32/DRV/motor-footprints
- `power-board/power-board.sch`: legacy schema bron voor power board
- `power-board/power-board.net`: geëxporteerde netlist
- `power-board/power-board.kicad_pcb`: eerste PCB met alleen power-footprints
- `stack-header-pinout.md`: interconnect pinout tussen beide boards
