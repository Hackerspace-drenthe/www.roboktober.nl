# Roboktober electronics resources

Deze map bevat de gedeelde elektronica-informatie voor de Roboktober
antweight-builds.

## Inhoud

- `components/`: stuklijst en selectiecriteria voor onderdelen.
- `firmware/`: upstream referenties en de gereviewde Roboktober-firmware.
- `pin-definitions/`: logisch pincontract en controlepunten voor modules.
- `wiring/`: bedradingsinstructies voor bouw en foutzoeken.
- `schema/`: elektrische ontwerpkeuzes en netspecificatie.
- `kicad/antweight-controller-v2/`: historische 1S KiCad-projectmap
  (gearchiveerd).
- `kicad/hsd-antweight-2s-pcb/`: actuele 2S KiCad-projectmap met externe
  voedingsinterfaces.

Er is nog maar één actief ontwerp in deze repository:

- 2S elektrisch contract: `schema/electrical-design-v2-2s.md`
- 2S wiring: `wiring/antweight-controller-v2-2s.md`
- 2S PCB: `kicad/hsd-antweight-2s-pcb/`

De oude 1S-documenten blijven alleen als archiefreferentie bestaan.

De bijbehorende PCB staat in `kicad/hsd-antweight-2s-pcb/` en is
proto-ready voor PCBWay 2-layer fabricage.

Deze vrijgave is bedoeld voor prototypes en validatie,
niet voor massaproductie.

## Fallback scenario bij PCB-vertraging

Als de PCB niet op tijd vrijgegeven en getest is,
schakelt het project over op handmatige bedrading en solderen.

Go/no-go beslismoment:

- 1 maand voor het event.

Go-criterium (PCB-pad blijft actief):

- PCB is elektrisch en mechanisch gevalideerd.
- Kritieke DRC/ERC blockers zijn opgelost.
- Minimaal een werkend praktijkprototype is stabiel onder belasting.

No-go-criterium (fallback activeren):

- Geen stabiel praktijkprototype beschikbaar.
- Open kritieke blockers op voeding, motorsturing of connector-layout.
- Productie- of levertijd van PCB haalt de eventplanning niet.

Fallback-uitvoering (handmatige wiring):

1. Gebruik de actuele pin-definitions en wiring-docs als bron van waarheid.
2. Bouw volgens de actieve 2S bedradingsinstructies.
3. Soldeer en test modulair: voeding, MCU, driver, dan motoruitgangen.
4. Test eerst met stroombegrensde voeding en zonder gemonteerde motoren.
5. Leg afwijkingen of workarounds direct vast in de wiring-documentatie.

Minimale materialen-check voor fallback:

- ESP32-C3 modules
- DRV8833 modules
- bekabeling, connectoren, krimpkous
- zekeringen/stroombegrenzing voor bench-test
- reservecomponenten voor rework

Publicatiestatus:

- KiCad updates mogen gepubliceerd worden voor review/samenwerking.
- De actuele 2S-print is proto-ready en mag voor proefseries worden
  gebruikt.
- Massaproductie blijft geblokkeerd totdat footprints en thermische
  validatie definitief zijn vastgelegd.

## Reference design

Het huidige (en enige) ontwerp gebruikt:

- ESP32-C3 SuperMini met de WORC GPIO5/6/7/9/10-mapping;
- een DRV8833-breakout zonder `nSLEEP`-pin (`IN1`-`IN4`, `VCC`, `GND`,
  `OUT1`-`OUT4`, `EEP` op `GND`, `ULT`/`FLT` onaangesloten);
- GPIO5 is hierdoor spare/onaangesloten;
- een externe spanningsomvormer via `U3` (`VIN`, `GND`, `VOUT=3V3`) voor
  de logicarail;
- J10 pad 3 is elektrisch `VBAT_SW` maar staat op de PCB-silk als `VLIPO`;
- de logo-silk leest `HACKERSPACE` boven `DRENTHE`;
- standaard ESP-voeding: alleen `3V3`; `3V5` is geen geldige bedrijfsspanning
  voor dit ontwerp;
- de failsafe loopt via het nulzetten van de motor-PWM/digitale outputs
  (`stopMotors()`);
- een ongeplaatste 2x6-uitbreidingsheader voor zeven vrije GPIO's,
  dubbele 3,3 V/GND en geschakelde accuspanning;
- gereviewde firmware met afzenderfilter, packetvalidatie en een
  200 ms failsafe;
- een proto-ready 2S KiCad-board met PCBWay releasepakket voor
  validatie en proefseries.

Zie `firmware/roboktober-controller-v2/README.md` voor gedrag en
compilatie, en `schema/electrical-design-v2-2s.md` voor
actuele release-eisen.

## KiCad

Status van recente KiCad-updates:

- Publicatie toegestaan voor samenwerking en review.
- Het actieve 2S-ontwerp is DRC-clean en ERC-error-clean.
- Gebruik het huidige pakket als prototype-release, niet als
  massaproductie-release.

Open `kicad/hsd-antweight-2s-pcb/rein-2s-controller.kicad_pro` met
KiCad 9 of nieuwer.

Het schema is als een door KiCad 9 leesbaar legacybestand opgenomen.
Dit houdt het pincontract controleerbaar zolang de definitieve
leveranciersfootprint nog niet is gekozen.

Voer voor een release minimaal uit:

```bash
kicad-cli sch erc --severity-error \
  kicad/hsd-antweight-2s-pcb/rein-2s-controller.kicad_sch
kicad-cli pcb drc kicad/hsd-antweight-2s-pcb/rein-2s-controller.kicad_pcb
```

Het ontwerp is een technisch startpunt, geen gecertificeerd product.
Test eerst met een stroombegrensde voeding en zonder gemonteerde
motoren.