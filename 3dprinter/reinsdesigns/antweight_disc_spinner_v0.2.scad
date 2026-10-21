// Disc spinner v0.2: wheels and N20 motors on the disc centre line, PCB centred between the motors (not on top).
// The robot turns on the spot about the disc centre. Flat front (-y) with a gravity-hinged wedge, radial spikes on both rims (invertible).
// Robot frame: x = width (centre line cx), y = length, z = 0 = tub bottom. Disc centre = (cx, cy) = axle.
// PCB-local coordinates (outline, holes, parts) are mapped by at_pcb(): length along y, ESP at the rear (or front with esp_front).

part = "assembly"; // [assembly, tub, lid, wedge, clamp, test_hinge, esp_clearance, wheel_clearance, pcb_clearance, motor_clearance, spike_clearance, lid_asm, wedge_asm, clamps_asm]
$fn = 96;

/* [PCB and parts] */
pcb_w = 78;
pcb_l = 56;
pcb_t = 1.6;
pcb_clear = 0.3;
pcb_narrow_w = 66;                   // PCB width over y_local 0..41, between the motors
pcb_yc = 20.5;                       // y_local centre of that narrow part, on the axle line
esp_front = false;                   // false: ESP / wide PCB end at the rear
pcb_holes = [[9.14, 3.14], [68.86, 3.14], [3.95, 44.56], [74.05, 44.56]];
pcb_pts = [[6, 0], [72, 0], [72, 41], [78, 41], [78, 48], [70, 56], [8, 56], [0, 48], [0, 41], [6, 41]];
switch_slot = [[63.65, 38.65], [66.65, 45.65]];
switch_holes = [[65.15, 34.7], [65.15, 49.6]];
switch_hole_d = 2;
drv_center = [37.93, 15.5];
drv_len = 6 * 2.54 + (22.5 - 8 * 2.54);
drv_w = 18;
drv_h = 3.5;
esp_body = [[28.1, 31.7], [48.4, 49.5]];
esp_h = 4.5;
jst_h = 6;
battery = [20, 30, 6.5];

/* [Drive] */
wheel_d = 34;
wheel_w = 10;
wheel_gap = 1;
ground_clearance = 1;
motor_gap = 2;                       // N20 terminals to the PCB edge
gearbox_l = 9;
gb_w = 12;
gb_h = 10;
motor_d = 12;
motor_l = 15;
motor_term_l = 3;
shaft_l = 10;
shaft_boss_d = 5.4;
motor_fit = 0.25;
clamp_squeeze = 0.2;
clamp_wall = 1.6;
clamp_top_t = 1.2;
clamp_screw_x = 12;
clamp_boss_d = 4.4;
clamp_head_d = 4.2;
clamp_head_recess = 1.6;
iw_t = 1.4;
gb_gap = 0.3;

/* [Shell] */
disc_cy = 40;
rim_r = 65;
ramp_a = 90;                         // vertical wall: the motors sit at the rim
ramp_t = 0.8;
floor_t = 0.8;
top_t = 0.8;
front_t = 1.6;
flat_k = 0.7;                        // flat front at this fraction of the radius ahead of the centre
standoff_d = 5;
pilot_d = 1.7;
screw_clear_d = 2.4;
screw_head_d = 4.2;
screw_head_recess = 1.2;
win_r0 = 26;                         // lightening windows (spokes stay under the PCB posts)
win_r0_lid = 34;                     // keeps clear of the ESP window
win_edge = 8;
win_sectors = [[16, 27], [52, 80], [100, 128], [153, 164], [-80, -42], [-138, -100]];

/* [Spikes] */
spike_reach = 9;                     // lid spikes, beyond the lid rim radius R
spike_w = 10;
spike_h = 8;
spike_tip_h = 1.2;
spike_root = 2;
spike_reach_low = 10;                // tub spikes: reach limited by the 150 mm square
spike_w_low = 14;
spike_h_low = 12;
spike_tip_h_low = 3;
spike_tip_w_low = 1;
spike_root_low = 2;                  // vertical wall: 2 mm into the wall
// skip the wedge (front) and the wheel arches (sides)
spike_angles = [30, 45, 60, 75, 90, 105, 120, 135, 150, 210, 330];

/* [Wedge and hinge] */
ear_x = 8;
ear_t = 5;
ear_fit = 0.4;
ear_r = 4.5;
ear_apex = 8;
tube_d = 7;
hinge_gap = 1;
hinge_dz = 2.8;
pin_pilot_d = 1.7;
pin_clear_d = 2.4;
plate_t = 1.6;
plate_l = 24;
plate_corner = 3;
fork_l = 4;
fork_x = [-18, -6, 6, 18];
fork_w0 = 7;
fork_w1 = 2.5;
bevel_l = 8;
tip_t = 0.5;
stop_angle = 50;
ledge_u = [2.5, 9];
ledge_w = 1.5;
ledge_t = 1.4;

// ---------- derived ----------
H = wheel_d - 2 * ground_clearance;
axle_z = H / 2;
cx = pcb_w / 2;
cy = disc_cy;
axle_y = cy;
cot = ramp_a >= 90 ? 0 : 1 / tan(ramp_a);
dt = ramp_t / sin(ramp_a);
R = rim_r;
R_top = R - H * cot;
function r_in(z) = R - dt - z * cot;
cw = gb_w + 2 * motor_fit;
motor_total = gearbox_l + motor_l + motor_term_l;
mount_l = gearbox_l + motor_l + motor_fit;
clamp_top_z = axle_z + gb_h / 2 - clamp_squeeze + clamp_top_t;
tub_h = clamp_top_z + 0.5;           // split above the motor clamps
pcb_z = 14.2;
gf_x = cx - pcb_narrow_w / 2 - motor_gap - motor_total;
iw_x1 = gf_x - gb_gap;
iw_x0 = iw_x1 - iw_t;
wheel_x0 = iw_x0 - wheel_gap - wheel_w;
clamp_screw_y = cw / 2 + 2.2;
well_y0 = axle_y - wheel_d / 2 - wheel_gap;
battery_pos = [cx - battery[0] / 2, cy - battery[1] / 2 - 6];   // forward of the axle: the PCB sits behind it
flat_y = cy - flat_k * R;
tip_r = R + spike_reach;
tip_r_low = R + spike_reach_low;
function pcb_pt(p) = esp_front ? [cx + pcb_w / 2 - p[0], cy + pcb_yc - p[1]] : [cx - pcb_w / 2 + p[0], cy - pcb_yc + p[1]];
function hole_ang(p) = atan2(pcb_pt(p)[1] - cy, pcb_pt(p)[0] - cx);
function in_window(a) = len([for (s = win_sectors) if (a > s[0] - 5 && a < s[1] + 5) 1]) > 0;
pcb_r = max([for (p = pcb_pts) norm(pcb_pt(p) - [cx, cy])]);
cradle_r = norm([cx - iw_x1, cw / 2 + clamp_wall]);
width = 2 * (cx - wheel_x0);

r_k = tube_d / 2;
z_h = axle_z + hinge_dz;
y_h = flat_y - r_k - hinge_gap;
L_tot = plate_l + fork_l;
wx0 = ear_x + ear_t + ear_fit;
wx1 = pcb_w - wx0;
h_up = z_h + ground_clearance;
rest_up = asin(h_up / sqrt(pow(L_tot, 2) + pow(r_k, 2))) - atan2(r_k, L_tot);
tip_y = y_h - L_tot * cos(rest_up) - r_k * sin(rest_up);
spike_y_max = cy + max([for (a = spike_angles) max(tip_r, tip_r_low) * sin(a)]);
length = spike_y_max - tip_y;

echo(str("Disc spinner v0.2: disc d", 2 * R, ", spikes d", 2 * tip_r_low, ", wheels ", width, " wide, height ", H, ", length incl. wedge ", length, " mm"));
echo(str("Axle on the disc centre y = ", axle_y, "; motors x ", gf_x, "..", gf_x + motor_total, " | PCB narrow part x ", cx - pcb_narrow_w / 2, "..", cx + pcb_narrow_w / 2, "; split z ", tub_h, ", wedge rest ", rest_up, " deg"));
echo("Screws: 4x M2x25 (lid posts, PCB, standoffs), 2x M2x10 wedge pivots, 4x M2x10 motor clamps");

assert(2 * tip_r_low <= 150 && width <= 150 && length <= 150, "exceeds 150 x 150 mm");
assert(spike_root_low >= spike_h_low * cot + 0.5, "tub spike root does not reach the cone wall");
assert(pcb_r + pcb_clear <= r_in(pcb_z + pcb_t) - 0.5, "PCB hits the conical wall");
assert(cradle_r <= r_in(tub_h) - 0.5, "motor cradle hits the conical wall");
assert(pcb_z + pcb_t + esp_h <= tub_h, "ESP above the split");
assert(tub_h + 0.5 < H - top_t, "no room above the split");
assert(floor_t + battery[2] < pcb_z - max(drv_h, jst_h), "battery hits the parts under the PCB");
assert(gf_x - shaft_l > wheel_x0 && gf_x - shaft_l < wheel_x0 + wheel_w, "motor shaft misses the wheel");
assert(rest_up < stop_angle - 5, "wedge rests on its stop");
assert(wx1 - wx0 > fork_x[3] * 2 + fork_w0, "forks outside the plate");
assert(len([for (p = pcb_holes) if (in_window(hole_ang(p))) 1]) == 0, "lightening windows hit the PCB posts");

// ---------- 2D / 3D helpers ----------
module mirror_x(m) {
    if (m == 0) children();
    else translate([pcb_w, 0, 0]) mirror([1, 0, 0]) children();
}
module rrect(r0, r1, r) translate([r0[0] + r, r0[1] + r]) offset(r = r) square([r1[0] - r0[0] - 2 * r, r1[1] - r0[1] - 2 * r]);
module at_pcb() {
    if (esp_front) translate([cx + pcb_w / 2, cy + pcb_yc]) rotate(180) children();
    else translate([cx - pcb_w / 2, cy - pcb_yc]) children();
}
module pcb_outline() polygon(pcb_pts);
module esp_win2d() at_pcb() rrect(esp_body[0] - [1, 1], esp_body[1] + [1, 1], 2);
module switch_win2d() at_pcb() {
    translate(switch_slot[0]) square(switch_slot[1] - switch_slot[0]);
    for (p = switch_holes) translate(p) circle(d = switch_hole_d);
}
module yz_extrude(x0, w) translate([x0, 0, 0]) rotate([90, 0, 90]) linear_extrude(w) children();
module slab(z0, z1) translate([-300, -300, z0]) cube([600, 600, z1 - z0]);

module sectors2d(r0, r1) intersection() {
    translate([cx, cy]) for (s = win_sectors)
        polygon(concat([for (k = [0 : 20]) r1 * [cos(s[0] + (s[1] - s[0]) * k / 20), sin(s[0] + (s[1] - s[0]) * k / 20)]],
                       [for (k = [20 : -1 : 0]) r0 * [cos(s[0] + (s[1] - s[0]) * k / 20), sin(s[0] + (s[1] - s[0]) * k / 20)]]));
    translate([-300, flat_y + front_t + 3]) square(600);
}

module outer3d() translate([cx, cy]) rotate_extrude() polygon([[0, 0], [R, 0], [R_top, H], [0, H]]);

module shell3d() translate([cx, cy]) rotate_extrude() difference() {
    polygon([[0, 0], [R, 0], [R_top, H], [0, H]]);
    polygon([[-1, floor_t], [r_in(floor_t), floor_t], [r_in(H - top_t), H - top_t], [-1, H - top_t]]);
}

module body_shell() {
    intersection() { shell3d(); translate([-300, flat_y, -2]) cube([600, 600, H + 4]); }
    intersection() { outer3d(); translate([-300, flat_y, 0]) cube([600, front_t, H]); }
}

module wheel_cuts() for (m = [0, 1]) mirror_x(m)
    translate([-100, axle_y, axle_z]) rotate([0, 90, 0]) cylinder(d = wheel_d + 2 * wheel_gap, h = iw_x0 + 100);

module well_wall(z0, z1) for (m = [0, 1]) mirror_x(m) intersection() {
    translate([iw_x0, well_y0, z0]) cube([iw_t, wheel_d + 2 * wheel_gap, z1 - z0]);
    outer3d();
}

// ---------- motor mount ----------
module motor_cavity() translate([0, axle_y, axle_z]) {
    yz_extrude(iw_x1 - 0.01, gf_x - iw_x1 + gearbox_l + motor_fit + 0.01)
        translate([-cw / 2, -gb_h / 2]) square([cw, gb_h - clamp_squeeze]);
    yz_extrude(gf_x + gearbox_l, motor_l + motor_term_l + 2) intersection() {
        circle(d = motor_d + 2 * motor_fit);
        translate([-cw / 2, -gb_h / 2]) square([cw, gb_h - clamp_squeeze]);
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

// ---------- spikes ----------
module spike_local(len, w, h, tip_h, tip_w) hull() {
    translate([0, -w / 2, 0]) cube([0.2, w, h]);
    translate([len - 0.4, -tip_w / 2, 0]) cube([0.4, tip_w, tip_h]);
}

module spikes_ring(r_rim, root, tip, w, h, tip_h, tip_w) for (a = spike_angles)
    translate([cx, cy, 0]) rotate(a) translate([r_rim - root, 0, 0]) spike_local(tip - r_rim + root, w, h, tip_h, tip_w);

module spikes_bottom() spikes_ring(R, spike_root_low, tip_r_low, spike_w_low, spike_h_low, spike_tip_h_low, spike_tip_w_low);
module spikes_top() translate([0, 0, H]) mirror([0, 0, 1]) spikes_ring(R_top, spike_root, tip_r, spike_w, spike_h, spike_tip_h, 0.7);

// ---------- hinge ears ----------
module hinge2d() translate([y_h, z_h]) mirror([1, 0]) children();

module ledges2d() hinge2d() {
    rotate(-stop_angle) translate([ledge_u[0], -r_k - ledge_t]) square([ledge_u[1] - ledge_u[0], ledge_t]);
    rotate(stop_angle) translate([ledge_u[0], -r_k + plate_t]) square([ledge_u[1] - ledge_u[0], ledge_t]);
}

module ear2d() union() {
    hull() {
        translate([flat_y, 0]) square([0.2, tub_h]);
        translate([y_h - 2, 0]) square([0.01, tub_h]);
        translate([y_h, z_h]) circle(r = ear_r);
        translate([y_h - ear_apex, z_h]) circle(r = 0.5);
        ledges2d();
    }
    translate([flat_y, 0]) square([front_t + 0.2, tub_h]);
}

module ears() for (m = [0, 1]) mirror_x(m) difference() {
    union() {
        yz_extrude(ear_x, ear_t) ear2d();
        yz_extrude(ear_x + ear_t - 0.01, ledge_w + 0.01) ledges2d();
    }
    translate([ear_x - 1, y_h, z_h]) rotate([0, 90, 0]) cylinder(d = pin_pilot_d, h = ear_t + 2);
    translate([ear_x - 1, y_h, z_h]) rotate([0, 90, 0]) cylinder(d = screw_head_d, h = 2.5);
}

// ---------- tub ----------
module tub() {
    difference() {
        union() {
            intersection() { body_shell(); slab(0, tub_h); }
            at_pcb() for (p = pcb_holes) translate([p[0], p[1], 0]) cylinder(d = standoff_d, h = pcb_z);
            for (sx = [-1, 1]) translate([cx + sx * (battery[0] / 2 + 1) - 0.5, battery_pos[1], 0]) cube([1, battery[1], 3]);
            for (m = [0, 1]) mirror_x(m) motor_cradle();
            well_wall(0, tub_h);
            ears();
            spikes_bottom();
        }
        wheel_cuts();
        for (m = [0, 1]) mirror_x(m)
            hull() for (z = [axle_z, tub_h + 1])
                translate([iw_x0 - 1, axle_y, z]) rotate([0, 90, 0]) cylinder(d = shaft_boss_d, h = iw_t + 2);
        at_pcb() for (p = pcb_holes) translate([p[0], p[1], pcb_z - 8]) cylinder(d = pilot_d, h = 9);
        translate([0, 0, -1]) linear_extrude(floor_t + 1.01) sectors2d(win_r0, R - win_edge);
    }
}

// ---------- lid (prints upside down) ----------
module lid() {
    difference() {
        union() {
            intersection() { body_shell(); slab(tub_h, H); }
            well_wall(tub_h, H);
            at_pcb() for (p = pcb_holes) translate([p[0], p[1], pcb_z + pcb_t]) cylinder(d = standoff_d, h = H - pcb_z - pcb_t - 0.01);
            spikes_top();
        }
        wheel_cuts();
        at_pcb() for (p = pcb_holes) {
            translate([p[0], p[1], pcb_z + pcb_t - 1]) cylinder(d = screw_clear_d, h = H);
            translate([p[0], p[1], H - screw_head_recess]) cylinder(d = screw_head_d, h = 2);
        }
        translate([0, 0, H - top_t - 1]) linear_extrude(top_t + 2) {
            esp_win2d();
            switch_win2d();
        }
        translate([0, 0, H - top_t - 1]) linear_extrude(top_t + 2.01) sectors2d(win_r0_lid, R_top - win_edge);
    }
}

// ---------- wedge ----------
module wedge_plan2d() {
    polygon([[wx0, 0], [wx1, 0], [wx1, plate_l - plate_corner], [wx1 - plate_corner, plate_l],
             [wx0 + plate_corner, plate_l], [wx0, plate_l - plate_corner]]);
    for (fx = fork_x) translate([cx + fx, 0])
        polygon([[-fork_w0 / 2, plate_l - 0.01], [fork_w0 / 2, plate_l - 0.01], [fork_w1 / 2, L_tot], [-fork_w1 / 2, L_tot]]);
}

// local frame: x = width, y = u (forward from the pivot), z = v; pivot on the x axis, plate under the pin
module wedge_local() difference() {
    union() {
        intersection() {
            translate([0, 0, -r_k]) linear_extrude(plate_t) wedge_plan2d();
            yz_extrude(wx0 - 1, wx1 - wx0 + 2)
                polygon([[-1, -r_k - 1], [L_tot + 1, -r_k - 1], [L_tot + 1, -r_k + tip_t],
                         [L_tot - bevel_l, -r_k + plate_t + 0.01], [-1, -r_k + plate_t + 0.01]]);
        }
        translate([wx0, 0, 0]) rotate([0, 90, 0]) cylinder(d = tube_d, h = wx1 - wx0);
    }
    translate([wx0 - 1, 0, 0]) rotate([0, 90, 0]) cylinder(d = pin_clear_d, h = wx1 - wx0 + 2);
}

module wedge_placed(a = -rest_up) translate([pcb_w, y_h, z_h]) rotate([0, 0, 180]) rotate([a, 0, 0]) wedge_local();

// ---------- ghosts / clearance envelopes ----------
module wheels_env(d = wheel_d) for (m = [0, 1]) mirror_x(m)
    translate([wheel_x0, axle_y, axle_z]) rotate([0, 90, 0]) cylinder(d = d, h = wheel_w);

module motors_env() for (m = [0, 1]) mirror_x(m) translate([0, axle_y, axle_z]) {
    yz_extrude(gf_x, gearbox_l) square([gb_w, gb_h], center = true);
    yz_extrude(gf_x + gearbox_l, motor_l + motor_term_l) intersection() {
        circle(d = motor_d);
        square([gb_w, gb_h], center = true);
    }
    translate([gf_x - shaft_l, 0, 0]) rotate([0, 90, 0]) cylinder(d = 3, h = shaft_l);
}

module parts_env() {
    at_pcb() {
        translate([0, 0, pcb_z + 0.02]) linear_extrude(pcb_t - 0.04) difference() {
            pcb_outline();
            for (p = pcb_holes) translate(p) circle(d = screw_clear_d);
        }
        translate([drv_center[0] - drv_len / 2, drv_center[1] - drv_w / 2, pcb_z - drv_h]) cube([drv_len, drv_w, drv_h]);
        translate([esp_body[0][0], esp_body[0][1], pcb_z + pcb_t])
            cube([esp_body[1][0] - esp_body[0][0], esp_body[1][1] - esp_body[0][1], esp_h]);
    }
    translate([battery_pos[0], battery_pos[1], floor_t]) cube(battery);
}

module assembly() {
    color("#3C3F41") render() tub();
    color("#C4C7CA") render() lid();
    color("#55595C") for (m = [0, 1]) mirror_x(m) motor_clamp();
    color("#B87333") wedge_placed();
    color("black", 0.6) wheels_env();
    color("silver", 0.6) motors_env();
    color("green", 0.4) parts_env();
}

if (part == "assembly") assembly();
else if (part == "tub") tub();
else if (part == "lid") translate([0, 0, H]) rotate([180, 0, 0]) lid();
else if (part == "wedge") translate([0, 0, r_k]) wedge_local();
else if (part == "clamp") translate([0, 0, clamp_top_z]) rotate([180, 0, 0]) translate([-gf_x, -axle_y, 0]) motor_clamp();
else if (part == "lid_asm") lid();
else if (part == "wedge_asm") wedge_placed();
else if (part == "clamps_asm") for (m = [0, 1]) mirror_x(m) motor_clamp();
else if (part == "test_hinge")
    intersection() { tub(); translate([ear_x - 1, y_h - ear_apex - 3, -1]) cube([ear_t + 14, flat_y - y_h + ear_apex + 8, H + 2]); }
else if (part == "esp_clearance") intersection() {
    lid();
    at_pcb() translate([esp_body[0][0] - 0.5, esp_body[0][1] - 0.5, pcb_z + pcb_t])
        cube([esp_body[1][0] - esp_body[0][0] + 1, esp_body[1][1] - esp_body[0][1] + 1, esp_h + 0.5]);
}
else if (part == "wheel_clearance") intersection() {
    wheels_env(wheel_d + 0.2);
    union() { tub(); lid(); wedge_placed(); }
}
else if (part == "pcb_clearance") intersection() { parts_env(); union() { tub(); lid(); } }
else if (part == "motor_clearance") intersection() {
    motors_env();
    union() { tub(); lid(); for (m = [0, 1]) mirror_x(m) motor_clamp(); }
}
else if (part == "spike_clearance") intersection() {
    union() { spikes_bottom(); spikes_top(); }
    union() { wheels_env(wheel_d + 0.2); for (a = [-rest_up, 50, -50]) wedge_placed(a); }
}
