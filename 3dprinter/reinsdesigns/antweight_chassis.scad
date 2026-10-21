// Antweight chassis v0.2 - see antweight-chassis-spec-plan-v0.1.md
// Coordinates = PCB-local: x = width, y = length (y = 0 is front), z = 0 is tub bottom.

part = "assembly"; // [assembly, tub, lid, wedge]
$fn = 48;

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
corner_r = 2;
standoff_d = 5;
pilot_d = 1.7;
screw_clear_d = 2.4;
lid_lip_h = 2;
lid_lip_t = 1.2;
lid_fit = 0.2;
snap_y = 50;
snap_r = 0.8;
strap_w = 11;
strap_slot = 2;
strap_x = [27, 51];
esp_window = false;
led_enable = true;
led_pos = [38, 42];                  // TODO: measure LED position on ESP32-C3
led_d = 3;

/* [Wedge] */
hinge_r = 3;
hinge_w_tub = 6;
hinge_w_wedge = 5;
hinge_gap = 0.4;
hinge_x = [20, 58];
wedge_l = 30;
wedge_t = 2;
wedge_angle = 23;                    // resting angle on the floor for wedge_l = 30

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
in_x0 = -pcb_clear;
in_x1 = pcb_w + pcb_clear;
in_y0 = -pcb_clear;
in_y1 = pcb_l + pcb_clear;
well_y0 = in_y0;
well_y1 = axle_y + wheel_d / 2 + wheel_gap;
out_front = [[ow_x0, in_y0 - wall], [pcb_w - ow_x0, well_y1 + wall]];
out_rear = [[in_x0 - wall, axle_y], [in_x1 + wall, in_y1 + wall]];
hinge_y = in_y0 - wall - hinge_r - 0.5;
width = pcb_w - 2 * ow_x0;
length = in_y1 - in_y0 + 2 * wall;
ch_y0 = axle_y - motor_w / 2 - motor_fit;
ch_y1 = axle_y + motor_w / 2 + motor_fit;

echo(str("Chassis ", width, " x ", length, " x ", H, " mm (excl. wedge), gearbox face x = ", gf_x));
echo(str("Ground clearance ", (wheel_d - H) / 2, " mm, PCB bottom z = ", pcb_z));

//assert(width <= 100, "chassis wider than 100 mm");
assert(well_y1 + wall <= 41 - pcb_clear, "wheel well hits wide PCB part");
assert(gf_x - shaft_l > ow_x1, "motor shaft hits outer wall");
assert(esp_window || pcb_z + pcb_t + esp_h <= tub_h, "ESP32 hits lid: set esp_window = true");
assert(battery_pos[1] - 1 - strap_slot > ch_y1 + 1.2, "strap slot hits motor saddle");

// ---------- helpers ----------
module rect(r) translate(r[0]) square(r[1] - r[0]);

module switch_win2d() {
    translate(switch_slot[0]) square(switch_slot[1] - switch_slot[0]);
    for (p = switch_holes) translate(p) circle(d = switch_hole_d);
}

module mirror_x(m) {
    if (m == 0) children();
    else translate([pcb_w, 0, 0]) mirror([1, 0, 0]) children();
}

module outline_outer()
    offset(r = corner_r) offset(delta = -corner_r) union() { rect(out_front); rect(out_rear); }

module outline_inner() offset(delta = -wall) outline_outer();

module pcb_outline()
    polygon([[6, 0], [72, 0], [72, 41], [78, 41], [78, 48], [70, 56],
             [8, 56], [0, 48], [0, 41], [6, 41]]);

module well_cut(z0, h)
    for (m = [0, 1]) mirror_x(m)
        translate([ow_x1, well_y0, z0]) cube([iw_x0 - ow_x1, well_y1 - well_y0, h]);

// ---------- tub ----------
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
            translate([hx - hinge_w_tub / 2, in_y0 - wall, axle_z - hinge_r - (in_y0 - wall - hinge_y)])
                cube([hinge_w_tub, wall, 2 * hinge_r + (in_y0 - wall - hinge_y)]);
        }
        translate([hx - hinge_w_tub / 2 - 1, hinge_y, axle_z]) rotate([0, 90, 0])
            cylinder(d = pilot_d, h = hinge_w_tub + 2);
    }
}

module tub() {
    difference() {
        union() {
            difference() {
                linear_extrude(tub_h) outline_outer();
                translate([0, 0, floor_t]) linear_extrude(tub_h) outline_inner();
            }
            for (m = [0, 1]) mirror_x(m) {
                translate([iw_x0, well_y0, 0]) cube([iw_t, well_y1 - well_y0, tub_h]);
                motor_saddles();
            }
            for (p = pcb_holes) translate([p[0], p[1], 0]) cylinder(d = standoff_d, h = pcb_z);
            for (hx = hinge_x) tub_knuckle(hx);
        }
        well_cut(-1, tub_h + 2);
        for (m = [0, 1]) mirror_x(m) {
            translate([iw_x0 - 1, axle_y, axle_z]) rotate([0, 90, 0]) cylinder(d = shaft_boss_d, h = iw_t + 2);
            translate([in_x0, snap_y, tub_h - lid_lip_h / 2]) sphere(r = snap_r);
        }
        for (p = pcb_holes) translate([p[0], p[1], pcb_z - 8]) cylinder(d = pilot_d, h = 9);
        for (sx = strap_x, sy = [battery_pos[1] - 1 - strap_slot, battery_pos[1] + battery[1] + 1])
            translate([sx - strap_w / 2, sy, -1]) cube([strap_w, strap_slot, floor_t + 2]);
    }
}

// ---------- lid ----------
module lid() {
    difference() {
        union() {
            translate([0, 0, tub_h]) linear_extrude(lid_t) outline_outer();
            translate([0, 0, tub_h - lid_lip_h]) linear_extrude(lid_lip_h + 0.01) difference() {
                offset(delta = -lid_fit) outline_inner();
                offset(delta = -lid_fit - lid_lip_t) outline_inner();
            }
            for (i = lid_screw_holes) translate([pcb_holes[i][0], pcb_holes[i][1], pcb_z + pcb_t])
                cylinder(d = standoff_d, h = tub_h - pcb_z - pcb_t + 0.01);
            for (m = [0, 1]) mirror_x(m)
                translate([in_x0 + lid_fit, snap_y, tub_h - lid_lip_h / 2]) sphere(r = snap_r - 0.2);
        }
        well_cut(-1, H + 2);
        for (m = [0, 1]) mirror_x(m)
            translate([ow_x1, well_y0 - 1, tub_h - lid_lip_h - 1])
                cube([iw_x1 + lid_fit - ow_x1, well_y1 - well_y0 + 1, lid_lip_h + 1]);
        for (i = lid_screw_holes) translate([pcb_holes[i][0], pcb_holes[i][1], pcb_z])
            cylinder(d = screw_clear_d, h = H);
        translate([0, 0, tub_h - 1]) linear_extrude(lid_t + 2) switch_win2d();
        if (led_enable) translate([led_pos[0], led_pos[1], tub_h - 1]) cylinder(d = led_d, h = lid_t + 2);
        if (esp_window) translate([esp_body[0][0] - 1, esp_body[0][1] - 1, tub_h - 1])
            cube([esp_body[1][0] - esp_body[0][0] + 2, esp_body[1][1] - esp_body[0][1] + 2, lid_t + 2]);
    }
}

// ---------- wedge (hinge axis = local x-axis, plate flat on z = -hinge_r) ----------
module wedge() {
    ks = [for (hx = hinge_x, s = [-1, 1]) hx + s * (hinge_w_tub / 2 + hinge_gap + hinge_w_wedge / 2)];
    difference() {
        union() {
            translate([ow_x0, -(hinge_r + hinge_gap + wedge_l), -hinge_r])
                cube([width, wedge_l, wedge_t]);
            for (kx = ks) hull() {
                translate([kx - hinge_w_wedge / 2, 0, 0]) rotate([0, 90, 0]) cylinder(r = hinge_r, h = hinge_w_wedge);
                translate([kx - hinge_w_wedge / 2, -(hinge_r + hinge_gap + 1), -hinge_r])
                    cube([hinge_w_wedge, 1, wedge_t]);
            }
        }
        for (kx = ks) translate([kx - hinge_w_wedge / 2 - 1, 0, 0]) rotate([0, 90, 0])
            cylinder(d = screw_clear_d, h = hinge_w_wedge + 2);
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
    color("lightgray") tub();
    color("white", 0.4) lid();
    color("red") translate([0, hinge_y, axle_z]) rotate([wedge_angle, 0, 0]) wedge();
    ghosts();
}

if (part == "assembly") assembly();
else if (part == "tub") tub();
else if (part == "lid") translate([0, 0, H]) rotate([180, 0, 0]) lid();
else if (part == "wedge") translate([0, 0, hinge_r]) wedge();
