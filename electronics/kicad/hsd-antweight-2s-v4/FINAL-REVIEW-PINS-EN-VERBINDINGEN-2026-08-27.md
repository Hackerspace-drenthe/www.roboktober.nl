# Final Review Rapport: Pins en Verbindingen (v4)

## Scope en besluit

Dit is de samengevoegde en verbeterde eindreview van pinplaatsing en verbindingen voor de v4 PCB.
Bron van waarheid is de daadwerkelijke PCB-layout (niet alleen de schema-netlist).

Eindconclusie: de PCB-plaatsing en de kritieke verbindingen zijn akkoord voor final review.

## Gebruikte bronnen

- PCB: electronics/kicad/hsd-antweight-2s-v4/hsd-antweight-2s-v4.kicad_pcb
- Netlist: electronics/kicad/hsd-antweight-2s-v4/production/pcbway/hsd-antweight-2s-v4.net
- Contracten (nu geharmoniseerd):
  - electronics/pin-definitions/esp32-c3-supermini-v2.md
  - electronics/pin-definitions/drv8833-module-v2.md

## Plaatsing (side-aware)

- U1 (ESP32-C3): F.Cu (bovenkant)
- U2 (DRV8833 breakout): B.Cu (onderkant)

Deze side-aware check is expliciet meegenomen: onderkant-montage geeft een gespiegeld aanzicht in top-view, daarom is gevalideerd op echte padnummers en netnamen op de PCB.

## Kritieke continuiteitschecks

Alle onderstaande checks zijn PASS op PCB-niveau:

- ESP -> DRV control: GPIO5, AIN2_GPIO6, AIN1_GPIO7, BIN1_GPIO9, BIN2_GPIO10
- Motoruitgangen: M1A/M1B naar J5, M2A/M2B naar J6
- Powerrails: VBAT_RAW, VBAT_SW, ESP_5V_IN, GND

## Gevalideerde pad-to-net mapping

### U1 (ESP32-C3, 2x8)

| Pad | Net |
| ---: | --- |
| 1 | GPIO5 |
| 2 | AIN2_GPIO6 |
| 3 | AIN1_GPIO7 |
| 4 | GPIO8_LED_EXPANSION |
| 5 | BIN1_GPIO9 |
| 6 | BIN2_GPIO10 |
| 7 | GPIO20_SPARE |
| 8 | GPIO21_SPARE |
| 9 | GPIO0_SPARE |
| 10 | GPIO1_SPARE |
| 11 | GPIO2_SPARE |
| 12 | GPIO3_SPARE |
| 13 | GPIO4_SPARE |
| 14 | 3V3 |
| 15 | GND |
| 16 | ESP_5V_IN |

### U2 (DRV8833 breakout, 2x6)

| Pad | Net |
| ---: | --- |
| 1 | AIN1_GPIO7 |
| 2 | (no net) |
| 3 | AIN2_GPIO6 |
| 4 | M2B |
| 5 | VBAT_SW |
| 6 | M2A |
| 7 | GND |
| 8 | M1B |
| 9 | BIN1_GPIO9 |
| 10 | M1A |
| 11 | BIN2_GPIO10 |
| 12 | GPIO5 |

## Header en power details

- J10:
  - pad 1 = ESP_5V_IN
  - pad 2 = GND
  - pad 3 = VBAT_SW
- JP1 (3-pin selector):
  - pad 1 = REG_5V_OUT
  - pad 2 = ESP_5V_IN
  - pad 3 = VBAT_SW

## Waarom eerdere afwijkingen ontstonden

De eerdere afwijkingen kwamen vooral door vergelijking met een verouderde contract-mapping en door netlist-only interpretatie zonder side-aware PCB-context.
De echte PCB-connectiviteit is nu leidend gevalideerd.

## Release verdict

- Plaatsing: akkoord
- Verbindingen: akkoord
- Pinvolgorde: akkoord volgens geactualiseerde contracten

Status: GO voor final review.
