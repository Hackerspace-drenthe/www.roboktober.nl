# Roboktober electronics resources

Deze map bevat de gedeelde elektronica-informatie voor de Roboktober
antweight-builds.

## Inhoud

- `components/`: stuklijst en selectiecriteria voor onderdelen.
- `firmware/`: upstream referenties en de gereviewde Roboktober-firmware.
- `pin-definitions/`: logisch pincontract en controlepunten voor modules.
- `wiring/`: bedradingsinstructies voor bouw en foutzoeken.
- `schema/`: elektrische ontwerpkeuzes en netspecificatie.
- `kicad/antweight-controller-v2/`: historische legacy KiCad-projectmap
  (gearchiveerd).
- `kicad/hsd-antweight-2s-v3-fix/`: leidend ontwerp voor de huidige
  ESP32-C3 controller revisie.

Het leidende actieve ontwerp in deze repository is nu:

- v3-fix PCB: `kicad/hsd-antweight-2s-v3-fix/`
- v3-fix power rule: `ESP_5V_IN` is de standaard ESP-voeding; 2S via 5V regulateur,
  1S direct alleen op de gevalideerde module
- v3-fix design notes: `docs/WALLIE-DESIGN-ISSUES.md`

De PCB in `kicad/hsd-antweight-2s-v3-fix/` is de bron van waarheid voor het actuele ontwerp.
Legacy 3.3V-only en oudere 2S-documenten blijven alleen als archiefreferentie
bestaan voor vergelijking; ze zijn niet meer leidend voor bouw-, validatie- of
release-werk.

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

Het leidende actieve ontwerp is de v3-fix revisie in `kicad/hsd-antweight-2s-v3-fix/`.

Belangrijkste actuele regels:

- ESP32-C3 SuperMini voedt via `ESP_5V_IN`, niet via een legacy 3.3V-only rail;
- de module maakt intern 3.3V zelf; geen raw batterij op de ESP 3.3V pin;
- standaard route: `VBAT -> 5V-regelaar -> ESP_5V_IN`;
- 1S direct op `ESP_5V_IN` is alleen geldig voor de specifiek geteste module in v3-fix;
- `VIN -> VOUT` bypass naar de ESP 3.3V rail blijft verboden;
- de v3-fix PCB en schematic zijn de bron van waarheid, niet de legacy 2S docs.

De oudere 2S/3.3V-only documenten in deze map blijven alleen als historische referentie
bestaan en mogen niet meer als actieve power-contract gelden.

Zie `docs/WALLIE-DESIGN-ISSUES.md` voor de definitieve design rules en
`kicad/hsd-antweight-2s-v3-fix/README.md` voor de actuele PCB mapping.

## KiCad

Status van recente KiCad-updates:

- v3-fix is het leidende ontwerp voor nieuwe hardware en validatie.
- De PCB en het schema in `kicad/hsd-antweight-2s-v3-fix/` zijn de source-of-truth.
- Legacy ontwerpen in `kicad/hsd-antweight-2s-pcb/` en oudere mappen zijn archief.

Open `kicad/hsd-antweight-2s-v3-fix/hsd-antweight-2s-v3.kicad_pro` met KiCad 9 of nieuwer.

Voor review/validatie moet de actuele power-path altijd controleren.
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