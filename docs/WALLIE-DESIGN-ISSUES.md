# Wallie - Design Issues

## Status

Alle gemelde issues zijn opgelost in de huidige `v4`-revisie.

## Beslissing

- Praktijkdefault: minimale 1S setup is leidend voor nieuwe builds.
- Praktijkdefault route: 1S charge/protection breakout naar `ESP_5V_IN`.
- ESP voeding: `ESP_5V_IN` is de standaard; de module maakt intern 3.3V.
- 2S-route: `accu -> 5V regulator -> ESP_5V_IN` is nog geldig als alternatief, maar niet de default voor deze print.
- Geen raw batterij op de ESP `3.3V`-pin.
- `VIN -> VOUT` bypass is niet toegestaan.
- Richtprijs complete 1S-kit: ongeveer EUR 25 per robot (inkoopindicatie).
- `v4` PCB is de bron van waarheid; oudere docs zijn archief.

## Gefixed

- `SLEEP` van de motor driver is verplaatst van `GND` naar `GPIO20_SPARE`.
- Power-routing en netlabels zijn gecorrigeerd (`REG_5V_OUT`, `ESP_5V_IN`).
- De 1S-default is vastgelegd en getest als minimale, goedkope basisopstelling.
- Sporen en pin-afstanden zijn aangepast zodat standaard headers/jumpers passen.
- Layout en plaatsing zijn verbeterd voor montage en kabelvoering.
- Elektrische issues zijn weg; eventuele resterende waarschuwingen zijn visueel/silkscreen-only.

## Verificatie

- De PCB in `electronics/kicad/hsd-antweight-2s-v4/` is leidend.
- De 5V-architectuur is consistent met de module en de gerealiseerde board-route.
- De eerder genoemde power- en routingproblemen zijn opgelost.
