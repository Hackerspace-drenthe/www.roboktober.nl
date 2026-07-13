# Antweight Page - Gemini Image Pack

Dit document helpt je om met Google Gemini sterke visuals te genereren voor de antweight pagina.

## 1) Doel van de beeldset

De pagina moet een wow-gevoel geven:
- Toegankelijk voor beginners
- Energie van robot combat
- Mix van stijlen: fotorealistisch, cinematic poster, technische infographic
- Focus op antweight schaal (max 150 gram)

## 2) Referentiebronnen (inhoudelijk)

Gebruik deze bronnen als context voor antweight-specifieke terminologie, stijl en realisme:
- RobotWars101 Antweight Resources: https://www.robotwars101.org/ants
- FingerTech Robotics (antweight onderdelen en kits): https://www.fingertechrobotics.com/
- Printables Antweight Designs: https://www.printables.com/search/models?q=antweight%20robot
- Thingiverse Antweight Models: https://www.thingiverse.com/search?q=antweight+robot
- Wikipedia Robot Combat (150g class context): https://en.wikipedia.org/wiki/Robot_combat

Let op:
- Gebruik bronnen uitsluitend als inspiratie voor schaal, onderdelen en stijl.
- Genereer alleen originele, nieuwe beelden zonder merken, logo's of herkenbare copyrighted robot-ontwerpen.
- Gebruik GEEN oude of generieke robot-afbeeldingen; alle output moet specifiek antweight (150g) zijn.

## 3) Gewenste beeldset voor deze pagina

Genereer minimaal deze 6 beelden:
- Hero arena shot (cinematic)
- Bouwtafel close-up (fotorealistisch)
- 4-stijlen collage (combat, show, simpel, geavanceerd)
- Technische exploded infographic
- Battle-ready beauty shot
- Community/workshop sfeerbeeld

## 4) Bestandsnamen (exact)

Plaats output in `roboktober-frontend/public/antweight-ai/` met deze namen:
- `hero-combat.png`
- `workbench-build.png`
- `styles-collage.png`
- `exploded-tech.png`
- `battle-beauty.png`
- `community-lab.png`

## 5) Gemini prompts (copy/paste)

### Prompt A - Hero combat
Create a high-energy cinematic hero image of a TINY 150-gram antweight combat robot (insect-class scale) battling in a small polycarbonate mini arena. Show clear small scale with visible arena edge details. Sparks flying, motion blur, dramatic side lighting, shallow depth of field, realistic materials. MUST be antweight-specific tiny robot, NOT generic combat robot. No logos, no text, no watermarks, original design only, 16:9 composition, ultra detailed.

### Prompt B - Workbench build
Photorealistic close-up of a maker workbench with TINY antweight (150g class) robot parts: micro N20 motors, 20mm wheels, small LiPo battery, micro ESC board, M2/M3 screws, 3D printed mini chassis parts. Show clear insect-class scale with hand for size reference. Warm workshop light, hands assembling tiny components. Educational and inspiring mood. MUST be antweight-specific parts, NOT generic robot parts. No brand marks, no text overlays, original composition only.

### Prompt C - 4 styles collage
Create a clean 2x2 collage showing four ANTWEIGHT (150g) robot styles, all clearly tiny/insect-class scale: 1) aggressive combat horizontal spinner with small weapon disc, 2) artistic show robot with colorful 3D-printed shell, 3) simple beginner wedge bot with clean geometry, 4) advanced engineered bot showing precision mini internals. ALL robots must be clearly antweight-sized (palm-sized). Consistent lighting, realistic tiny scale visible, no logos, no text, original designs only.

### Prompt D - Exploded tech infographic visual
Technical render style: exploded view of a TINY 150-gram antweight robot showing 3D-printed chassis (palm-sized), micro N20 drive motors, small LiPo battery, micro ESC/receiver, weapon module, M2/M3 fasteners. MUST show clear antweight scale (insect-class, fits in hand). Clean background, blueprint-inspired but modern, high readability through visuals only, no labels, no text, original design only.

### Prompt E - Battle beauty shot
Studio-quality product shot of a TINY battle-ready 150g antweight robot (clearly palm-sized/insect-class) on dark matte surface. Show clear small scale with size context. Dramatic rim lighting, subtle smoke haze, premium metal and 3D-printed polymer textures, realistic wear marks from antweight combat. MUST be antweight-specific tiny robot. No brand, no text, original design only.

### Prompt F - Community lab
Inspiring scene in a makerspace: diverse group of teens and adults testing TINY antweight (150g, palm-sized) robots on a small tabletop practice arena. Show clear scale with robots visibly small compared to people and tools. Friendly maker atmosphere, 3D printers and practical tools visible, warm cinematic lighting. MUST show antweight-specific tiny robots, NOT generic combat robots. No logos, no text, original scene only.

## 6) Aanbevolen Gemini instellingen

- Aspect ratio:
  - Hero: 16:9
  - Overige: 4:3 of 3:2
- Style strength: medium-high
- Photorealism: high for A, B, E, F
- Negative constraints:
  - no text
  - no logos
  - no watermark
  - no copyrighted robot likeness
  - no old/generic robot images
  - MUST be new, original antweight-specific (150g) content

## 7) Kwaliteitscheck voor publiceren

- **Schaal klopt**: robots zijn duidelijk klein/insect-class/150g antweight (palm-sized)
- **Antweight-specifiek**: geen generieke of grote combat robots, alleen antweight
- Geen vervormde onderdelen (wielen, assen, schroeven)
- Geen tekst artifacts
- Geen herkenbare IP of TV-show branding
- **Nieuwe originele beelden**: geen oude afbeeldingen hergebruiken
- Visueel consistent kleurpalet met de site

## 8) Snelle vervangworkflow

1. Genereer 6 beelden met prompts A-F.
2. Exporteer als PNG.
3. Hernoem exact volgens sectie 4.
4. Plaats in `roboktober-frontend/public/antweight-ai/`.
5. Herlaad de pagina `/antweight`.

## 9) Gemini batch prompt (1 run, consistente serie)

Gebruik deze prompt als je in een keer een complete, consistente beeldset wilt laten maken:

```text
You are creating a cohesive image series for a Dutch event website page about ANTWEIGHT combat robots (150-gram/insect-class ONLY). Generate 6 BRAND NEW ORIGINAL images with a shared visual language (same color mood, lighting philosophy, realism level), no logos, no watermarks, no text overlays, no copyrighted robot likeness, no old/generic robot images.

Core art direction:
- Hybrid style: 60% photorealistic, 25% cinematic poster energy, 15% technical infographic clarity.
- Tone: inspiring, energetic, maker-friendly, safe but exciting.
- Palette: dark steel blues + warm orange highlights, with occasional electric cyan accents.
- Arena safety cues: clear polycarbonate shielding, mini-scale context.
- **CRITICAL Scale fidelity: ALL robots MUST clearly read as TINY/INSECT-CLASS (150g antweight, palm-sized, NOT large combat robots).**
- **MUST be NEW original antweight-specific content, NOT old or generic robot images.**

Output all 6 images with these exact intents and filenames:

1) hero-combat.png
  - 16:9 cinematic hero shot
  - TINY 150g antweight robot (clearly insect-class/palm-sized) in small polycarbonate mini arena, sparks, motion blur, dramatic sidelight
  - show clear small scale context
  - high excitement, premium composition, NEW original design

2) workbench-build.png
  - 4:3 photoreal maker bench close-up
  - ANTWEIGHT-specific tiny parts: micro N20 motors, 20mm wheels, small LiPo, micro ESC, M2/M3 screws, 3D printed mini chassis, hands assembling
  - show clear insect-class scale with hand size reference
  - warm workshop mood, educational and inviting, NEW original composition

3) styles-collage.png
  - 4:3 clean 2x2 collage
  - top-left: aggressive ANTWEIGHT (150g) combat horizontal spinner
  - top-right: artistic ANTWEIGHT show robot with 3D-printed shell
  - bottom-left: simple beginner ANTWEIGHT wedge bot
  - bottom-right: advanced engineered ANTWEIGHT precision bot
  - ALL robots clearly palm-sized/insect-class scale
  - keep consistent lighting and true mini scale, NEW original designs

4) exploded-tech.png
  - 4:3 technical exploded render
  - TINY 150g ANTWEIGHT robot: 3D-printed chassis, micro N20 drivetrain, small LiPo battery, micro ESC/controller, weapon module, M2/M3 fasteners
  - MUST show clear antweight scale (palm-sized components)
  - blueprint-inspired clean background, no labels/text, NEW original design

5) battle-beauty.png
  - 4:3 studio beauty shot
  - battle-ready TINY 150g ANTWEIGHT robot (clearly palm-sized) on dark matte surface, rim light, subtle haze
  - show clear small scale context
  - premium 3D-printed and metal materials, light wear marks, NEW original design

6) community-lab.png
  - 4:3 inspirational makerspace scene
  - diverse teens/adults testing TINY ANTWEIGHT (150g, palm-sized) robots on small tabletop practice arena
  - show clear scale: robots visibly small compared to people
  - warm cinematic social energy, 3D printers and practical tools visible, NEW original scene

Hard constraints:
- **MUST be ANTWEIGHT-SPECIFIC (150g/insect-class/palm-sized) robots in ALL images.**
- **MUST be NEW, ORIGINAL content - no old or generic robot images.**
- No text in the image.
- No brand logos.
- No copied robot identities from TV shows.
- No unrealistic giant-scale robots.
- Maintain clear tiny scale and visual consistency across all 6 outputs.

If your system supports seed/style lock: keep one master style seed across all images.
```

### Gebruikstip

Als Gemini geen multi-image batch in een prompt ondersteunt, run dan 6 losse generaties met dezelfde art-direction regels hierboven en hergebruik dezelfde style reference/seed waar mogelijk.
