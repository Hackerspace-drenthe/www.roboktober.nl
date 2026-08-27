# DRV8833 breakout v2 contract

Dit contract is geharmoniseerd met de actuele v4 PCB-layout (electronics/kicad/hsd-antweight-2s-v4/hsd-antweight-2s-v4.kicad_pcb).

## Plaatsing

- U2 (DRV8833 breakout) staat op B.Cu (onderkant).
- Side-aware interpretatie is toegepast: top-view en bottom-montage kunnen visueel gespiegeld lijken.
- Daarom is onderstaande contractmapping gebaseerd op echte U2 padnummers en PCB-netten.

## U2 padmapping op de v4 PCB

| U2 pad | Net | Betekenis |
| ---: | --- | --- |
| 1 | AIN1_GPIO7 | Motor links input 1 |
| 2 | (no net) | Niet aangesloten |
| 3 | AIN2_GPIO6 | Motor links input 2 |
| 4 | M2B | Motor rechts uitgang B |
| 5 | VBAT_SW | Driver voedingsrail |
| 6 | M2A | Motor rechts uitgang A |
| 7 | GND | Common ground |
| 8 | M1B | Motor links uitgang B |
| 9 | BIN1_GPIO9 | Motor rechts input 1 |
| 10 | M1A | Motor links uitgang A |
| 11 | BIN2_GPIO10 | Motor rechts input 2 |
| 12 | GPIO5 | Board-level control line |

## Randvoorwaarden

- C1/C2 ontkoppeling op driver-rail blijft verplicht.
- GPIO9 blijft een strapping-pin op de ESP; vermijd ongewenste bias.
- Eerste power-up met stroombegrenzing en zonder motoren onder last.
