# Wallie - Design Issues Overzicht

Bron: opmerkingen van Wallieonline op de huidige PCB-revisie.

Doel van dit document:
- 1 plek met alle gemelde problemen
- per punt status bijhouden (opgelost / open / nog valideren)
- concrete vervolgactie afspreken

## Samenvatting

- W-01 opgelost in `v3-fix`: `SLEEP` van de motor driver is verplaatst van `GND` naar `GPIO20_SPARE`.
- Kritiek: huidige VREG-opzet is niet 1S-veilig zoals nu getest (W-02/W-03).
- Mechanisch/UX: schakelaar-eilanden, motorconnector-orientatie en VREG-plaatsing kunnen beter.

## Impactanalyse openstaande punten

Schaal:
- Kans: Laag / Middel / Hoog
- Impact: Laag / Middel / Hoog / Kritiek
- Prioriteit: P0 (direct), P1 (eerstvolgend), P2 (kan later)

| ID | Kans | Impact | Technische impact | Productimpact | Verwachte inspanning | Prioriteit | Advies |
|---|---|---|---|---|---|---|---|
| W-02 | Hoog | Kritiek | 1S voeding werkt niet betrouwbaar of niet; kans op brownouts/resetgedrag en instabiele logica-voeding. | 1S use-case faalt in de praktijk; verhoogde supportlast en retourrisico. | Middel (schema + PCB reroute + validatie) | P0 | Kies en implementeer 1S/2S voedingsarchitectuur voordat nieuwe productie wordt vrijgegeven. |
| W-03 | Hoog | Kritiek | Mogelijke overvoltage op 3.3V-domein bij bypass; risico op directe ESP-schade. | Hoog risico op field failures en beschadigde boards. | Laag-Middel (beveiliging/routewijziging + duidelijke jumperlogica) | P0 | Blokkeer of herontwerp VIN->VOUT bypasspad zodat 3.3V pin nooit direct 1S piekspanning ziet. |
| W-04 | Middel | Hoog | 5V-regelaaroptie lost 1S-compatibiliteit vaak robuust op, maar vraagt hercontrole thermisch en pin-compatibiliteit ESP-board. | Biedt eenvoudige gebruikersroute voor 1S/2S mits goed uitgewerkt. | Middel (componentkeuze + layout + test) | P1 | Technisch kansrijke hoofdoptie; valideren op warmte, efficiency en ESP-module toleranties. |
| W-05 | Middel | Middel-Hoog | Gescheiden 1S/2S aansluitroute verlaagt foutkans door expliciete aansluitkeuze, maar verhoogt routingcomplexiteit en kans op gebruikersfout zonder labeling. | Betere flexibiliteit, maar vraagt duidelijke UX-markering op PCB/silkscreen. | Middel (connector/jumper architectuur + documentatie) | P1 | Goede fallback of combinatie-optie; alleen doen met heldere labeling en anti-misuse ontwerp. |

### Samenvattende impactconclusie

1. W-02 en W-03 vormen samen een release blocker voor veilige 1S ondersteuning.
2. W-04 en W-05 zijn oplossingsrichtingen; kies er minimaal één als definitieve architectuur.
3. Zonder keuze op W-04/W-05 blijft het risico van W-02/W-03 structureel aanwezig.

### Aanbevolen beslisvolgorde

1. Besluit architectuur: `W-04` (5V-regelaar), `W-05` (gescheiden paden), of hybride.
2. Verwerk schema + PCB in `v3-fix` met expliciete anti-misuse logica.
3. Valideer 1S en 2S met meetrapport (opstart, belasting, temperatuur, spanning op ESP-rails).
4. Pas daarna productie-assets en release-notes bijwerken.

## Oplosplan 1S/2S compatibiliteit

Doel:
- Een enkele PCB-revisie die veilig werkt op zowel 1S als 2S, afhankelijk van de aangesloten accu.
- Geen scenario waarbij de ESP 3.3V rail direct accuspanning kan zien.

Vastgestelde keuzes (2026-08-22):
1. Voedingsarchitectuur: `5V-regelaar als hoofdroute`.
2. Selectiemechanisme: `2-pin jumper/shunt` voor 1S/2S keuze.
3. Acceptatie-eis: `alle drie verplicht` (geen overvoltage, betrouwbare boot 1S/2S, thermisch binnen marge).

### Fase 0 - Architectuurkeuze (beslis binnen 1 iteratie)

Keuzes:
1. Optie A: `VREG 5V` gebruiken en ESP voeden via `5V` pin.
2. Optie B: `VREG 3.3V` behouden, maar 1S/2S paden fysiek scheiden met duidelijke selectie.
3. Optie C: Hybride: 5V hoofdroute + expliciete selectie/labeling voor 1S/2S.

Voorkeursrichting op basis van risico:
1. Gekozen: Optie A (`5V-regelaar`) als primaire route.
2. Alleen Optie B gebruiken als er later harde redenen zijn tegen 5V-route (thermisch, componentbeschikbaarheid, efficiëntie).

### Fase 1 - Electrisch ontwerp (schema)

Verplichte ontwerpregels:
1. Verbied directe `VIN->VOUT` bypass naar ESP `3.3V` rail.
2. Maak voedingstopologie expliciet voor 1S en 2S (jumper/solder-bridge/header met eenduidige stand).
3. Voeg anti-misuse toe:
  - duidelijke netnamen (`PWR_1S_IN`, `PWR_2S_IN`, `ESP_5V_IN`, `ESP_3V3_IN`),
  - duidelijke silkscreen labels bij selectiepunten.
4. Documenteer toegestane configuraties in schema-opmerking (wat mag bij 1S, wat mag bij 2S).

### Fase 2 - PCB implementatie (v3-fix)

1. Route voedingspaden met prioriteit op:
  - spanningsveiligheid,
  - lage spanningsval,
  - thermische marge rond regelaar.
2. Plaats keuze-elementen (jumper/switch/pads) fysiek logisch en dicht bij voedingsinvoer.
3. Houd testpunten vrij voor:
  - accuspanning,
  - regulator uitgang,
  - ESP voedingspin.

### Fase 3 - Validatie en kwalificatie

Minimale testmatrix:
1. 1S idle + piekbelasting (motorstart) + brownout-check.
2. 2S idle + piekbelasting + thermische check regulator.
3. Boottest 20x op 1S en 20x op 2S zonder ongewenste motordraai.
4. Meting op ESP voedingspin:
  - nooit boven toegestane railspanning,
  - geen instabiele dips buiten tolerantie.

Exit-criteria:
1. Geen overvoltage op ESP-rail in alle geldige configuraties.
2. Geen reset/brownout tijdens normale motorpieken.
3. DRC/ERC zonder nieuwe kritieke fouten.

### Fase 4 - Productie en documentatie

1. Werk assemblage-instructie bij met 1S/2S configuratievoorbeelden.
2. Voeg silkscreen legend toe in productiebeeld (welke stand voor 1S, welke voor 2S).
3. Publiceer korte gebruikersnotitie: "accu kiezen -> jumperstand -> veilige opstart".

### Concreet besluitvoorstel

1. Bestel VREG in 5V-variant als primaire route voor de volgende testspin.
2. Implementeer in `v3-fix` een veilige selectie voor 1S/2S zonder directe 3.3V-bypass.
3. Voer testmatrix uit en pas daarna pas productiebestanden aan.

### Kandidaatmodules (goedkoop) voor test

Opmerking:
- Prijzen op AliExpress zijn dynamisch; onderstaande is indicatief op basis van huidige listing-info.
- Voor eindkeuze telt gemeten gedrag onder belasting zwaarder dan reviewscore.

| Kandidaat | Type | Indicatieve specs | Indicatieve prijs | Toepassing in plan | Eerste oordeel |
|---|---|---|---|---|---|
| Ali-link 1005007709104686 | Buck-boost | 3-15V in, 1-15V uit, 700mA / 5W | ~€0,20-€0,63 | Universeel pad (1S/2S) naar 5V | Zeer goedkoop, maar beperkte stroomreserve. |
| Ali-link 1005007708991209 | Buck-boost | 3-15V in, 1-15V uit, 700mA / 5W | ~€0,63 | Universeel pad (1S/2S) naar 5V | Bruikbaar voor lichte belasting/proto. |
| Ali-link 1005009401493195 | Buck-boost | 3-15V in, 1-15V uit, 700mA / 5W | ~€1,35 | Universeel pad (1S/2S) naar 5V | Iets duurder, zelfde klasse als boven. |
| Ali-link 1005009806778521 | Boost (MT3608) | 2-24V in, step-up, "2A max" (praktisch lager) | ~€0,88/stuk (2pcs deal) | 1S -> 5V pad | Goede budgetkeuze voor 1S-route. |
| Ali-link 1005011717825517 | Buck (LM2596) | 3-40V in, 1.5-35V uit (instelbaar) | ~€1,20 | 2S -> 5V pad | Bewezen en robuust voor 2S-route. |
| Ali-link 1005008119669317 | Buck (DD4012SA) | 5-40V in, vaste uitgangen, 1A | ~€0,44 | 2S -> 5V pad | Erg goedkoop, alleen voor 2S/buck-pad. |

### Aanbevolen shortlist voor deze PCB

1. 1S-pad: MT3608 (boost) kandidaat.
2. 2S-pad: LM2596 of DD4012SA (buck) kandidaat.
3. Buck-boost 700mA modules alleen als fallback/prototype, niet als voorkeurs-EVT voor motorpieken.

### Inkoop- en bench-test matrix

Doel:
- Snel objectief vergelijken op stabiliteit, thermiek en piekgedrag bij ESP + motoromgeving.

Inkoopvoorstel:
1. Bestel per kandidaat 5 stuks.
2. Test minimaal 3 stuks per type (samplevariatie).

Testmatrix per kandidaat:
1. No-load spanning ingesteld op 5.00V.
2. 150mA continue belasting (ESP+radio profiel).
3. 300-500mA piekbelasting (boot + randapparatuur piek).
4. 1S test bij 4.2V, 3.7V en 3.3V accuniveau.
5. 2S test bij 8.4V, 7.4V en 6.6V accuniveau.
6. Temperatuurmeting na 10 minuten op nominale belasting.
7. Ripple-check en dip-check op 5V-uitgang bij load steps.

Go/No-Go criteria per module:
1. Uitgang blijft binnen 5.0V +/- 5% onder alle geldige testcondities.
2. Geen reset/brownout van ESP in boottest.
3. Componenttemperatuur blijft binnen veilige marge (praktisch richtpunt: geen thermische runaway / niet structureel >85C op hotspot zonder koeling).
4. Geen zichtbaar instabiel gedrag (oscillatie, hoorbare coil-whine met spanningsdip, random uitval).

## Issue-lijst

| ID | Onderwerp | Probleem (Wallie) | Voorgestelde oplossing | Status | Actiehouder | Opmerking |
|---|---|---|---|---|---|---|
| W-01 | Driver `SLEEP` net | `SLEEP` hing aan `GND`; daardoor geen gecontroleerde enable na boot en risico op wielen die bewegen bij opstarten. | Verbind `SLEEP` met een ESP GPIO die tijdens boot laag blijft; zet na boot hoog in firmware. | Opgelost in v3-fix | Hardware + Firmware | `SLEEP` is gekoppeld aan `GPIO20_SPARE`; firmware moet de pin na init hoog zetten. |
| W-02 | 1S compatibiliteit VREG | Huidige VREG-opzet werkt niet met 1S accu (praktisch getest). | Herontwerp voedingspad voor 1S/2S ondersteuning. | In uitvoering in v3-fix | Hardware | 5V-route voorbereid; definitieve 1S/2S validatie met gekozen module nog nodig. |
| W-03 | Bypass zonder VREG | Direct overbruggen van VIN->VOUT zonder VREG kan 4.2V op ESP 3.3V pin zetten. | Niet toestaan in huidige vorm; alternatief voedingsschema verplicht. | Deels opgelost in v3-fix | Hardware | JP1 directe VBAT->ESP bypass verwijderd; DRC/testbevestiging nog uitvoeren. |
| W-04 | Alternatief A (5V route) | Eenvoudige route voor 1S/2S ontbreekt. | Gebruik 5V-regelaar en voed de ESP via 5V pin; bypass VIN->VOUT wordt dan bruikbaar voor 1S. | In uitvoering in v3-fix | Hardware | Netten omgezet naar `REG_5V_OUT` en `ESP_5V_IN`; modulekeuze + benchvalidatie nog open. |
| W-05 | Alternatief B (gescheiden aansluitingen) | Geen duidelijke scheiding tussen 1S en 2S accuaansluiting. | Breng 5V-pin apart naar voren voor aparte 1S/2S aansluiting. | Te beoordelen | Hardware | Kan combinatie met jumpers/solder-bridge krijgen. |
| W-06 | Schakelaar-eilanden afstand | Eilanden staan te ver uit elkaar voor pinheader+jumper als eenvoudige schakelaar. | Eilanden dichter bij elkaar plaatsen (header/jumper-proof). | Opgelost in v3-fix | Hardware | Pitch gezet op 2.54 mm voor standaard jumper/header gebruik. |
| W-07 | Mechanische optimalisatie | Orientatie motoraansluitingen en plaatsing VREG konden beter. | Routing/plaatsing optimaliseren op assemblage en kabelvoering. | Opgelost in v3-fix | Hardware | Geoptimaliseerd in v3-fix; resterende fine-tuning volgt handmatig later. |

## Besluiten die nog nodig zijn

1. Firmwaregedrag voor `SLEEP`: pin standaard laag houden tijdens boot en pas na init hoog zetten.
2. Definitieve VREG-modulekeuze op basis van benchtestresultaten (1S/2S, thermiek, rimpel, brownout).

## Geimplementeerd in v3-fix (1S/2S traject)

1. ESP-voedingsinvoer hernoemd naar `ESP_5V_IN` (voor voeding via ESP 5V-pin).
2. Externe regelaar-uitgang hernoemd naar `REG_5V_OUT`.
3. JP1 gewijzigd naar veilige 5V-selectie:
  - pad1 = `REG_5V_OUT`
  - pad2 = `ESP_5V_IN`
  - pad3 = geen net (oude directe `VBAT_SW` bypass verwijderd)
4. U3-regelaarlabel aangepast naar 5V-route in schema (`5V_BUCK_BOOST`).
5. `5V_NC` labels in schema vervangen door `ESP_5V_IN`.

Open na implementatie:
1. DRC/ERC draaien op v3-fix na netwijzigingen.
2. Fysieke benchvalidatie met gekozen goedkope module(s) volgens testmatrix.
3. Eventueel W-05 later toevoegen als extra fysieke 1S/2S scheiding gewenst is.

## Besluiten vastgesteld

1. Voeding: `5V-regelaar` als hoofdroute.
2. 1S/2S selectie: `2-pin jumper/shunt`.
3. Validatievrijgave: alle drie verplicht
  - geen overvoltage op ESP-rail,
  - betrouwbare boot op 1S en 2S,
  - thermisch binnen marge bij piekbelasting.

## Aanbevolen validatie na aanpassingen

- Elektrisch:
  - Boottest met motoren aangesloten (geen onverwachte motorbeweging).
  - 1S en 2S voedingstest, inclusief spanningsmeting op ESP voedingspin.
  - Controle op correcte `SLEEP` timing na reset/power-cycle.
- Mechanisch:
  - Check montagegemak van motorconnector-orientatie.
  - Check bereikbaarheid van VREG en schakelaar/jumper.
- DRC/ERC:
  - Nieuwe DRC/ERC run en afwijkingen documenteren.

## Statushistorie

- 2026-08-22: Eerste vastlegging op basis van Wallieonline-notes.
- 2026-08-22: W-01 opgelost in `v3-fix` door DRV8833 `SLEEP` van `GND` naar `GPIO20_SPARE` te verplaatsen.
- 2026-08-22: W-06 opgelost in `v3-fix` door schakelaar-pitch naar 2.54 mm te zetten.
- 2026-08-22: W-07 als opgelost gemarkeerd in `v3-fix`; resterende punten worden later handmatig afgewerkt.
- 2026-08-22: 1S/2S planbesluiten vastgezet: 5V-regelaar hoofdroute, 2-pin jumper selectie, volledige 3-delige validatie verplicht.
- 2026-08-22: W-02/W-03/W-04 verder uitgewerkt in `v3-fix` met 5V-netmigratie en verwijderen van directe VBAT-bypass op JP1.
- Nog te doen: per item markeren wat al opgelost is in de huidige PCB-revisie.
