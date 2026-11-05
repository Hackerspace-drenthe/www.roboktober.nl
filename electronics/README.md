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
- `kicad/hsd-antweight-2s-v4/`: leidend ontwerp voor de huidige
  ESP32-C3 controller revisie.
- `kicad/hsd-antweight-2s-v3-fix/`: historische tussenrevisie (archief).

Het leidende actieve ontwerp in deze repository is nu:

- v4 PCB: `kicad/hsd-antweight-2s-v4/`
- v4 power rule: minimale 1S setup is de standaard; 2S via regulator is optioneel
- v4 design notes: `docs/WALLIE-DESIGN-ISSUES.md`
- historische inkoopindicatie complete 1S-kit: ongeveer EUR 25 per robot

## Projectstatus

Op 2026-11-05 heeft de gebruiker bevestigd dat de v4-elektronica werkt en
goedgekeurd is; het 2026-project is afgerond. Dit is een brede
gebruikersbevestiging, geen nieuw testresultaat, certificering of verklaring
dat elke optionele voedingsconfiguratie afzonderlijk is getest.

De PCB in `kicad/hsd-antweight-2s-v4/` is de bron van waarheid voor het actuele ontwerp.
Legacy 3.3V-only en oudere 2S-documenten blijven alleen als archiefreferentie
bestaan voor vergelijking; ze zijn niet meer leidend voor bouw-, validatie- of
release-werk.

Publicatiestatus:

- De actieve v4-elektronica is door de gebruiker goedgekeurd zoals hierboven
  beschreven; er staat geen 2026 go/no-go- of prototypebesluit meer open.
- Die goedkeuring is geen certificering of bewijs van massaproductiegeschiktheid.
- Eerdere projectnotities beschreven de v4-print als proto-ready voor
  proefseries en hielden massaproductie tegen totdat footprints en thermische
  validatie definitief waren. Dat is een historische statusnotitie, geen
  openstaand projectbesluit; de huidige gebruikersbevestiging beweert niet dat
  die voorwaarden later zijn afgerond of dat massaproductie is vrijgegeven.
- De eerder genoemde circa EUR 25 per complete 1S-kit is een historische
  inkoopindicatie, geen actuele offerte.

## Reference design

Het leidende actieve ontwerp is de v4 revisie in `kicad/hsd-antweight-2s-v4/`.

Belangrijkste actuele regels:

- ESP32-C3 SuperMini voedt via `ESP_5V_IN`, niet via een legacy 3.3V-only rail;
- de module maakt intern 3.3V zelf; geen raw batterij op de ESP 3.3V pin;
- standaard route: minimale 1S setup via charge/protection breakout op de v4;
- optionele route: `2S -> 5V-regelaar -> ESP_5V_IN`;
- `VIN -> VOUT` bypass naar de ESP 3.3V rail blijft verboden;
- de v4 PCB en schematic zijn de bron van waarheid, niet de legacy 2S docs.

De oudere 2S/3.3V-only documenten in deze map blijven alleen als historische referentie
bestaan en mogen niet meer als actieve power-contract gelden.

Zie `docs/WALLIE-DESIGN-ISSUES.md` voor de definitieve design rules en
`kicad/hsd-antweight-2s-v4/README.md` voor de actuele PCB mapping.

## KiCad

Ontwerpstatus:

- v4 is het leidende ontwerp voor nieuwe hardware en validatie.
- De PCB en het schema in `kicad/hsd-antweight-2s-v4/` zijn de source-of-truth.
- Legacy ontwerpen in `kicad/hsd-antweight-2s-pcb/` en oudere mappen zijn archief.

Open `kicad/hsd-antweight-2s-v4/hsd-antweight-2s-v4.kicad_pro` met KiCad 9 of nieuwer.

Controleer bij een nieuwe revisie of onafhankelijke herhaling de actuele
power-path aan de hand van de PCB en het pincontract.

Voer voor een nieuwe revisie of onafhankelijke release-validatie minimaal uit:

```bash
kicad-cli sch erc --severity-error \
  kicad/hsd-antweight-2s-v4/hsd-antweight-2s-v4.kicad_sch
kicad-cli pcb drc kicad/hsd-antweight-2s-v4/hsd-antweight-2s-v4.kicad_pcb
```

Het ontwerp is geen gecertificeerd product. De bevestiging van de gebruiker
hierboven verandert dat niet. Test bij een nieuwe bouw eerst met een
stroombegrensde voeding en zonder gemonteerde motoren.