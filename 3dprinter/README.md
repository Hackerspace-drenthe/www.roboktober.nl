# 3D printer onderdelen (Wallie mini robot)

Deze map bevat 3D-print onderdelen voor de antweight robotbehuizing.

## Bestanden

- `Wallieonline+Mini+Robot+chassis/wallieonline-mini-robot.STL`
  - Chassis/body met plekken voor o.a. wielen, motoren en LiPo.
- `Wallieonline+Mini+Robot+chassis/wallieonline-mini-robot-top.STL`
  - Deksel/topplaat.

## V4 PCB compatibiliteit (fit-check)

De topplaat is vergeleken met de contour en gaten van:

- `electronics/kicad/hsd-antweight-2s-v4/hsd-antweight-2s-v4.kicad_pcb`

Resultaat van de check:

- PCB buitenmaat (Edge.Cuts): 78.0 x 56.0 mm
- Top STL footprint: 78.0 x 56.0 mm
- Verwachte gaten uit PCB contour: 6x cirkel met radius 1.0 mm (diameter 2.0 mm)
- In de top STL zijn op alle 6 verwachte gatlocaties overeenkomstige cirkel-ringen gevonden.

Praktisch betekent dit dat de V4 KiCad print/de shape als deksel gebruikt kan worden (zelfde footprint en montagegatposities), onder de aanname dat de STL correct op schaal is geexporteerd/geprint (100%).

## Printadvies

- Print op 100% schaal (geen fit-to-page/slicer scaling).
- Controleer na print:
  - afstand tussen tegenoverliggende montagegaten,
  - passing op de daadwerkelijke V4 print.
- Gebruik proefmontage voordat je definitief afwerkt.
