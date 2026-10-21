// Antweight chassis v0.3 "design" - functional base = antweight_chassis.scad (v0.2)
// Design language: German/Nordic minimalism with Porsche cues:
// haunches over the wheels, soft nose, round headlights, rear light bar, slatted floor, Braun-style grille.
// Coordinates = PCB-local: x = width, y = length (y = 0 is front), z = 0 is tub bottom.

part = "assembly"; // [assembly, tub, lid, lid_accent, wedge]
$fn = 64;

/* [PCB] */
pcb_w = 78;
pcb_l = 56;
pcb_t = 1.6;
pcb_clear = 0.3;
pcb_holes = [[9.14, 3.14], [68.86, 3.14], [3.95, 44.56], [74.05, 44.56]];
lid_screw_holes = [0, 1];            // indices in pcb_holes clamped by the lid
switch_slot = [[63.65, 38.65], [66.65, 45.65]];
switch_holes = [[65.15, 34.7], [65.15, 49.6]];   // switch mounting holes (pcb-dimensions.DXF)
switch_hole_d = 2;

/* [Components (estimates, measure!)] */
drv_body = [[28.7, 6.5], [47.2, 24.5]];
drv_h = 9;
esp_body = [[28.1, 31.7], [48.4, 49.5]];
esp_h = 4;                           // ~7 when mounted on headers
battery = [40, 20, 6];
battery_pos = [19, 33];

/* [Drive] */
wheel_d = 32;
wheel_w = 10;
wheel_gap = 1;
axle_y = 22;
motor_w = 12;
motor_h = 10;
gearbox_l = 9;
motor_l = 15;
motor_term_l = 1.5;
shaft_d = 3;
shaft_l = 10;
shaft_boss_d = 5;
motor_fit = 0.15;
drv_clear = 0.5;

/* [Chassis] */
H = 26;
wall = 1.6;
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
strap_x = [27, 51];
esp_window = false;
led_enable = false;                  // ESP grille already shows the LED
led_pos = [38, 42];
led_d = 3;

/* [Design] */
nose_r = 6;
haunch_r = 10;
edge_c = 1.0;                        // stepped edge chamfer, top and bottom
layer_h = 0.2;
line_h = 1.0;                        // character line at axle height
line_d = 0.4;
arch_r = 1.5;
accent_d = 0.6;
lightbar = [[12, 54.4], [66, 55.6]];
headlight_pos = [[16, 7], [62, 7]];
headlight_d = 5;
grille_hole_d = 2.5;
grille_pitch = 4;
lid_grille = [[29.5, 33.5], [47.5, 48.5]];
slot_w = 2.5;
slot_pitch = 5;
floor_slots = [[[14, 7.5], [64, 13]], [[31, 16], [47, 28]]];
wedge_hole_d = 3;
wedge_hole_pitch = 5;
wedge_hole_rows = 3;
wedge_tip_l = 6;
wedge_tip_t = 0.6;

/* [Wedge] */
hinge_r = 3;
hinge_w_tub = 6;
hinge_w_wedge = 5;
hinge_gap = 0.4;
hinge_x = [20, 58];
wedge_l = 30;
wedge_t = 2;
wedge_angle = 23;                    // resting angle on the floor for wedge_l = 30

/* [Colours (assembly preview)] */
c_tub = "#D9D9D6";
c_lid = "#F2F2F0";
c_wedge = "#3A3D40";
c_accent = "#C8102E";

// ---------- derived ----------
axle_z = H / 2;
pcb_z = axle_z + motor_h / 2 + 0.5;
tub_h = H - lid_t;
motor_total = gearbox_l + motor_l + motor_term_l;
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
well_y0 = in_y0;
well_y1 = axle_y + wheel_d / 2 + wheel_gap;
front_y = in_y0 - wall;
hinge_y = front_y - hinge_r - 0.5;
width = pcb_w - 2 * ow_x0;
length = in_y1 - in_y0 + 2 * wall;
ch_y0 = axle_y - motor_w / 2 - motor_fit;
ch_y1 = axle_y + motor_w / 2 + motor_fit;
// wheel chord where it crosses the floor / lid plane
arch_half = sqrt(pow(wheel_d / 2, 2) - pow(axle_z - floor_t, 2)) + wheel_gap;
n_chamf = round(edge_c / layer_h);

echo(str("Chassis ", width, " x ", length, " x ", H, " mm (excl. wedge), gearbox face x = ", gf_x));
echo(str("Ground clearance ", (wheel_d - H) / 2, " mm, PCB bottom z = ", pcb_z));

//assert(width <= 100, "chassis wider than 100 mm");
assert(well_y1 + iw_t <= 41 - pcb_clear, "wheel well hits wide PCB part");
assert(gf_x - shaft_l > ow_x1, "motor shaft hits outer wall");
assert(esp_window || pcb_z + pcb_t + esp_h <= tub_h, "ESP32 hits lid: set esp_window = true");
assert(battery_pos[1] - 1 - strap_slot > ch_y1 + 1.2, "strap slot hits motor saddle");

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

module front_block()
    hull() {
        for (x = [ow_x0 + nose_r, pcb_w - ow_x0 - nose_r]) translate([x, front_y + nose_r]) circle(r = nose_r);
        translate([ow_x0, well_y1 + iw_t - 0.01]) square([width, 0.01]);
    }

// closing (haunch fillet) then opening; opening r = pcb_clear + wall keeps PCB clearance intact
module outline_outer()
    offset(r = pcb_clear + wall) offset(delta = -(pcb_clear + wall))
        offset(r = -haunch_r) offset(r = haunch_r) union() {
            front_block();
            offset(r = pcb_clear + wall) pcb_outline();
        }

module outline_inner() offset(delta = -wall) outline_outer();

module well_rect() translate([ow_x1, well_y0]) square([iw_x0 - ow_x1, well_y1 - well_y0]);

module arch_opening() rrect([ow_x1, axle_y - arch_half], [iw_x0, axle_y + arch_half], arch_r);

module grid_holes(r0, r1, pitch, d, stagger = false) {
    nx = floor((r1[0] - r0[0]) / pitch) + 1;
    ny = floor((r1[1] - r0[1]) / pitch) + 1;
    ox = r0[0] + (r1[0] - r0[0] - (nx - 1) * pitch) / 2;
    oy = r0[1] + (r1[1] - r0[1] - (ny - 1) * pitch) / 2;
    for (i = [0 : nx - 1], j = [0 : ny - 1])
        let (sx = (stagger && j % 2 == 1) ? pitch / 2 : 0)
            if (!stagger || i < nx - 1 || j % 2 == 0)
                translate([ox + i * pitch + sx, oy + j * pitch]) circle(d = d);
}

module floor_slots2d()
    for (r = floor_slots) {
        n = floor((r[1][0] - r[0][0] - slot_w) / slot_pitch) + 1;
        ox = r[0][0] + (r[1][0] - r[0][0] - (n - 1) * slot_pitch) / 2;
        for (i = [0 : n - 1])
            slot2d([ox + i * slot_pitch, r[0][1] + slot_w / 2], [ox + i * slot_pitch, r[1][1] - slot_w / 2], slot_w);
    }

module accents2d() {
    slot2d([lightbar[0][0], (lightbar[0][1] + lightbar[1][1]) / 2],
           [lightbar[1][0], (lightbar[0][1] + lightbar[1][1]) / 2], lightbar[1][1] - lightbar[0][1]);
    for (p = headlight_pos) translate(p) circle(d = headlight_d);
}

// ---------- 3D helpers ----------
// stepped chamfer in print-layer increments; bottom = true tapers towards z = 0
module chamfered_extrude(h, bottom = true) {
    for (i = [0 : n_chamf - 1])
        translate([0, 0, bottom ? i * layer_h : h - (i + 1) * layer_h])
            linear_extrude(layer_h + 0.01) offset(delta = -(edge_c - (i + 0.5) * layer_h)) children();
    translate([0, 0, bottom ? edge_c : 0]) linear_extrude(h - edge_c + 0.01) children();
}

module body_solid() chamfered_extrude(tub_h, true) outline_outer();

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
    translate([0, 0, axle_z - line_h / 2]) linear_extrude(line_h) difference() {
        offset(delta = 1) outline_outer();
        offset(delta = -line_d) outline_outer();
    }

module motor_saddles() {
    for (sx = [gf_x + 1, gf_x + gearbox_l + motor_l - 6])
        difference() {
            translate([sx, ch_y0 - 1.2, 0]) cube([5, ch_y1 - ch_y0 + 2.4, axle_z + 3]);
            translate([sx - 1, ch_y0, axle_z - motor_h / 2 - motor_fit]) cube([7, ch_y1 - ch_y0, motor_h + 10]);
        }
}

module tub_knuckle(hx) {
    difference() {
        hull() {
            translate([hx - hinge_w_tub / 2, hinge_y, axle_z]) rotate([0, 90, 0])
                cylinder(r = hinge_r, h = hinge_w_tub);
            // 45 deg underside so it prints without support
            translate([hx - hinge_w_tub / 2, front_y, axle_z - hinge_r - (front_y - hinge_y)])
                cube([hinge_w_tub, wall, 2 * hinge_r + (front_y - hinge_y)]);
        }
        translate([hx - hinge_w_tub / 2 - 1, hinge_y, axle_z]) rotate([0, 90, 0])
            cylinder(d = pilot_d, h = hinge_w_tub + 2);
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
            for (m = [0, 1]) mirror_x(m) motor_saddles();
            for (p = pcb_holes) translate([p[0], p[1], 0]) cylinder(d = standoff_d, h = pcb_z);
            for (hx = hinge_x) tub_knuckle(hx);
        }
        for (m = [0, 1]) mirror_x(m) {
            translate([0, 0, -1]) linear_extrude(floor_t + 2) arch_opening();
            translate([iw_x0 - 1, axle_y, axle_z]) rotate([0, 90, 0]) cylinder(d = shaft_boss_d, h = iw_t + 2);
        }
        for (sx = snap_x) translate([sx, in_y1, tub_h - lid_lip_h / 2]) sphere(r = snap_r);
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
            for (sx = snap_x) translate([sx, in_y1 - lid_fit, tub_h - lid_lip_h / 2]) sphere(r = snap_r - 0.2);
        }
        for (m = [0, 1]) mirror_x(m) {
            translate([0, 0, tub_h - 1]) linear_extrude(lid_t + 2) arch_opening();
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

// ---------- wedge (hinge axis = local x-axis, plate flat on z = -hinge_r) ----------
module wedge() {
    y_root = -(hinge_r + hinge_gap);
    y_tip = y_root - wedge_l;
    ks = [for (hx = hinge_x, s = [-1, 1]) hx + s * (hinge_w_tub / 2 + hinge_gap + hinge_w_wedge / 2)];
    difference() {
        union() {
            intersection() {
                translate([0, 0, -hinge_r]) linear_extrude(wedge_t) hull() {
                    for (x = [ow_x0 + nose_r, pcb_w - ow_x0 - nose_r]) translate([x, y_tip + nose_r]) circle(r = nose_r);
                    translate([ow_x0, y_root - 0.01]) square([width, 0.01]);
                }
                // tapered leading edge
                translate([ow_x0 - 1, 0, 0]) rotate([90, 0, 90]) linear_extrude(width + 2)
                    polygon([[y_tip - 1, -hinge_r], [y_tip - 1, -hinge_r + wedge_tip_t],
                             [y_tip + wedge_tip_l, -hinge_r + wedge_t], [y_root + 1, -hinge_r + wedge_t],
                             [y_root + 1, -hinge_r]]);
            }
            for (kx = ks) hull() {
                translate([kx - hinge_w_wedge / 2, 0, 0]) rotate([0, 90, 0]) cylinder(r = hinge_r, h = hinge_w_wedge);
                translate([kx - hinge_w_wedge / 2, y_root - 1, -hinge_r]) cube([hinge_w_wedge, 1, wedge_t]);
            }
        }
        for (kx = ks) translate([kx - hinge_w_wedge / 2 - 1, 0, 0]) rotate([0, 90, 0])
            cylinder(d = screw_clear_d, h = hinge_w_wedge + 2);
        translate([0, 0, -hinge_r - 1]) linear_extrude(wedge_t + 2)
            grid_holes([ow_x0 + nose_r, y_root - 8 - (wedge_hole_rows - 1) * wedge_hole_pitch],
                       [pcb_w - ow_x0 - nose_r, y_root - 8], wedge_hole_pitch, wedge_hole_d, true);
    }
}

// ---------- ghost components ----------
module ghosts() {
    for (m = [0, 1]) mirror_x(m) {
        color("black", 0.6) translate([wheel_x0, axle_y, axle_z]) rotate([0, 90, 0]) cylinder(d = wheel_d, h = wheel_w);
        color("silver", 0.6) translate([gf_x, ch_y0 + motor_fit, axle_z - motor_h / 2]) cube([motor_total, motor_w, motor_h]);
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
}

module assembly() {
    color(c_tub) tub();
    color(c_lid) lid();
    color(c_accent) lid_accent();
    color(c_wedge) translate([0, hinge_y, axle_z]) rotate([wedge_angle, 0, 0]) wedge();
    ghosts();
}

if (part == "assembly") assembly();
else if (part == "tub") tub();
else if (part == "lid") translate([0, 0, H]) rotate([180, 0, 0]) lid();
else if (part == "lid_accent") translate([0, 0, H]) rotate([180, 0, 0]) lid_accent();
else if (part == "wedge") translate([0, 0, hinge_r]) wedge();
