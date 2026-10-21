// Antweight "Disc Mini" v0.1 - smallest round variant of Disc v0.1 (same internals).
// The circle is centred on the PCB (smallest circle around it) instead of the axle, and the conical ramp
// is steeper (60 deg), so the diameter is set by the PCB corners. No ribs: the steep cone carries itself.
// Wheels poke through floor, top and ramp sides (drives inverted, no wedge then).
// 1S 300 mAh LiPo between the motors, ESP32-C3 + DRV8833 soldered flat; tub/lid split at the PCB top.
// Coordinates = PCB-local: x = width, y = length (y = 0 = narrow PCB edge), z = 0 = tub bottom.
include <disc_geometry.scad>

part = "assembly"; // [assembly, tub, lid, lid_glass, motor_clamp, test_motor]
$fn = 96;

/* [PCB] */
pcb_w = 78;
pcb_l = 56;
pcb_t = 1.6;
pcb_clear = 0.3;
pcb_gap = 0.3;                       // PCB above the motor clamps
pcb_holes = [[9.14, 3.14], [68.86, 3.14], [3.95, 44.56], [74.05, 44.56]];
pcb_pts = [[6, 0], [72, 0], [72, 41], [78, 41], [78, 48], [70, 56], [8, 56], [0, 48], [0, 41], [6, 41]];
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

/* [Drive] */
wheel_d = 34;
wheel_w = 10;
wheel_gap = 1;
ground_clearance = 1.0;              // body to floor, both orientations
axle_y = 21;
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
disc_cy = 28.5;                      // disc centre y: smallest circle around the PCB
ramp_a = 60;                         // steeper = smaller disc; also prints better on the flipped lid
ramp_t = 1.2;
floor_t = 1.0;
top_t = 1.2;
iw_t = 1.4;
gb_gap = 0.3;
standoff_d = 5;
pilot_d = 1.7;
screw_clear_d = 2.4;
screw_head_d = 4.2;
screw_head_recess = 1.2;
strap_w = 11;
strap_slot = 2;
strap_gap = 0.5;
win_margin = 1;
win_r = [45, 54];                    // floor windows (annular sectors), radius around the disc centre
win_sectors = [[25, 65], [75, 105], [115, 155], [222, 252], [262, 278], [288, 318]];

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
cx = pcb_w / 2;
C = [cx, disc_cy];
battery_pos = [cx - battery[0] / 2, axle_y - battery[1] / 2];
well_y0 = axle_y - wheel_d / 2 - wheel_gap;
well_y1 = axle_y + wheel_d / 2 + wheel_gap;

cot = 1 / tan(ramp_a);
dt = ramp_t / sin(ramp_a);
pcb_r = max([for (p = pcb_pts) norm(p - C)]);
// smallest lip diameter that keeps the PCB corners inside the ramp at the split
rim_d = ceil(2 * (pcb_r + pcb_clear + dt + tub_h * cot));
R = rim_d / 2;
R_top = R - H * cot;
function r_in(z) = R - dt - z * cot;  // inner ramp radius at height z
wheel_span = pcb_w - 2 * wheel_x0;

echo(str("Disc Mini d", rim_d, " x ", H, " mm (wheels ", wheel_span, " wide), ramp ", ramp_a, " deg, top d", 2 * R_top,
         ", spin circle d", 2 * (R + abs(disc_cy - axle_y))));
echo(str("PCB corner radius ", pcb_r, " / inner ramp at the split ", r_in(tub_h), ", split z = ", tub_h,
         ", ESP top ", tub_h + esp_h, " / lid underside ", H - top_t));
echo(str("Colours: tub ", pal[0][0], "; lid + clamps ", pal[1][0], "; window ", pal[2][0]));
echo("Screws: 4x M2x14 (lid + PCB into tub), 4x M2x10 motor clamps");

assert(rim_d <= 150, "disc does not fit the 150 mm square");
assert(well_y1 + iw_t <= 41 - pcb_clear, "wheel well hits wide PCB part");
assert(tub_h + esp_h <= H - top_t - 0.5, "ESP32 hits the lid");
assert(floor_t + battery[2] < pcb_z - max(drv_h, jst_h) - 0.5, "battery hits parts under the PCB");
assert(battery_pos[0] - strap_gap - strap_slot > gf_x + mount_l, "battery / strap slot hits motor cradle");
assert(win_r[1] < r_in(floor_t) - 2, "floor windows reach the ramp");
assert(max([for (p = pcb_holes) norm(p - C)]) + standoff_d / 2 < win_r[0], "floor windows hit the standoffs");

// ---------- 2D helpers ----------
module well_rect() translate([ow_x1, well_y0]) square([iw_x0 - ow_x1, well_y1 - well_y0]);

module strap_slots2d()
    for (x = [battery_pos[0] - strap_gap - strap_slot, battery_pos[0] + battery[0] + strap_gap])
        translate([x, axle_y - strap_w / 2]) square([strap_slot, strap_w]);

// sectors stay clear of the wheel wells (around the x axis); spokes in between
module floor_windows2d() translate(C)
    for (s = win_sectors) {
        steps = 24;
        polygon(concat(
            [for (k = [0 : steps]) win_r[1] * [cos(s[0] + (s[1] - s[0]) * k / steps), sin(s[0] + (s[1] - s[0]) * k / steps)]],
            [for (k = [steps : -1 : 0]) win_r[0] * [cos(s[0] + (s[1] - s[0]) * k / steps), sin(s[0] + (s[1] - s[0]) * k / steps)]]));
    }

// ---------- 3D helpers ----------
module well_rings() for (m = [0, 1]) mirror_x(m) linear_extrude(H) difference() {
    offset(delta = iw_t) well_rect();
    well_rect();
}

module wheel_cuts() for (m = [0, 1]) mirror_x(m)
    translate([ow_x1, axle_y, axle_z]) rotate([0, 90, 0]) cylinder(d = wheel_d + 2 * wheel_gap, h = iw_x0 - ow_x1);

// ---------- tub ----------
module tub() {
    difference() {
        union() {
            intersection() {
                union() {
                    shell();
                    well_rings();
                    for (p = pcb_holes) translate([p[0], p[1], 0]) cylinder(d = standoff_d, h = pcb_z);
                }
                outer3d();
                slab(0, tub_h);
            }
            for (m = [0, 1]) mirror_x(m) motor_cradle();
        }
        wheel_cuts();
        for (m = [0, 1]) mirror_x(m)
            // open-top shaft slot: motor with wheel fitted drops straight in
            hull() for (z = [axle_z, tub_h + 1])
                translate([iw_x0 - 1, axle_y, z]) rotate([0, 90, 0]) cylinder(d = shaft_boss_d, h = iw_t + 2);
        for (p = pcb_holes) translate([p[0], p[1], pcb_z - 8]) cylinder(d = pilot_d, h = 9);
        translate([0, 0, -1]) linear_extrude(floor_t + 2) {
            strap_slots2d();
            floor_windows2d();
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
        wheel_cuts();
        for (p = pcb_holes) {
            translate([p[0], p[1], tub_h - 1]) cylinder(d = screw_clear_d, h = H);
            translate([p[0], p[1], H - screw_head_recess]) cylinder(d = screw_head_d, h = 2);
        }
        translate([0, 0, H - top_t - 1]) linear_extrude(top_t + 2) {
            esp_win2d();
            switch_win2d();
        }
    }
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
