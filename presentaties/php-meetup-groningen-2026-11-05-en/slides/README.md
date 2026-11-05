# Roboktober — from joystick to wheel · review package

**Project status — 5 November 2026:** Rein confirms Roboktober 2026 is complete. The website is operational and stable and electronics v4 works and is approved. This package remains available for presentation and reuse; review is complete. Practical checks for a subsequent screening remain necessary.

Talk for PHP Meetup Groningen, November 5, 2026 (about 47 minutes + questions).
Central question: what happens when you push the joystick forward on your phone?

**Status — 4 November 2026:** review complete and approved; all comments,
including Wallie's, have been addressed. Rein confirms that all necessary
usage rights, licence conditions and permissions for this presentation have
been satisfied. Credits and licences remain in place.

## Getting started

1. Extract the ZIP completely (do not open it from the ZIP preview).
2. Open `presentatie/index.html` in Chrome, Edge, or Firefox.
3. The slides, video, and source gallery work offline; no web server is required. External websites and repository links need an internet connection. Hardware toolchains are not included.

## Controls

All display assets are bundled locally: images and product photos, video,
reveal.js, code highlighting, scripts and CSS. Fonts use the local system font
stack; no webfonts are downloaded. There is no CDN fallback. AliExpress and other
external links are clickable source credits, not image sources. Only opening
external websites or the page behind the QR code requires internet. Extract the
entire package, including `vendor/`, `js/` and `img/`.

| Key | Action |
|---|---|
| → / space | Next step (many slides build up with each click) |
| ← | Previous step |
| `S` | Speaker notes in a separate window; automatically follow each click (allow the pop-up) |
| `Esc` / `O` | Overview of all slides |
| `F` | Full screen |

- Slide 8 contains a video of Wallie with audio; click play. **The video is in Dutch and has no subtitles.**
- Slide 20 contains the complete public ESP-NOW firmware: eight clicks highlight the code blocks, each with its own explanation alongside (what, why, and effect on the robot); scroll inside the code window for all 205 lines. Revision `fb3afa886117`, © 2026 wallieonline, GPL-3.0-or-later; the license is included locally. This is not the WebUI source.
- Slide 34 covers code in two click steps with exact example results. Slide 35 is then a separate design gallery with 9 click steps, without code. Model names link to the source files in this package's `3dprinter/` folder.

## Contents

- `presentatie/` — the slides (reveal.js, included locally)
- `3dprinter/` — OpenSCAD and STL sources for the design gallery
- `PLAN.md` — talk structure, timing, and assumptions

## Photo attribution

The photo `img/esp32-c3-supermini-photo.png` on slide 13 is by Dr. Tobias Weltner,
[done.land](https://done.land/components/microcontroller/families/esp/esp32/developmentboards/esp32-c3/c3supermini/).
The [original photo](https://done.land/assets/img/c3_anglefront_overview_t.png)
is included locally, unchanged, under
[CC BY-ND 4.0](https://creativecommons.org/licenses/by-nd/4.0/).
Attribution and the license also appear directly below the photo.

The photo `img/drv8833-module-photo.jpg` is the
[AliExpress product photo supplied by Rein](https://ae-pic-a1.aliexpress-media.com/kf/S74f2a70c6db24777afee9bef22fa2001N.jpg?width=1000&height=1000&hash=2000),
included locally without changes. Rein has confirmed the necessary usage rights
for this presentation; this does not grant an open licence to third parties.

The [N20 motor](https://ae-pic-a1.aliexpress-media.com/kf/He59f980f66ec4a4baa1d3b447724f1e0S.jpg)
and [wheel](https://ae-pic-a1.aliexpress-media.com/kf/S4df94380f31f496cb5fecc3295f65284O.jpg)
product photos were also supplied by Rein. Local files: `img/n20-motor-photo.jpg`
(cropped to one complete motor) and `img/n20-wheel-photo.jpg` (unchanged).
Rein has also confirmed the necessary usage rights for these photos in this
presentation; this does not grant an open licence to third parties.

## Review complete

- The through-line, level for PHP developers, language, pacing and content have been reviewed and approved.
- Wallie's comments and all other review feedback have been addressed.
- The speaker notes (`S`) explain the purpose and timing of each slide. On slides with click steps, only the current step's speaking text is shown, including when stepping backwards or jumping directly to a step. Slides without click steps retain their complete notes.

No review actions remain open.
