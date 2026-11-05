# Roboktober Monorepo

Dit is de centrale repository voor Roboktober van Hackerspace Drenthe: de website en API, elektronica, robotfirmware, presentaties, 3D-printontwerpen en foto's.

Roboktober is een oktober-evenement rond combat robots, met focus op bouwen, leren en community.

## Wallie: website, uitleg en oorspronkelijke code

**Wallies werk is een belangrijke basis en inspiratiebron voor de robotbesturing, het mini-robotchassis en de presentatie.** Bezoek zijn eigen website en GitHub voor de oorspronkelijke uitleg, projecten en actuele ontwikkelingen:

- **Website:** [wallieonline.nl](https://wallieonline.nl/)
- **Robotcontroller-uitleg:** [WORC Robot Controller (Nederlands)](https://wallieonline.nl/blogs/worc-robot-controller-nl.html)
- **GitHub-profiel en alle repositories:** [github.com/wallieonline](https://github.com/wallieonline)

### Repositories voor de besturing

| Repository van Wallie | Wat vind je daar? |
| --- | --- |
| [wallieonline/worc-robot-controller-esp32c3](https://github.com/wallieonline/worc-robot-controller-esp32c3) | Robotcontrollerfirmware voor de ESP32-C3 en DRV8833; bron voor de upstream referenties en codevoorbeelden in dit project. |
| [wallieonline/worc-remote-controller-esp32](https://github.com/wallieonline/worc-remote-controller-esp32) | ESP32-afstandsbediening: de zenderkant van de besturingsketen en bron voor de presentatievoorbeelden. |

De lokale Roboktober-firmware en eigen ontwerpen zijn niet hetzelfde als Wallies actuele upstream projecten. De bewaarde firmwarebron en herkomst staan onder [electronics/firmware/upstream](./electronics/firmware/upstream/). Raadpleeg de licentie en bronvermelding van het betreffende bestand bij gebruik of herpublicatie; deze repository verleent geen algemene rechten op Wallies werk.

## Projectdoelen

De doelen zijn uitgewerkt in het [projectplan](./docs/PLAN.md):

1. Mensen warm maken voor het event met teaser-content, planning en sfeer.
2. Deelnemers helpen bouwen met complete en praktische bouwinformatie.
3. Assets duurzaam beschikbaar houden, zoals BOM, STL, bouwdocs, blog en links.
4. Een herbruikbare basis neerzetten voor toekomstige Roboktober-edities.

## Context

- Eerste editie: 2026.
- Publieke domeinnaam: https://roboktober.nl.
- Website-taal: Nederlands.

## Repository-structuur

De repository combineert **robotbouw**, **software**, **presentaties** en **beeldmateriaal**. De belangrijkste mappen staan hieronder. Firmware is onderdeel van `electronics`; er is geen aparte firmwaremap in de root.

```text
.
├── electronics/                 Elektronica en robotfirmware
│   ├── components/             Onderdelen en selectiecriteria
│   ├── pin-definitions/        Pincontracten voor de modules
│   ├── wiring/                 Bedrading en bouwinstructies
│   ├── schema/                 Elektrische ontwerpkeuzes
│   ├── kicad/                  PCB-projecten en revisies
│   └── firmware/               Eigen firmware en upstream bronnen
├── 3dprinter/                  Chassis en 3D-printonderdelen
│   ├── Wallieonline+Mini+Robot+chassis/
│   └── reinsdesigns/           Eigen ontwerpen en varianten
├── presentaties/               Presentatiebronnen per editie en taal
├── fotos/                      Foto's en video per onderwerp
├── roboktober-api/             Website-backend en beheer
├── roboktober-frontend/        Publieke website
├── deploy/                    Server- en deploymentbestanden
└── docs/                      Plannen en verdiepende documentatie
```

Dit is een functioneel overzicht, geen volledige bestandslijst. Gebruik de links hieronder voor de inhoud en instructies per onderdeel.

### 1. Robotbouw: elektronica en firmware

[electronics](./electronics/README.md) bevat de informatie om de besturing van een antweight-robot op te bouwen: onderdelen, voedingskeuzes, pinnen, bedrading en PCB-ontwerpen. De controller draait rond een ESP32-C3 en een DRV8833-motordriver.

| Map | Inhoud en gebruik |
| --- | --- |
| [components](./electronics/components/) | Onderdelen/stuklijsten en selectiecriteria voor de elektronica. |
| [pin-definitions](./electronics/pin-definitions/) | Pincontracten en controlepunten: welke aansluiting hoort bij welke functie? |
| [wiring](./electronics/wiring/) | Bedradingsinstructies voor de bouw en het zoeken naar aansluitproblemen. |
| [schema](./electronics/schema/) | Elektrische ontwerpkeuzes en netspecificaties. |
| [kicad](./electronics/kicad/) | PCB-bronnen en revisies. Het [v4-project](./electronics/kicad/hsd-antweight-2s-v4/) is volgens de elektronica-handleiding het leidende ontwerp; oudere revisies zijn archiefreferenties. |
| [firmware](./electronics/firmware/) | Software die op de robotcontroller draait, met eigen firmware en bewaarde upstream bronnen. |

Binnen firmware vind je:

- [roboktober-controller-v2](./electronics/firmware/roboktober-controller-v2/README.md): de eigen controllerfirmware, inclusief hardwarecontract, zenderconfiguratie, motorbesturing, invoervalidatie, failsafe en compileerinstructies.
- [upstream](./electronics/firmware/upstream/): oorspronkelijke referentiecode met bijbehorende licentie, om herkomst en verschillen te kunnen volgen.

**Begin bij de elektronica-handleiding en controleer daarna het pincontract en de firmwareconfiguratie.** Een passend bestand of geslaagde compilatie is geen bewijs dat de fysieke robot veilig werkt. Test met de wielen vrij van de grond en controleer de uitschakeling bij verbindingsverlies.

### 2. Mechanica: chassis en 3D-printontwerpen

[3dprinter](./3dprinter/) bevat ontwerpbronnen en exports voor robotchassis en onderdelen. Een `.scad`-bestand is bewerkbare OpenSCAD-broncode; een `.stl` is een mesh voor onder andere de slicer. Er staan ook STEP-bestanden en afbeeldingen voor bepaalde ontwerpen.

- [Wallieonline+Mini+Robot+chassis](./3dprinter/Wallieonline+Mini+Robot+chassis/): Wallies mini-robotchassis en topplaat, met STL-bestanden en OpenSCAD-/STEP-varianten.
- [reinsdesigns](./3dprinter/reinsdesigns/): eigen ontwerpen en opeenvolgende varianten, waaronder Disc Spinner, Limulus, Passive Aggressive, Racer, Rhomboid en Mostly Harmless.
- [Print- en montagehandleiding](./3dprinter/README.md): schaaladvies en de beschreven passing van Wallies topplaat op de v4-PCB.

Versienummers in bestandsnamen maken ontwerpevolutie zichtbaar; ze betekenen niet automatisch dat elke variant is gebouwd of gevalideerd. Controleer vóór het printen de afmetingen, beschikbare onderdelen en de passing van jouw combinatie van chassis en elektronica.


### 4. Presentaties en uitleg

[presentaties](./presentaties/) bevat de presentatiebronnen per bijeenkomst en taal. Voor PHP Meetup Groningen op **5 november 2026** zijn er twee versies:

- [Nederlands](./presentaties/php-meetup-groningen-2026-11-05/slides/README.md)
- [Engels](./presentaties/php-meetup-groningen-2026-11-05-en/slides/README.md)

Elke versie heeft een `PLAN.md` en een `slides/`-map met de presentatie, stijlen, scripts, lokale afbeeldingen/video en speaker notes. De bijbehorende README beschrijft het openen en verpakken van de slides. De presentatie volgt de keten van joystick tot wiel en verbindt software, elektronica en chassisontwerp.

### 5. Foto's en video

[fotos](./fotos/) bewaart lokaal beeldmateriaal uit de bouw en het evenement:

| Map | Onderwerp |
| --- | --- |
| [controller](./fotos/controller/) | Controllers en besturing. |
| [mensenbezig](./fotos/mensenbezig/) | Deelnemers en bouwactiviteiten. |
| [robots](./fotos/robots/) | Gebouwde robots. |
| [video](./fotos/video/) | Videomateriaal. |

Presentaties hebben daarnaast hun eigen lokale slide-assets. Een bestand in deze repository betekent niet automatisch dat het vrij herpubliceerbaar is: behoud bronvermeldingen en controleer de toepasselijke rechten en toestemmingen.

### 6. Website, API en server

- [roboktober-api](./roboktober-api/README.md): Laravel-backend, API en Filament-beheer.
- [roboktober-frontend](./roboktober-frontend/README.md): Vue-frontend voor de publieke website.
- [deploy](./deploy/README.md): deploymentinstructies en configuratie voor Apache en systemd.

Deze onderdelen vormen het webplatform, niet de robotfirmware of de zelfstandige WebGUI-simulator. Architectuur, lokale ontwikkeling, Docker en deployment zijn uitgewerkt in [docs/SERVER.md](./docs/SERVER.md).

### 7. Plannen en projectdocumentatie

[docs](./docs/README.md) is de ingang naar de verdiepende documentatie:

- [PLAN.md](./docs/PLAN.md): functioneel en technisch masterplan.
- [PLAN-CHANGES.md](./docs/PLAN-CHANGES.md): aanvullingen en wijzigingen op het plan.
- [Architectuur](./docs/ARCHITECTUUR.md): platformarchitectuur.
- [Perspakket](./docs/pers/README.md): persinformatie en geselecteerd beeldmateriaal.
- [ADR's](./docs/adr/README.md) en [release notes](./docs/release-notes/README.md): vastgelegde keuzes en wijzigingshistoriek.

## Waar begin je?

| Je wilt… | Begin hier |
| --- | --- |
| Een robot bouwen of aansluiten | [Elektronica](./electronics/README.md), daarna [bedrading](./electronics/wiring/) en [firmware](./electronics/firmware/roboktober-controller-v2/README.md). |
| Een chassis kiezen, aanpassen of printen | [3D-printontwerpen](./3dprinter/) en de [printinstructies](./3dprinter/README.md). |
| Bediening en moves lokaal uitproberen | [WebGUI-handleiding](./webgui/README.md) of direct de [simulator](./webgui/index.html). |
| De presentatie bekijken of voorbereiden | [Nederlandse](./presentaties/php-meetup-groningen-2026-11-05/slides/README.md) of [Engelse presentatie](./presentaties/php-meetup-groningen-2026-11-05-en/slides/README.md). |
| Beeldmateriaal zoeken | [Foto's en video](./fotos/); voor persgebruik het [perspakket](./docs/pers/README.md). |
| De website lokaal draaien of deployen | [Website en server](./docs/SERVER.md). |
| Scope, keuzes en projecthistoriek begrijpen | [Documentatie-index](./docs/README.md). |
