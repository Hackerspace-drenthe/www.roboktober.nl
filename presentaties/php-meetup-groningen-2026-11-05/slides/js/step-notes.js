// Uses Reveal's native fragment notes, including highlight.js-generated fragments.
window.RoboStepNotes = {
  id: 'step-notes',
  init(Reveal) {
    const english = document.documentElement.lang === 'en';
    const text = pair => pair[english ? 1 : 0];
    const scripts = {
      3: {
        context: ['Introductie · circa 1 min', 'Introduction · about 1 min'],
        steps: [
          ['Ik ben Rein Velt, software engineer en maker. Dawn is mijn werkgever, niet de organisator van Roboktober.',
            'I am Rein Velt, software engineer and maker. Dawn is my employer, not the organiser of Roboktober.'],
          ['Van de Nederlandse PHP Gebruikersgroep naar Hackerspace Drenthe: ik breng graag mensen samen die iets willen maken.',
            'From the Dutch PHP User Group to Hackerspace Drenthe: I enjoy bringing people together to build things.'],
          ['In maart, april en mei werkte ik aan Mei MeshCore Maand en Drenthenoodnetwerk.nl. Roboktober kwam daarna.',
            'In March, April and May I worked on Mei MeshCore Maand and Drenthenoodnetwerk.nl. Roboktober came afterwards.'],
          ['Deze zomer kwamen website, printplaten, onderdelen, chassis en organisatie samen. Van de eerste commit in juli naar de kickoff in oktober; daar kom ik later op terug.',
            'This summer brought together the website, PCBs, parts, chassis and organisation. From the first commit in July to the kickoff in October; I will return to that later.'],
          ['Ik ben meer bouwer dan prater. Vandaag laat ik vooral zien wat we gebouwd hebben; daarna door naar het publiek.',
            'I am more of a builder than a talker. Today I will mainly show what we built; now over to the audience.'],
        ],
      },
      4: {
        context: ['Publiekspeiling · kort houden', 'Audience poll · keep it brief'],
        steps: [
          ['Wie heeft weleens hardware ontworpen? Handen omhoog. Onthoud de verhouding voor de uitleg over KiCad.',
            'Who has designed hardware before? Hands up. Remember the split for the KiCad explanation.'],
          ['Wie heeft een Arduino of ESP32 geprogrammeerd? Elektronicakennis is niet nodig; ik leg de begrippen uit vanuit software.',
            'Who has programmed an Arduino or ESP32? Electronics knowledge is not required; I will explain the concepts through software.'],
        ],
      },
      7: {
        context: ['Wallie · circa 2 min inclusief videofragment', 'Wallie · about 2 min including the video clip'],
        steps: [
          ['Stel Wallie kort voor. Hij brengt de robotkennis mee: 1e op de INDI Robot Games 2020, waar ook Master Milo meedeed, en 2e op Boeliebots 2026 in Groningen. Het videofragment is offline beschikbaar; controleer toestemming voor vertoning.',
            'Briefly introduce Wallie. He brings the robot expertise: 1st at INDI Robot Games 2020, where Master Milo also competed, and 2nd at Boeliebots 2026 in Groningen. The video clip is available offline; check permission to show it.'],
          ['Wallie komt in de hackerspace onderdelen maken met de frees en CNC. Zo kwamen onze werelden bij elkaar.',
            'Wallie visits the hackerspace to make parts with the mill and CNC equipment. That brought our worlds together.'],
          ['Daaruit ontstond het idee voor een gezamenlijk evenement: mensen enthousiast maken voor techniek en de hackerspace.',
            'That led to the idea of a joint event: getting people excited about technology and the hackerspace.'],
        ],
      },
      8: {
        context: ['Invalshoek · circa 20 s', 'Perspective · about 20 s'],
        steps: [
          ['De vorige uitleg keek vanuit de robotbouwer. Geen verschil in slimheid, maar een ander vertrekpunt.',
            'The earlier explanation took the robot builder’s perspective. This is a different starting point, not a difference in ability.'],
          ['Vandaag kijken we als developers: wat zit er tussen input en beweging, en welke softwarevaardigheden herkennen we?',
            'Today we look as developers: what sits between input and movement, and which software skills do we recognise?'],
          ['We volgen code, printplaten en schema’s als één keten. Geen losse techniekcollegeblokken.',
            'We follow code, PCBs and schematics as one chain, not separate technical lectures.'],
          ['Je hoeft geen elektronica te kennen. Vraag het als ik een stap oversla. Brug naar wat we samen gebouwd hebben.',
            'You do not need electronics knowledge. Ask if I skip a step. Bridge to what we built together.'],
        ],
      },
      13: {
        context: ['Robot open op tafel · 2 min totaal; onderdelen circa 1 min', 'Robot opened up · 2 min total; parts about 1 min'],
        steps: [
          ['Zet de echte robot uitgeschakeld open op tafel. Het chassis houdt alles op zijn plaats. Eerst de onderdelen, daarna volgen we één commando.',
            'Open the actual robot on the table with power off. The chassis holds everything in place. First the parts, then we follow one command.'],
          ['De accu levert de energie: 1S LiPo, 3,7 V nominaal en 4,2 V vol. Motorstroom komt uit de accu, niet uit de ESP32. Dit beeld is schematisch.',
            'The battery supplies energy: 1S LiPo, 3.7 V nominal and 4.2 V full. Motor current comes from the battery, not the ESP32. This image is schematic.'],
          ['De schakelaar of uitneembare plug onderbreekt de voeding fysiek, in serie met de accu, nooit over de accupolen. Kies een stroomgeschikte uitvoering en volg het reglement. Software stoppen is niet stroom uit; ontkoppel ook USB vóór werkzaamheden.',
            'The switch or removable link physically interrupts power, in series with the battery, never across its terminals. Use a current-rated component and follow event rules. Software stopping is not power off; disconnect USB before working on it too.'],
          ['De kale PCB verbindt onderdelen via koperbanen. De print voert geen code uit: houd PCB en ESP32 expliciet uit elkaar.',
            'The bare PCB connects parts through copper traces. The board does not execute code: explicitly distinguish PCB from ESP32.'],
          ['De ESP32-C3 ontvangt draadloze commando’s en rekent gas en sturen om naar motorcommando’s. Hier draait de firmware, niet op de kale print.',
            'The ESP32-C3 receives wireless commands and converts throttle and steering into motor commands. Firmware runs here, not on the bare board.'],
          ['De DRV8833 is de vermogensschakelaar tussen ESP en motoren. De ESP beslist; de driver schakelt de accuspanning. Geen motor rechtstreeks aan een GPIO.',
            'The DRV8833 is the power-switching stage between ESP and motors. The ESP decides; the driver switches battery voltage. Never connect a motor directly to a GPIO.'],
          ['Twee N20-motoren drijven de wielen aan. Verschil tussen links en rechts maakt sturen mogelijk. Brug: wat gebeurt er als ik op mijn telefoon vooruit duw?',
            'Two N20 motors drive the wheels. Differences between left and right make steering possible. Bridge: what happens when I push forward on my phone?'],
          ['Het commando komt binnen op de ESP32: gas en sturen. Dit is de signaalroute, nog niet de energieroute.',
            'The command reaches the ESP32: throttle and steering. This is the signal path, not yet the power path.'],
          ['De ESP rekent en maakt PWM. Vanaf nu is ons voorbeeld ná schalen: gas 200, sturen 0, beide motorcommando’s 200. Dat is circa 78% aan-tijd, niet gegarandeerd toerental.',
            'The ESP calculates and generates PWM. From here our example is after scaling: throttle 200, steering 0, both motor commands 200. That means about 78% on-time, not a guaranteed speed.'],
          ['Rood volgt de accu via aan/uit naar VM van de DRV8833. De accu levert de motorenergie; de ESP alleen stuursignalen.',
            'Red follows the battery through on/off to VM on the DRV8833. The battery supplies motor energy; the ESP only supplies control signals.'],
          ['De H-brug schakelt richting en aan-tijd. Blauw fel: GPIO6 en GPIO10 met PWM; GPIO7 en GPIO9 staan op 0. De banen zijn afkomstig uit KiCad; geen pinnen uit het hoofd leren.',
            'The H-bridge switches direction and on-time. Bright blue: GPIO6 and GPIO10 carry PWM; GPIO7 and GPIO9 are at 0. Traces come from KiCad; no need to memorise pins.'],
          ['Geel gaat van driveruitgangen naar motorconnectoren. Beide kanten hetzelfde commando: ongeveer rechtdoor. Geen verse berichten gedurende 500 ms? Dan moeten de motoren stoppen; dat bespreken we straks.',
            'Yellow runs from driver outputs to motor connectors. Equal commands on both sides: roughly straight ahead. No fresh messages for 500 ms? The motors must stop; we will cover that shortly.'],
        ],
      },
      17: {
        context: ['ESP-NOW · laatste 2 min van het controllerblok', 'ESP-NOW · final 2 min of the controller section'],
        steps: [
          ['Vanaf hier de bestaande openbare ESP-NOW-broncode, niet de nieuwe WebUI-release. Acht uint16_t-kanalen vormen samen 16 bytes. De struct is voor de slide op één regel gezet. Geef de remote rond.',
            'From here we use the existing public ESP-NOW source, not the new WebUI release. Eight uint16_t channels make 16 bytes. The struct is condensed onto one line for the slide. Pass the remote around.'],
          ['De zender vult gas en sturen in en verstuurt de struct elke 20 ms. Het fragment is ingekort; de kanaalcommentaren zijn onze uitleg.',
            'The transmitter fills throttle and steering and sends the struct every 20 ms. This excerpt is abridged; the channel comments are our explanation.'],
          ['De ontvanger controleert de berichtlengte en kopieert de bytes met memcpy naar dezelfde struct. Kanaalcontrole en schalen volgen in het firmwareblok.',
            'The receiver checks message length and copies bytes with memcpy into the same struct. Channel checks and scaling follow in the firmware section.'],
          ['Zoals pack/unpack: beide kanten moeten exact dezelfde indeling gebruiken. Een lengtecheck alleen is geen volledige inhoudsvalidatie.',
            'Like pack/unpack: both sides must use exactly the same layout. A length check alone is not full content validation.'],
        ],
      },
      19: {
        context: ['Firmware · flashen kort uitleggen', 'Firmware · briefly explain flashing'],
        steps: [
          ['De webflasher gebruikt Web Serial: browser → USB → ESP32. Geen lokale toolchain nodig, wel een ondersteunde Chromium-browser.',
            'The web flasher uses Web Serial: browser → USB → ESP32. No local toolchain needed, but a supported Chromium browser is required.'],
          ['Praktische valkuilen: een kabel die alleen laadt en een ontbrekende driver. Gebruik een datakabel en test vooraf.',
            'Practical pitfalls: a charge-only cable and a missing driver. Use a data cable and test beforehand.'],
          ['De software staat nu op de chip. Brug naar de volledige code: wat doet die met een ontvangen commando?',
            'The software is now on the chip. Bridge to the complete source: what does it do with an incoming command?'],
        ],
      },
      21: {
        context: ['Schalen en deadband · circa 1 min', 'Scaling and deadband · about 1 min'],
        steps: [
          ['De if controleert of het RC-kanaal binnen de ingestelde grenzen valt. Rond 1500 is joystick-neutraal, niet een motorcommando.',
            'The if checks whether the RC channel is within configured limits. Around 1500 is joystick neutral, not a motor command.'],
          ['map zet de kanaalschaal om naar −255…255; constrain begrenst het resultaat. Vanaf hier gebruiken we gas 200, sturen 0 ná schalen.',
            'map converts the channel scale to −255…255; constrain limits the result. From here we use throttle 200, steering 0 after scaling.'],
          ['De deadband maakt −40…40 hard nul: ruis en speling mogen geen kruipende robot opleveren. Buiten-bereikwaarden houden in deze bron hun vorige waarde.',
            'The deadband makes −40…40 exactly zero: noise and play must not make the robot creep. Out-of-range values retain their previous value in this source.'],
        ],
      },
      22: {
        context: ['Mixing · circa 2 min; wielanimatie is illustratief', 'Mixing · about 2 min; wheel animation is illustrative'],
        steps: [
          ['Met de mixer aan: links is gas plus sturen, rechts gas min sturen. Resultaten worden begrensd op −255…255. L is motor A, R motor B; de print is van onderen gezien gespiegeld.',
            'With mixing on: left is throttle plus steering, right is throttle minus steering. Results are limited to −255…255. L is motor A, R motor B; the board is mirrored because we view it from below.'],
          ['Gas 200, sturen 0 geeft links en rechts 200: ongeveer rechtdoor. Dit voorspelt niet het werkelijke toerental.',
            'Throttle 200, steering 0 gives 200 on both sides: roughly straight ahead. This does not predict actual wheel speed.'],
          ['Gas 200, sturen 100 geeft eerst 300 en 100. Links wordt begrensd op 255; het verschil maakt een bocht.',
            'Throttle 200, steering 100 first gives 300 and 100. Left is limited to 255; the difference makes a turn.'],
          ['Gas 0, sturen 150 geeft 150 en −150: tegengestelde richtingen, draaien op de plek. Negatief betekent dat de andere driveringang PWM krijgt.',
            'Throttle 0, steering 150 gives 150 and −150: opposite directions, turning on the spot. Negative means the other driver input receives PWM.'],
          ['De invert-instellingen keren per motor de richting om: instelling veranderen in plaats van opnieuw solderen. Opgeslagen in EEPROM. Mixer uit is tank-modus: één ingang per motor.',
            'Inversion settings reverse each motor: change a setting instead of resoldering. Stored in EEPROM. Mixing off means tank mode: one input per motor.'],
        ],
      },
      23: {
        context: ['PWM · aan-tijd, niet toerental', 'PWM · on-time, not speed'],
        steps: [
          ['PWM is snel aan/uit schakelen, zoals een dimmer. Bij positieve aansturing staat de ene driveringang laag en krijgt de andere PWM.',
            'PWM switches rapidly on/off, like a dimmer. For a positive command one driver input is low and the other receives PWM.'],
          ['Bij een negatief commando wisselen de ingangen: dezelfde grootte, andere stroomrichting en dus achteruit.',
            'For a negative command the inputs swap: the same magnitude, opposite current direction, so reverse.'],
          ['Bij nul staan beide ingangen laag: geen aandrijving. ledcWrite is een Arduino-ESP32-corefunctie. In deze core-2.x-bron gebruikt die kanalen; 200/255 is circa 78% aan-tijd, niet 78% snelheid.',
            'At zero both inputs are low: no drive. ledcWrite is an Arduino-ESP32 core function. In this core-2.x source it uses channels; 200/255 is about 78% on-time, not 78% speed.'],
        ],
      },
      25: {
        context: ['Failsafe · laatste 2 min firmwareblok; wielen in de lucht', 'Failsafe · final 2 min of firmware section; wheels off the ground'],
        steps: [
          ['millis minus lastCommandTime meet hoelang er geen bericht is ontvangen. Meer dan 500 ms zonder vers commando activeert de timeout.',
            'millis minus lastCommandTime measures time since the last message. More than 500 ms without a fresh command activates the timeout.'],
          ['Zet gas en sturen beide op nul. Geen input is een expliciete toestand, niet het eindeloos herhalen van de laatste snelheid.',
            'Set both throttle and steering to zero. No input is an explicit state, not permission to repeat the last speed forever.'],
          ['updateMotors voert de nulwaarden uit: de motoren krijgen een stopcommando. De software blijft doorlopen.',
            'updateMotors applies the zero values: the motors get a stop command. The software keeps running.'],
          ['In onze v2 gaat ook nSLEEP laag, zodat de motordriver wordt uitgeschakeld. Dit is aanvullend op het voorbeeld, en nog steeds geen fysieke accuschakelaar.',
            'Our v2 also pulls nSLEEP low to disable the motor driver. This is additional to the example, and still not a physical battery disconnect.'],
          ['Enige failsafe-demo: met wielen in de lucht de remote uitzetten, of telefoon-WiFi uit bij WebUI. Robot stopt en led knippert. Daarna de verbinding herstellen.',
            'The only failsafe demo: with wheels off the ground, turn off the remote, or phone WiFi for WebUI. The robot stops and the LED blinks. Restore the connection afterwards.'],
        ],
      },
      27: {
        context: ['PCB-componenten en voeding · circa 1 min', 'PCB components and power · about 1 min'],
        steps: [
          ['De print verbindt de onderdelen. Dit is de onderkant: links en rechts zijn gespiegeld ten opzichte van bovenaanzicht.',
            'The PCB connects the parts. This is the underside: left and right are mirrored compared with a top view.'],
          ['De ESP32-C3 SuperMini doet WiFi/ESP-NOW en de firmwareberekeningen. Stuursignalen, geen motorstroom.',
            'The ESP32-C3 SuperMini handles WiFi/ESP-NOW and firmware calculations. Control signals, not motor current.'],
          ['De DRV8833 bevat twee H-bruggen voor twee motoren en schakelt de voeding volgens de ESP-signalen.',
            'The DRV8833 contains two H-bridges for two motors and switches power according to the ESP signals.'],
          ['Twee N20-gearmotoren zetten stroom om in beweging. Het label 6 V is de motoraanduiding, geen reden om de ESP op die spanning aan te sluiten.',
            'Two N20 gearmotors turn current into motion. The 6 V label is the motor rating, not a reason to power the ESP at that voltage.'],
          ['Volg rood: accu → aan/uit → DRV8833. Bij een optionele 2S-opstelling krijgt ESP_5V_IN voeding via de 5V-regelaar, nooit rechtstreeks 2S.',
            'Follow red: battery → on/off → DRV8833. With an optional 2S setup, ESP_5V_IN is supplied through the 5V regulator, never directly from 2S.'],
          ['Nooit VIN naar 3.3V overbruggen: dan krijgt de ESP32 accuspanning op de verkeerde aansluiting. Controleer het schema en de voedingsvariant vóór inschakelen.',
            'Never bridge VIN to 3.3V: that puts battery voltage on the wrong ESP32 connection. Check the schematic and power variant before switching on.'],
        ],
      },
      28: {
        context: ['Pincontract · circa 1 min; geen registerles', 'Pin contract · about 1 min; not a register lesson'],
        steps: [
          ['Dit is de afspraak tussen firmware en print. De naam in de code moet naar hetzelfde net en dezelfde pin verwijzen; je hoeft de nummers niet te onthouden.',
            'This is the contract between firmware and PCB. A code name must refer to the same net and pin; no need to memorise the numbers.'],
          ['GPIO5 bestuurt nSLEEP via STBY_OUT_PIN. Dit maakt de driver vrij of zet hem in standby.',
            'GPIO5 controls nSLEEP through STBY_OUT_PIN. This enables the driver or puts it in standby.'],
          ['GPIO6/7 zijn de twee stuuringangen voor motor links. De koperbaan licht op. Bij tijdgebrek is dit ene voorbeeld voldoende.',
            'GPIO6/7 are the two control inputs for the left motor. The copper trace lights up. If short on time, this one example is enough.'],
          ['GPIO9/10 doen hetzelfde voor motor rechts. Verwisselen verandert de draairichting; daarom hebben we invert-instellingen.',
            'GPIO9/10 do the same for the right motor. Swapping them changes direction; that is why we have inversion settings.'],
          ['GPIO8 stuurt de failsafe-led aan. Schema en firmware moeten dezelfde afspraak volgen, ook voor een eenvoudige indicator.',
            'GPIO8 controls the failsafe LED. Schematic and firmware must follow the same contract, even for a simple indicator.'],
        ],
      },
      30: {
        context: ['Validatie · circa 1 min; overlays zijn didactisch, geen metingen', 'Validation · about 1 min; overlays illustrate checks, not measurements'],
        steps: [
          ['Geautomatiseerde controles lijken op CI, maar bewijzen niet dat de robot werkt. ERC controleert het schema; DRC de ontwerpregels op de print.',
            'Automated checks resemble CI, but do not prove that the robot works. ERC checks the schematic; DRC checks PCB design rules.'],
          ['Plan: schrijf eerst het pincontract op in Markdown. Welke pin hoort bij welk net? Daarmee kun je de uitkomst controleren.',
            'Plan: first write the pin contract in Markdown. Which pin belongs to which net? That gives you something to check the result against.'],
          ['Do: AI en ik passen schema en layout aan in KiCad. Een wijziging is pas klaar na controle, niet na generatie.',
            'Do: AI and I update schematic and layout in KiCad. A change is complete after verification, not after generation.'],
          ['Check koperafstand: blijven verschillende netten ver genoeg uit elkaar? De overlay is uitleg, geen gemeten KiCad-afstand.',
            'Check copper clearance: are different nets far enough apart? The overlay explains the idea; it is not a measured KiCad clearance.'],
          ['Check baanbreedte: past de breedte bij de ontwerpregels en verwachte stroom? De oplichtende route is een bestaande GPIO6-verbinding.',
            'Check trace width: does it meet design rules and expected current? The highlighted route is an existing GPIO6 connection.'],
          ['Check verbindingen: geen vergeten, niet-verbonden pads. Een visueel doorlopende baan alleen is geen elektrisch bewijs.',
            'Check connectivity: no forgotten, unconnected pads. A visually continuous trace alone is not electrical proof.'],
          ['Act: fouten herstellen en een nieuwe revisie maken. De getoonde DRC heeft nul overtredingen en nul unconnected pads. De drie ERC-meldingen rond J4_ALT/REG_5V_OUT moet je apart uitleggen of oplossen.',
            'Act: fix errors and make a new revision. The displayed DRC has zero violations and zero unconnected pads. The three ERC messages around J4_ALT/REG_5V_OUT must be explained or resolved separately.'],
          ['Menselijke review: vergelijk met het pincontract, laat Wallie meekijken en test fysiek. Een AI-review is geen deterministische test.',
            'Human review: compare against the pin contract, ask Wallie to review, and test physically. An AI review is not a deterministic test.'],
          ['25 van de 30 gemonteerde printplaten werken; 150 kale printen geproduceerd. Even laten landen. Uitval door antenne/WiFi-problemen en montagefouten, geen ontwerpfout gevonden. Advies van Rein: Super Mini Plus V2 met externe antenne, dezelfde footprint, iets duurder. Nog geen gemeten verbetering: variant/pinout en antenneruimte controleren en bereik in de robot testen.',
            '25 of 30 assembled PCBs work; 150 bare boards produced. Let that land. Failures from antenna/WiFi issues and assembly errors; no design fault found. Rein recommends Super Mini Plus V2 with external antenna, same footprint, slightly more expensive. No measured improvement yet: check variant/pinout and antenna space, then test range in the robot.'],
        ],
      },
      32: {
        context: ['3D-ontwerp en OpenSCAD · publiekspeiling · circa 20 s', '3D design and OpenSCAD · audience poll · about 20 s'],
        steps: [
          ['Wie heeft al eens iets in 3D ontworpen? Laat handen opsteken, ongeacht het programma. Houd het kort; deze peiling valt binnen de tijd voor het chassisblok.',
            'Who has designed something in 3D before? Invite a show of hands, regardless of the tool. Keep it brief; this poll is part of the chassis section budget.'],
          ['Wie kent OpenSCAD? Laat opnieuw handen opsteken. Maak onderscheid tussen het programma kennen en er zelf mee gewerkt hebben.',
            'Who knows OpenSCAD? Invite another show of hands. Distinguish knowing the tool from having used it yourself.'],
          ['Wie heeft er al iets mee ontworpen? Gebruik de reacties om het niveau te kiezen, zonder uitgebreide verhalen. Brug: een 3D-model kan ook code zijn.',
            'Who has designed something with it? Use the responses to pitch the explanation, without lengthy stories. Bridge: a 3D model can be code too.'],
        ],
      },
      33: {
        context: ['Waarom OpenSCAD · kort; geen wedstrijd tussen CAD-tools', 'Why OpenSCAD · brief; not a contest between CAD tools'],
        steps: [
          ['Voor mijn workflow is het chassis een tekstmodel. Programmeerkennis helpt, ook zonder eerdere CAD-ervaring.',
            'For my workflow the chassis is a text model. Programming knowledge helps, even without previous CAD experience.'],
          ['Een git diff laat precies zien welke maat veranderde. Hier groeide de speling van 0,2 naar 0,3 mm omdat de PCB te strak paste.',
            'A git diff shows exactly which dimension changed. Here clearance grew from 0.2 to 0.3 mm because the PCB fit was too tight.'],
          ['Geef een maat een naam en hergebruik die. Eén aanpassing werkt door in alle daarvan afgeleide vormen.',
            'Name and reuse a dimension. One change propagates through all shapes derived from it.'],
          ['Asserts bewaken alleen de voorwaarden die je opschrijft. Een groen model vervangt geen testprint of meting.',
            'Asserts guard only conditions you explicitly write. A passing model does not replace a test print or measurement.'],
          ['AI kan tekstmodellen helpen lezen en schrijven. De circa 9.700 regels zijn geen kwaliteitsbewijs: jij blijft verantwoordelijk voor maten en testen.',
            'AI can help read and write text models. About 9,700 lines are not proof of quality: you remain responsible for dimensions and testing.'],
        ],
      },
      34: {
        context: ['OpenSCAD-code · circa 2 min; ontwerpen volgen op de volgende dia', 'OpenSCAD code · about 2 min; designs follow on the next slide'],
        steps: [
          ['Eerst leren we de code lezen, zonder robotontwerpen ernaast. Twee stappen met vereenvoudigde, uitvoerbare voorbeelden en hun exacte resultaat. Een module levert geometrie, een functie een waarde. Wij werken in millimeters; parameters zijn definities.',
            'First we learn to read the code, without robot designs beside it. Two steps with simplified, runnable examples and their exact output. A module produces geometry, a function returns a value. We use millimetres; parameters are definitions.'],
          ['Links: geef PCB-maten en speling een naam en leid de plaatmaat daarvan af. cube maakt een blok; [x,y,z] geeft breedte, lengte en hoogte. Rechts: union voegt plaat en cilinder samen; translate verplaatst de vorm. difference trekt de tweede cilinder af en maakt zo een doorgaand gat. De beelden tonen precies deze scripts, geen robotontwerpen.',
            'Left: name PCB dimensions and clearance, then derive plate dimensions. cube creates a block; [x,y,z] gives width, length and height. Right: union combines plate and cylinder; translate moves a shape. difference subtracts the second cylinder to create a through-hole. The images show these exact scripts, not robot designs.'],
          ['Links: een module maakt één busje met gat; de lus plaatst vier exemplaren. Rechts: import leest het meegeleverde DXF-profiel, linear_extrude geeft het dikte. STL/3MF zijn 3D-meshes; DXF/SVG zijn 2D-profielen. use laadt modules uit een SCAD-bestand, zonder toplevel-geometrie; include neemt die ook mee. De losse assert onderaan is een voorbeeld van een eiscontrole nadat wand is gedefinieerd; hij bewijst geen pasvorm. Volledige scripts en DXF zijn aanklikbaar en offline meegeleverd. Nu pas naar de robotontwerpen.',
            'Left: a module creates one standoff with a hole; the loop places four copies. Right: import reads the included DXF profile, linear_extrude adds thickness. STL/3MF are 3D meshes; DXF/SVG are 2D profiles. use loads modules from a SCAD file without top-level geometry; include brings that in too. The separate assert below illustrates a requirement check after wall is defined; it does not prove fit. Complete scripts and DXF are linked and included offline. Now move on to robot designs.'],
        ],
      },
      39: {
        context: ['Publiek stuurt · 4 min; begrensd rijvlak, geen actief wapen', 'Audience driving · 4 min; bounded area, no active weapon'],
        steps: [
          ['Twee robots, één bestuurder per robot. Verbindingskaartjes met de echte SSID en het wachtwoord liggen klaar; vooraf testen. De failsafe-demo is al geweest, hier alleen rijden.',
            'Two robots, one driver per robot. Have cards with the real SSID and password ready; test beforehand. The failsafe demo has already happened; here we just drive.'],
          ['Wissel alleen van bestuurder als de robot stilstaat. Laat het publiek sturen en pas de uitleg toe; bewaak het rijvlak en de tijd.',
            'Change drivers only while the robot is stationary. Let the audience drive and apply the explanation; watch the driving area and time.'],
        ],
      },
      40: {
        context: ['Website als distributiekanaal · 2 min, geen tweede architectuurtalk', 'Website as distribution channel · 2 min, not a second architecture talk'],
        steps: [
          ['Deelnemers hebben één plek nodig voor onderdelen, firmware en bouwstappen. Daarom deze website; Laravel en Vue zijn het middel, niet het hoofdonderwerp.',
            'Participants need one place for parts, firmware and build instructions. That is why this website exists; Laravel and Vue are the means, not the main topic.'],
          ['De repository bundelt website en bouwbronnen: KiCad, OpenSCAD, pincontracten, besluiten en onderdelenlijst. Firmwarevoorbeelden blijven de bestaande ESP-NOW-code. Wallies nieuwe firmware is aangekondigd voor 5 november op zijn GitHub; controleer de release.',
            'The repository brings together website and build sources: KiCad, OpenSCAD, pin contracts, decisions and parts list. Firmware examples remain the existing ESP-NOW code. Wallie announced new firmware for 5 November on his GitHub; check the release.'],
        ],
      },
      45: {
        context: ['Afsluiting · circa 1 min', 'Closing · about 1 min'],
        steps: [
          ['We volgden één commando van joystick tot wiel. De hele keten werkt met principes die je als developer al kent: open software en standaarden als basis; toetsbare eisen als contract; code als gereedschap, ook voor PCB en chassis; tests als bewijs, digitaal én fysiek. AI helpt bij elke stap, maar jij bepaalt, controleert en valideert. AI-output is geen bewijs. Open als basis betekent niet dat alles open is: ESP-NOW is een leveranciersprotocol.',
            'We followed one command from joystick to wheel. The entire chain uses principles you already know as a developer: open software and standards as foundations; testable requirements as contracts; code as a tool, including PCB and chassis design; tests as proof, digitally and physically. AI assists at every step, but you define, check and validate. AI output is not proof. Open foundations does not mean everything is open: ESP-NOW is a vendor protocol.'],
          ['Als je kunt programmeren, kun je ook robots bouwen. Dezelfde principes. Nu beweegt het. Laat de kernboodschap even landen.',
            'If you can program, you can build robots too. The same principles. Now it moves. Let the core message land.'],
          ['Je blijft developer — alleen rijdt je bug nu weg. Korte afsluiter, dan door naar zelf beginnen en vragen.',
            'You are still a developer — only now your bug drives away. A short closing joke, then how to get started and questions.'],
        ],
      },
    };

    Reveal.on('ready', () => {
      const slides = Reveal.getSlides();
      const firmware = slides.find(slide => slide.querySelector('.firmware-full'));
      const gallery = slides.find(slide => slide.querySelector('.scad-stage'));
      const clean = element => element.textContent.replace(/\s+/g, ' ').trim();
      scripts[slides.indexOf(firmware) + 1] = {
        context: ['Volledige firmware · circa 2 min; oorspronkelijke ESP-NOW-bron', 'Complete firmware · about 2 min; original ESP-NOW source'],
        steps: [...firmware.querySelectorAll('.firmware-explanation')].map((panel, index) => {
          let speech = [...panel.children].map(clean).join('\n\n');
          if (index === 0) speech += text([
            ' Alle 205 regels zijn lokaal beschikbaar. Volg de uitvoeringsroute, niet de bestandsvolgorde: setup één keer, ontvangst via callback, loop steeds opnieuw.',
            ' All 205 lines are available locally. Follow execution, not file order: setup once, receipt through a callback, loop repeatedly.',
          ]);
          if (index === 7) speech += text([
            ' ledcWrite staat in de Arduino-ESP32-core, esp32-hal-ledc.h/.c. Deze bron gebruikt core-2.x-kanalen; core 3.x gebruikt ledcWriteChannel voor kanalen en ledcWrite voor pinnen.',
            ' ledcWrite is in the Arduino-ESP32 core, esp32-hal-ledc.h/.c. This source uses core-2.x channels; core 3.x uses ledcWriteChannel for channels and ledcWrite for pins.',
          ]);
          return [speech, speech];
        }),
      };
      scripts[slides.indexOf(gallery) + 1] = {
        context: ['Ontwerpgalerij · richttijd 1 min; maximaal 2 min bij vragen', 'Design gallery · target 1 min; up to 2 min for questions'],
        steps: [
          [
            'De code hebben we gezien. Nu alleen de robotontwerpen vergelijken: negen families, geen lineaire versiegeschiedenis; modellen niet op gelijke schaal. Houd het overzicht kort. Rein bevestigt: Disc Spinner v0.5 (‘Lief Monster’) werd 3e, Limulus v0.1 werd 4e. Passive Aggressive presteerde goed; de opmerking over bestuurders is een knipoog. Mostly Harmless is mijn favoriet. Actieve wapens waren niet toegestaan; de ontwerpnaam Disc Spinner zegt niets over de wedstrijdconfiguratie.',
            'We have covered the code. Now compare only robot designs: nine families, not one linear version history; models are not to the same scale. Keep the overview brief. Rein confirms: Disc Spinner v0.5 (‘Lief Monster’) placed 3rd, Limulus v0.1 placed 4th. Passive Aggressive performed well; the comment about drivers is tongue-in-cheek. Mostly Harmless is my favourite. Active weapons were not allowed; the Disc Spinner design name does not establish its competition configuration.',
          ],
          ...[...gallery.querySelectorAll('.scad-panel')].map(panel => {
            let speech = [
              clean(panel.querySelector('h2')),
              clean(panel.querySelector('.scad-group-description')),
              ...[...panel.querySelectorAll('.scad-design-grid img')].map(image => `${image.alt}: ${image.title}`),
            ].join('\n\n');
            if (clean(panel.querySelector('h2')) === 'Mostly Harmless') speech += text([
              '\n\nDit is mijn favoriet. De complete robot weegt 140 gram, niet alleen het chassis. De wedge kantelt tegenstanders; de achterkant gebruik ik om te duwen. Mijn ervaring: hij nam het zelfs op tegen Wallies bewapende robot met cirkelzaag en kwam er zonder schade uit. Dat is één ervaring, geen algemene garantie op sterkte of veiligheid. Dit verandert niets aan het verbod op actieve wapens bij Roboktober 2026.',
              '\n\nThis is my favourite. The complete robot weighs 140 grams, not just the chassis. The wedge flips opponents; I use the rear to push. My experience: it even faced Wallie’s armed robot with a circular saw and came away without damage. That is one experience, not a general guarantee of strength or safety. It does not change the ban on active weapons at Roboktober 2026.',
            ]);
            return [speech, speech];
          }),
        ],
      };

      slides.forEach((slide, index) => {
        const fragments = [...slide.querySelectorAll('.fragment')];
        if (!fragments.length) return;
        const groups = [...new Set(fragments.map(fragment => Number(fragment.dataset.fragmentIndex)))].sort((a, b) => a - b);
        const script = scripts[index + 1];
        if (!script || groups.some(group => !Number.isInteger(group)) || script.steps.length !== groups.length + 1) {
          throw new Error(`Speaker notes do not match click steps on slide ${index + 1}`);
        }
        const context = text(script.context);
        const note = step => `${context}\n${step === 0 ? text(['Start', 'Start']) : text(['Klik ', 'Click ']) + step} / ${groups.length}\n\n${text(script.steps[step])}`;
        const aside = slide.querySelector('aside.notes');
        if (!aside) throw new Error(`Missing speaker notes on slide ${index + 1}`);
        aside.replaceChildren();
        for (const paragraph of note(0).split('\n\n')) {
          const p = document.createElement('p');
          p.textContent = paragraph;
          aside.append(p);
        }
        fragments.forEach(fragment => {
          const step = groups.indexOf(Number(fragment.dataset.fragmentIndex)) + 1;
          fragment.dataset.notes = note(step);
        });
      });
    });
  },
};
