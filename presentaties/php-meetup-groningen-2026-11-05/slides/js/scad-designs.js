const SCAD_DESIGNS = [
  ['Wallie · origineel chassis', 'Wallieonline+Mini+Robot+chassis/wallieonline-mini-robot.STL', 'Het oorspronkelijke chassis als STL-model.'],
  ['Wallie · originele topplaat', 'Wallieonline+Mini+Robot+chassis/wallieonline-mini-robot-top.STL', 'De bijbehorende topplaat als afzonderlijk onderdeel.'],
  ['Wallie · parametrische variant', 'Wallieonline+Mini+Robot+chassis/wallieonline-mini-robot-parametric.scad', 'Dezelfde mesh, nu met instelbare buitenmaten; geen nieuw chassisontwerp.'],
  ['Wallie · opgebouwd uit basisvormen', 'Wallieonline+Mini+Robot+chassis/wallieonline-mini-robot-primitives.scad', 'Een reconstructie met geometrische basisvormen, naast het originele meshmodel.'],
  ['Chassis v0.2 · de basis', 'reinsdesigns/antweight_chassis.scad', 'De basis van de parametrische chassisreeks.'],
  ['Chassis v0.3 · vormgeving', 'reinsdesigns/antweight_chassis_v0.3_design.scad', 'De functionele basis krijgt afgeronde vormen en ontwerpdetails.'],
  ['Chassis v0.4 · geïntegreerd', 'reinsdesigns/antweight_chassis_v0.4_integrated.scad', 'Een geïntegreerde neus en een ander intern ontwerp.'],
  ['Disc v0.1 · rond', 'reinsdesigns/antweight_disc_v0.1.scad', 'Een ronde carrosserie: een heel andere vorm rond de componenten.'],
  ['Disc Mini v0.1 · compact', 'reinsdesigns/antweight_disc_mini_v0.1.scad', 'De compacte variant van de ronde ontwerplijn.'],
  ['Disc Wedge v0.1', 'reinsdesigns/antweight_disc_wedge_v0.1.scad', 'Een ronde basis met een afgevlakte voorkant.', 'disc_assembly'],
  ['Disc Spinner v0.1', 'reinsdesigns/antweight_disc_spinner_v0.1.scad', 'De eerste versie van deze afzonderlijke Disc-ontwerplijn.'],
  ['Disc Spinner v0.2', 'reinsdesigns/antweight_disc_spinner_v0.2.scad', 'Volgende ontwerpiteratie; vergelijk het silhouet en de indeling.'],
  ['Disc Spinner v0.3', 'reinsdesigns/antweight_disc_spinner_v0.3.scad', 'Een nieuwe versie binnen dezelfde familie.'],
  ['Disc Spinner v0.4', 'reinsdesigns/antweight_disc_spinner_v0.4.scad', 'Een compactere kern en kleinere ronde buitenvorm.'],
  ['Disc Spinner v0.5', 'reinsdesigns/antweight_disc_spinner_v0.5.scad', 'De nieuwste beschikbare versie in deze reeks.'],
  ['Rhomboid v0.1 · hoekig', 'reinsdesigns/antweight_rhomboid_v0.1.scad', 'Een parallellogram als zijprofiel, in plaats van een ronde vorm.'],
  ['Limulus v0.1 · biomimicry', 'reinsdesigns/antweight_limulus_v0.1.scad', 'Een ontwerp geïnspireerd op een degenkrab.'],
  ['Limulus v0.2 · doorontwikkeling', 'reinsdesigns/antweight_limulus_v0.2.scad', 'Een verhoogde elektronicakap en een aangepaste staart.'],
  ['Passive Aggressive v0.1', 'reinsdesigns/antweight_passive_aggressive_v0.1.scad', 'Een eigen carrosseriefamilie met een uitgesproken gezicht.'],
  ['Passive Aggressive · 4WD', 'reinsdesigns/antweight_passive_aggressive_4wd_v0.1.scad', 'Vier wielen in plaats van twee: dezelfde familie, een andere indeling.'],
  ['Mostly Harmless · chassis', 'reinsdesigns/mostlyharmless.stl', 'Ook bestaande STL-ontwerpen horen in het overzicht, niet alleen OpenSCAD-bronnen.'],
  ['Mostly Harmless · dak', 'reinsdesigns/mopstlyharmlessroof.stl', 'Het afzonderlijke dak uit dezelfde bestandsset.'],
  ['Aurora v0.1 · 50 gram', 'reinsdesigns/aurora_50g_rc_v0.1.scad', 'Een kleine radiografisch bestuurde demonstratierobot; een andere gewichtsklasse.'],
  ['DuoBot v0.1 · modulair', 'reinsdesigns/duobot_v0.1.scad', 'Een modulair robotconcept. Geometrie is een ontwerpvoorstel, geen bewijs van werking.'],
];

if (typeof module !== 'undefined') module.exports = SCAD_DESIGNS;

const SCAD_DESIGN_GROUPS = [
  ['Wallie', 'Origineel, topplaat en reconstructies; geen vier historische versies.', {
    point: '1. Hergebruik: een bestaand meshmodel kan achter een module zitten.',
    code: 'use <wallieonline-mini-robot.scad>\n\nMAIN_BBOX_SIZE = [94, 20, 105];\nmain_size = MAIN_BBOX_SIZE;',
    source: 'Wallieonline+Mini+Robot+chassis/wallieonline-mini-robot-parametric.scad',
    detail: 'use importeert modules en functies, niet de geometrie op het hoogste niveau.',
  }, 'wallieonline-'],
  ['Chassis · v0.2 tot v0.4', 'Vergelijk de basis, vormgeving en geïntegreerde uitvoering.', {
    point: '2. Parameters: geef maten en speling een naam.',
    code: 'pcb_w = 78;\npcb_l = 56;\npcb_t = 1.6;\npcb_clear = 0.3;',
    source: 'reinsdesigns/antweight_chassis.scad',
    detail: 'Nominale printplaatmaten en extra pasruimte zijn verschillende parameters.',
  }, 'antweight_chassis'],
  ['Disc · varianten', 'Rond, compact en met een afgevlakte voorkant.', {
    point: '3. Geometrie: snij een profiel uit en draai het tot een holle vorm.',
    code: 'module shell() translate(C) rotate_extrude()\n  difference() {\n    polygon([[0, 0], [R, 0], [R_top, H], [0, H]]);\n    polygon([[-1, floor_t], [r_in(floor_t), floor_t],\n      [r_in(H-top_t), H-top_t], [-1, H-top_t]]);\n  }',
    source: 'reinsdesigns/disc_geometry.scad',
    detail: 'De gedeelde Disc-bibliotheek: buitenprofiel min binnenprofiel, daarna een omwenteling.',
  }, 'antweight_disc_v', 'antweight_disc_mini_', 'antweight_disc_wedge_'],
  ['Disc Spinner · v0.1 tot v0.5', 'Vijf versies van dezelfde ontwerplijn naast elkaar · ontwerpstudie: actieve wapens zijn bij Roboktober 2026 niet toegestaan.', {
    point: '4. Afleiden: bereken maten uit de geometrie, niet met de hand.',
    code: 'pcb_r = max([\n  for (p = pcb_pts) norm(p - [cx, cy])\n]);',
    source: 'reinsdesigns/antweight_disc_spinner_v0.5.scad',
    detail: 'norm geeft de afstand tot het middelpunt; max kiest de verste PCB-hoek.',
  }, 'antweight_disc_spinner_'],
  ['Rhomboid', 'Een hoekig zijprofiel in plaats van een ronde vorm.', {
    point: '5. Herhalen: één lus plaatst alle afstandhouders.',
    code: 'for (p = pcb_holes)\n  translate([p[0], p[1], 0])\n    cylinder(d = standoff_d, h = pcb_z);',
    source: 'reinsdesigns/antweight_rhomboid_v0.1.scad',
    detail: 'De gatenlijst is de bron; translate plaatst dezelfde vorm op elke coördinaat.',
  }, 'antweight_rhomboid_'],
  ['Limulus · v0.1 en v0.2', 'Biomimicry en doorontwikkeling naast elkaar.', {
    point: '6. Doorbouwen: include neemt een bestaand ontwerp als basis.',
    code: 'include <antweight_limulus_v0.1.scad>\n\nesp_stack_h = 17;\nesp_margin = 0.8;\ncanopy_wall = 1.2;',
    source: 'reinsdesigns/antweight_limulus_v0.2.scad',
    detail: 'Anders dan use neemt include ook toplevel-code mee; v0.2 bouwt voort op v0.1.',
  }, 'antweight_limulus_'],
  ['Passive Aggressive · 2WD en 4WD', 'Twee varianten binnen dezelfde ontwerpfamilie.', {
    point: '7. Combineren: een lus over kanten én assen maakt vier posities.',
    code: 'for (m = [0, 1], a = axles)\n  mirror_x(m) motor_cradle(a);',
    source: 'reinsdesigns/antweight_passive_aggressive_4wd_v0.1.scad',
    detail: 'Twee kanten × twee assen; mirror_x is een eigen helper, geen ingebouwde functie.',
  }, 'antweight_passive_aggressive_'],
  ['Mostly Harmless', 'Chassis en bijbehorend dak; twee onderdelen, geen twee versies.', {
    point: '8. Importeren: ook een STL zonder parametrische bron kan meedoen.',
    code: 'import("mostlyharmless.stl");\nimport("mopstlyharmlessroof.stl");',
    source: 'reinsdesigns/mostlyharmless.stl',
    example: true,
    detail: 'Importvoorbeeld in dezelfde map; een mesh geeft niet de oorspronkelijke parameters terug.',
  }, 'mostlyharmless.', 'mopstlyharmlessroof.'],
  ['Aurora · 50 gram', 'Een radiografisch bestuurde demonstratierobot in een andere gewichtsklasse.', {
    point: '9. Controleren: assert bewaakt een expliciete maatvoorwaarde.',
    code: 'assert(\n  wheel_x - wheel_w / 2 > body_w / 2,\n  "wheel overlaps the chassis side wall"\n);',
    source: 'reinsdesigns/aurora_50g_rc_v0.1.scad',
    detail: 'Dit controleert wielvrijloop in het model, niet de werkelijke passing of het gewicht.',
  }, 'aurora_'],
  ['DuoBot · modulair', 'Een modulair robotconcept; geometrie is geen bewijs van werking.', {
    point: '10. Samenstellen: dezelfde module levert meerdere robotvarianten.',
    code: 'if (part == "assembly") {\n  robot("attack");\n  translate([0, pair_pitch, 0]) robot("defend");\n}',
    source: 'reinsdesigns/duobot_v0.1.scad',
    detail: 'Een argument kiest de variant; translate zet de tweede robot in de samenstelling.',
  }, 'duobot_'],
].map(([title, description, lesson, ...prefixes]) => ({
  title,
  description,
  lesson,
  designs: SCAD_DESIGNS.filter(([, source]) =>
    prefixes.some(prefix => source.split('/').pop().startsWith(prefix))),
}));
