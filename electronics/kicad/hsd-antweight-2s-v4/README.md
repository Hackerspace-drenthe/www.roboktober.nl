# V4 print: module-based carrier board voor 1S LiPo default

Deze map bevat de v4-variant van de antweight-controllerprint. Het uitgangspunt is simpel:

- de PCB is de bron van waarheid
- de fysieke layout is gebaseerd op de bewezen v3-fix print
- de standaardopstelling is 1S LiPo
- de DRV8833 sleep/control pin zit op GPIO5
- 2S is alleen een expliciete optionele route, niet de default

## 1. Wat is de v4 in praktijk?

De v4 is geen volledig custom power-design. Het is een compacte carrier board voor bestaande modules:

- ESP32-C3 SuperMini
- DRV8833-breakout
- 1S LiPo charge/protection breakout
- motorconnectors
- testpoints en debug-headers

Het doel is om een praktische print te hebben die compact genoeg is voor een robot, zonder een ingewikkelde custom-voedingsarchitectuur te bouwen.

> Als een label, schema of document afwijkt van de PCB, dan geldt de PCB als de juiste referentie.

## 2. Standaardvoeding: 1S LiPo is de default

Voor deze robotbouw is de standaardconfiguratie:

- 1S LiPo batterij
- charge/protection breakout module
- output naar de PCB-power-rail
- ESP-logic en motor-power gescheiden houden

Dit betekent:

- de accu is een 1S-cel, ongeveer 3,7V tot 4,2V
- de ESP krijgt een veilige, gestabiliseerde 5V-rail via de module-route
- de motorstroom blijft apart van de ESP-logica
- de print is ontworpen voor de praktische standaardbouw, niet voor een complexe custom-power-variant

### 2.1 Waarom 1S als default?

- simpel
- compact
- veilig
- makkelijk te reproduceert
- het werkt het beste voor een kleine antweight-bouw met N20-motoren

### 2.2 Optionele 2S-route

Een 2S-configuratie kan als aparte route worden gebruikt, maar dat is bedoeld als alternatief en niet als standaardopstelling.

- 2S accu -> regulator -> ESP_5V_IN
- deze route moet expliciet worden gekozen en gecontroleerd
- meer vermogen, maar ook meer stroom, meer warmte en meer aandacht nodig

## 3. Belangrijkste elektrische afspraken

### DRV8833 sleep/control

GPIO5 is de sleep/control pin voor de DRV8833 in deze configuratie.

- niet zomaar vervangen door een willekeurige GPIO
- deze pin is onderdeel van de bedoeling van de print
- als je firmware of schema hier vanaf wijkt, dan is de PCB de bron van waarheid

### Power rails

Op deze print gelden deze afspraken:

- VBAT_RAW: ruwe batterijspanning
- VBAT_SW: geschakelde / gebruikelijke batterijrail
- ESP_5V_IN: 5V-ingang voor de ESP
- REG_5V_OUT: 5V-uitgang van een optionele regulator/route
- GND: massa

### Default path

De standaard route is:

- 1S LiPo charge/protection breakout -> VBAT_SW / ESP_5V_IN
- motor-power en ESP-power gescheiden houden
- 2S route aparte en optioneel

## 4. Eerste opstart: veilig en eenvoudig

### Stap 1: controleer de print

- controleer op beschadigde sporen of pads
- controleer de batterijconnector
- controleer de jumper/route voor je gekozen voeding

### Stap 2: kies je voeding

Gebruik deze eenvoudige regel:

- beginner / standaard: 1S-setup
- wedstrijd / speciale build: 2S optioneel via juiste regulator-route

Als je niet zeker bent, begin met de standaard 1S-opstelling.

### Stap 3: sluit de batterij aan

- zet de stroom uit voordat je iets aansluit
- sluit de batterij met juiste polariteit aan
- controleer GND en batterijpoles nogmaals

### Stap 4: sluit de motoren aan

- linker motor op linker connector
- rechter motor op rechter connector
- controleer of de pinning klopt

### Stap 5: zet de print aan

- controleer dat de ESP opstart
- controleer status en gedrag
- test zonder extra belasting eerst

## 5. Testen met USB-5V

Je kunt de ESP ook testen zonder de accu aan te sluiten door de print via USB-5V te voeden.

### Praktische regel

Gebruik altijd deze volgorde:

1. zet de batterij los
2. voer de print alleen via USB-5V
3. test de ESP en firmware
4. sluit daarna pas de accu weer aan voor een volledige batterijtest

### Belangrijk

Als USB-5V en accu tegelijk aanwezig zijn, kunnen beide voedingen op dezelfde print samenkomen. Dat is niet automatisch fataal, maar het kan leiden tot ongewenste backfeeding of onduidelijk gedrag.

Voor een veilige eerste test: batterij loskoppelen, dan USB-5V gebruiken, daarna pas opnieuw met accu testen.

## 6. Veilige bedradingsregels

- geen raw batterij direct op de 3.3V-rail van de ESP
- geen motor rechtstreeks op een ESP-pin
- alleen voeding op de daarvoor bestemde power-rails
- GND goed aansluiten tussen batterij, ESP en driver
- GPIO5 niet verwarren met een algemene vrije GPIO

## 7. Snelle technische samenvatting

- ESP-voeding: ESP_5V_IN
- standaard default: 1S LiPo
- optioneel alternatief: 2S via regulator route
- motorsturing: DRV8833
- sleep/control: GPIO5
- common ground: GND

## 8. Projectbestanden

- PCB: hsd-antweight-2s-v4.kicad_pcb
- Schema: hsd-antweight-2s-v4.kicad_sch
- Project: hsd-antweight-2s-v4.kicad_pro
- renders: renders/

## 9. Belangrijkste opmerking

Deze print is de werkelijke bron van waarheid. De v4 is een duidelijke, compacte carrier-board-variant op basis van de bewezen v3-fix layout, met 1S LiPo als standaard en GPIO5 als DRV8833 sleep/control line.

De huidige PCB is opnieuw gecontroleerd en is clean volgens de DRC: 0 violations, 0 unconnected items.
