// Antweight chassis v0.4 "integrated" - one body, embedded invertible wedge nose
// Design language: Braun/Rams + Nordic calm + Porsche cues (haunches, round lights, light bar, slatted deck).
// Nose: V-profile (lip on the floor + mirrored lip on top, apex on the axle/character line),
// ball pocket between two forks: flip, ride over and robot football in both orientations.
// Coordinates = PCB-local: x = width, y = length (y = 0 is front), z = 0 is tub bottom.

part = "lid"; // [assembly, tub, lid, lid_accent, motor_clamp, test_motor, test_front]
$fn = 64;

/* [PCB] */
pcb_w = 78;
pcb_l = 56;
pcb_t = 1.6;
pcb_clear = 0.3;
pcb_gap = 0.3;                       // PCB above the motor clamps
pcb_holes = [[9.14, 3.14], [68.86, 3.14], [3.95, 44.56], [74.05, 44.56]];
lid_screw_holes = [0, 1];            // indices in pcb_holes clamped by the lid
switch_slot = [[63.65, 38.65], [66.65, 45.65]];
switch_holes = [[65.15, 34.7], [65.15, 49.6]];   // switch mounting holes (pcb-dimensions.DXF)
switch_hole_d = 2;

/* [Components (estimates, measure!)] */
// DRV8833 module: 2x6 pins (KiCad pad centre), same width as the ESP32-C3 SuperMini (18 mm);
// length = 6 pins + the same end margin as the SuperMini (22.5 - 8 x 2.54)
drv_center = [37.93, 15.5];
drv_pins = 6;
drv_w = 18;
drv_len = drv_pins * 2.54 + (22.5 - 8 * 2.54);
drv_body = [drv_center - [drv_len / 2, drv_w / 2], drv_center + [drv_len / 2, drv_w / 2]];
drv_h = 9;
esp_body = [[28.1, 31.7], [48.4, 49.5]];
esp_h = 4;                           // ~7 when mounted on headers
battery = [50, 30, 8];               // bay for a bigger 1S LiPo (~600-800 mAh); body length follows
battery_y0 = 33;

/* [Drive] */
wheel_d = 32;
wheel_w = 10;
wheel_gap = 1;
ground_clearance = 1.0;              // lips must nearly touch the floor in both orientations
body_lower = 2;                      // body is this much lower than the nose (lid side)
axle_y = 22;
gearbox_l = 9;
gb_w = 10;                           // gearbox width (horizontal)
gb_h = 9;                            // gearbox height = motor flat-to-flat
motor_d = 10;                        // round motor, flats top/bottom, inside the gearbox contour
motor_l = 15;
motor_term_l = 3;
shaft_d = 3;
shaft_l = 10;
shaft_boss_d = 5;
motor_fit = 0.1;                     // per side, horizontal
drv_clear = 0.3;

/* [Motor clamp] */
clamp_squeeze = 0.2;                 // clamp closes this far onto the motor
clamp_wall = 1.6;
clamp_top_t = 1.2;
clamp_screw_x = 12;                  // from gearbox face
clamp_screw_y = 7;                   // from axle
clamp_boss_d = 4.4;
clamp_head_d = 4.2;
clamp_head_recess = 1.6;

/* [Chassis] */
wall = 1.6;
front_t = 2.4;                       // front wall = apex face, takes the hits
floor_t = 1.2;
lid_t = 1.2;
iw_t = 1.4;
gb_gap = 0.3;
standoff_d = 5;
pilot_d = 1.7;
screw_clear_d = 2.4;
lid_lip_h = 2;
lid_lip_t = 1.2;
lid_fit = 0.2;
snap_x = [25, 53];                   // snaps on the rear wall
snap_r = 0.8;
strap_w = 11;
strap_slot = 2;
strap_x = [33, 45];                  // between the motor cradles
esp_window = true;
led_enable = false;                  // ESP grille already shows the LED
led_pos = [38, 42];
led_d = 3;

/* [Nose (embedded wedge)] */
nose_l = 14;
apex_h = 4;
tip_t = 0.6;
ramp_t = 1.6;
nose_ribs = 10;
rib_t = 1.2;
pocket_enable = true;
ball_d = 40;                         // ping-pong ball
pocket_depth = 12;

/* [Design] */
nose_r = 6;
haunch_r = 10;
edge_c = 1.0;                        // stepped edge chamfer, top and bottom
layer_h = 0.2;
line_h = 1.0;                        // character line at axle height = nose apex
line_d = 0.4;
arch_r = 1.5;
accent_d = 0.6;
lightbar_x = [12, 66];
lightbar_w = 1.2;
lightbar_inset = 1.3;                // from the inner rear wall
headlight_pos = [[16, 7], [62, 7]];
headlight_d = 5;
grille_hole_d = 2.5;
grille_pitch = 4;
lid_grille = [[29.5, 33.5], [47.5, 48.5]];
slot_w = 2.5;
slot_pitch = 5;
floor_slots = [[[14, 7.5], [64, 12]], [[31, 16], [47, 28]]];

/* [Colours] */
// Two-tone body split on the character line: dark base, light top, thin metallic line.
// braun_graphite uses only the filaments currently in the AMS (dark gray, silver, transparent, copper).
palette = "braun_graphite"; // [braun_graphite, porsche_guards_red, nordic_walnut]
two_tone_preview = true;             // exact colour split in F5 (slow first time); false = fast preview

// [tub lower, tub upper + nose top, character line, lid + clamps, lights], each [filament, preview hex]
pal = palette == "porsche_guards_red"
        ? [["dark gray", "#3C3F41"], ["red", "#C8102E"], ["silver", "#C4C7CA"], ["red", "#C8102E"], ["transparent", "#E8F0F2"]]
    : palette == "nordic_walnut"
        ? [["coffee brown", "#6F4E37"], ["silver", "#C4C7CA"], ["gold", "#D4AF37"], ["silver", "#C4C7CA"], ["transparent", "#E8F0F2"]]
        : [["dark gray", "#3C3F41"], ["silver", "#C4C7CA"], ["copper", "#B87333"], ["silver", "#C4C7CA"], ["transparent", "#E8F0F2"]];
lights_alpha = pal[4][0] == "transparent" ? 0.5 : 1;

// ---------- derived ----------
H = wheel_d - 2 * ground_clearance - body_lower;
nose_h = wheel_d - 2 * ground_clearance;
axle_z = nose_h / 2;
clamp_top_z = axle_z + gb_h / 2 - clamp_squeeze + clamp_top_t;
pcb_z = clamp_top_z + pcb_gap;
tub_h = H - lid_t;
motor_total = gearbox_l + motor_l + motor_term_l;
mount_l = gearbox_l + motor_l + motor_fit;
gf_x = min(drv_body[0][0] - drv_clear - motor_total,
           pcb_w - (drv_body[1][0] + drv_clear + motor_total));
iw_x1 = gf_x - gb_gap;
iw_x0 = iw_x1 - iw_t;
wheel_x1 = iw_x0 - wheel_gap;
wheel_x0 = wheel_x1 - wheel_w;
ow_x1 = wheel_x0 - wheel_gap;
ow_x0 = ow_x1 - wall;
in_y0 = -pcb_clear;
in_y1 = pcb_l + pcb_clear;
battery_pos = [pcb_w / 2 - battery[0] / 2, battery_y0];
rear_ext = max(0, battery_y0 + battery[1] + 1 + strap_slot + 0.5 - in_y1);
rear_y = in_y1 + rear_ext;
well_y0 = in_y0;
well_y1 = axle_y + wheel_d / 2 + wheel_gap;
front_y = in_y0 - front_t;
width = pcb_w - 2 * ow_x0;
length = rear_y - front_y + wall;
// wheel chord where it crosses the floor / the (lower) lid
arch_half = sqrt(pow(wheel_d / 2, 2) - pow(axle_z - floor_t, 2)) + wheel_gap;
arch_half_lid = sqrt(pow(wheel_d / 2, 2) - pow(H - lid_t - axle_z, 2)) + wheel_gap;
n_chamf = round(edge_c / layer_h);
// snapped to the layer grid so the slicer can colour it with one height-range change
line_z0 = round((axle_z - line_h / 2) / layer_h) * layer_h;

y_tip = front_y - nose_l;
y_back = front_y + 0.5;              // nose overlap into the front wall (below the lid only)
z_lo = axle_z - apex_h / 2;
slope = (z_lo - tip_t) / (front_y - y_tip);
ramp_angle = atan(slope);
t_v = ramp_t / cos(ramp_angle);
pocket_r = ball_d / 2 + 1;
pocket_c = [pcb_w / 2, y_tip + pocket_depth - pocket_r];

echo(str("Chassis ", width, " x ", length + nose_l, " x ", H, " mm incl. nose (nose ", nose_h, " mm), gearbox face x = ", gf_x));
echo(str("Ground clearance ", ground_clearance, " mm, PCB bottom z = ", pcb_z, ", ramp angle ", ramp_angle, " deg"));
echo(str("Battery bay ", battery, " mm, rear extension ", rear_ext, " mm"));
echo(str("Band colour (slicer height range): z ", line_z0, " - ", line_z0 + line_h, " mm"));
echo(str("Colours tub: 0-", line_z0, " ", pal[0][0], " | ", line_z0, "-", line_z0 + line_h, " ", pal[2][0],
         " | ", line_z0 + line_h, "-top ", pal[1][0], "; lid + clamps ", pal[3][0], "; lights ", pal[4][0]));
echo(str("Motor clamps: 4x M2x10, clamp top z = ", clamp_top_z));

//assert(width <= 100, "chassis wider than 100 mm");
assert(well_y1 + iw_t <= 41 - pcb_clear, "wheel well hits wide PCB part");
assert(gf_x - shaft_l > ow_x1, "motor shaft hits outer wall");
assert(esp_window || pcb_z + pcb_t + esp_h <= tub_h, "ESP32 hits lid: set esp_window = true");
assert(min(strap_x) - strap_w / 2 > gf_x + mount_l && max(strap_x) + strap_w / 2 < pcb_w - gf_x - mount_l,
       "strap slot hits motor cradle");
assert(90 - ramp_angle <= 50, "nose overhang too steep to print without support");
assert(floor_t + battery[2] < pcb_z - 9, "battery hits parts under the PCB");
assert(battery_pos[0] > 6.5 + 1 && battery_pos[0] + battery[0] < pcb_w - 6.5 - 1, "battery hits rear standoffs");

// ---------- 2D helpers ----------
module mirror_x(m) {
    if (m == 0) children();
    else translate([pcb_w, 0, 0]) mirror([1, 0, 0]) children();
}

module rrect(r0, r1, r) translate([r0[0] + r, r0[1] + r]) offset(r = r) square([r1[0] - r0[0] - 2 * r, r1[1] - r0[1] - 2 * r]);

module slot2d(p0, p1, w) hull() { translate(p0) circle(d = w); translate(p1) circle(d = w); }

module switch_win2d() {
    translate(switch_slot[0]) square(switch_slot[1] - switch_slot[0]);
    for (p = switch_holes) translate(p) circle(d = switch_hole_d);
}

module pcb_outline()
    polygon([[6, 0], [72, 0], [72, 41], [78, 41], [78, 48], [70, 56],
             [8, 56], [0, 48], [0, 41], [6, 41]]);

// PCB outline stretched at the rear for the battery bay
module body_plan()
    polygon([[6, 0], [72, 0], [72, 41], [78, 41], [78, 48 + rear_ext], [70, 56 + rear_ext],
             [8, 56 + rear_ext], [0, 48 + rear_ext], [0, 41], [6, 41]]);

// body: haunch fillet (closing), soft corners (opening), square front where the nose attaches
module outline_outer()
    union() {
        offset(r = pcb_clear + wall) offset(delta = -(pcb_clear + wall))
            offset(r = -haunch_r) offset(r = haunch_r) union() {
                translate([ow_x0, front_y]) square([width, well_y1 + iw_t - front_y]);
                offset(r = pcb_clear + wall) body_plan();
            }
        translate([ow_x0, front_y]) square([width, 4]);
    }

module outline_inner()
    intersection() {
        offset(delta = -wall) outline_outer();
        translate([-50, in_y0]) square([200, 200]);
    }

module well_rect() translate([ow_x1, well_y0]) square([iw_x0 - ow_x1, well_y1 - well_y0]);

module arch_opening(half = arch_half) rrect([ow_x1, axle_y - half], [iw_x0, axle_y + half], arch_r);

module grid_holes(r0, r1, pitch, d) {
    nx = floor((r1[0] - r0[0]) / pitch) + 1;
    ny = floor((r1[1] - r0[1]) / pitch) + 1;
    ox = r0[0] + (r1[0] - r0[0] - (nx - 1) * pitch) / 2;
    oy = r0[1] + (r1[1] - r0[1] - (ny - 1) * pitch) / 2;
    for (i = [0 : nx - 1], j = [0 : ny - 1]) translate([ox + i * pitch, oy + j * pitch]) circle(d = d);
}

module floor_slots2d()
    for (r = floor_slots) {
        n = floor((r[1][0] - r[0][0] - slot_w) / slot_pitch) + 1;
        ox = r[0][0] + (r[1][0] - r[0][0] - (n - 1) * slot_pitch) / 2;
        for (i = [0 : n - 1])
            slot2d([ox + i * slot_pitch, r[0][1] + slot_w / 2], [ox + i * slot_pitch, r[1][1] - slot_w / 2], slot_w);
    }

module accents2d() {
    slot2d([lightbar_x[0], rear_y - lightbar_inset], [lightbar_x[1], rear_y - lightbar_inset], lightbar_w);
    for (p = headlight_pos) translate(p) circle(d = headlight_d);
}

module nose_plan()
    offset(r = 1) offset(delta = -1) difference() {
        hull() {
            for (x = [ow_x0 + nose_r, pcb_w - ow_x0 - nose_r]) translate([x, y_tip + nose_r]) circle(r = nose_r);
            translate([ow_x0, y_back - 0.01]) square([width, 0.01]);
        }
        if (pocket_enable) translate(pocket_c) circle(r = pocket_r);
    }

// side profiles in (y, z), lower half; the upper half is mirrored about the axle
module lower_ramp2d()
    polygon([[y_tip, 0], [y_tip, tip_t], [y_back, z_lo + slope * (y_back - front_y)],
             [y_back, z_lo + slope * (y_back - front_y) - t_v], [y_tip + (t_v - tip_t) / slope, 0]]);

module lower_solid2d()
    polygon([[y_tip, 0], [y_tip, tip_t], [y_back, z_lo + slope * (y_back - front_y)], [y_back, 0]]);

// ---------- 3D helpers ----------
module yz_extrude(x0, w) translate([x0, 0, 0]) rotate([90, 0, 90]) linear_extrude(w) children();

module z_mirror(m) {
    if (m == 0) children();
    else translate([0, 0, nose_h]) mirror([0, 0, 1]) children();
}

// stepped chamfer in print-layer increments; bottom = true tapers towards z = 0
module chamfered_extrude(h, bottom = true) {
    for (i = [0 : n_chamf - 1])
        translate([0, 0, bottom ? i * layer_h : h - (i + 1) * layer_h])
            linear_extrude(layer_h + 0.01) offset(delta = -(edge_c - (i + 0.5) * layer_h)) children();
    translate([0, 0, bottom ? edge_c : 0]) linear_extrude(h - edge_c + 0.01) children();
}

module body_solid() chamfered_extrude(tub_h, true) outline_outer();

// ---------- nose ----------
module nose() {
    rib_xs = [for (i = [0 : nose_ribs - 1]) ow_x0 + rib_t / 2 + i * (width - rib_t) / (nose_ribs - 1)];
    intersection() {
        union()
            for (m = [0, 1]) z_mirror(m) {
                yz_extrude(ow_x0 - 1, width + 2) lower_ramp2d();
                for (x = rib_xs) yz_extrude(x - rib_t / 2, rib_t) lower_solid2d();
            }
        linear_extrude(nose_h) nose_plan();
        // only overlap the front wall below the lid
        union() {
            translate([-50, y_tip - 1, -1]) cube([200, front_y - y_tip + 1, nose_h + 2]);
            translate([-50, y_tip - 1, -1]) cube([200, y_back - y_tip + 1, tub_h + 1]);
        }
        // upper ribs slope from the nose tip down to the lower lid
        yz_extrude(-50, 200) polygon([[y_tip - 1, -1], [y_tip - 1, nose_h], [y_tip, nose_h],
                                      [front_y, H], [y_back + 1, H], [y_back + 1, -1]]);
    }
}

// ---------- tub ----------
module well_boxes()
    intersection() {
        body_solid();
        for (m = [0, 1]) mirror_x(m) linear_extrude(tub_h) difference() {
            offset(delta = iw_t) well_rect();
            well_rect();
        }
    }

module character_line()
    translate([0, 0, line_z0]) linear_extrude(line_h) difference() {
        offset(delta = 1) outline_outer();
        offset(delta = -line_d) outline_outer();
    }

// motor pocket shared by cradle and clamp: rectangular gearbox, round motor with flats.
// The gearbox corners cannot pass into the round section, so the motor is locked axially.
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

module motor_cradle() {
    x0 = iw_x1 - 0.01;
    l = gf_x + mount_l - x0;
    cw = gb_w + 2 * motor_fit;
    difference() {
        union() {
            // U-bed under the motor
            translate([x0, axle_y - cw / 2 - clamp_wall, axle_z - gb_h / 2 - 1.2])
                cube([l, cw + 2 * clamp_wall, gb_h / 2 + 1.2]);
            for (s = [-1, 1]) translate([x0, axle_y + (s < 0 ? -cw / 2 - clamp_wall : cw / 2), 0])
                cube([l, clamp_wall, axle_z]);
            for (rx = [x0, gf_x + gearbox_l - 1, gf_x + mount_l - clamp_wall])
                translate([rx, axle_y - cw / 2, 0]) cube([clamp_wall, cw, axle_z]);
            clamp_screws(0, axle_z, clamp_boss_d);
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
            clamp_screws(axle_z, clamp_top_z - axle_z, clamp_boss_d);
        }
        motor_cavity();
        clamp_screws(axle_z - 1, clamp_top_z - axle_z + 2, screw_clear_d);
        clamp_screws(clamp_top_z - clamp_head_recess, clamp_head_recess + 1, clamp_head_d);
    }
}

module tub() {
    difference() {
        union() {
            difference() {
                union() {
                    difference() {
                        body_solid();
                        translate([0, 0, floor_t]) linear_extrude(tub_h) outline_inner();
                    }
                    well_boxes();
                }
                character_line();
            }
            nose();
            for (m = [0, 1]) mirror_x(m) motor_cradle();
            for (p = pcb_holes) translate([p[0], p[1], 0]) cylinder(d = standoff_d, h = pcb_z);
        }
        for (m = [0, 1]) mirror_x(m) {
            translate([0, 0, -1]) linear_extrude(floor_t + 2) arch_opening();
            translate([iw_x0 - 1, axle_y, axle_z]) rotate([0, 90, 0]) cylinder(d = shaft_boss_d, h = iw_t + 2);
        }
        for (sx = snap_x) translate([sx, rear_y, tub_h - lid_lip_h / 2]) sphere(r = snap_r);
        for (p = pcb_holes) translate([p[0], p[1], pcb_z - 8]) cylinder(d = pilot_d, h = 9);
        for (sx = strap_x, sy = [battery_pos[1] - 1 - strap_slot, battery_pos[1] + battery[1] + 1])
            translate([sx - strap_w / 2, sy, -1]) cube([strap_w, strap_slot, floor_t + 2]);
        translate([0, 0, -1]) linear_extrude(floor_t + 2) floor_slots2d();
    }
}

// ---------- lid ----------
module lid() {
    difference() {
        union() {
            translate([0, 0, tub_h]) chamfered_extrude(lid_t, false) outline_outer();
            translate([0, 0, tub_h - lid_lip_h]) linear_extrude(lid_lip_h + 0.01) difference() {
                offset(delta = -lid_fit) outline_inner();
                offset(delta = -lid_fit - lid_lip_t) outline_inner();
            }
            for (i = lid_screw_holes) translate([pcb_holes[i][0], pcb_holes[i][1], pcb_z + pcb_t])
                cylinder(d = standoff_d, h = tub_h - pcb_z - pcb_t + 0.01);
            for (sx = snap_x) translate([sx, rear_y - lid_fit, tub_h - lid_lip_h / 2]) sphere(r = snap_r - 0.2);
        }
        for (m = [0, 1]) mirror_x(m) {
            translate([0, 0, tub_h - 1]) linear_extrude(lid_t + 2) arch_opening(arch_half_lid);
            translate([ow_x1 - 1, well_y0 - 2, tub_h - lid_lip_h - 1])
                cube([iw_x1 + lid_fit - ow_x1 + 1, well_y1 + iw_t + lid_fit - well_y0 + 2, lid_lip_h + 1]);
        }
        for (i = lid_screw_holes) translate([pcb_holes[i][0], pcb_holes[i][1], pcb_z])
            cylinder(d = screw_clear_d, h = H);
        translate([0, 0, tub_h - 1]) linear_extrude(lid_t + 2) {
            switch_win2d();
            grid_holes(lid_grille[0], lid_grille[1], grille_pitch, grille_hole_d);
            if (led_enable) translate(led_pos) circle(d = led_d);
            if (esp_window) rrect(esp_body[0] - [1, 1], esp_body[1] + [1, 1], 2);
        }
        translate([0, 0, H - accent_d]) linear_extrude(accent_d + 1) accents2d();
    }
}

// inlay for the multicolour printer: load as extra part of the lid in the slicer
module lid_accent() translate([0, 0, H - accent_d]) linear_extrude(accent_d) accents2d();

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
            yz_extrude(gf_x + gearbox_l + motor_l, motor_term_l)
                for (s = [-1, 1]) translate([s * 2.5, 0]) square([1.5, 3], center = true);
        }
        color("silver", 0.6) translate([gf_x - shaft_l, axle_y, axle_z]) rotate([0, 90, 0]) cylinder(d = shaft_d, h = shaft_l);
    }
    color("green", 0.5) translate([0, 0, pcb_z]) linear_extrude(pcb_t) difference() {
        pcb_outline();
        for (p = pcb_holes) translate(p) circle(d = 2);
    }
    color("purple", 0.6) translate([drv_body[0][0], drv_body[0][1], pcb_z - drv_h])
        cube([drv_body[1][0] - drv_body[0][0], drv_body[1][1] - drv_body[0][1], drv_h]);
    color("blue", 0.6) translate([esp_body[0][0], esp_body[0][1], pcb_z + pcb_t])
        cube([esp_body[1][0] - esp_body[0][0], esp_body[1][1] - esp_body[0][1], esp_h]);
    color("orange", 0.6) translate([battery_pos[0], battery_pos[1], floor_t]) cube(battery);
    if (pocket_enable) color("white", 0.8) translate([pocket_c[0], pocket_c[1], ball_d / 2 - ground_clearance]) sphere(d = ball_d);
}

module tub_zone(z0, z1) intersection() { render() tub(); translate([-50, -100, z0]) cube([200, 300, z1 - z0]); }

module assembly() {
    if (two_tone_preview) {
        color(pal[0][1]) render() tub_zone(-1, line_z0);
        color(pal[2][1]) render() tub_zone(line_z0, line_z0 + line_h);
        color(pal[1][1]) render() tub_zone(line_z0 + line_h, nose_h + 1);
    } else {
        color(pal[1][1]) tub();
        color(pal[2][1]) translate([0, 0, line_z0 + 0.01]) linear_extrude(line_h - 0.02) difference() {
            offset(delta = -line_d + 0.05) outline_outer();
            offset(delta = -wall) outline_outer();
        }
    }
    color(pal[3][1]) lid();
    color(pal[3][1]) for (m = [0, 1]) mirror_x(m) motor_clamp();
    color(pal[4][1], lights_alpha) lid_accent();
    ghosts();
}

if (part == "assembly") assembly();
else if (part == "tub") tub();
else if (part == "lid") translate([0, 0, H]) rotate([180, 0, 0]) lid();
else if (part == "lid_accent") translate([0, 0, H]) rotate([180, 0, 0]) lid_accent();
// print 2x; the same part fits left and right (rotate 180 deg)
else if (part == "motor_clamp") translate([0, 0, clamp_top_z]) rotate([180, 0, 0]) translate([-gf_x, -axle_y, 0]) motor_clamp();
// quick fit tests: left motor cradle with wheel-well wall / front section with nose and both cradles
else if (part == "test_motor")
    intersection() { tub(); translate([iw_x0 - 1, axle_y - clamp_screw_y - clamp_boss_d, -1]) cube([mount_l + 4, 2 * (clamp_screw_y + clamp_boss_d), axle_z + 1]); }
else if (part == "test_front")
    intersection() { tub(); translate([-50, y_tip - 1, -1]) cube([200, axle_y + clamp_screw_y + clamp_boss_d - y_tip + 1, nose_h + 2]); }
