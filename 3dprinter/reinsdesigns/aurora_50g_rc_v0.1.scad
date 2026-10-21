// AURORA — 50 g radio-controlled showcase robot, v0.1
// Passive mobility only: no weapon, wedge, lifter, or powered attachment.
// Hardware envelopes are starting assumptions; measure the selected parts before printing.

part = "assembly"; // [assembly, tub, lid, bumper, wheel]
show_components = true;
$fn = 48;

/* [Chassis] */
body_l = 58;
body_w = 42;
body_h = 14;
body_z = 4;
wall = 1.2;
floor_t = 1.2;
lid_t = 1.2;
corner_r = 7;
lid_fit = 0.25;

/* [Drive - estimated micro gearmotor envelope] */
wheel_d = 22;
wheel_w = 5;
wheel_gap = 1;
motor_l = 18;
motor_w = 8;
motor_h = 10;
motor_fit = 0.25;
shaft_d = 2;
shaft_l = 4;
axle_z = wheel_d / 2;
motor_x = body_w / 2 - wall - motor_l / 2;
wheel_x = body_w / 2 + wheel_w / 2 + wheel_gap;

/* [Electronics envelopes - measure before printing] */
battery = [20, 14, 5.5];
battery_pos = [0, 12, body_z + floor_t];
receiver = [18, 10, 3];
receiver_pos = [0, -13, body_z + floor_t + 7];
electronics_clearance = 0.4;
strap_w = 2;
strap_l = 5;
strap_x = [-12, 12];
strap_y = [7, 17];

/* [Details] */
bumper_l = body_w - 8;
bumper_w = 3.5;
bumper_h = 3.5;
bumper_z = body_z + 5;
skid_r = 2.1;
skid_y = [-22, 22];
screw_x = [-15, 15];
screw_y = [-22, 22];
pilot_d = 1.5;
screw_clear_d = 2.1;
light_d = 2.2;

module rounded_prism(size, radius) {
    linear_extrude(height = size[2])
        offset(r = radius)
            square([size[0] - 2 * radius, size[1] - 2 * radius], center = true);
}

module motor_cavity(side) {
    translate([side * motor_x, 0, axle_z - (motor_h + 2 * motor_fit) / 2])
        rounded_prism([motor_l + 2 * motor_fit,
                       motor_w + 2 * motor_fit,
                       motor_h + 2 * motor_fit], 1.2);
}

module screw_bosses() {
    for (x = screw_x, y = screw_y)
        translate([x, y, body_z + floor_t])
            cylinder(d = 4.2, h = body_h - floor_t);
}

module tub() {
    difference() {
        union() {
            difference() {
                translate([0, 0, body_z])
                    rounded_prism([body_l, body_w, body_h], corner_r);
                translate([0, 0, body_z + floor_t])
                    rounded_prism([body_l - 2 * wall,
                                   body_w - 2 * wall,
                                   body_h], corner_r - wall);
            }
            screw_bosses();
        }

        for (side = [-1, 1]) {
            motor_cavity(side);
            translate([side * (body_w / 2 - wall / 2), 0, axle_z])
                rotate([0, 90, 0])
                    cylinder(d = shaft_d + 0.5, h = wall + 2, center = true);
        }

        for (x = screw_x, y = screw_y)
            translate([x, y, body_z + floor_t - 0.1])
                cylinder(d = pilot_d, h = body_h + 1);

        for (x = strap_x, y = strap_y)
            translate([x - strap_w / 2, y - strap_l / 2, body_z - 0.1])
                cube([strap_w, strap_l, floor_t + 0.2]);

        for (side = [-1, 1])
            translate([side * (body_w / 2 - wall / 2), body_l / 2 - 9, body_z + 7])
                rotate([0, 90, 0])
                    cylinder(d = light_d, h = wall + 2, center = true);
    }
}

module lid() {
    difference() {
        translate([0, 0, body_z + body_h - lid_t])
            rounded_prism([body_l - lid_fit,
                           body_w - lid_fit,
                           lid_t], corner_r - lid_fit / 2);

        for (x = screw_x, y = screw_y)
            translate([x, y, body_z + body_h - lid_t - 0.1])
                cylinder(d = screw_clear_d, h = lid_t + 0.2);

        for (side = [-1, 1])
            translate([side * (body_w / 2 - 10), body_l / 2 - 9, body_z + body_h - lid_t - 0.1])
                cylinder(d = light_d, h = lid_t + 0.2);
    }
}

module bumpers() {
    for (side = [-1, 1])
        color([0.91, 0.48, 0.20])
            translate([0, side * (body_l / 2 + bumper_w / 2 - 0.4), bumper_z])
                rounded_prism([bumper_l, bumper_w, bumper_h], 1.4);
}

module wheel() {
    difference() {
        union() {
            rotate([0, 90, 0])
                cylinder(d = wheel_d, h = wheel_w, center = true);
            rotate([0, 90, 0])
                cylinder(d = 7, h = wheel_w + 1.4, center = true);
        }
        rotate([0, 90, 0])
            cylinder(d = shaft_d, h = wheel_w + 2, center = true);
    }
}

module wheels() {
    for (side = [-1, 1])
        translate([side * wheel_x, 0, axle_z])
            color([0.12, 0.16, 0.19])
                wheel();
}

module passive_skids() {
    for (y = skid_y)
        color([0.16, 0.78, 0.82])
            translate([0, y, skid_r])
                scale([1.6, 1, 1])
                    sphere(r = skid_r);
}

module component_envelopes() {
    for (side = [-1, 1])
        color([0.63, 0.67, 0.70], 0.65)
            translate([side * motor_x, 0, axle_z])
                cube([motor_l, motor_w, motor_h], center = true);

    color([0.92, 0.55, 0.21], 0.75)
            translate([battery_pos[0], battery_pos[1], battery_pos[2] + battery[2] / 2])
            cube(battery, center = true);

    color([0.25, 0.55, 0.72], 0.65)
        translate(receiver_pos)
            cube(receiver, center = true);

    for (side = [-1, 1])
        color([0.72, 0.76, 0.78])
        translate([side * (body_w / 2 - wall + shaft_l / 2), 0, axle_z])
            rotate([0, 90, 0])
                cylinder(d = shaft_d, h = shaft_l, center = true);
}

module assembly() {
    color([0.20, 0.28, 0.36])
        tub();
    color([0.82, 0.86, 0.88])
        lid();
    bumpers();
    wheels();
    passive_skids();
    if (show_components)
        component_envelopes();
}

assert(body_l > body_w && body_w > motor_l + 2 * wall,
       "chassis dimensions do not leave room for the motor pockets");
assert(body_h > floor_t + lid_t + motor_h + 2 * motor_fit,
       "motor envelope does not fit the chassis height");
assert(wheel_x - wheel_w / 2 > body_w / 2,
       "wheel overlaps the chassis side wall");
assert(battery[0] + 2 * electronics_clearance < body_w - 2 * wall,
       "battery bay is wider than the available interior");
assert(abs(battery_pos[1] - receiver_pos[1]) >
       (battery[1] + receiver[1]) / 2 + electronics_clearance,
       "battery and receiver envelopes overlap");
assert(max(strap_x) + strap_w < body_w / 2 - wall &&
       min(strap_x) - strap_w > -body_w / 2 + wall,
       "battery strap slots are outside the floor");

echo(str("AURORA v0.1 body ", body_l, " x ", body_w, " x ", body_h,
         " mm; overall envelope about ", body_l + 2 * (bumper_w - 0.4), " x ",
         2 * (wheel_x + wheel_w / 2), " mm"));
echo("50 g is the competition mass limit, not a predicted build mass. Weigh the assembled robot.");
echo("Check event size rules and measure the selected motors, battery, receiver and wheel hubs before printing.");

if (part == "assembly") assembly();
else if (part == "tub") tub();
else if (part == "lid") lid();
else if (part == "bumper") bumpers();
else if (part == "wheel") wheel();
