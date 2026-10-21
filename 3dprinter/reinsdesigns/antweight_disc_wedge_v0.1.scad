// Flat-front disc antweight with a gravity-hinged four-fork wedge.
// Motors sit aft of the PCB, in the same low plane rather than underneath it.
// Reuses the proven N20 mount and PCB details from Disc Mini v0.1.
include <disc_geometry.scad>

// Parameters required by the imported reusable geometry modules.
pcb_w = 78;
pcb_l = 56;
pcb_t = 1.6;
pcb_clear = 0.3;
pcb_gap = 0.3;
pcb_holes = [[9.14, 3.14], [68.86, 3.14], [3.95, 44.56], [74.05, 44.56]];
pcb_pts = [[6, 0], [72, 0], [72, 41], [78, 41], [78, 48], [70, 56], [8, 56], [0, 48], [0, 41], [6, 41]];
switch_slot = [[63.65, 38.65], [66.65, 45.65]];
switch_holes = [[65.15, 34.7], [65.15, 49.6]];
switch_hole_d = 2;
drv_center = [37.93, 15.5];
drv_pins = 6;
drv_w = 18;
drv_len = drv_pins * 2.54 + (22.5 - 8 * 2.54);
drv_body = [drv_center - [drv_len / 2, drv_w / 2], drv_center + [drv_len / 2, drv_w / 2]];
drv_h = 3.5;
esp_body = [[28.1, 31.7], [48.4, 49.5]];
esp_h = 4.5;
jst_h = 6;
battery = [20, 30, 6.5];
wheel_d = 34;
wheel_w = 10;
wheel_gap = 1;
ground_clearance = 1;
gearbox_l = 9;
gb_w = 12;
gb_h = 10;
motor_d = 12;
motor_l = 15;
motor_term_l = 3;
shaft_d = 3;
shaft_l = 10;
shaft_boss_d = 5;
motor_fit = 0.25;
drv_clear = 0.3;
clamp_squeeze = 0.2;
clamp_wall = 1.6;
clamp_top_t = 1.2;
clamp_screw_x = 12;
clamp_screw_margin = 1;
clamp_boss_d = 4.4;
clamp_head_d = 4.2;
clamp_head_recess = 1.6;
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
pal = [["dark gray", "#3C3F41"], ["silver", "#C4C7CA"], ["transparent", "#E8F0F2"]];

part = "disc_assembly"; // [disc_assembly, disc_tub, disc_lid, wedge, motor_clamp, test_hinge, esp_clearance, motor_clearance, wedge_clearance]
$fn = 96;

/* [Relayout and light shell] */
disc_cy = 43;
ramp_a = 80;
ramp_t = 0.8;
floor_t = 0.8;
top_t = 0.8;
axle_y = pcb_l + 4 + wheel_d / 2 + wheel_gap + iw_t;
pcb_z = 14.2;
win_r = [54, 58];
well_y1 = 39.3; // Legacy assertion guard; the new motor bay is aft of the PCB.
flat_y = -8;
wall = 0.9;
side_t = 1.0;
floor_window_enable = true;

/* [Wedge and flat nose] */
ear_t = 5;
ear_fit = 0.4;
ear_x = 10;
ear_r = 4.5;
ear_apex = 8;
tube_d = 7;
hinge_gap = 1;
hinge_dz = 2.8;
pin_clear_d = 2.4;
pin_pilot_d = 1.7;
plate_t = 1.6;
plate_l = 24;
fork_l = 4;
fork_x = [-18, -6, 6, 18];
fork_w0 = 7;
fork_w1 = 2.5;
plate_corner = 3;
bevel_l = 8;
tip_t = 0.5;
stop_angle = 50;
ledge_u = [2.5, 9];
ledge_w = 1.5;
ledge_t = 1.4;

/* [Print strength] */
rib_t = 1.0;
layer_h = 0.2;
motor_bed_t = 1.2;
min_shell_clear = 0.5;

// Recompute dependent geometry after the layout overrides above.
H = wheel_d - 2 * ground_clearance;
axle_z = H / 2;
tub_h = pcb_z + pcb_t;
cx = pcb_w / 2;
C = [cx, disc_cy];
cot = 1 / tan(ramp_a);
dt = ramp_t / sin(ramp_a);
pcb_r = max([for (p = pcb_pts) norm(p - C)]);
rim_d = ceil(2 * (pcb_r + pcb_clear + dt + tub_h * cot));
R = rim_d / 2;
R_top = R - H * cot;
function r_in(z) = R - dt - z * cot;
motor_total = gearbox_l + motor_l + motor_term_l;
mount_l = gearbox_l + motor_l + motor_fit;
cw = gb_w + 2 * motor_fit;
clamp_top_z = axle_z + gb_h / 2 - clamp_squeeze + clamp_top_t;
clamp_screw_y = gb_w / 2 + motor_fit + clamp_screw_margin + screw_clear_d / 2;
gf_x = min(drv_body[0][0] - drv_clear - motor_total,
           pcb_w - (drv_body[1][0] + drv_clear + motor_total));
iw_x1 = gf_x - gb_gap;
iw_x0 = iw_x1 - iw_t;
wheel_x0 = iw_x0 - wheel_gap - wheel_w;
ow_x1 = wheel_x0 - wheel_gap;
battery_pos = [pcb_w / 2 - battery[0] / 2, 12];
well_y0 = axle_y - wheel_d / 2 - wheel_gap;
motor_front_y = axle_y - wheel_d / 2 - wheel_gap - iw_t;
motor_rear_y = axle_y + wheel_d / 2 + wheel_gap + iw_t;
y_h = flat_y - tube_d / 2 - hinge_gap;
z_h = axle_z + hinge_dz;
r_k = tube_d / 2;
wx0 = ear_x + ear_t + ear_fit;
wx1 = pcb_w - wx0;
L_tot = plate_l + fork_l;
lift = ground_clearance;
h_up = z_h + lift;
rest_up = asin(h_up / sqrt(pow(L_tot, 2) + pow(r_k, 2))) - atan2(r_k, L_tot);
tip_y = y_h - L_tot * cos(rest_up) - r_k * sin(rest_up);
rear_y = disc_cy + R;
overall_length = rear_y - tip_y;

echo(str("Flat Disc Wedge v0.1: disc d", rim_d, ", body height ", H,
         ", width <= ", rim_d, ", total length ", overall_length, " mm"));
echo(str("PCB y 0..", pcb_l, "; N20 axle y ", axle_y, "; gap from PCB end to motor well ", motor_front_y - pcb_l,
         " mm; wedge rest angle ", rest_up, " deg"));
echo("Two M2x10 wedge pivots; four forks. Print one wedge and two motor clamps.");
echo("50 g target is printed plastic only. No printed-mass claim until slicer volume and filament density are checked.");

assert(rim_d <= 150, "disc width exceeds 150 mm");
assert(overall_length <= 150, "disc and wedge exceed 150 mm total length");
assert(motor_front_y >= pcb_l + 4, "motor bay overlaps the PCB footprint");
assert(pcb_z - drv_h >= floor_t + battery[2] + 0.5, "battery or DRV8833 clearance is insufficient");
assert(tub_h + esp_h <= H - top_t - 0.5, "ESP32 hits the upper shell");
assert(gf_x - shaft_l > ow_x1, "N20 shaft hits the outer wheel wall");
assert(r_in(tub_h) >= pcb_r + pcb_clear, "PCB corners hit the conical shell at the split");
assert(rest_up < stop_angle - 5, "wedge reaches its swing stop before resting on the floor");

module flat_clip() {
    translate([-200, flat_y, -2]) cube([400, 400, H + 4]);
}

module flat_outer() intersection() { outer3d(); flat_clip(); }

module flat_shell() intersection() { shell(); flat_clip(); }

module motor_arches() for (side = [0, 1]) mirror_x(side)
    translate([ow_x1, axle_y, axle_z]) rotate([0, 90, 0])
        cylinder(d = wheel_d + 2 * wheel_gap, h = iw_x0 - ow_x1);

module flat_ear_stops() hinge_frame() {
    rotate(-stop_angle) translate([ledge_u[0], -r_k - ledge_t])
        square([ledge_u[1] - ledge_u[0], ledge_t]);
    rotate(stop_angle) translate([ledge_u[0], -r_k + plate_t])
        square([ledge_u[1] - ledge_u[0], ledge_t]);
}

module hinge_frame() translate([y_h, z_h]) mirror([1, 0]) children();

module flat_ear_profile() union() {
    hull() {
        translate([flat_y, 0]) square([0.2, tub_h]);
        translate([y_h - 2, 0]) square([0.2, tub_h]);
        translate([y_h, z_h]) circle(r = ear_r);
        translate([y_h - ear_apex, z_h]) circle(r = 0.5);
        flat_ear_stops();
    }
    translate([flat_y - 0.01, 0]) square([1.1, tub_h]);
}

module flat_ears() for (side = [0, 1]) mirror_x(side)
    difference() {
        yz_extrude(ear_x, ear_t) flat_ear_profile();
        translate([ear_x - 1, y_h, z_h]) rotate([0, 90, 0]) cylinder(d = pin_pilot_d, h = ear_t + 2);
        translate([ear_x - 1, y_h, z_h]) rotate([0, 90, 0]) cylinder(d = screw_head_d, h = 1.5);
    }

module flat_motor_ribs() for (side = [0, 1]) mirror_x(side) {
    translate([iw_x0, motor_front_y + 2, floor_t])
        cube([iw_t, motor_rear_y - motor_front_y - 4, axle_z - floor_t - gb_h / 2]);
    translate([iw_x0, motor_rear_y - 2, floor_t])
        cube([gf_x + mount_l - iw_x0, 1.2, axle_z - floor_t]);
}

module flat_tub() difference() {
    union() {
        intersection() {
            union() {
                flat_shell();
                for (p = pcb_holes) translate([p[0], p[1], 0]) cylinder(d = standoff_d, h = pcb_z);
                for (side = [0, 1]) mirror_x(side) motor_cradle();
                flat_motor_ribs();
            }
            flat_outer();
            slab(0, tub_h);
        }
        flat_ears();
    }
    motor_arches();
    for (side = [0, 1]) mirror_x(side)
        hull() for (z = [axle_z, tub_h + 1])
            translate([iw_x0 - 1, axle_y, z]) rotate([0, 90, 0]) cylinder(d = shaft_boss_d, h = iw_t + 2);
    for (p = pcb_holes) translate([p[0], p[1], pcb_z - 8]) cylinder(d = pilot_d, h = 9);
    for (x = [battery_pos[0] - strap_gap - strap_slot, battery_pos[0] + battery[0] + strap_gap])
        translate([x, battery_pos[1] - 1, -1]) cube([strap_slot, strap_w, floor_t + 2]);
    for (side = [0, 1]) mirror_x(side)
        translate([ow_x1, axle_y, axle_z]) rotate([0, 90, 0]) cylinder(d = wheel_d + 2 * wheel_gap, h = iw_x0 - ow_x1);
    for (side = [0, 1]) mirror_x(side)
        translate([ear_x - 1, y_h, z_h]) rotate([0, 90, 0]) cylinder(d = pin_pilot_d, h = ear_t + 2);
}

module flat_lid() difference() {
    intersection() {
        union() {
            flat_shell();
            for (p = pcb_holes) translate([p[0], p[1], tub_h])
                cylinder(d = standoff_d, h = H - tub_h);
        }
        flat_outer();
        slab(tub_h, H);
    }
    for (side = [0, 1]) mirror_x(side)
        translate([ow_x1, axle_y, axle_z]) rotate([0, 90, 0]) cylinder(d = wheel_d + 2 * wheel_gap, h = iw_x0 - ow_x1);
    for (p = pcb_holes) {
        translate([p[0], p[1], tub_h - 1]) cylinder(d = screw_clear_d, h = H);
        translate([p[0], p[1], H - screw_head_recess]) cylinder(d = screw_head_d, h = 2);
    }
    translate([0, 0, H - top_t - 1]) linear_extrude(top_t + 2) {
        esp_win2d();
        switch_win2d();
    }
}

module flat_wedge_plan() {
    polygon([[wx0, 0], [wx1, 0], [wx1, plate_l - plate_corner],
             [wx1 - plate_corner, plate_l], [wx0 + plate_corner, plate_l], [wx0, plate_l - plate_corner]]);
    for (fx = fork_x) translate([cx + fx, 0])
        polygon([[-fork_w0 / 2, plate_l - 0.01], [fork_w0 / 2, plate_l - 0.01],
                 [fork_w1 / 2, L_tot], [-fork_w1 / 2, L_tot]]);
}

module flat_wedge_local() difference() {
    union() {
        intersection() {
            translate([0, 0, -r_k]) linear_extrude(plate_t) flat_wedge_plan();
            yz_extrude(wx0 - 1, wx1 - wx0 + 2)
                polygon([[-1, -r_k - 1], [L_tot + 1, -r_k - 1], [L_tot + 1, -r_k + tip_t],
                         [L_tot - bevel_l, -r_k + plate_t + 0.01], [-1, -r_k + plate_t + 0.01]]);
        }
        translate([wx0, 0, 0]) rotate([0, 90, 0]) cylinder(d = tube_d, h = wx1 - wx0);
    }
    translate([wx0 - 1, 0, 0]) rotate([0, 90, 0]) cylinder(d = pin_clear_d, h = wx1 - wx0 + 2);
}

module flat_wedge_placed(angle = -rest_up) {
    translate([pcb_w, y_h, z_h]) rotate([0, 0, 180]) rotate([angle, 0, 0]) flat_wedge_local();
}

module flat_ghosts() {
    for (side = [0, 1]) mirror_x(side) {
        color("black", 0.7) translate([wheel_x0, axle_y, axle_z]) rotate([0, 90, 0]) cylinder(d = wheel_d, h = wheel_w);
        color("silver", 0.7) translate([0, axle_y, axle_z]) {
            yz_extrude(gf_x, gearbox_l) square([gb_w, gb_h], center = true);
            yz_extrude(gf_x + gearbox_l, motor_l) intersection() {
                circle(d = motor_d);
                square([gb_w, gb_h], center = true);
            }
        }
    }
    color("green", 0.55) translate([0, 0, pcb_z]) linear_extrude(pcb_t) pcb_outline();
    color("purple", 0.6) translate([drv_body[0][0], drv_body[0][1], pcb_z - drv_h])
        cube([drv_body[1][0] - drv_body[0][0], drv_body[1][1] - drv_body[0][1], drv_h]);
    color("blue", 0.6) translate([esp_body[0][0], esp_body[0][1], tub_h])
        cube([esp_body[1][0] - esp_body[0][0], esp_body[1][1] - esp_body[0][1], esp_h]);
    color("orange", 0.7) translate([battery_pos[0], battery_pos[1], floor_t]) cube(battery);
}

module flat_assembly() {
    color(pal[0][1]) render() flat_tub();
    color(pal[1][1]) render() flat_lid();
    color(pal[2][1], 0.65) lid_glass();
    color(pal[1][1]) for (side = [0, 1]) mirror_x(side) motor_clamp();
    color("#B87333") flat_wedge_placed();
    flat_ghosts();
}

if (part == "disc_assembly") flat_assembly();
else if (part == "disc_tub") flat_tub();
else if (part == "disc_lid") translate([0, 0, H]) rotate([180, 0, 0]) flat_lid();
else if (part == "wedge") translate([0, 0, r_k]) flat_wedge_local();
else if (part == "motor_clamp")
    translate([0, 0, clamp_top_z]) rotate([180, 0, 0]) translate([-gf_x, -axle_y, 0]) motor_clamp();
else if (part == "test_hinge")
    intersection() { flat_tub(); translate([ear_x - 1, y_h - ear_apex - 3, -1]) cube([ear_t + 14, flat_y - y_h + ear_apex + 8, H + 2]); }
else if (part == "esp_clearance") intersection() { flat_lid(); translate([0, 0, tub_h]) linear_extrude(esp_h + 0.8) esp_win2d(); }
else if (part == "motor_clearance") intersection() {
    union() { flat_tub(); flat_lid(); }
    for (side = [0, 1]) mirror_x(side)
        translate([gf_x - shaft_l, axle_y - 10, axle_z - 8]) cube([motor_total + shaft_l, 20, 18]);
}
else if (part == "wedge_clearance") intersection() { flat_tub(); flat_wedge_placed(); }