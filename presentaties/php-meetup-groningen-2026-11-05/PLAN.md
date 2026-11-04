# Plan: Roboktober-presentatie, PHP Meetup Groningen

> Status: inhoud en slides op elkaar afgestemd; reviewpakket klaar voor meelezers. Reviewfeedback, Battle Day-winnaar en een getimede repetitie staan nog open.

## 1. Basisgegevens

| Onderdeel | Waarde |
| --- | --- |
| Event | PHP Meetup Groningen, donderdag 5 november 2026 |
| Spreker | Rein; Wallie via een videofragment |
| Publiek | Developers, engineers en makers; geen elektronicakennis vereist |
| Taal | Nederlands; technische termen bij eerste gebruik uitleggen |
| Duur | 60 minuten: 47 minuten verhaal inclusief video en demo, 2 minuten uitloop, 11 minuten Q&A |
| Format | Statische reveal.js-slides, lokaal te openen via `file://`; PDF als noodbackup |
| Demo | Robots zonder actief wapen, een afgebakend rijvlak, één bestuurder per robot |
| Mogelijke assistent | Hendrie, lid van Hackerspace Drenthe; talk blijft solo uitvoerbaar |

## 2. Belofte aan het publiek

**Centrale vraag:** wat gebeurt er als je op je telefoon de joystick naar voren duwt?

**Kernboodschap:** als je kunt programmeren, kun je ook robots bouwen. Elektronica, 3D-ontwerpen, websites en robots zijn allemaal te maken, aan te sturen en te valideren met code; interfaces, versiebeheer, review en testen werken ook voor een robot van 150 gram. Slotzin op de recap-dia.

De robottechniek is het verhaal. Roboktober is de aanleiding. Website en AI zijn korte zijpaden die verklaren hoe het project uitvoerbaar werd, geen tweede en derde talk.

Na afloop kan iemand:

1. De route uitleggen: controller → firmware → PWM → H-brug → motor.
2. Uitleggen waarom de robot zonder verse commando's moet stoppen.
3. Bekende softwareprincipes herkennen in pincontracten, ontwerpcontroles en parametrische 3D-modellen.
4. Zelf beginnen via de onderdelenlijst, bouwgids, firmware en chassisbestanden.

### Didactische afspraken

- Begin met beweging, niet met een lijst onderdelen.
- Leg elk nieuw begrip uit voordat je de afkorting als bekend gebruikt: printplaat (PCB), PWM, H-brug, deadband en failsafe.
- Eén idee per slide. Codefragmenten bij voorkeur maximaal tien regels; weglatingen en vereenvoudigingen expliciet labelen.
- De hoofdroute blijft hetzelfde, ook als het transport wisselt van WebSocket naar ESP-NOW.
- Eén doorlopend rekenvoorbeeld: **na schalen en deadband** gas `200`, sturen `0` → links `200`, rechts `200` → ongeveer 78% dutycycle.
- RC-kanaalwaarden rond `1500`, motorcommando's rond `0` en dutycycle in procenten zijn verschillende schalen.
- Extra bochtvoorbeelden dienen alleen om mixing uit te leggen, niet als nieuwe hoofdroute.
- Geen elektronicakennis of pinnummers uit het hoofd verlangen; steeds terug naar de betekenis.
- Geen niveaugrappen ten koste van deelnemers of Wallie. Vragen stellen moet laagdrempelig blijven.
- Hardware laten zien of rondgeven zonder te wachten tot iedereen klaar is.

## 3. Opbouw en timing

De tijden zijn een repetitiedoel, geen gemeten duur. Repetitie inclusief 84 seconden video, fragmentstappen, demo-overgangen en publieksbesturing.

Korter blijft het doel. Alleen als de uitleg dit echt nodig heeft mag het verhaal uitlopen tot maximaal 60 minuten; dit gaat ten koste van uitloop/Q&A, niet boven op het uur. Houd bij extra OpenSCAD-uitleg (richttijd 2 min, maximaal 5 min) de klok bij en kort zijpaden en herhaalde uitleg in.

Bij alle dia's met klikstappen wisselt het S-sprekersvenster automatisch mee: startnotitie, daarna alleen de spreektekst voor de huidige stap plus korte context/timing. Dit gebruikt de standaard fragmentnotities van reveal.js, ook bij code-highlights, gelijktijdige fragmenten en de gegenereerde galerij. Terugklikken en rechtstreeks naar een stap springen geven dezelfde notitie. Dia's zonder klikstappen houden hun bestaande notities. `slides/js/step-notes.js` bewaakt dat iedere klikgroep een tekst heeft; bij gewijzigde fragmenten moeten de notities mee worden aangepast.

| Sectie | Minuten | Cumulatief | Vraag |
| --- | ---: | ---: | --- |
| 3.1 Cold open, Rein en publiekspeiling | 3 | 3 | Waarom rijdt hier een robot? |
| 3.2 Wallie, inclusief video | 2 | 5 | Waar komt de robotkennis vandaan? |
| 3.3 Roboktober en de invalshoek van vandaag | 2 | 7 | Waarom bouwden we dit samen? |
| 3.4 Antweight en veiligheid | 2 | 9 | Wat bouwen we en onder welke grenzen? |
| 3.5.1 De hele commandoroute | 2 | 11 | Welke stappen zitten tussen duim en wiel? |
| 3.5.2 Controller | 5 | 16 | Hoe wordt een beweging een bericht? |
| 3.5.3 Firmware, PWM, H-brug en failsafe | 12 | 28 | Hoe wordt een bericht beweging, en wat als het wegvalt? |
| 3.5.4 Printplaat en validatie | 4 | 32 | Hoe verbinden we dit betrouwbaar? |
| 3.5.5 Chassis als code, inclusief ontwerpgalerij | 4 | 36 | Hoe worden losse onderdelen één robot? |
| 3.6 Publiek stuurt | 4 | 40 | Kun je het zelf gebruiken? |
| 3.7 Website als distributiekanaal | 2 | 42 | Hoe krijgen deelnemers bouwinformatie? |
| 3.8 Traject en AI-ondersteuning | 2 | 44 | Hoe kwam het project tot stand? |
| 3.9 Praktijkresultaat | 2 | 46 | Werkte de gedeelde basis? |
| 3.10 Recap en zelf beginnen | 1 | 47 | Wat neem je mee? |
| Uitloop | 2 | 49 | Ruimte voor vragen en demo-overgangen |
| Q&A | 11 | 60 | Verdieping naar belangstelling |

### 3.1 Cold open en introductie

- Groot Roboktober-logo op het scherm; robot rijdt binnen op een begrensd vlak. Geen voorwerp richting publiek of van een onbeschermde tafel duwen.
- Daarna: Rein, software engineer bij Dawn Technology, oprichter Nederlandse PHP Gebruikersgroep / PHPBenelux en Hackerspace Drenthe.
- Brug: van softwarecommunity naar makercommunity.
- Publiekspeiling: wie heeft gesoldeerd, wie heeft Arduino/ESP32 geprogrammeerd?
- Belofte: vandaag één commando volgen. Het tijdlijnverhaal bewaren tot het einde.

### 3.2 Wallie

- WallieOnline, Team Upstart, robotervaring en samenwerking met de hackerspace.
- Korte introductie, dan Wallie zelf: **0:26–1:50, duur 1:24**, uit de kickoff-opname.
- Video staat op de profielslide; niet alle bullets voorlezen naast de video.
- Toestemming controleren. De lokale MP4 is als data opgenomen in `slides/js/wallie-video.js` voor `file://`-compatibiliteit.
- Geluid standaard aan, handmatig starten; zaalgeluid vooraf controleren.

### 3.3 Roboktober

- Samen bouwen, testen en rijden: Hackerspace Drenthe + WallieOnline.
- Programma 2026: kickoff 7 oktober, bouwen 14 en 21 oktober, keuring en Battle Day 28 oktober.
- Overgang: bij de bouwavonden bouwen en rijden; vandaag kijken we als developers onder de motorkap.

### 3.4 Antweight en veiligheid

- Onze klasse: maximaal 150 g inclusief accu; voor deze bouwopzet een kubus van 15 × 15 × 15 cm.
- Onderdelenkit ongeveer EUR 25; geen belofte dat gereedschap en alle bijkomende kosten inbegrepen zijn.
- Bij Roboktober 2026 geen actieve wapens. Een gesloten arena alleen is geen toestemming voor spinners: geschikt reglement, afscherming en keuring blijven nodig.
- Geen uitstap naar andere gewichtsklassen; controleer regels altijd voor het bedoelde evenement.

### 3.5.1 Overzicht: van duim naar wiel

De bestaande overzichtsslide toont zeven onderdelen één voor één, elk met een groot beeld en een eigen functie: chassis → accu → aan/uit → kale PCB → ESP32 → motordriver → motoren/wielen. Chassis staat direct in beeld, zes klikken tonen de andere onderdelen. Aan/uit toont een schakelaar of uitneembare plug/pincap die de accuvoeding fysiek onderbreekt, niet de softwarematige failsafe. Kies een van buiten bereikbare uitvoering geschikt voor de motorstroom; ontkoppel ook andere voedingen vóór werkzaamheden. PCB en ESP32 zijn afzonderlijke stappen: de print verbindt, de microcontroller voert code uit. Chassis en PCB gebruiken bestaande renders; accu en voedingsonderbreking zijn schematisch. N20-motor en wiel staan als afzonderlijke productfoto's naast elkaar binnen de zevende stap, met bronvermelding (AliExpress, aangeleverd door Rein; gebruiksrechten nog te controleren). Wijs de onderdelen ook op de uitgeschakelde, geopende robot aan. Reserveer ongeveer 60 seconden binnen de bestaande 2 minuten.

De ESP32-C3 SuperMini wordt met een echte foto getoond (Tobias Weltner, done.land, CC BY-ND 4.0; ongewijzigd en lokaal meegeleverd). De DRV8833 gebruikt de door Rein aangeleverde AliExpress-productfoto, eveneens lokaal meegeleverd; controleer de gebruiksrechten vóór verdere openbare verspreiding.

De volgende klik toont op dezelfde slide het overzicht met de eerste commandostap; nog vier klikken voltooien de bestaande vijf animatiestappen. Totaal elf klikken, geen extra slide. Brug: "Wat gebeurt er als je op je telefoon vooruit duwt?"

1. Telefoon/WebSocket of remote/ESP-NOW levert gas en sturen.
2. ESP32-C3 ontvangt, controleert, schaalt en mixt.
3. PWM is het snel aan/uit schakelen van stuursignalen.
4. De DRV8833 bevat twee H-bruggen; de accu levert de motorenergie.
5. N20-motoren bewegen de wielen.

Laat de echte print zien, maar leg pinnen en voedingsvarianten nog niet uit.

### 3.5.2 Controller

- Telefoon: de robot biedt een eigen WiFi-netwerk en webinterface op `192.168.4.1`; geen app of externe router.
- WebSocket: berichten over een open verbinding. Joystick of tankbesturing met twee sliders.
- Remote: tweede ESP32, analoge stick, ESP-NOW. Beide routes leveren gas en sturen.
- Dia 16 vermeldt Wallies aangekondigde nieuwe firmwarerelease op **5 november 2026**, de presentatiedag, met een link naar [zijn GitHub-profiel](https://github.com/wallieonline). Controleer op de dag zelf de beschikbaarheid.
- **Overgang hardop:** de codevoorbeelden blijven afkomstig uit de bestaande openbare ESP-NOW-firmware, niet uit de aangekondigde nieuwe release. Mixing en motorcode zijn hetzelfde, het transport verschilt.
- Alleen gedeelde struct, twee kanaaltoewijzingen, verzenden en lengtecheck tonen. Volledige broncode via links.
- Takeaway: een vast gegevensformaat is een interface, net als `pack()`/`unpack()`.

### 3.5.3 Firmware, PWM, H-brug en failsafe

- Flashen via USB en Web Serial, ondersteunde Chromium-browser; datakabel vereist.
- Na flashen: één extra slide met de volledige openbare ESP-NOW/DRV8833-firmware (205 regels, bronversie `fb3afa886117`, GPL-3.0-or-later). Acht klikken belichten achtereenvolgens bericht/grenzen, toestand, setup/PWM, radio/callback, ontvangst, mixing, richting/PWM en loop/failsafe; bibliotheken/pinnen zijn direct uitgelicht. De code scrolt mee en blijft volledig offline beschikbaar, inclusief licentie. Dit is niet de WebUI-broncode. Per codeblok verschijnt eigen uitleg (wat, waarom, effect op de robot), synchroon met de highlights, ook bij terugklikken. Benoem ook de beperking bij ontvangst: ongeldige kanaalwaarden behouden hun vorige waarde, maar berichten met de juiste lengte vernieuwen wel de timeout. Circa twee minuten binnen het bestaande firmwareblok; houd de volgende detaildia's korter.
- Ontvangen: berichtlengte en kanaalbereik controleren. Schalen naar −255…255, deadband maakt ruis rond neutraal nul.
- Rekenvoorbeeld vanaf hier: gas 200, sturen 0. De concrete RC-invoer hangt af van de ingestelde grenzen.
- Mixing: links = gas + sturen; rechts = gas − sturen; begrenzen op −255…255.
- PWM: bijvoorbeeld 200 ≈ 78% aan bij 8 bit; **niet** 78% snelheid of vermogen beloven.
- H-brug kiest stroomrichting, PWM bepaalt de aan-tijd. De motorenergie komt uit de accu, niet uit de GPIO.
- Elke codeweergave krijgt een bronlink en is als ingekort of vereenvoudigd herkenbaar.

#### Veilig stoppen hoort bij de firmware

- Sluit het firmwareblok af met de vraag: wat gebeurt er als het commando wegvalt?
- Na meer dan 500 ms zonder pakket: gas en sturen nul, motoren stoppen; LED knippert.
- Analogie: heartbeat-timeout, naar een veilige toestand bij ontbrekende input.
- **Eén live bewijs:** wielen omhoog, remote uit of WiFi-verbinding weg. Bij de latere publieksdemo niet opnieuw uitleggen.
- Geen algemene veiligheids- of authenticatiegarantie aan alleen deze timeout verbinden.

### 3.5.4 Printplaat en validatie

- Overgang: de motor draait; hoe verbinden we de onderdelen betrouwbaar?
- ESP32-C3, DRV8833, motoren en accu. Geen voedingsbypass naar 3,3 V.
- Pincontract: schema en firmware moeten dezelfde interface volgen. Eén motor als voorbeeld is genoeg.
- Eerste PCB-ontwerp: iteraties v2 → v3 → v4 vóór de productieoplage.
- ERC/DRC zijn automatische elektrische en ontwerpregelcontroles, geen bewijs dat een schakeling functioneel correct is.
- Validatie-slide: per klik geanimeerde koperafstand, baanbreedte en aansluitingscontrole op de printplaat. Expliciet een illustratie, geen live KiCad-check of maatmeting; daarna pas DRC-resultaat, afzonderlijke ERC-meldingen en menselijke review. Animaties respecteren verminderde beweging.
- Controle tegen het pincontract, menselijke review door Wallie en fysieke tests zijn aanvullend nodig. AI-review niet presenteren als deterministische test.
- Bevestigd resultaat: 150 kale printplaten geproduceerd (zonder onderdelen); tot nu toe 30 gemonteerd, 25 werkend. Uitval door afwijkende ESP32's met slechte wifi en montagefouten (user error), niet door het ontwerp.

### 3.5.5 Chassis als code

- Slide 32: korte publiekspeiling, circa 20 seconden binnen het chassisblok. Eerst: wie heeft al eens iets in 3D ontworpen? Daarna per klik: wie kent OpenSCAD, en wie heeft er al iets mee ontworpen? Stem de uitleg af op de reacties.

- Overgang: losse onderdelen zijn nog geen robot.
- OpenSCAD past bij mijn tekstgebaseerde workflow: parameters, diffs, review en expliciete maatvoorwaarden.
- Visuele CAD-tools kunnen óók parametrisch en automatiseerbaar zijn; geen karikatuur van Fusion/FreeCAD.
- Een `assert` bewaakt alleen de geprogrammeerde voorwaarde. Testprints blijven nodig voor pasvorm en toleranties.
- Slide 34: aparte code-dia met twee klikstappen, circa twee minuten. Eerst herbruikbare maten, basisvormen en translate/union/difference; daarna busjes met schroefgaten via module en lus, import met linear_extrude en asserts. Alleen code en de exacte voorbeeldresultaten, geen robotontwerpen. De vier bestaande voorbeeldscripts en profile.dxf blijven offline beschikbaar. Renders opnieuw maken met `xvfb-run -a node slides/scripts/render-scad-examples.mjs` (OpenSCAD vereist).
- Slide 35: aparte ontwerpgalerij, zonder code, met 9 klikstappen en 23 beelden. Richttijd één minuut, maximaal twee minuten bij vragen. Eén klik per ontwerpfamilie, met versies en onderdelen naast elkaar: Wallie, chassis v0.2/v0.3/v0.4, Disc/Mini/Wedge, Disc Spinner v0.1–v0.5, Rhomboid, Limulus v0.1/v0.2, Passive Aggressive/4WD, Mostly Harmless met dak en DuoBot. Nordic Racer blijft op de volgende vergelijkingsslide.
- Verschillende ontwerplijnen expliciet benoemen, niet één lineaire versiegeschiedenis. Geen dubbele meshconversies, exportbestanden of hulpbibliotheken. Renders zijn niet op gelijke schaal en bewijzen geen pasvorm of werking.
- Resultaten op dia 35, bevestigd door Rein: Disc Spinner v0.5 reed als ‘Lief Monster’ naar de 3e plaats; Limulus v0.1 werd 4e. Passive Aggressive presteerde goed, met een luchtige opmerking over bestuurders. Mostly Harmless is Reins favoriet: complete robot 140 g, wedge voor kantelen, duwer achterop en zeer sterk. Volgens Rein kwam hij zonder schade uit een confrontatie met Wallies robot met cirkelzaag; één ervaring, geen algemene veiligheidsgarantie. De naam Disc Spinner impliceert geen actief wapen in de wedstrijd; die waren niet toegestaan.
- Bronbestanden blijven bereikbaar; vooruit én achteruit bladeren werkt op beide dia's met reveal.js-fragmenten.
- Disc Spinner-iteraties op dia 35: de eerste versies waren te groot; binnen het budget van 50 g voor het geprinte chassis werd het materiaal te dun. Uiteindelijk kleiner, dikker materiaal, sterker en minder spikes (bevestigd door Rein). Dit is niet het totale robotgewicht.
- Varianten laten zien: dezelfde printplaatmaten, andere behuizing.

### 3.6 Publiek stuurt

- Begrensd rijvlak, geen actief wapen, maximaal één bestuurder per robot.
- Werkelijke SSID en wachtwoord op een kaartje bij elke robot, vooraf getest.
- Bij problemen binnen één minuut wisselen naar een vooraf verbonden telefoon of reserve-robot; talk niet laten vastlopen op netwerkconfiguratie.

### 3.7 Website

- Laravel API, Filament en Vue slechts als herkenningspunt.
- Hoofdles: bouwgids, onderdelenlijst en downloads maken samen bouwen mogelijk.
- Website en bouwbestanden op GitHub; ESP-NOW-firmware bij WallieOnline. Niet zeggen dat alle WebUI-broncode al openbaar is.
- Geen opsomming van alle ontwikkeltools of registratieprocessen.

### 3.8 Traject en AI

- **Ongeveer drie maanden voorbereiding:** eerste commit 2 juli tot kickoff 7 oktober. Bouwavonden en Battle Day volgen daarna in oktober.
- Enkele mijlpalen in plaats van een inkooplog: website, printontwerp/productie, firmware/chassis, kickoff.
- AI hielp itereren; Rein bleef verantwoordelijk voor doelen, keuzes, validatie en correcties. Wallie leverde robotkennis en review.
- Takeaway: AI helpt verbreden, maar neemt verantwoordelijkheid en fysieke tests niet over.

### 3.9 Praktijkresultaat

- Bevestigde Battle Day-cijfers: ruim 30 bezoekers (volle bak), 18 robots in eerste instantie, 12 robots in de competitie.
- Concrete tegenslag en les: niet elke robot werkte op tijd. Rein had 5 werkende robots meegenomen en leende die uit aan kinderen zonder werkende robot, zodat zij toch konden meedoen.
- Printplaatresultaat (25 van 30 werkend) staat al bij de validatie (3.5) en wordt hier niet herhaald.
- Foto uit het bouwtraject met correct onderschrift; geen verzonnen winnaar (uitslag alleen noemen na bevestiging).

### 3.10 Afsluiting

- Laatste dia (48), na Q&A: grote QR-code en klikbare URL naar https://www.roboktober.nl/app/presentaties/php-meetup-groningen-2026-11-05/ met beide talen en offline downloads. Laat staan om te scannen.

- Slide 45 sluit de rode draad af: van joystick tot wiel, met dezelfde softwareprincipes. Vier korte lessen: open software en standaarden als basis; toetsbare eisen als contract; code als gereedschap, ook voor hardware; tests als bewijs, ook fysiek. AI helpt bij elke stap; de gebruiker bepaalt, controleert en valideert. De commandoketen en bestaande kernboodschap blijven staan.
- Beantwoord de openingsvraag in de volgorde controller → firmware → PWM/H-brug → motor.
- Benoem veilig stoppen expliciet.
- Afsluitzin: **je blijft developer — alleen rijdt je bug nu weg.**
- Eén vervolgstap: roboktober.nl voor onderdelenlijst en bouwgids; WallieOnline voor firmware; kom naar de hackerspace.

## 4. Reserve-inhoud en inkorten

Niet toevoegen aan de hoofdroute: volledige zender-/ontvangercode, historische code-review, alle kanaalnamen, volledige pin-/voedingstabellen en uitgebreide inkoopdata.

Bij uitloop:
1. Pincontract alleen aan één motor uitleggen.
2. Chassisvergelijking kort houden; één parameter en één assert.
3. Tijdlijn in één zin; geen tweede AI-verhaal.
4. Publieksdemo met één vooraf verbonden bestuurder.

Niet schrappen: openingsvraag, complete commandoroute, failsafe en afsluiting.

## 5. Laatste voorbereiding

- [ ] Getimede repetitie: doel 47 minuten inclusief video, fragmenten en demo; chassisreeks met leerpunten vlot in circa twee minuten.
- [x] Definitieve eventcijfers en concreet leermoment verzameld (30+ bezoekers, 18 → 12 robots, 5 uitgeleende robots).
- [ ] Battle Day-uitslag/winnaar bevestigen (optioneel).
- [ ] Reglement, kitprijs en de cijfers op de slides nogmaals controleren.
- [ ] Toestemming Wallie en hoorbaarheid van de video controleren.
- [x] Offline reviewpakket gecontroleerd: 48 dia's; afbeeldingen, video en galerijlinks zijn lokaal aanwezig. De volledige firmwaredia laadt ook vanaf `file://`, met alle 205 bronregels en negen uitlichtstappen. Op de avond de map inclusief `vendor/` en `js/wallie-video.js` meenemen.
- [x] Reviewpakket voor meelezers in `review/` (niet in git): `bash slides/scripts/build-review-package.sh [--full] review`. Compact (±12 MB) of volledig met video's en alle 3D-bronnen (±112 MB). Begint met `00-LEES-MIJ-EERST.html`; tekst in `slides/scripts/LEESMIJ-review.md`.
- [ ] Feedback van reviewers verwerken.
- [ ] Na wijziging van de MP4: `bash slides/scripts/embed-wallie-video.sh`.
- [ ] Demo-robots, kaartjes, één bestuurder per robot en drukke WiFi-omgeving testen.
- [ ] Begrensd rijvlak, reserve-robot, vooraf verbonden telefoon, accu's en laadmiddelen.
- [ ] Kale en bestukte printplaat plus chassisvarianten klaarleggen.
- [ ] PDF exporteren; video en animaties werken daarin niet. Fysieke onderdelen of stilstaande beelden als alternatief gebruiken.
- [ ] Met Hendrie afstemmen of hij assisteert; geen afhankelijkheid van zijn aanwezigheid.

## 6. Bronnen

- [Slides en gebruiksinstructies](slides/README.md)
- [Reviewer-LEESMIJ](slides/scripts/LEESMIJ-review.md) en [pakketscript](slides/scripts/build-review-package.sh)
- [Projectoverzicht](../../README.md), [masterplan](../../docs/PLAN.md) en [wijzigingslog](../../docs/PLAN-CHANGES.md)
- [Eventprogramma](../../docs/pers/persbericht-2026-08-31.md)
- [Elektronica](../../electronics/README.md), [pincontracten](../../electronics/pin-definitions/) en [onderdelen](../../electronics/components/)
- [Firmware v2](../../electronics/firmware/roboktober-controller-v2/README.md) en [ontwerpissues](../../docs/WALLIE-DESIGN-ISSUES.md)
- [OpenSCAD en chassis](../../3dprinter/README.md)
- [Openbare ontvanger](https://github.com/wallieonline/worc-robot-controller-esp32c3) en [zender](https://github.com/wallieonline/worc-remote-controller-esp32)
- De code-slides verwijzen naar specifieke upstream commits. Behoud bronvermelding en controleer de licentie van die versie bij verdere verspreiding.
- [Kickoff-livestream](https://www.youtube.com/live/qtz0UkJw4DU)
