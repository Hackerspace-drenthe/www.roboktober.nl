# Roboktober electronics resources

Deze map bevat de gedeelde elektronica-informatie voor de Roboktober antweight-builds.

## Inhoud

- `components/`: stuklijst en selectiecriteria voor onderdelen.
- `firmware/`: upstream referenties en de gereviewde Roboktober-firmware.
- `pin-definitions/`: logisch pincontract en controlepunten voor modules.
- `wiring/`: bedradingsinstructies voor bouw en foutzoeken.
- `schema/`: elektrische ontwerpkeuzes en netspecificatie.
- `kicad/antweight-controller/`: KiCad-project voor het carrierboard.

## Referentieontwerp

Het eerste ontwerp is een carrierboard voor:

- ESP32-C2 SuperMini-module;
- DRV8833 dual-motordrivermodule;
- generieke 3,3 V buck-boost-regulatormodule van minimaal 500 mA;
- 1S LiPo;
- twee N20-motoren;
- JST-PH 2-polige aansluitingen voor accu en motoren.

## V2 reference design

V2 staat naast het oorspronkelijke ontwerp en gebruikt:

- ESP32-C3 SuperMini met de WORC GPIO5/6/7/9/10-mapping;
- een DRV8833-breakout zonder `nSLEEP`-pin (`IN1`-`IN4`, `VCC`, `GND`, `OUT1`-`OUT4`, `EEP` op `GND`, `ULT`/`FLT` onaangesloten); GPIO5 is hierdoor spare/onaangesloten, en de failsafe loopt via het nulzetten van de motor-PWM/digitale outputs (`stopMotors()`);
- een ongeplaatste 2x6-uitbreidingsheader voor zeven vrije GPIO's, dubbele 3,3 V/GND en geschakelde accuspanning;
- gereviewde firmware met afzenderfilter, packetvalidatie en een 200 ms failsafe;
- een placement-only KiCad-board totdat exacte leverancierfootprints en gemeten motorstromen bekend zijn.

Zie `firmware/roboktober-controller-v2/README.md` voor gedrag en compilatie, en `schema/electrical-design-v2.md` voor release blockers.

> [!WARNING]
> `ESP32-C2 SuperMini` is geen gestandaardiseerde modulefootprint. Controleer de fysieke pinvolgorde van de gekochte module tegen `pin-definitions/esp32-c2-supermini.md` voordat headers worden gesoldeerd of een PCB wordt besteld.

## KiCad

Open `kicad/antweight-controller/antweight-controller.kicad_pro` met KiCad 9 of nieuwer. Het schema is als een door KiCad 9 leesbaar legacybestand opgenomen; dit houdt het pincontract controleerbaar zolang de definitieve leveranciersfootprint nog niet is gekozen.

Het afzonderlijke v2-project staat in `kicad/antweight-controller-v2/`.

Voer voor een release minimaal uit:

```bash
kicad-cli sch erc kicad/antweight-controller/antweight-controller.sch
kicad-cli pcb drc kicad/antweight-controller/antweight-controller.kicad_pcb
```

Het ontwerp is een technisch startpunt, geen gecertificeerd product. Test eerst met een stroombegrensde voeding en zonder gemonteerde motoren.