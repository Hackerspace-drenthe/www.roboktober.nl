// DUOBOT: two independent N20 robots, inline passive docking, rolling lifter.
// Front is -y. PCB coordinates match the existing 78 x 56 mm board.
// Prototype mechanics: docking capture is passive; release is manual.

part = "assembly"; // [assembly, attack, defend, tub, roof, visor, lifter, bumper, skid, drive_wheel, tire, motor_clamp, dock_female, dock_male, dock_test, electronics_clearance, wheel_clearance, dock_clearance, lifter_clearance, cam_clearance, pair_clearance]
bot = "attack"; // [attack, defend]
$fn = 48;

/* [Hardware] */
pcb_w = 78;
pcb_l = 56;
pcb_t = 1.6;
pcb_z = 28;
pcb_holes = [[9.14, 3.14], [68.86, 3.14], [3.95, 44.56], [74.05, 44.56]];
esp_body = [[28.1, 31.7], [48.4, 49.5]];
esp_h = 17;
drv_size = [22.5, 18, 9];
drv_center = [37.93, 15.5];
battery = [30, 20, 6.5];
battery_y = 48;
switch_slot = [[63.65, 38.65], [66.65, 45.65]];
switch_holes = [[65.15, 34.7], [65.15, 49.6]];

/* [Drive] */
wheel_d = 34;
wheel_w = 10;
wheel_gap = 2.6;
ground_clearance = 6;
axle_y = 21;
gf_x = 6;
gearbox_l = 9;
motor_l = 15;
motor_term_l = 3;
gb_w = 12;
gb_h = 10;
motor_d = 12;
motor_fit = 0.25;
shaft_l = 10;
shaft_d = 3.15;
shaft_flat = 1.0;
wheel_phase = 0; // [0:1:360]
cam_e = 3;
cam_r = 8;
cam_t = 1.4;
tire_t = 1.5;

/* [Chassis] */
wall = 1.6;
floor_t = 1.6;
roof_t = 1.6;
iw_t = 1.4;
side_t = 1.2;
core_front = -3;
rear_y = 76;
nose_y = -12;
deck_z = 38;
visor_top = 50;
pilot_d = 1.7;
screw_d = 2.4;
standoff_d = 5;

/* [Lifter] */
pivot_y = 48;
pivot_z = 33;
arm_t = 1.2;
follower_z = 16;

/* [Dock] */
dock_z = 13;
dock_gap = 0.4;
pair_pitch = 89;
latch_flex = 0; // [0:0.1:1.6]
show_components = true;

cx = pcb_w / 2;
axle_z = wheel_d / 2 - ground_clearance;
cw = gb_w + 2 * motor_fit;
clamp_top_z = axle_z + gb_h / 2 - 0.2 + 1.2;
tub_h = pcb_z + pcb_t;
iw_x1 = gf_x - 0.3;
iw_x0 = iw_x1 - iw_t;
wheel_x0 = iw_x0 - wheel_gap - wheel_w;
ow_x1 = wheel_x0 - 0.6;
ow_x0 = ow_x1 - side_t;
width = pcb_w - 2 * ow_x0;
well_y0 = axle_y - wheel_d / 2 - 1;
well_y1 = axle_y + wheel_d / 2 + 1;
clamp_screw_y = cw / 2 + 2.2;
clamp_x = gf_x + 12;
mount_l = gearbox_l + motor_l + motor_fit;
arm_x = wheel_x0 + wheel_w + cam_t - arm_t / 2;

function cam_lift_angle(phase) =
    let(vertical = axle_z - cam_e * cos(phase) - pivot_z,
        horizontal = pivot_y + 3 - axle_y - cam_e * sin(phase),
        radius = sqrt(pow(vertical, 2) + pow(horizontal, 2)))
    atan2(horizontal, vertical) - acos((follower_z - pivot_z - cam_r) / radius);
lifter_angle = cam_lift_angle(wheel_phase);

assert(width <= 100 && rear_y + 12 - nose_y <= 100, "module exceeds 100 x 100 mm");
assert(pcb_z - drv_size[2] > clamp_top_z + 1, "DRV overlaps motor clamps");
assert(gf_x + gearbox_l + motor_l + motor_term_l + 1 < cx, "motors overlap");
assert(gf_x - shaft_l > wheel_x0, "shaft protrudes beyond tire");
assert(well_y1 + iw_t < 41, "wheel well intersects rear PCB mounting posts");
assert(battery_y + battery[1] + 1 < rear_y - wall, "battery hits rear wall");
assert(tub_h + esp_h + 0.8 < visor_top - roof_t, "ESP hits visor roof");
assert(wheel_gap > cam_t + arm_t / 2 + 0.3, "follower cannot fit beside wheel");
assert(lifter_angle >= -25 && lifter_angle <= 0.01, "lifter exceeds prototype travel");

echo(str("DUOBOT module width ", width, ", body length ", rear_y - nose_y,
         ", attack including dock ", rear_y + 12 - nose_y, ", shell height ", visor_top,
         "; coupled length ", pair_pitch + rear_y - nose_y, " mm"));
echo("2 N20 per bot, 4 total; 34 x 10 TPU tires, custom wheel hubs with integrated eccentric cams.");
echo("17 mm ESP stack, raised PCB. Rolling lifter follows wheel rotation; no independent lift or hold.");
echo("Drive-in snap dock, manual release. Navigation, contact sensing and coordinated firmware not included.");

module mirror_x(side) {
    if (side == 0) children();
    else translate([pcb_w, 0, 0]) mirror([1, 0, 0]) children();
}

module yz_extrude(x0, thickness) {
    translate([x0, 0, 0]) rotate([90, 0, 90]) linear_extrude(thickness) children();
}

module rrect(lower, upper, radius) {
    translate(lower + [radius, radius]) offset(r = radius)
        square(upper - lower - [2 * radius, 2 * radius]);
}

module pcb_outline() {
    polygon([[6, 0], [72, 0], [72, 41], [78, 41], [78, 48], [70, 56],
             [8, 56], [0, 48], [0, 41], [6, 41]]);
}

module plan() {
    hull() {
        rrect([ow_x0, core_front], [pcb_w - ow_x0, 43], 4);
        rrect([-1.6, 42], [pcb_w + 1.6, rear_y], 6);
    }
}

module well2d() {
    translate([ow_x1, well_y0]) square([iw_x0 - ow_x1, well_y1 - well_y0]);
}

module motor_cavity() {
    translate([0, axle_y, axle_z]) {
        yz_extrude(gf_x - motor_fit, gearbox_l + 2 * motor_fit)
            square([cw, gb_h + 2 * motor_fit], center = true);
        yz_extrude(gf_x + gearbox_l - 0.01, motor_l + motor_term_l + 1)
            intersection() {
                circle(d = motor_d + 2 * motor_fit);
                square([cw, gb_h + 2 * motor_fit], center = true);
            }
    }
}

module clamp_screws(z0, height, diameter) {
    for (direction = [-1, 1]) translate([clamp_x, axle_y + direction * clamp_screw_y, z0])
        cylinder(d = diameter, h = height);
}

module motor_block(z0, height) {
    union() {
        translate([iw_x1, axle_y - cw / 2 - 1.6, z0])
            cube([gf_x + mount_l - iw_x1, cw + 3.2, height]);
        for (direction = [-1, 1]) hull() {
            translate([clamp_x, axle_y + direction * clamp_screw_y, z0]) cylinder(d = 5, h = height);
            translate([clamp_x - 2, axle_y + direction * cw / 2 - 0.8, z0]) cube([4, 1.6, height]);
        }
    }
}

module motor_cradle() {
    difference() {
        motor_block(floor_t - 0.1, axle_z - floor_t + 0.1);
        motor_cavity();
        clamp_screws(axle_z - 7, 8, pilot_d);
    }
}

module motor_clamp() {
    difference() {
        motor_block(axle_z, clamp_top_z - axle_z);
        motor_cavity();
        clamp_screws(axle_z - 1, 10, screw_d);
        clamp_screws(clamp_top_z - 1.2, 2, 4.4);
    }
}

module female_dock() {
    difference() {
        translate([cx - 13, rear_y - wall, dock_z - 6]) cube([26, 12 + wall, 12]);
        translate([cx - 9 - dock_gap, rear_y, dock_z - 4 - dock_gap])
            cube([18 + 2 * dock_gap, 13, 8 + 2 * dock_gap]);
        translate([cx - 12, rear_y + 8, dock_z - 4 - dock_gap])
            linear_extrude(8 + 2 * dock_gap)
                polygon([[0, 4.1], [2.6, 0], [21.4, 0], [24, 4.1]]);
        for (direction = [-1, 1])
            translate([cx + direction * 11 - 2, rear_y + 2.2, dock_z - 2.5]) cube([4, 4.3, 5]);
    }
}

module male_dock() {
    translate([cx, 0, dock_z]) {
        translate([-9, 6, -4]) cube([18, 2.2, 8]);
        for (direction = [-1, 1]) {
            translate([direction < 0 ? -9 + latch_flex : 7.8 - latch_flex, -11, -2]) cube([1.2, 19, 4]);
            translate([direction * (8.4 - latch_flex), -9.5, -2]) linear_extrude(4)
                polygon([[0, -2], [direction * 2.4, 0.2], [direction * 2.4, 1.5], [0, 1.5]]);
        }
        translate([-4, -12, -3.5]) linear_extrude(7)
            polygon([[0, 0], [8, 0], [8, 20], [0, 20]]);
    }
}

module tub(kind = bot) {
    difference() {
        union() {
            difference() {
                linear_extrude(tub_h) plan();
                translate([0, 0, floor_t]) linear_extrude(tub_h) offset(delta = -wall) plan();
                if (kind == "defend")
                    translate([cx - 13.4, core_front - 1, dock_z - 6.4])
                        cube([26.8, 6, 12.8]);
            }
            for (side = [0, 1]) mirror_x(side) {
                motor_cradle();
                linear_extrude(tub_h) difference() { offset(delta = iw_t) well2d(); well2d(); }
            }
            for (position = pcb_holes) translate([position[0], position[1], floor_t - 0.1])
                cylinder(d = standoff_d, h = pcb_z - floor_t + 0.1);
            for (direction = [-1, 1]) translate([cx + direction * (battery[0] / 2 + 1) - 0.6, battery_y, floor_t - 0.1])
                cube([1.2, battery[1], 3]);
            if (kind == "defend") translate([cx - 9, 6, floor_t - 0.1]) cube([18, 2.2, dock_z - 4 - floor_t + 0.2]);
            if (kind == "attack") female_dock();
            else male_dock();
        }
        for (side = [0, 1]) mirror_x(side) {
            translate([0, 0, -1]) linear_extrude(tub_h + 2) well2d();
            hull() for (height = [axle_z, tub_h + 1])
                translate([iw_x0 - 1, axle_y, height]) rotate([0, 90, 0]) cylinder(d = 5.4, h = iw_t + 2);
            translate([ow_x0 - 1, axle_y, axle_z]) rotate([0, 90, 0]) cylinder(d = 30, h = side_t + 2);
        }
        for (position = pcb_holes) translate([position[0], position[1], pcb_z - 8]) cylinder(d = pilot_d, h = 9);
        for (direction = [-1, 1]) translate([cx + direction * (battery[0] / 2 + 3) - 1, battery_y + 5, -1]) cube([2, 10, floor_t + 2]);
        if (kind == "attack") for (side = [0, 1]) mirror_x(side)
            translate([arm_x - 1.5, core_front - 1, 8]) cube([3, 16, tub_h + 1]);
        if (kind == "defend") for (mount_x = [12, 66])
            translate([mount_x, core_front + 3, 8]) rotate([90, 0, 0]) cylinder(d = screw_d, h = 8);
        for (mount_x = [cx - 3, cx + 3]) translate([mount_x, 71, -1]) cylinder(d = screw_d, h = floor_t + 2);
    }
}

module visor_opening() { rrect([22, 28], [56, 54], 2); }

module visor_hull(inset = 0) {
    hull() {
        translate([0, 0, deck_z + 1.4]) linear_extrude(0.2)
            rrect([21 + inset, 27 + inset], [57 - inset, 55 - inset], 2);
        translate([0, 0, visor_top - inset - 0.2]) linear_extrude(0.2)
            rrect([24 + inset, 29 + inset], [54 - inset, 53 - inset], 2);
    }
}

module visor() {
    difference() {
        union() {
            translate([0, 0, deck_z]) linear_extrude(roof_t) rrect([14, 23], [64, 59], 3);
            visor_hull();
        }
        translate([0, 0, deck_z - 1]) linear_extrude(roof_t + 3) visor_opening();
        visor_hull(roof_t);
        for (mount_x = [17, 61], mount_y = [27, 55])
            translate([mount_x, mount_y, deck_z - 1]) cylinder(d = screw_d, h = 5);
    }
}

module roof(kind = bot) {
    difference() {
        union() {
            difference() {
                translate([0, 0, tub_h]) linear_extrude(deck_z - tub_h) plan();
                translate([0, 0, tub_h - 1]) linear_extrude(deck_z - roof_t - tub_h + 1)
                    offset(delta = -wall) plan();
            }
            for (position = pcb_holes) translate([position[0], position[1], tub_h]) cylinder(d = 5, h = deck_z - tub_h);
            for (mount_x = [17, 61], mount_y = [27, 55])
                translate([mount_x, mount_y, deck_z - 5]) cylinder(d = 6, h = 5);
            for (side = [0, 1]) mirror_x(side) for (rib_y = [5, 12, 19]) hull() {
                translate([1, rib_y, deck_z - 0.2]) cylinder(r = 2, h = 1.6);
                translate([12, rib_y + 3, deck_z - 0.2]) cylinder(r = 2, h = kind == "attack" ? 3 : 6);
            }
            if (kind == "attack") for (side = [0, 1]) mirror_x(side)
                translate([arm_x + arm_t / 2 + 0.4, pivot_y, tub_h]) cube([3, 6, pivot_z - tub_h + 3]);
        }
        for (position = pcb_holes) {
            translate([position[0], position[1], tub_h - 1]) cylinder(d = screw_d, h = 20);
            translate([position[0], position[1], deck_z - 1.2]) cylinder(d = 4.4, h = 10);
        }
        translate([0, 0, deck_z - roof_t - 1]) linear_extrude(roof_t + 2) {
            visor_opening();
            translate(switch_slot[0]) square(switch_slot[1] - switch_slot[0]);
            for (position = switch_holes) translate(position) circle(d = 2);
        }
        for (mount_x = [17, 61], mount_y = [27, 55])
            translate([mount_x, mount_y, deck_z - 4.5]) cylinder(d = pilot_d, h = 6);
        if (kind == "attack") for (side = [0, 1]) mirror_x(side)
            translate([arm_x - 3, pivot_y + 3, pivot_z]) rotate([0, 90, 0]) cylinder(d = screw_d, h = 8);
        if (kind == "attack") for (side = [0, 1]) mirror_x(side)
            translate([arm_x - 1.5, core_front - 1, tub_h - 1])
                cube([3, pivot_y + 8 - core_front, deck_z + 12 - tub_h]);
    }
}

module shaft_hole(height) {
    intersection() {
        cylinder(d = shaft_d, h = height);
        translate([-shaft_d / 2, -shaft_d / 2, 0]) cube([shaft_d, shaft_d / 2 + shaft_flat, height]);
    }
}

module drive_wheel(with_cam = true) {
    difference() {
        union() {
            cylinder(d = wheel_d - 2 * tire_t, h = 1.6);
            cylinder(d = 10, h = wheel_w + (with_cam ? cam_t : 0));
            difference() {
                cylinder(d = wheel_d - 2 * tire_t, h = wheel_w);
                translate([0, 0, 1.6]) cylinder(d = 27, h = wheel_w);
            }
            if (with_cam) translate([cam_e, 0, wheel_w - 0.1]) cylinder(r = cam_r, h = cam_t + 0.1);
        }
        translate([0, 0, -1]) shaft_hole(wheel_w + cam_t + 2);
        translate([0, -6, wheel_w / 2]) rotate([-90, 0, 0]) cylinder(d = pilot_d, h = 7);
    }
}

module tire() {
    difference() {
        cylinder(d = wheel_d, h = wheel_w);
        translate([0, 0, -1]) cylinder(d = wheel_d - 2 * tire_t - 0.2, h = wheel_w + 2);
        for (angle = [0 : 20 : 340]) rotate([0, 0, angle])
            translate([wheel_d / 2 - 0.4, -0.4, -1]) cube([1, 0.8, wheel_w + 2]);
    }
}

module wheels(kind = bot, envelope = false) {
    for (side = [0, 1]) mirror_x(side)
        translate([wheel_x0, axle_y, axle_z]) rotate([0, 90, 0]) rotate([0, 0, wheel_phase]) {
            if (envelope) cylinder(d = wheel_d + 0.3, h = wheel_w);
            else {
                color("#33383A") drive_wheel(kind == "attack");
                color("#141819") tire();
            }
        }
}

module lifter() {
    translate([0, pivot_y + 3, pivot_z]) rotate([lifter_angle, 0, 0])
        translate([0, -pivot_y - 3, -pivot_z]) difference() {
            union() {
                yz_extrude(ow_x0 + 1, width - 2)
                    polygon([[nose_y, -ground_clearance], [nose_y, -ground_clearance + 1.2],
                             [core_front - 0.4, 12], [core_front - 0.4, 9], [nose_y + 3, -ground_clearance]]);
                for (side = [0, 1]) mirror_x(side) yz_extrude(arm_x - arm_t / 2, arm_t) {
                    hull() {
                        translate([core_front - 1, 11]) circle(r = 1.8);
                        translate([8, 33]) circle(r = 1.8);
                    }
                    hull() {
                        translate([8, 33]) circle(r = 1.8);
                        translate([pivot_y + 3, pivot_z]) circle(r = 2.5);
                    }
                    translate([axle_y - 6, follower_z]) square([18, pivot_z - follower_z]);
                }
            }
            for (side = [0, 1]) mirror_x(side)
                translate([arm_x - 2, pivot_y + 3, pivot_z]) rotate([0, 90, 0]) cylinder(d = screw_d, h = 4);
        }
}

module bumper(single = false) {
    for (side = [0 : single ? 0 : 1]) mirror_x(side) difference() {
        yz_extrude(ow_x0 + 1, cx - 16 - ow_x0 - 1) hull() {
            translate([nose_y + 2, -ground_clearance + 2]) circle(r = 2);
            translate([core_front - 0.5, 10]) circle(r = 2);
        }
        translate([12, core_front + 2, 8]) rotate([90, 0, 0]) cylinder(d = pilot_d, h = 8);
    }
}

module skid() {
    difference() {
        hull() {
            translate([cx, 71, -ground_clearance]) cylinder(d = 8, h = 1);
            translate([0, 0, -0.8]) linear_extrude(0.8) rrect([cx - 7, 66], [cx + 7, 76], 2);
        }
        for (mount_x = [cx - 3, cx + 3]) translate([mount_x, 71, -4]) cylinder(d = pilot_d, h = 5);
    }
}

module electronics() {
    translate([0, 0, pcb_z]) linear_extrude(pcb_t - 0.01) difference() {
        pcb_outline();
        for (position = pcb_holes) translate(position) circle(d = screw_d);
    }
    translate([esp_body[0][0] - 0.8, esp_body[0][1] - 0.8, tub_h])
        cube([esp_body[1][0] - esp_body[0][0] + 1.6, esp_body[1][1] - esp_body[0][1] + 1.6, esp_h + 0.8]);
    translate([drv_center[0] - drv_size[0] / 2, drv_center[1] - drv_size[1] / 2, pcb_z - drv_size[2]]) cube(drv_size);
    translate([cx - battery[0] / 2, battery_y, floor_t]) cube(battery);
}

module robot(kind) {
    color(kind == "attack" ? "#3B4144" : "#B87333") render() tub(kind);
    color(kind == "attack" ? "#C4C7CA" : "#3B4144") render() roof(kind);
    color("#B0E8E7", 0.6) visor();
    wheels(kind);
    color("#55595C") for (side = [0, 1]) mirror_x(side) motor_clamp();
    if (kind == "attack") color("#B87333") lifter();
    else color("#C4C7CA") bumper();
    color("#55595C") skid();
    if (show_components) color("#329272", 0.3) electronics();
}

if (part == "assembly") { robot("attack"); translate([0, pair_pitch, 0]) robot("defend"); }
else if (part == "attack") robot("attack");
else if (part == "defend") robot("defend");
else if (part == "tub") tub();
else if (part == "roof") translate([0, 0, -tub_h]) roof();
else if (part == "visor") translate([0, 0, -deck_z]) visor();
else if (part == "lifter")
    translate([0, 0, (width - 2) / 2]) rotate([0, 90, 0]) translate([-cx, 0, 0]) lifter();
else if (part == "bumper")
    translate([0, 0, cx - 16]) rotate([0, 90, 0]) bumper(true);
else if (part == "skid") rotate([180, 0, 0]) translate([-cx, -71, 0]) skid();
else if (part == "drive_wheel") drive_wheel(bot == "attack");
else if (part == "tire") tire();
else if (part == "motor_clamp")
    translate([0, 0, clamp_top_z]) rotate([180, 0, 0]) translate([-gf_x, -axle_y, 0]) motor_clamp();
else if (part == "dock_test") { female_dock(); translate([0, pair_pitch, 0]) male_dock(); }
else if (part == "dock_female") translate([-cx, -rear_y + wall, -dock_z + 6]) female_dock();
else if (part == "dock_male") translate([-cx, 12, -dock_z + 4]) male_dock();
else if (part == "electronics_clearance") intersection() {
    electronics();
    union() { roof(); visor(); for (side = [0, 1]) mirror_x(side) motor_clamp(); }
}
else if (part == "wheel_clearance") intersection() {
    wheels(envelope = true);
    union() { tub(); roof(); }
}
else if (part == "dock_clearance") intersection() { female_dock(); translate([0, pair_pitch, 0]) male_dock(); }
else if (part == "lifter_clearance") intersection() {
    lifter();
    union() { tub("attack"); roof("attack"); }
}
else if (part == "cam_clearance") intersection() {
    lifter();
    translate([0, 0, -0.02]) wheels("attack");
}
else if (part == "pair_clearance") intersection() {
    union() { tub("attack"); roof("attack"); skid(); }
    translate([0, pair_pitch, 0]) union() { tub("defend"); roof("defend"); bumper(); skid(); }
}