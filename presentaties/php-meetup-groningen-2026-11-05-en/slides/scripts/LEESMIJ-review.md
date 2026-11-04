# Roboktober — from joystick to wheel · review package

Talk for PHP Meetup Groningen, November 5, 2026 (about 47 minutes + questions).
Central question: what happens when you push the joystick forward on your phone?

## Getting started

1. Extract the ZIP completely (do not open it from the ZIP preview).
2. Open `presentatie/index.html` in Chrome, Edge, or Firefox.
3. The slides, video, and source gallery work offline; no web server is required. External websites and repository links need an internet connection. Hardware toolchains are not included.

## Controls

| Key | Action |
|---|---|
| → / space | Next step (many slides build up with each click) |
| ← | Previous step |
| `S` | Speaker notes in a separate window; automatically follow each click (allow the pop-up) |
| `Esc` / `O` | Overview of all slides |
| `F` | Full screen |

- Slide 8 contains a video of Wallie with audio; click play. **The video is in Dutch and has no subtitles.**
- Slide 20 contains the complete public ESP-NOW firmware: eight clicks highlight the code blocks, each with its own explanation alongside (what, why, and effect on the robot); scroll inside the code window for all 205 lines. Revision `fb3afa886117`, © 2026 wallieonline, GPL-3.0-or-later; the license is included locally. This is not the WebUI source.
- Slide 33 is a gallery with 10 click steps. Model names link to the source files in this package's `3dprinter/` folder.

## Contents

- `presentatie/` — the slides (reveal.js, included locally)
- `3dprinter/` — OpenSCAD and STL sources for the design gallery
- `PLAN.md` — talk structure, timing, and assumptions

## Feedback wanted

- Is the through-line (one joystick command from phone to wheel) easy to follow?
- Is the level right for PHP developers without hardware experience?
- Language, pacing, crowded slides, unclear or inaccurate claims.
- The speaker notes (`S`) explain the purpose and timing of each slide. On slides with click steps, only the current step's speaking text is shown, including when stepping backwards or jumping directly to a step. Slides without click steps retain their complete notes.

Please refer to slide numbers (bottom-right corner) in your feedback.
