# Roboktober — from joystick to wheel · review package

**Project status — 5 November 2026:** Rein confirms Roboktober 2026 is complete. Website operational and stable; electronics v4 works and is approved. This is the reviewed presentation package, available for reuse, not an open review request.

Talk for PHP Meetup Groningen, November 5, 2026 (about 47 minutes + questions).
Central question: what happens when you push the joystick forward on your phone?

## Getting started

1. Extract the ZIP completely (do not open it from the ZIP preview).
2. Open `presentatie/index.html` in Chrome, Edge, or Firefox.
3. The slides, video, and source gallery work offline; no web server is required. External websites and repository links need an internet connection. Hardware toolchains are not included.

## Controls

All display assets (including product photos), video, scripts and CSS are local
to the package; there are no external fonts or CDN fallbacks. AliExpress and
other source links do not load images. Only opening external websites or the
page behind the QR code requires internet. Extract the entire package, including
`vendor/`, `js/` and `img/`.

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

## Review complete

- Review complete and approved on 4 November 2026; Wallie's comments and all other review feedback have been addressed.
- Rein confirms that all necessary usage rights, licence conditions and permissions for this presentation have been satisfied. Credits and licences remain in place; this does not grant an open licence to third parties.
- The speaker notes (`S`) explain the purpose and timing of each slide. On slides with click steps, only the current step's speaking text is shown, including when stepping backwards or jumping directly to a step. Slides without click steps retain their complete notes.

No review actions remain open.
