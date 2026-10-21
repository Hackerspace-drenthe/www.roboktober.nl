// Racer v0.4 "nordic" - v0.3 with a one-piece shell roof: open roll cage, dorsal spine, wing with endplates,
// wheel-pod fairings and shoulders, ribbed underside. The roof prints right side up without supports
// (multi-part AMS: roof, roof_glass, roof_wing). Rear PCB holes: long M2 screws through the roof + spacers.
// Chassis-v0.4 internals (N20 cradles + clamps, PCB, 34x10 wheels).
// Design: calm convex hull plan, sloped nose, faceted glass canopy over the ESP32 ("pilot"),
// one copper character line, front and rear light bars, rear thrusters.
// Driving direction is +y: the axle sits at the rear, the nose tip glides on the floor (slight rake).
// Coordinates = PCB-local: x = width, y = length (y = 0 is the narrow PCB edge = rear), z = 0 is tub bottom.

part = "tub"; // [assembly, tub, roof, roof_glass, roof_wing, spacer, motor_clamp, test_motor]
$fn = 64;

/* [PCB] */
pcb_w = 78;
pcb_l = 56;
pcb_t = 1.6;
pcb_clear = 0.3;
pcb_gap = 0.3;                       // PCB above the motor clamps
pcb_holes = [[9.14, 3.14], [68.86, 3.14], [3.95, 44.56], [74.05, 44.56]];
lid_screw_holes = [0, 1];            // rear PCB holes: M2 x 16 through roof + spacer + PCB into the tub
switch_slot = [[63.65, 38.65], [66.65, 45.65]];
switch_holes = [[65.15, 34.7], [65.15, 49.6]];   // switch mounting holes (pcb-dimensions.DXF)
switch_hole_d = 2;

/* [Components (estimates, measure!)] */
drv_center = [37.93, 15.5];
drv_pins = 6;
drv_w = 18;
drv_len = drv_pins * 2.54 + (22.5 - 8 * 2.54);
drv_body = [drv_center - [drv_len / 2, drv_w / 2], drv_center + [drv_len / 2, drv_w / 2]];
drv_h = 9;
esp_body = [[28.1, 31.7], [48.4, 49.5]];
top_h = 17;                          // ESP32 + DRV8833 (on top) on stacked headers, above the PCB top
battery = [50, 30, 8];
battery_y0 = 60;                     // ahead of the PCB, so the axle can sit low

/* [Drive] */
wheel_d = 34;
wheel_w = 10;
wheel_gap = 1;
ground_clearance = 7.0;                // under the axle; the nose tip touches the floor
axle_y = 19;
// N20 (Pololu micro metal gearmotor): gearbox 12 x 10 x 9, can d12 with flats at 10, 24-26 mm long
gearbox_l = 9;
gb_w = 12;
gb_h = 10;
motor_d = 12;
motor_l = 15;
motor_term_l = 3;
shaft_d = 3;
shaft_l = 10;
shaft_boss_d = 5;
motor_fit = 0.25;                    // per side, FDM slide fit
drv_clear = 0.3;

/* [Motor clamp] */
clamp_squeeze = 0.2;
clamp_wall = 1.6;
clamp_top_t = 1.2;
clamp_screw_x = 12;
clamp_screw_margin = 1;              // motor slot edge to screw hole edge
clamp_boss_d = 4.4;
clamp_head_d = 4.2;
clamp_head_recess = 1.6;

/* [Chassis] */
H = 28;                              // deck height (lid top)
wall = 1.6;
floor_t = 1.2;
lid_t = 1.6;
iw_t = 1.4;
gb_gap = 0.3;
standoff_d = 5;
pilot_d = 1.7;
screw_clear_d = 2.4;
skirt_h = 3;                         // roof walls below the deck; the roof prints right side up on this plane
rim_t = 1.2;                         // tub locating rim inside the roof skirt
rim_h = 2;
lid_fit = 0.2;
snap_x = [25, 53];                   // snaps on the front wall
snap_r = 0.8;
strap_w = 11;
strap_slot = 2;
strap_x = [33, 45];
floor_slots = [[[14, 7.5], [64, 12]], [[31, 16], [47, 28]]];
slot_w = 2.5;
slot_pitch = 5;

/* [Body shape] */
rear_r = 8;
rear_ext = 3;                        // tail behind the PCB, room for the wheels
full_width_y = 54;                   // body keeps the wheel-pod width up to here, then tapers
nose_l = 30;
tip_r = 10;
tip_h = 6;

/* [Canopy] */
cnp_clear = 1;                       // around and above the top components
cnp_wall = 1.6;
cnp_c = 2;                           // 45 degree top chamfer (max 2)
cnp_rear_ext = 3;                    // opening longer to the rear (USB plug room)
cage_bar = 2.5;
cage_sill = 2;
cage_panels = 3;                     // windows along each side

/* [Wing + roof styling] */
wing_gap = 13;                       // wing underside above the deck; the dorsal spine runs at this height
wing_t = 1.6;
wing_chord = 9;
wing_y0 = -3.5;
wing_x = [16, 62];                   // endplate centres = wing tips
fin_t = 2;
endplate_ext = 2;                    // endplates above the wing top
spine_t = 2;
pod_h = 3;                           // wheel-pod fairing, also the ceiling over the wheel pocket
pod_ext = 1.5;                       // fairing beyond the inner well wall
shoulder_foot = [12, -8];            // shoulder front end: x, y relative to the deck front
rib_t = 1.2;
rib_x = [12, 24];                    // mirrored
rib_y = [12, 28, 44, 60, 76];

/* [Design] */
edge_c = 1.0;
layer_h = 0.2;
line_h = 1.0;
line_d = 0.4;
arch_r = 1.5;
accent_d = 0.6;
lightbar_w = 1.2;
front_bar_x = [22, 56];
rear_bar_x = [20, 58];
lightbar_inset = 1.5;
thruster_d = 9;
thruster_dx = 12;
thruster_ring = 1.2;
thruster_depth = 0.8;

/* [Colours] */
palette = "nordic_arctic"; // [nordic_arctic, nordic_night]
two_tone_preview = true;
// [tub lower, tub upper, character line, lid + clamps, glass + lights, wing], each [filament, preview hex]
pal = palette == "nordic_night"
    ? [["dark gray", "#3C3F41"], ["dark gray", "#2E3032"], ["copper", "#B87333"], ["dark gray", "#2E3032"],
       ["transparent", "#E8F0F2"], ["copper", "#B87333"]]
    : [["dark gray", "#3C3F41"], ["silver", "#C4C7CA"], ["copper", "#B87333"], ["silver", "#C4C7CA"],
       ["transparent", "#E8F0F2"], ["dark gray", "#3C3F41"]];

// ---------- derived ----------
axle_z = wheel_d / 2 - ground_clearance;
clamp_top_z = axle_z + gb_h / 2 - clamp_squeeze + clamp_top_t;
pcb_z = clamp_top_z + pcb_gap;
tub_h = H - lid_t - skirt_h;         // split line
deck_z0 = H - lid_t;
ledge_w = lid_fit + rim_t;
motor_total = gearbox_l + motor_l + motor_term_l;
mount_l = gearbox_l + motor_l + motor_fit;
clamp_screw_y = gb_w / 2 + motor_fit + clamp_screw_margin + screw_clear_d / 2;
gf_x = min(drv_body[0][0] - drv_clear - motor_total,
           pcb_w - (drv_body[1][0] + drv_clear + motor_total));
iw_x1 = gf_x - gb_gap;
iw_x0 = iw_x1 - iw_t;
wheel_x0 = iw_x0 - wheel_gap - wheel_w;
ow_x1 = wheel_x0 - wheel_gap;
ow_x0 = ow_x1 - wall;
cx = pcb_w / 2;
width = pcb_w - 2 * ow_x0;
in_y0 = -pcb_clear - rear_ext;
in_y1 = max(pcb_l + pcb_clear, battery_y0 + battery[1] + 1 + strap_slot + 0.5);
rear_y = in_y0 - wall;
deck_y1 = in_y1 + wall;
y_tip = deck_y1 + nose_l;
battery_pos = [cx - battery[0] / 2, battery_y0];
well_y0 = in_y0;
well_y1 = axle_y + wheel_d / 2 + wheel_gap;
arch_half = sqrt(pow(wheel_d / 2, 2) - pow(axle_z - floor_t, 2)) + wheel_gap;
pocket_top = axle_z + wheel_d / 2 + wheel_gap;
wing_z1 = H + wing_gap;
spacer_h = tub_h - pcb_z - pcb_t;
rear_bar_y = in_y0 + lightbar_inset;
n_chamf = round(edge_c / layer_h);
line_z0 = round((axle_z - line_h / 2) / layer_h) * layer_h;
rake = atan(ground_clearance / (y_tip - axle_y));
cnp_open = [[min(esp_body[0][0], drv_body[0][0]) - cnp_clear,
             min(esp_body[0][1], drv_body[0][1]) - cnp_clear - cnp_rear_ext],
            [max(esp_body[1][0], drv_body[1][0]) + cnp_clear, max(esp_body[1][1], drv_body[1][1]) + cnp_clear]];
cnp_zc = pcb_z + pcb_t + top_h + cnp_clear;
cnp_top = cnp_zc + cnp_c + cnp_wall;

echo(str("Racer ", width, " x ", y_tip - rear_y, " x ", H, " mm (deck), canopy top ", cnp_top,
         ", endplate top ", wing_z1 + wing_t + endplate_ext, " mm, split line z = ", tub_h, ", spacers ", spacer_h, " mm"));
echo(str("PCB bottom z = ", pcb_z, ", rake ", rake, " deg (nose tip glides), nose slope ", atan((H - tip_h) / nose_l), " deg"));
echo(str("Colours tub: 0-", line_z0, " ", pal[0][0], " | ", line_z0, "-", line_z0 + line_h, " ", pal[2][0],
         " | ", line_z0 + line_h, "-top ", pal[1][0], "; roof + clamps + spacers ", pal[3][0], "; glass + lights ", pal[4][0],
         "; wing ", pal[5][0]));

assert(well_y1 + iw_t <= 41 - pcb_clear, "wheel well hits wide PCB part");
assert(gf_x - shaft_l > ow_x1, "motor shaft hits outer wall");
assert(cnp_c <= 2, "canopy chamfer exceeds the opening corner radius");
assert(wing_z1 >= cnp_top + 1, "spine/wing too low over the canopy");
assert(pocket_top <= H + pod_h - 1, "pod fairing too thin over the wheel");
assert(spacer_h > 1, "no room for the roof spacers");
assert(min(strap_x) - strap_w / 2 > gf_x + mount_l && max(strap_x) + strap_w / 2 < pcb_w - gf_x - mount_l,
       "strap slot hits motor cradle");
assert(battery_y0 - 1 - strap_slot >= pcb_l + pcb_clear || floor_t + battery[2] < pcb_z - drv_h,
       "battery hits parts under the PCB");
assert(axle_z + wheel_d / 2 <= H, "wheel sticks out above the deck");
assert(battery_pos[0] > 6.5 + 1 && battery_pos[0] + battery[0] < pcb_w - 6.5 - 1, "battery hits rear standoffs");
assert(full_width_y >= well_y1 + iw_t, "wheel pods taper too early");
for (i = lid_screw_holes, x = wing_x)
    assert(abs(pcb_holes[i][0] - x) > fin_t / 2 + 3, "wing endplate blocks a roof screw");

// ---------- 2D helpers ----------
module mirror_x(m) {
    if (m == 0) children();
    else translate([pcb_w, 0, 0]) mirror([1, 0, 0]) children();
}

module rrect(r0, r1, r) translate([r0[0] + r, r0[1] + r]) offset(r = r) square([r1[0] - r0[0] - 2 * r, r1[1] - r0[1] - 2 * r]);

module slot2d(p0, p1, w) hull() { translate(p0) circle(d = w); translate(p1) circle(d = w); }

module pcb_outline()
    polygon([[6, 0], [72, 0], [72, 41], [78, 41], [78, 48], [70, 56],
             [8, 56], [0, 48], [0, 41], [6, 41]]);

// convex plan: full width over the wheel pods, straight taper into a round nose tip
module plan() hull() {
    for (x = [ow_x0 + rear_r, pcb_w - ow_x0 - rear_r]) translate([x, rear_y + rear_r]) circle(r = rear_r);
    translate([ow_x0, full_width_y - 0.01]) square([width, 0.01]);
    translate([cx, y_tip - tip_r]) circle(r = tip_r);
}

module deck_plan() intersection() { plan(); translate([-50, rear_y - 1]) square([200, deck_y1 - rear_y + 1]); }

module outline_inner()
    intersection() {
        offset(delta = -wall) plan();
        translate([-50, in_y0]) square([200, in_y1 - in_y0]);
    }

module well_rect() translate([ow_x1, well_y0]) square([iw_x0 - ow_x1, well_y1 - well_y0]);

module arch_opening(half = arch_half) rrect([ow_x1, axle_y - half], [iw_x0, axle_y + half], arch_r);

module floor_slots2d()
    for (r = floor_slots) {
        n = floor((r[1][0] - r[0][0] - slot_w) / slot_pitch) + 1;
        ox = r[0][0] + (r[1][0] - r[0][0] - (n - 1) * slot_pitch) / 2;
        for (i = [0 : n - 1])
            slot2d([ox + i * slot_pitch, r[0][1] + slot_w / 2], [ox + i * slot_pitch, r[1][1] - slot_w / 2], slot_w);
    }

module accents2d() {
    slot2d([front_bar_x[0], deck_y1 - wall - lightbar_inset], [front_bar_x[1], deck_y1 - wall - lightbar_inset], lightbar_w);
    slot2d([rear_bar_x[0], in_y0 + lightbar_inset], [rear_bar_x[1], in_y0 + lightbar_inset], lightbar_w);
}

module cnp_open2d(grow = 0) rrect(cnp_open[0] - [grow, grow], cnp_open[1] + [grow, grow], 2 + grow);

// left wheel pod incl. its walls (mirror for the right one)
module well_zone2d() translate([-50, -50]) square([iw_x1 + 50, well_y1 + iw_t + 50]);

module switch_win2d() {
    translate(switch_slot[0]) square(switch_slot[1] - switch_slot[0]);
    for (p = switch_holes) translate(p) circle(d = switch_hole_d);
}

module rib_lines2d() {
    for (x = rib_x, m = [0, 1]) mirror_x(m) translate([x - rib_t / 2, -50]) square([rib_t, 300]);
    for (y = rib_y) translate([-50, y - rib_t / 2]) square([200, rib_t]);
}

module pod2d() intersection() {
    offset(delta = -edge_c) plan();
    translate([-50, rear_y - 1]) square([iw_x1 + pod_ext + 50, well_y1 + iw_t + pod_ext - rear_y + 1]);
}

// ---------- 3D helpers ----------
module yz_extrude(x0, w) translate([x0, 0, 0]) rotate([90, 0, 90]) linear_extrude(w) children();

module chamfered_extrude(h, bottom = true) {
    for (i = [0 : n_chamf - 1])
        translate([0, 0, bottom ? i * layer_h : h - (i + 1) * layer_h])
            linear_extrude(layer_h + 0.01) offset(delta = -(edge_c - (i + 0.5) * layer_h)) children();
    translate([0, 0, bottom ? edge_c : 0]) linear_extrude(h - edge_c + 0.01) children();
}

// ---------- motor mount (same as v0.4) ----------
module motor_cavity() {
    w = gb_w + 2 * motor_fit;
    translate([0, axle_y, axle_z]) {
        yz_extrude(iw_x1 - 0.01, gf_x - iw_x1 + gearbox_l + motor_fit + 0.01)
            translate([-w / 2, -gb_h / 2]) square([w, gb_h - clamp_squeeze]);
        yz_extrude(gf_x + gearbox_l, motor_l + motor_term_l + 2) intersection() {
            circle(d = motor_d + 2 * motor_fit);
            translate([-w / 2, -gb_h / 2]) square([w, gb_h - clamp_squeeze]);
        }
    }
}

module clamp_screws(z, h, d) {
    for (s = [-1, 1]) translate([gf_x + clamp_screw_x, axle_y + s * clamp_screw_y, z]) cylinder(d = d, h = h);
}

module clamp_ears(z, h) {
    cw = gb_w + 2 * motor_fit;
    for (s = [-1, 1]) hull() {
        translate([gf_x + clamp_screw_x, axle_y + s * clamp_screw_y, z]) cylinder(d = clamp_boss_d, h = h);
        translate([gf_x + clamp_screw_x - clamp_boss_d / 2, axle_y + (s < 0 ? -cw / 2 - clamp_wall : cw / 2), z])
            cube([clamp_boss_d, clamp_wall, h]);
    }
}

module motor_cradle() {
    x0 = iw_x1 - 0.01;
    l = gf_x + mount_l - x0;
    cw = gb_w + 2 * motor_fit;
    difference() {
        union() {
            translate([x0, axle_y - cw / 2 - clamp_wall, axle_z - gb_h / 2 - 1.2])
                cube([l, cw + 2 * clamp_wall, gb_h / 2 + 1.2]);
            for (s = [-1, 1]) translate([x0, axle_y + (s < 0 ? -cw / 2 - clamp_wall : cw / 2), 0])
                cube([l, clamp_wall, axle_z]);
            for (rx = [x0, gf_x + gearbox_l - 1, gf_x + mount_l - clamp_wall])
                translate([rx, axle_y - cw / 2, 0]) cube([clamp_wall, cw, axle_z]);
            clamp_ears(0, axle_z);
        }
        motor_cavity();
        clamp_screws(axle_z - 8, 9, pilot_d);
    }
}

module motor_clamp() {
    x0 = iw_x1 + 0.2;
    cw = gb_w + 2 * motor_fit;
    difference() {
        union() {
            translate([x0, axle_y - cw / 2 - clamp_wall, axle_z])
                cube([gf_x + mount_l - x0, cw + 2 * clamp_wall, clamp_top_z - axle_z]);
            clamp_ears(axle_z, clamp_top_z - axle_z);
        }
        motor_cavity();
        clamp_screws(axle_z - 1, clamp_top_z - axle_z + 2, screw_clear_d);
        clamp_screws(clamp_top_z - clamp_head_recess, clamp_head_recess + 1, clamp_head_d);
    }
}

// ---------- tub ----------
module well_boxes()
    intersection() {
        chamfered_extrude(tub_h, true) plan();
        for (m = [0, 1]) mirror_x(m) linear_extrude(tub_h) difference() {
            offset(delta = iw_t) well_rect();
            well_rect();
        }
    }

module character_line()
    translate([0, 0, line_z0]) linear_extrude(line_h) difference() {
        offset(delta = 1) plan();
        offset(delta = -line_d) plan();
    }

// solid nose (slicer infill keeps it light), top slopes from the deck to the glide tip
module nose_cap() intersection() {
    translate([0, 0, tub_h - 0.01]) linear_extrude(H - tub_h + 0.01) plan();
    translate([-50, deck_y1 - 0.01, 0]) cube([200, 200, H + 1]);
}

module nose_profile_cut()
    yz_extrude(-50, 200)
        polygon([[deck_y1, H], [y_tip, tip_h], [y_tip + 20, tip_h], [y_tip + 20, H + 20], [deck_y1, H + 20]]);

module thrusters()
    for (s = [-1, 1]) translate([cx + s * thruster_dx, rear_y - 1, axle_z]) rotate([-90, 0, 0]) difference() {
        cylinder(d = thruster_d, h = 1 + thruster_depth);
        translate([0, 0, -1]) cylinder(d = thruster_d - 2 * thruster_ring, h = 3 + thruster_depth);
    }

// locating rim on a small inner collar; the roof skirt slides over it, snaps on the front
module tub_rim() {
    translate([0, 0, tub_h - ledge_w - 0.5]) linear_extrude(ledge_w + 0.5 + 0.01) difference() {
        outline_inner();
        offset(delta = -ledge_w) outline_inner();
        for (m = [0, 1]) mirror_x(m) well_zone2d();
    }
    translate([0, 0, tub_h]) linear_extrude(rim_h) difference() {
        offset(delta = -lid_fit) outline_inner();
        offset(delta = -ledge_w) outline_inner();
        for (m = [0, 1]) mirror_x(m) well_zone2d();
    }
    for (sx = snap_x) translate([sx, in_y1 - lid_fit, tub_h + rim_h / 2]) sphere(r = snap_r - 0.2);
}

module tub() {
    difference() {
        union() {
            difference() {
                union() {
                    difference() {
                        chamfered_extrude(tub_h, true) plan();
                        translate([0, 0, floor_t]) linear_extrude(tub_h) outline_inner();
                    }
                    well_boxes();
                    nose_cap();
                }
                character_line();
            }
            for (m = [0, 1]) mirror_x(m) motor_cradle();
            for (p = pcb_holes) translate([p[0], p[1], 0]) cylinder(d = standoff_d, h = pcb_z);
            tub_rim();
        }
        nose_profile_cut();
        thrusters();
        for (m = [0, 1]) mirror_x(m) {
            translate([0, 0, -1]) linear_extrude(floor_t + 2) arch_opening();
            // open-top shaft slot: motor with wheel fitted drops straight in
            hull() for (z = [axle_z, tub_h + 1])
                translate([iw_x0 - 1, axle_y, z]) rotate([0, 90, 0]) cylinder(d = shaft_boss_d, h = iw_t + 2);
        }
        for (p = pcb_holes) translate([p[0], p[1], pcb_z - 8]) cylinder(d = pilot_d, h = 9);
        for (sx = strap_x, sy = [battery_pos[1] - 1 - strap_slot, battery_pos[1] + battery[1] + 1])
            translate([sx - strap_w / 2, sy, -1]) cube([strap_w, strap_slot, floor_t + 2]);
        translate([0, 0, -1]) linear_extrude(floor_t + 2) floor_slots2d();
    }
}

// ---------- roof: prints right side up on the skirt plane (z = tub_h), multi-part with roof_glass + roof_wing ----------
module roof_cavity2d() difference() {
    outline_inner();
    for (m = [0, 1]) mirror_x(m) well_zone2d();
}

// ribs, opening frames and screw bosses: stand on the bed and carry the bridged deck
module roof_ribs2d() {
    intersection() {
        rib_lines2d();
        difference() {
            offset(delta = -ledge_w - 0.3) outline_inner();
            for (m = [0, 1]) mirror_x(m) offset(delta = 0.3) well_zone2d();
            cnp_open2d();
            switch_win2d();
        }
    }
    difference() { cnp_open2d(rib_t); cnp_open2d(); }
    difference() { offset(delta = rib_t) switch_win2d(); switch_win2d(); }
    for (i = lid_screw_holes) translate(pcb_holes[i]) circle(d = standoff_d);
}

// wheel-pod fairing: vertical outer side, 45 degree inner and front faces
module pod() hull() {
    translate([0, 0, H - 0.01]) linear_extrude(0.01) pod2d();
    translate([0, 0, H + pod_h - 0.01]) linear_extrude(0.01) intersection() {
        offset(delta = -edge_c) plan();
        translate([-50, rear_y - 1]) square([iw_x1 + pod_ext - pod_h + 50, well_y1 + iw_t + pod_ext - pod_h - rear_y + 1]);
    }
}

// shoulder ridge: runs from the pod top forward and fades into the deck
module shoulder() intersection() {
    hull() {
        translate([ow_x0, well_y1 + iw_t + pod_ext - pod_h - 0.01, H]) cube([iw_x1 + pod_ext - pod_h - ow_x0, 0.01, pod_h]);
        translate([shoulder_foot[0], deck_y1 + shoulder_foot[1], H]) cylinder(r = 1, h = 0.01);
    }
    translate([0, 0, H - 1]) linear_extrude(pod_h + 2) offset(delta = -edge_c) plan();
}

module roof() {
    difference() {
        union() {
            translate([0, 0, tub_h]) chamfered_extrude(H - tub_h, false) deck_plan();
            for (m = [0, 1]) mirror_x(m) { pod(); shoulder(); }
        }
        translate([0, 0, tub_h - 1]) linear_extrude(deck_z0 - tub_h + 1) difference() {
            roof_cavity2d();
            roof_ribs2d();
        }
        // wheel pocket, flat bridged ceiling under the pod fairing
        for (m = [0, 1]) mirror_x(m) translate([0, 0, tub_h - 1]) linear_extrude(pocket_top - tub_h + 1) well_rect();
        for (sx = snap_x) translate([sx, in_y1, tub_h + rim_h / 2]) sphere(r = snap_r);
        translate([0, 0, tub_h - 1]) linear_extrude(H - tub_h + 2) { cnp_open2d(); switch_win2d(); }
        for (i = lid_screw_holes) translate([pcb_holes[i][0], pcb_holes[i][1], tub_h - 1]) cylinder(d = screw_clear_d, h = H);
        translate([0, 0, H - accent_d]) linear_extrude(accent_d + 1) accents2d();
    }
}

module canopy_outer() {
    e = cnp_wall * (sqrt(2) - 1);
    hull() {
        translate([0, 0, H]) linear_extrude(cnp_zc + e - H) cnp_open2d(cnp_wall);
        translate([0, 0, cnp_top - 0.01]) linear_extrude(0.01) cnp_open2d(e - cnp_c);
    }
}

// light bars (transparent)
module roof_glass() translate([0, 0, H - accent_d]) linear_extrude(accent_d) accents2d();

// open roll cage: sill, posts, a closed hoop along the 45 degree chamfer, cross bars on top (bridged)
module cage() {
    x0 = cnp_open[0][0];
    x1 = cnp_open[1][0];
    y0 = cnp_open[0][1];
    y1 = cnp_open[1][1];
    pitch = (y1 - y0) / cage_panels;
    b = cage_bar;
    difference() {
        canopy_outer();
        hull() {
            translate([0, 0, H - 1]) linear_extrude(cnp_zc - H + 1) cnp_open2d();
            translate([0, 0, cnp_zc + cnp_c - 0.01]) linear_extrude(0.01) cnp_open2d(-cnp_c);
        }
        for (i = [0 : cage_panels - 1]) {
            ya = y0 + i * pitch + (i == 0 ? b : b / 2);
            yb = y0 + (i + 1) * pitch - (i == cage_panels - 1 ? b : b / 2);
            translate([-50, ya, H + cage_sill]) cube([200, yb - ya, cnp_zc - H - cage_sill]);
            ta = i == 0 ? y0 + cnp_c + b : ya;
            tb = i == cage_panels - 1 ? y1 - cnp_c - b : yb;
            translate([x0 + cnp_c + b, ta, cnp_zc + cnp_c - 0.5]) cube([x1 - x0 - 2 * (cnp_c + b), tb - ta, 10]);
        }
        translate([x0 + b, y0 - 10, H + cage_sill]) cube([x1 - x0 - 2 * b, y1 - y0 + 20, cnp_zc - H - cage_sill]);
    }
}

// wing with endplates + roll cage + dorsal pylon from the cage's rear hoop to the wing; the plate bridges endplate to pylon
module roof_wing() {
    zt = wing_z1 + wing_t;
    x0 = wing_x[0] - fin_t / 2;
    yz_extrude(x0, wing_x[1] + fin_t / 2 - x0)
        polygon([[wing_y0, wing_z1], [wing_y0 + wing_chord - wing_t, wing_z1], [wing_y0 + wing_chord, wing_z1 + wing_t / 2],
                 [wing_y0 + wing_chord, zt], [wing_y0, zt]]);
    for (x = wing_x) yz_extrude(x - fin_t / 2, fin_t)
        polygon([[wing_y0 + 0.5, H], [wing_y0 + wing_chord + 2, H], [wing_y0 + wing_chord, zt + endplate_ext],
                 [wing_y0 - 1, zt + endplate_ext]]);
    cage();
    difference() {
        yz_extrude(cx - spine_t / 2, spine_t)
            polygon([[rear_bar_y + lightbar_w, H], [cnp_open[0][1] + cnp_c + cage_bar, H],
                     [cnp_open[0][1] + cnp_c + cage_bar, cnp_top], [wing_y0 + wing_chord, zt], [wing_y0, zt], [wing_y0, wing_z1]]);
        canopy_outer();
    }
}

// 2x, between PCB top and the roof ribs at the rear PCB holes
module spacer() difference() {
    cylinder(d = standoff_d, h = spacer_h);
    translate([0, 0, -1]) cylinder(d = screw_clear_d, h = spacer_h + 2);
}

// ---------- ghost components ----------
module ghosts() {
    for (m = [0, 1]) mirror_x(m) {
        color("black", 0.6) translate([wheel_x0, axle_y, axle_z]) rotate([0, 90, 0]) cylinder(d = wheel_d, h = wheel_w);
        color("silver", 0.6) translate([0, axle_y, axle_z]) {
            yz_extrude(gf_x, gearbox_l) square([gb_w, gb_h], center = true);
            yz_extrude(gf_x + gearbox_l, motor_l) intersection() {
                circle(d = motor_d);
                square([gb_w, gb_h], center = true);
            }
        }
        color("silver", 0.6) translate([gf_x - shaft_l, axle_y, axle_z]) rotate([0, 90, 0]) cylinder(d = shaft_d, h = shaft_l);
    }
    color("green", 0.5) translate([0, 0, pcb_z]) linear_extrude(pcb_t) pcb_outline();
    color("purple", 0.6) translate([drv_body[0][0], drv_body[0][1], pcb_z - drv_h])
        cube([drv_body[1][0] - drv_body[0][0], drv_body[1][1] - drv_body[0][1], drv_h]);
    color("blue", 0.6) translate([esp_body[0][0], esp_body[0][1], pcb_z + pcb_t])
        cube([esp_body[1][0] - esp_body[0][0], esp_body[1][1] - esp_body[0][1], top_h]);
    color("purple", 0.3) translate([drv_body[0][0], drv_body[0][1], pcb_z + pcb_t])
        cube([drv_body[1][0] - drv_body[0][0], drv_body[1][1] - drv_body[0][1], top_h]);
    color("orange", 0.6) translate([battery_pos[0], battery_pos[1], floor_t]) cube(battery);
}

module tub_zone(z0, z1) intersection() { render() tub(); translate([-50, -100, z0]) cube([200, 300, z1 - z0]); }

module tub_coloured() {
    if (two_tone_preview) {
        color(pal[0][1]) render() tub_zone(-1, line_z0);
        color(pal[2][1]) render() tub_zone(line_z0, line_z0 + line_h);
        color(pal[1][1]) render() tub_zone(line_z0 + line_h, H + 1);
    } else color(pal[1][1]) tub();
}

module assembly() {
    tub_coloured();
    color(pal[3][1]) roof();
    color(pal[3][1]) for (m = [0, 1]) mirror_x(m) motor_clamp();
    color(pal[3][1]) for (i = lid_screw_holes) translate([pcb_holes[i][0], pcb_holes[i][1], pcb_z + pcb_t]) spacer();
    color(pal[4][1], 0.5) roof_glass();
    color(pal[5][1]) roof_wing();
    ghosts();
}

// shown level; on the floor the racer rests on the wheels and the nose tip (rake)
// F6 (CGAL) in OpenSCAD 2021.01 drops all colours; use F5 to see them
// roof, roof_glass and roof_wing share one transform: load them together as one multi-part object
if (part == "assembly") assembly();
else if (part == "tub") { if ($preview) tub_coloured(); else tub(); }
else if (part == "roof") color(pal[3][1]) translate([0, 0, -tub_h]) roof();
else if (part == "roof_glass") color(pal[4][1], 0.7) translate([0, 0, -tub_h]) roof_glass();
else if (part == "roof_wing") color(pal[5][1]) translate([0, 0, -tub_h]) roof_wing();
else if (part == "spacer") color(pal[3][1]) spacer();
else if (part == "motor_clamp") color(pal[3][1]) translate([0, 0, clamp_top_z]) rotate([180, 0, 0]) translate([-gf_x, -axle_y, 0]) motor_clamp();
else if (part == "test_motor")
    intersection() { tub(); translate([iw_x0 - 1, axle_y - clamp_screw_y - clamp_boss_d, -1]) cube([mount_l + 4, 2 * (clamp_screw_y + clamp_boss_d), axle_z + 1]); }
