# Plan: Roboktober talk, PHP Meetup Groningen

> Status: content and slides are aligned; the review package is ready for reviewers. Review feedback, the Battle Day winner, and a timed rehearsal are still pending.

## 1. Essentials

| Item | Value |
| --- | --- |
| Event | PHP Meetup Groningen, Thursday, November 5, 2026 |
| Speaker | Rein; Wallie appears in a video clip |
| Audience | Developers, engineers, and makers; no electronics experience required |
| Language | Dutch; explain technical terms the first time they are used |
| Duration | 60 minutes: 47 minutes for the talk, including video and demo; 2 minutes of buffer; 11 minutes for Q&A |
| Format | Static reveal.js slides, opened locally via `file://`; PDF as a backup |
| Demo | Robots without active weapons, a bounded driving area, one driver per robot |
| Possible assistant | Hendrie, member of Hackerspace Drenthe; the talk can still be delivered solo |

## 2. Audience promise

**Central question:** what happens when you push the joystick forward on your phone?

**Core message:** if you can program, you can build robots too. Electronics, 3D designs, websites, and robots can all be built, controlled, and validated with code; interfaces, version control, review, and testing also work for a 150-gram robot. This is the closing line on the recap slide.

The robot technology is the story. Roboktober is the reason for it. The website and AI are brief side paths that explain how the project became feasible, not separate talks in their own right.

By the end, attendees should be able to:

1. Explain the route: controller → firmware → PWM → H-bridge → motor.
2. Explain why the robot must stop when it stops receiving fresh commands.
3. Recognize familiar software principles in pin contracts, design checks, and parametric 3D models.
4. Get started themselves using the parts list, build guide, firmware, and chassis files.

### Teaching principles

- Start with movement, not a parts list.
- Explain each new concept before treating its abbreviation as familiar: printed circuit board (PCB), PWM, H-bridge, deadband, and failsafe.
- One idea per slide. Keep code snippets to ten lines where possible; clearly label omissions and simplifications.
- Keep the main route the same even when the transport changes from WebSocket to ESP-NOW.
- Use one continuous numerical example: **after scaling and deadband**, throttle `200`, steering `0` → left `200`, right `200` → about 78% duty cycle.
- RC channel values around `1500`, motor commands around `0`, and duty cycle percentages are different scales.
- Use extra turning examples only to explain mixing, not as a new main route.
- Do not expect the audience to know electronics or pin numbers by heart; keep returning to what they mean.
- Avoid jokes at the expense of participants or Wallie. Make it easy for people to ask questions.
- Show or pass around hardware without waiting for everyone to finish.

## 3. Structure and timing

These timings are a rehearsal target, not a measured duration. Rehearse with the 84-second video, slide fragments, demo transitions, and audience control included.

Shorter remains the goal. Only if the explanation truly needs it may the talk run up to an absolute maximum of 60 minutes; this uses buffer/Q&A time, not an extra hour. For deeper OpenSCAD explanation (target 2 min, up to 5 min), watch the clock and shorten side paths and repeated explanations.

On every slide with click steps, the S speaker window automatically follows the current step: initial notes, then only that step's speaking text plus brief context/timing. This uses reveal.js's native fragment notes, including code highlights, simultaneous fragments and the generated gallery. Stepping backwards and jumping directly to a step show the same notes. Slides without click steps retain their original notes. `slides/js/step-notes.js` checks that every click group has notes; update them when fragments change.

| Section | Minutes | Cumulative | Question |
| --- | ---: | ---: | --- |
| 3.1 Cold open, Rein, and audience poll | 3 | 3 | Why is there a robot driving here? |
| 3.2 Wallie, including video | 2 | 5 | Where does the robot knowledge come from? |
| 3.3 Roboktober and today's perspective | 2 | 7 | Why did we build this together? |
| 3.4 Antweight and safety | 2 | 9 | What are we building, and within what limits? |
| 3.5.1 The full command route | 2 | 11 | What happens between thumb and wheel? |
| 3.5.2 Controller | 5 | 16 | How does a movement become a message? |
| 3.5.3 Firmware, PWM, H-bridge, and failsafe | 12 | 28 | How does a message become movement, and what happens if it is lost? |
| 3.5.4 PCB and validation | 4 | 32 | How do we connect this reliably? |
| 3.5.5 Chassis as code, including design gallery | 4 | 36 | How do separate parts become one robot? |
| 3.6 Audience takes control | 4 | 40 | Can you use it yourself? |
| 3.7 Website as a distribution channel | 2 | 42 | How do participants get build information? |
| 3.8 Project journey and AI assistance | 2 | 44 | How did the project come together? |
| 3.9 Practical results | 2 | 46 | Did the shared foundation work? |
| 3.10 Recap and getting started | 1 | 47 | What should you take away? |
| Buffer | 2 | 49 | Room for questions and demo transitions |
| Q&A | 11 | 60 | Explore topics based on interest |

### 3.1 Cold open and introduction

- Put the large Roboktober logo on screen; drive the robot onto a bounded area. Do not propel an object toward the audience or off an unprotected table.
- Then introduce Rein: software engineer at Dawn Technology, founder of the Dutch PHP User Group / PHPBenelux and Hackerspace Drenthe.
- Bridge from the software community to the maker community.
- Audience poll: who has soldered, and who has programmed an Arduino/ESP32?
- Promise to follow one command today. Save the project timeline for the end.

### 3.2 Wallie

- WallieOnline, Team Upstart, robot experience, and collaboration with the hackerspace.
- Brief introduction, then Wallie himself: **0:26–1:50, duration 1:24**, from the kickoff recording.
- The video is on the profile slide; do not read all the bullets aloud while it plays.
- Confirm permission. The local MP4 is embedded as data in `slides/js/wallie-video.js` for `file://` compatibility.
- Sound is on by default; start it manually and check the room audio beforehand.

### 3.3 Roboktober

- Build, test, and drive together: Hackerspace Drenthe + WallieOnline.
- 2026 program: kickoff on October 7, build nights on October 14 and 21, inspection and Battle Day on October 28.
- Transition: participants build and drive at the build nights; today we look under the hood as developers.

### 3.4 Antweight and safety

- Our class: a maximum of 150 g including the battery; for this build, a 15 × 15 × 15 cm cube.
- Parts kit costs about EUR 25; do not promise that tools and all additional costs are included.
- No active weapons at Roboktober 2026. A closed arena alone does not permit spinners: appropriate rules, shielding, and inspection are still required.
- Do not digress into other weight classes; always check the rules for the event in question.

### 3.5.1 Overview: from thumb to wheel

The existing overview slide shows seven parts one at a time, each with a large image and its own purpose: chassis → battery → on/off → bare PCB → ESP32 → motor driver → motors/wheels. The chassis appears immediately; six clicks show the other parts. On/off shows a switch or removable link plug/jumper cap that physically interrupts battery power, not the software failsafe. Choose a component accessible from outside and rated for the motor current; disconnect other power sources before working on the robot. PCB and ESP32 are separate steps: the board connects parts, the microcontroller runs code. Chassis and PCB use existing renders; battery and power disconnect are schematic. N20 motor and wheel appear as separate product photos alongside each other within the seventh step, with attribution (AliExpress, supplied by Rein; usage rights still to be checked). Also point out the parts on the opened, powered-off robot. Allow about 60 seconds within the existing 2 minutes.

The ESP32-C3 SuperMini is shown with a real photo (Tobias Weltner, done.land, CC BY-ND 4.0; unchanged and included locally). The DRV8833 uses the AliExpress product photo supplied by Rein, also included locally; check usage rights before further public distribution.

The next click shows the overview with the first command step on the same slide; four more clicks complete the existing five animation steps. Eleven clicks in total, no extra slide. Bridge: "What happens when you push forward on your phone?"

1. A phone/WebSocket or remote/ESP-NOW provides throttle and steering.
2. The ESP32-C3 receives, validates, scales, and mixes the inputs.
3. PWM rapidly switches the control signals on and off.
4. The DRV8833 contains two H-bridges; the battery supplies the motor power.
5. N20 motors turn the wheels.

Show the actual PCB, but do not explain pins or power-supply variations yet.

### 3.5.2 Controller

- Phone: the robot provides its own WiFi network and web interface at `192.168.4.1`; no app or external router is needed.
- WebSocket: messages sent over an open connection. Joystick or tank-style control with two sliders.
- Remote: a second ESP32, analog stick, and ESP-NOW. Both routes provide throttle and steering.
- Slide 16 mentions Wallie's announced new firmware release on **5 November 2026**, presentation day, with a link to [his GitHub profile](https://github.com/wallieonline). Check availability on the day.
- **Say this transition out loud:** the code examples still come from the existing public ESP-NOW firmware, not the announced new release. Mixing and motor code are the same; the transport differs.
- Show only the shared struct, two channel assignments, sending, and the length check. Link to the full source code.
- Takeaway: a fixed data format is an interface, just like `pack()`/`unpack()`.

### 3.5.3 Firmware, PWM, H-bridge, and failsafe

- Flash over USB using Web Serial in a supported Chromium browser; a data-capable cable is required.
- After flashing: one extra slide with the complete public ESP-NOW/DRV8833 firmware (205 lines, revision `fb3afa886117`, GPL-3.0-or-later). Eight clicks highlight message/limits, state, setup/PWM, radio/callback, receipt, mixing, direction/PWM, and loop/failsafe; libraries/pins are highlighted initially. Code scrolls with the highlights and remains fully available offline, including its license. This is not the WebUI source. Each code block gets its own explanation (what, why, effect on the robot), synchronized with the highlights, including when stepping backwards. Also explain the receipt limitation: invalid channel values retain their previous value, but correctly sized messages still refresh the timeout. Allow about two minutes within the existing firmware section; keep the following detail slides shorter.
- Receive: validate message length and channel range. Scale to −255…255; deadband sets noise around neutral to zero.
- Start the numerical example here: throttle 200, steering 0. The actual RC input depends on the configured limits.
- Mixing: left = throttle + steering; right = throttle − steering; clamp to −255…255.
- PWM: for example, 200 ≈ 78% on at 8-bit resolution; **do not** claim that this means 78% speed or power.
- The H-bridge selects the current direction; PWM determines the on-time. Motor power comes from the battery, not the GPIO.
- Give each code display a source link and clearly indicate whether it is shortened or simplified.

#### Safe stopping belongs in the firmware

- End the firmware section with the question: what happens if the command is lost?
- After more than 500 ms without a packet: throttle and steering go to zero, the motors stop, and the LED flashes.
- Analogy: a heartbeat timeout; move to a safe state when input is missing.
- **One live demonstration:** lift the wheels, turn off the remote, or disconnect WiFi. Do not explain it again during the later audience demo.
- Do not imply that this timeout alone provides a general safety or authentication guarantee.

### 3.5.4 PCB and validation

- Transition: the motor is turning; how do we connect the parts reliably?
- ESP32-C3, DRV8833, motors, and battery. No power bypass to 3.3 V.
- Pin contract: the schematic and firmware must follow the same interface. One motor is enough as an example.
- First PCB design: iterations v2 → v3 → v4 before the production run.
- ERC/DRC are automated electrical and design-rule checks, not proof that a circuit works correctly.
- Validation slide: copper clearance, trace width, and connectivity checks on the PCB, animated one click at a time. Explicitly label this as an illustration, not a live KiCad check or measurement; then show the DRC result, separate ERC messages, and human review. Animations respect reduced-motion preferences.
- Checking against the pin contract, human review by Wallie, and physical tests are also necessary. Do not present AI review as a deterministic test.
- Confirmed result: 150 bare PCBs produced (without components); 30 assembled so far, 25 working. Failures were due to ESP32s with poor WiFi and assembly mistakes (user error), not the design.

### 3.5.5 Chassis as code

- Transition: separate parts are not a robot yet.
- OpenSCAD fits my text-based workflow: parameters, diffs, review, and explicit dimensional constraints.
- Visual CAD tools can also be parametric and automated; do not caricature Fusion/FreeCAD.
- An `assert` only checks the condition that has been programmed. Test prints are still needed to check fit and tolerances.
- Slide 33: a design gallery with 10 click steps and 24 images, about two minutes. One click per design family, with versions and parts side by side: Wallie (original, top plate, and reconstructions), chassis v0.2/v0.3/v0.4, Disc/Mini/Wedge, Disc Spinner v0.1–v0.5, Rhomboid, Limulus v0.1/v0.2, Passive Aggressive/4WD, Mostly Harmless with roof, Aurora, and DuoBot. Nordic Racer was removed only from this gallery; the example remains on the following comparison slide.
- Each click teaches one concept for programmers new to OpenSCAD: modules/use, reusable dimensions and derived clearance, primitives with translate/union/difference, calculations, standoffs with screw holes using a module and loop, include, loop combinations, STL/3MF versus DXF/SVG with linear_extrude, asserts and assemblies. Steps 2, 3, 5 and 8 are labeled teaching examples alongside related real designs, not the source of the renders; the top half shows large robot designs; the bottom half shows compact code beside its exact result. Example scripts and profile.dxf are included offline. Regenerate renders with `xvfb-run -a node slides/scripts/render-scad-examples.mjs` (requires OpenSCAD). Other excerpts come from linked models. Allow about two minutes for the overview; use notes for detail and keep other chassis context brief.
- Explicitly name the different design lines rather than presenting them as one linear version history. No duplicate mesh conversions, export files, or helper libraries. Renders are not to scale and do not prove fit or functionality.
- Give each step its own render and a short selected code excerpt. Source files remain accessible; reveal.js fragments work when navigating both forward and backward.
- Show variants: same PCB dimensions, different housing.

### 3.6 Audience takes control

- Bounded driving area, no active weapon, maximum of one driver per robot.
- Put the actual SSID and password on a card with each robot, and test them beforehand.
- If there is a problem, switch within one minute to a pre-connected phone or backup robot; do not let the talk get stuck on network configuration.

### 3.7 Website

- Laravel API, Filament, and Vue only as familiar reference points.
- Main lesson: the build guide, parts list, and downloads make building together possible.
- Website and build files are on GitHub; ESP-NOW firmware is from WallieOnline. Do not say that all WebUI source code is already public.
- Do not list every development tool or registration process.

### 3.8 Project journey and AI

- **About three months of preparation:** first commit on July 2 through the October 7 kickoff. Build nights and Battle Day follow later in October.
- Show a few milestones rather than a purchasing log: website, PCB design/production, firmware/chassis, kickoff.
- AI helped with iteration; Rein remained responsible for goals, decisions, validation, and corrections. Wallie contributed robot expertise and review.
- Takeaway: AI helps you explore more broadly, but does not take over responsibility or physical testing.

### 3.9 Practical results

- Confirmed Battle Day figures: more than 30 visitors (a full house), initially 18 robots, 12 robots in the competition.
- Concrete setback and lesson: not every robot worked in time. Rein had brought 5 working robots and lent them to children whose robots were not working, so they could still take part.
- The PCB result (25 of 30 working) is already covered under validation (3.5) and is not repeated here.
- Use a photo from the build process with an accurate caption; do not invent a winner (only mention the result once confirmed).

### 3.10 Closing

- Slide 43 closes the common thread: from joystick to wheel, using the same software principles. Four short lessons: open software and standards as foundations; testable requirements as contracts; code as a tool, including hardware; tests as proof, including physical tests. AI assists at every step; the user defines, checks and validates. Keep the command chain and existing core message.
- Answer the opening question in this order: controller → firmware → PWM/H-bridge → motor.
- Explicitly mention safe stopping.
- Closing line: **you’re still a developer — now your bug drives away.**
- One next step: roboktober.nl for the parts list and build guide; WallieOnline for the firmware; visit the hackerspace.

## 4. Backup content and cuts

Do not add to the main route: full transmitter/receiver code, historical code review, all channel names, full pin/power tables, or detailed purchasing data.

If running late:
1. Explain the pin contract for just one motor.
2. Keep the chassis comparison short; show one parameter and one assert.
3. Cover the timeline in one sentence; do not add a second AI story.
4. Run the audience demo with one pre-connected driver.

Do not cut: the opening question, the complete command route, the failsafe, or the closing.

## 5. Final preparation

- [ ] Timed rehearsal: target 47 minutes including video, fragments, and demo; move briskly through the chassis sequence and its takeaways in about two minutes.
- [x] Final event figures and a concrete lesson collected (30+ visitors, 18 → 12 robots, 5 robots lent out).
- [ ] Confirm the Battle Day result/winner (optional).
- [ ] Recheck the rules, kit price, and figures on the slides.
- [ ] Confirm Wallie's permission and check that the video is audible.
- [x] Offline review package checked: 45 slides; images, video, and gallery links are present locally. The complete firmware slide also loads from `file://`, with all 205 source lines and nine highlighting stages. Bring the folder, including `vendor/` and `js/wallie-video.js`, on the day.
- [x] Build the reviewer package into `review/` (not in git): `bash slides/scripts/build-review-package.sh [--full] review`. Compact (±12 MB) or full, with videos and all 3D sources (±112 MB). It starts with `00-READ-ME-FIRST.html`; text is in `slides/scripts/LEESMIJ-review.md`.
- [ ] Process reviewer feedback.
- [ ] After changing the MP4: `bash slides/scripts/embed-wallie-video.sh`.
- [ ] Test demo robots, cards, one driver per robot, and a busy WiFi environment.
- [ ] Bounded driving area, backup robot, pre-connected phone, batteries, and chargers.
- [ ] Prepare bare and assembled PCBs plus chassis variants.
- [ ] Export a PDF; video and animations do not work in it. Use physical parts or still images as alternatives.
- [ ] Coordinate with Hendrie about assisting; the talk must not depend on his presence.

## 6. Sources

- [Slides and usage instructions](slides/README.md)
- [Reviewer README](slides/scripts/LEESMIJ-review.md) and [package builder](slides/scripts/build-review-package.sh)
- [Project overview](../../README.md), [master plan](../../docs/PLAN.md), and [change log](../../docs/PLAN-CHANGES.md)
- [Event program](../../docs/pers/persbericht-2026-08-31.md)
- [Electronics](../../electronics/README.md), [pin contracts](../../electronics/pin-definitions/), and [components](../../electronics/components/)
- [Firmware v2](../../electronics/firmware/roboktober-controller-v2/README.md) and [design issues](../../docs/WALLIE-DESIGN-ISSUES.md)
- [OpenSCAD and chassis](../../3dprinter/README.md)
- [Public receiver](https://github.com/wallieonline/worc-robot-controller-esp32c3) and [transmitter](https://github.com/wallieonline/worc-remote-controller-esp32)
- The code slides link to specific upstream commits. Preserve attribution and check the license for that version before redistributing.
- [Kickoff livestream](https://www.youtube.com/live/qtz0UkJw4DU)
