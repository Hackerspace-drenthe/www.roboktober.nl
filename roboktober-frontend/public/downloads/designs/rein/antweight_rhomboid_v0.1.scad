// Antweight "Rhomboid" v0.1 - minimal, light, invertible 2WD pusher (passive).
// Side view is a parallelogram, point-symmetric about the axle: the front ramps up from the floor,
// the rear ramps down to the floor. Flipped over, the rear lip becomes the floor-side wedge.
// 1S 300 mAh LiPo between the motors under the PCB; ESP32-C3 and DRV8833 breakouts soldered flat to the PCB.
// 34x10 wheels + 2x N20 (Pololu); wheels stick out top and bottom.
// Tub/lid split at the PCB top: the lid clamps the PCB, 4 screws through lid + PCB into the tub.
// Coordinates = PCB-local: x = width, y = length (y = 0 = narrow PCB edge = front), z = 0 = tub bottom.
// Driving direction is -y.

part = "assembly"; // [assembly, tub, lid, lid_glass, motor_clamp, test_motor]
$fn = 64;

/* [PCB] */
pcb_w = 78;
pcb_l = 56;
pcb_t = 1.6;
pcb_clear = 0.3;
pcb_gap = 0.3;                       // PCB above the motor clamps
pcb_holes = [[9.14, 3.14], [68.86, 3.14], [3.95, 44.56], [74.05, 44.56]];
switch_slot = [[63.65, 38.65], [66.65, 45.65]];
switch_holes = [[65.15, 34.7], [65.15, 49.6]];   // switch mounting holes (pcb-dimensions.DXF)
switch_hole_d = 2;

/* [Components (estimates, measure!)] */
drv_center = [37.93, 15.5];          // DRV8833 breakout soldered flat under the PCB
drv_pins = 6;
drv_w = 18;
drv_len = drv_pins * 2.54 + (22.5 - 8 * 2.54);
drv_body = [drv_center - [drv_len / 2, drv_w / 2], drv_center + [drv_len / 2, drv_w / 2]];
drv_h = 3.5;
esp_body = [[28.1, 31.7], [48.4, 49.5]];
esp_h = 4.5;                         // ESP32-C3 SuperMini soldered flat on top, incl. USB-C
jst_h = 6;                           // JST-PH headers under the PCB
battery = [20, 30, 6.5];             // 1S 300 mAh (602030), lengthwise between the motors
battery_y0 = 5;

/* [Drive] */
wheel_d = 34;
wheel_w = 10;
wheel_gap = 1;
ground_clearance = 1.0;              // body to floor, both orientations
axle_y = 21;                         // also the centre of symmetry
// N20 (Pololu micro metal gearmotor): gearbox 12 x 10 x 9, can d12 with flats at 10
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
clamp_screw_margin = 1;
clamp_boss_d = 4.4;
clamp_head_d = 4.2;
clamp_head_recess = 1.6;

/* [Shell] */
ramp_a = 45;                         // both ramps; 45 degrees prints without support (tub and lid)
ramp_t = 1.6;
floor_t = 1.0;
top_t = 1.2;
side_t = 1.6;
iw_t = 1.4;
gb_gap = 0.3;
standoff_d = 5;
pilot_d = 1.7;
screw_clear_d = 2.4;
screw_head_d = 4.2;
screw_head_recess = 1.2;
strap_w = 11;
strap_slot = 2;
strap_gap = 0.5;                     // battery to strap slot
arch_r = 1.5;
win_margin = 1;                      // ESP window around the module
chev_w = 2.5;
chev_pitch = 5.5;
chev_rise = 0.5;
floor_chevrons = [[[4, -21], [74, -1]]];

/* [Colours] */
palette = "graphite_silver"; // [graphite_silver, graphite_mono, graphite_copper]
// [tub, lid + clamps, window], each [filament, preview hex]
pal = palette == "graphite_mono"
        ? [["dark gray", "#3C3F41"], ["dark gray", "#2E3032"], ["transparent", "#E8F0F2"]]
    : palette == "graphite_copper"
        ? [["dark gray", "#3C3F41"], ["copper", "#B87333"], ["transparent", "#E8F0F2"]]
        : [["dark gray", "#3C3F41"], ["silver", "#C4C7CA"], ["transparent", "#E8F0F2"]];

// ---------- derived ----------
H = wheel_d - 2 * ground_clearance;
axle_z = H / 2;
clamp_top_z = axle_z + gb_h / 2 - clamp_squeeze + clamp_top_t;
pcb_z = clamp_top_z + pcb_gap;
tub_h = pcb_z + pcb_t;
motor_total = gearbox_l + motor_l + motor_term_l;
mount_l = gearbox_l + motor_l + motor_fit;
cw = gb_w + 2 * motor_fit;
clamp_screw_y = gb_w / 2 + motor_fit + clamp_screw_margin + screw_clear_d / 2;
gf_x = min(drv_body[0][0] - drv_clear - motor_total,
           pcb_w - (drv_body[1][0] + drv_clear + motor_total));
iw_x1 = gf_x - gb_gap;
iw_x0 = iw_x1 - iw_t;
wheel_x0 = iw_x0 - wheel_gap - wheel_w;
ow_x1 = wheel_x0 - wheel_gap;
ow_x0 = ow_x1 - side_t;
cx = pcb_w / 2;
width = pcb_w - 2 * ow_x0;
in_y0 = -pcb_clear;
battery_pos = [cx - battery[0] / 2, battery_y0];
well_y0 = axle_y - wheel_d / 2 - wheel_gap;
well_y1 = axle_y + wheel_d / 2 + wheel_gap;
arch_half = sqrt(pow(wheel_d / 2, 2) - pow(axle_z - floor_t, 2)) + wheel_gap;
arch_half_top = sqrt(pow(wheel_d / 2, 2) - pow(H - top_t - axle_z, 2)) + wheel_gap;

cot = 1 / tan(ramp_a);
run = H * cot;
dt = ramp_t / sin(ramp_a);           // horizontal ramp thickness
// rear foot y: inner rear face clears the PCB, the battery and the wheel
y_r = max(pcb_l + pcb_clear - pcb_z * cot + dt,
          battery_y0 + battery[1] + 0.3 - floor_t * cot + dt,
          axle_y - axle_z * cot + dt + (wheel_d / 2 + wheel_gap) / sin(ramp_a));
y_f = 2 * axle_y - y_r;              // front shoulder, point symmetry about the axle
y_lip = y_f - run;
y_tail = y_r + run;
function inner_front_y(z) = y_lip + z * cot + dt;
function outer_top_z(y) = min(H, (y - y_lip) / cot);

echo(str("Rhomboid ", width, " x ", y_tail - y_lip, " x ", H, " mm, ramps ", ramp_a, " deg, lips at y ", y_lip,
         " / ", y_tail, ", wheels +-", ground_clearance, " mm"));
echo(str("PCB bottom z = ", pcb_z, ", split z = ", tub_h, ", ESP top ", tub_h + esp_h, " / lid underside ", H - top_t,
         ", PCB front clearance under the ramp ", in_y0 - inner_front_y(tub_h), " mm"));
echo(str("Colours: tub ", pal[0][0], "; lid + clamps ", pal[1][0], "; window ", pal[2][0]));
echo(str("Screws: 2x M2x12 front + 2x M2x14 rear (lid + PCB into tub), 4x M2x10 motor clamps"));

assert(well_y1 + iw_t <= 41 - pcb_clear, "wheel well hits wide PCB part");
assert(gf_x - shaft_l > ow_x1, "motor shaft hits outer wall");
assert(inner_front_y(tub_h) <= in_y0, "PCB front edge hits the front ramp");
assert(tub_h + esp_h <= H - top_t - 0.5, "ESP32 hits the lid");
assert(floor_t + battery[2] < pcb_z - max(drv_h, jst_h) - 0.5, "battery hits parts under the PCB");
assert(battery_pos[0] - strap_gap - strap_slot > gf_x + mount_l && battery_pos[0] + battery[0] + strap_gap + strap_slot < pcb_w - gf_x - mount_l,
       "battery / strap slot hits motor cradle");
assert(battery_y0 >= inner_front_y(floor_t) + 0.3, "battery hits the front ramp");

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

// side profile (y, z); e extends the ramp lines beyond the floor and the top
module side2d(e = 0)
    polygon([[y_lip - e * cot, -e], [y_r - e * cot, -e], [y_tail + e * cot, H + e], [y_f + e * cot, H + e]]);

module inner2d() intersection() {
    offset(delta = -ramp_t) side2d(10);
    translate([-200, floor_t]) square([400, H - top_t - floor_t]);
}

module well_rect() translate([ow_x1, well_y0]) square([iw_x0 - ow_x1, well_y1 - well_y0]);

module arch_opening(half) rrect([ow_x1, axle_y - half], [iw_x0, axle_y + half], arch_r);

module chevrons2d(r0, r1) intersection() {
    translate(r0 + [chev_w / 2, chev_w / 2]) square(r1 - r0 - [chev_w, chev_w]);
    mx = (r0[0] + r1[0]) / 2;
    rise = (mx - r0[0]) * chev_rise;
    for (y = [r0[1] - rise : chev_pitch : r1[1]]) {
        slot2d([r0[0], y + rise], [mx, y], chev_w);
        slot2d([mx, y], [r1[0], y + rise], chev_w);
    }
}

module esp_win2d() rrect(esp_body[0] - [win_margin, win_margin], esp_body[1] + [win_margin, win_margin], 2);

module switch_win2d() {
    translate(switch_slot[0]) square(switch_slot[1] - switch_slot[0]);
    for (p = switch_holes) translate(p) circle(d = switch_hole_d);
}

module strap_slots2d()
    for (x = [battery_pos[0] - strap_gap - strap_slot, battery_pos[0] + battery[0] + strap_gap])
        translate([x, battery_y0 + battery[1] / 2 - strap_w / 2]) square([strap_slot, strap_w]);

// ---------- 3D helpers ----------
module yz_extrude(x0, w) translate([x0, 0, 0]) rotate([90, 0, 90]) linear_extrude(w) children();

module outer3d() yz_extrude(ow_x0, width) side2d();

module slab(z0, z1) translate([-200, -200, z0]) cube([400, 400, z1 - z0]);

module shell() difference() {
    outer3d();
    yz_extrude(ow_x0 + side_t, width - 2 * side_t) inner2d();
}

module well_rings() for (m = [0, 1]) mirror_x(m) linear_extrude(H) difference() {
    offset(delta = iw_t) well_rect();
    well_rect();
}

// ---------- motor mount ----------
module motor_cavity() {
    translate([0, axle_y, axle_z]) {
        yz_extrude(iw_x1 - 0.01, gf_x - iw_x1 + gearbox_l + motor_fit + 0.01)
            translate([-cw / 2, -gb_h / 2]) square([cw, gb_h - clamp_squeeze]);
        yz_extrude(gf_x + gearbox_l, motor_l + motor_term_l + 2) intersection() {
            circle(d = motor_d + 2 * motor_fit);
            translate([-cw / 2, -gb_h / 2]) square([cw, gb_h - clamp_squeeze]);
        }
    }
}

module clamp_screws(z, h, d) {
    for (s = [-1, 1]) translate([gf_x + clamp_screw_x, axle_y + s * clamp_screw_y, z]) cylinder(d = d, h = h);
}

module clamp_ears(z, h) {
    for (s = [-1, 1]) hull() {
        translate([gf_x + clamp_screw_x, axle_y + s * clamp_screw_y, z]) cylinder(d = clamp_boss_d, h = h);
        translate([gf_x + clamp_screw_x - clamp_boss_d / 2, axle_y + (s < 0 ? -cw / 2 - clamp_wall : cw / 2), z])
            cube([clamp_boss_d, clamp_wall, h]);
    }
}

module motor_cradle() {
    x0 = iw_x1 - 0.01;
    l = gf_x + mount_l - x0;
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
module tub() {
    difference() {
        union() {
            intersection() {
                union() {
                    shell();
                    well_rings();
                    // rear standoffs grow out of the rear ramp
                    for (p = pcb_holes) translate([p[0], p[1], 0]) cylinder(d = standoff_d, h = pcb_z);
                }
                outer3d();
                slab(0, tub_h);
            }
            for (m = [0, 1]) mirror_x(m) motor_cradle();
        }
        for (m = [0, 1]) mirror_x(m) {
            translate([0, 0, -1]) linear_extrude(floor_t + 2) arch_opening(arch_half);
            // open-top shaft slot: motor with wheel fitted drops straight in
            hull() for (z = [axle_z, tub_h + 1])
                translate([iw_x0 - 1, axle_y, z]) rotate([0, 90, 0]) cylinder(d = shaft_boss_d, h = iw_t + 2);
        }
        for (p = pcb_holes) translate([p[0], p[1], pcb_z - 8]) cylinder(d = pilot_d, h = 9);
        translate([0, 0, -1]) linear_extrude(floor_t + 2) {
            strap_slots2d();
            for (r = floor_chevrons) chevrons2d(r[0], r[1]);
        }
    }
}

// ---------- lid (prints upside down) ----------
module lid() {
    difference() {
        intersection() {
            union() {
                shell();
                well_rings();
                for (p = pcb_holes) translate([p[0], p[1], tub_h]) cylinder(d = standoff_d, h = H);
            }
            outer3d();
            slab(tub_h, H);
        }
        for (m = [0, 1]) mirror_x(m) translate([0, 0, H - top_t - 1]) linear_extrude(top_t + 2) arch_opening(arch_half_top);
        for (p = pcb_holes) {
            translate([p[0], p[1], tub_h - 1]) cylinder(d = screw_clear_d, h = H);
            translate([p[0], p[1], outer_top_z(p[1] - screw_head_d / 2) - screw_head_recess]) cylinder(d = screw_head_d, h = H);
        }
        translate([0, 0, H - top_t - 1]) linear_extrude(top_t + 2) {
            esp_win2d();
            switch_win2d();
        }
    }
}

// transparent window over the ESP32 (LED visible): load as extra part of the lid (same transform)
module lid_glass() translate([0, 0, H - top_t]) linear_extrude(top_t) esp_win2d();

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
    color("blue", 0.6) translate([esp_body[0][0], esp_body[0][1], tub_h])
        cube([esp_body[1][0] - esp_body[0][0], esp_body[1][1] - esp_body[0][1], esp_h]);
    color("orange", 0.6) translate([battery_pos[0], battery_pos[1], floor_t]) cube(battery);
}

module assembly() {
    color(pal[0][1]) render() tub();
    color(pal[1][1]) render() lid();
    color(pal[2][1], 0.6) lid_glass();
    color(pal[1][1]) for (m = [0, 1]) mirror_x(m) motor_clamp();
    ghosts();
}

// lid + lid_glass share one transform: load them together as one multi-part object
if (part == "assembly") assembly();
else if (part == "tub") color(pal[0][1]) tub();
else if (part == "lid") color(pal[1][1]) translate([0, 0, H]) rotate([180, 0, 0]) lid();
else if (part == "lid_glass") color(pal[2][1]) translate([0, 0, H]) rotate([180, 0, 0]) lid_glass();
// print 2x; the same part fits left and right (rotate 180 deg)
else if (part == "motor_clamp") color(pal[1][1]) translate([0, 0, clamp_top_z]) rotate([180, 0, 0]) translate([-gf_x, -axle_y, 0]) motor_clamp();
else if (part == "test_motor")
    intersection() { tub(); translate([iw_x0 - 1, axle_y - clamp_screw_y - clamp_boss_d, -1]) cube([mount_l + 4, 2 * (clamp_screw_y + clamp_boss_d), axle_z + 1]); }
