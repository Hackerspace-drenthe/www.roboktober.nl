# Roboktober electronics resources

Deze map bevat de gedeelde elektronica-informatie voor de Roboktober antweight-builds.

## Inhoud

- `components/`: stuklijst en selectiecriteria voor onderdelen.
- `firmware/`: upstream referenties en de gereviewde Roboktober-firmware.
- `pin-definitions/`: logisch pincontract en controlepunten voor modules.
- `wiring/`: bedradingsinstructies voor bouw en foutzoeken.
- `schema/`: elektrische ontwerpkeuzes en netspecificatie.
- `kicad/antweight-controller-v2/`: KiCad-project voor het carrierboard.

De 2S-voedingsvariant is apart gespecificeerd in `schema/electrical-design-v2-2s.md`, met bedrading in `wiring/antweight-controller-v2-2s.md` en een eigen `components/BOM-v2-2s.csv`. Deze variant heeft nog geen vrijgegeven PCB; het bestaande v2-board blijft uitsluitend voor 1S.

## Fallback scenario bij PCB-vertraging

Als de PCB niet op tijd vrijgegeven en getest is, schakelt het project over op handmatige bedrading en solderen.

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
2. Bouw volgens de bestaande 1S/2S bedradingsinstructies.
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
- Tot vrijgave blijven ze development-only en niet productie-klaar.

## Reference design

Het huidige (en enige) ontwerp gebruikt:

- ESP32-C3 SuperMini met de WORC GPIO5/6/7/9/10-mapping;
- een DRV8833-breakout zonder `nSLEEP`-pin (`IN1`-`IN4`, `VCC`, `GND`, `OUT1`-`OUT4`, `EEP` op `GND`, `ULT`/`FLT` onaangesloten); GPIO5 is hierdoor spare/onaangesloten, en de failsafe loopt via het nulzetten van de motor-PWM/digitale outputs (`stopMotors()`);
- een ongeplaatste 2x6-uitbreidingsheader voor zeven vrije GPIO's, dubbele 3,3 V/GND en geschakelde accuspanning;
- gereviewde firmware met afzenderfilter, packetvalidatie en een 200 ms failsafe;
- een placement-only KiCad-board totdat exacte leverancierfootprints en gemeten motorstromen bekend zijn.

Zie `firmware/roboktober-controller-v2/README.md` voor gedrag en compilatie, en `schema/electrical-design-v2.md` voor release blockers.

## KiCad

Status van recente KiCad-updates:
- Publicatie toegestaan voor samenwerking en review.
- Huidige updates zijn in development stage en nog niet werkend.
- Niet gebruiken als release- of productieontwerp.

Open `kicad/antweight-controller-v2/antweight-controller-v2.kicad_pro` met KiCad 9 of nieuwer. Het schema is als een door KiCad 9 leesbaar legacybestand opgenomen; dit houdt het pincontract controleerbaar zolang de definitieve leveranciersfootprint nog niet is gekozen.

Voer voor een release minimaal uit:

```bash
kicad-cli sch erc kicad/antweight-controller-v2/antweight-controller-v2.sch
kicad-cli pcb drc kicad/antweight-controller-v2/antweight-controller-v2.kicad_pcb
```

Het ontwerp is een technisch startpunt, geen gecertificeerd product. Test eerst met een stroombegrensde voeding en zonder gemonteerde motoren.