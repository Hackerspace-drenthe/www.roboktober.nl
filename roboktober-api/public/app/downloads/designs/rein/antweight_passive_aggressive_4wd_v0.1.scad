// Antweight "Passive Aggressive 4WD" v0.1 - invertible 4WD pusher, passive weapons only.
// Derived from Passive Aggressive v0.1: same gravity-hinged wedge with 4 wedgelets on the front mandibles,
// angry eyes, tail spikes. Now 4x N20 (Pololu) + 4x 34x10 wheels; wheels stick out top and bottom.
// Second DRV8833 (loose module) lies in a floor frame between the rear motors. Wire its IN1-IN4 in parallel
// with DRV1 (left front + left rear = same signal), VM/GND from the battery; DRV1 drives the front motors.
// Side "intakes" (V-notches) between the wheel pods; the battery sits between the axles, strapped across x.
// Coordinates = PCB-local: x = width, y = length (y = 0 = narrow PCB edge = front), z = 0 = tub bottom.
// Driving direction is -y.

part = "assembly"; // [assembly, tub, lid, lid_glass, wedge, motor_clamp, test_motor, test_hinge, test_wedge]
$fn = 64;

/* [PCB] */
pcb_w = 78;
pcb_l = 56;
pcb_t = 1.6;
pcb_clear = 0.3;
pcb_gap = 0.3;                       // PCB above the motor clamps
pcb_holes = [[9.14, 3.14], [68.86, 3.14], [3.95, 44.56], [74.05, 44.56]];
lid_screw_holes = [0, 1];            // front PCB holes: M2 x 16 through lid boss + PCB into the tub
switch_slot = [[63.65, 38.65], [66.65, 45.65]];
switch_holes = [[65.15, 34.7], [65.15, 49.6]];   // switch mounting holes (pcb-dimensions.DXF)
switch_hole_d = 2;

/* [Components (estimates, measure!)] */
drv_center = [37.93, 15.5];          // DRV8833 #1 in its socket under the PCB
drv_pins = 6;
drv_w = 18;
drv_len = drv_pins * 2.54 + (22.5 - 8 * 2.54);
drv_body = [drv_center - [drv_len / 2, drv_w / 2], drv_center + [drv_len / 2, drv_w / 2]];
drv_h = 9;
drv2_size = [drv_len, drv_w, 5];     // DRV8833 #2, loose module flat on the floor, wires soldered on top
drv2_dy = 1;                         // module centre behind the rear axle
drv2_fit = 0.3;
drv2_frame = [1.2, 3, 4];            // corner brackets: wall, height, leg length
esp_body = [[28.1, 31.7], [48.4, 49.5]];
esp_h = 4;                           // ESP32-C3 on top, soldered low (max ~2 mm header spacer)
battery = [50, 30, 8];
battery_y0 = 29.5;                   // between the axles, under the PCB

/* [Drive] */
wheel_d = 34;
wheel_w = 10;
wheel_gap = 1;
ground_clearance = 1.5;              // body to floor, both orientations
axle_y = 21;                         // front axle; the rear axle follows from the PCB and the battery
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
clamp_screw_x = 9.3;                 // from the gearbox face; keeps the bosses clear of the battery
clamp_screw_margin = 1;
clamp_boss_d = 4.4;
clamp_head_d = 4.2;
clamp_head_recess = 1.6;

/* [Chassis] */
wall = 1.6;
side_t = 2.0;                        // outer wall beside the wheels
front_t = 2.0;
floor_t = 1.2;
lid_t = 1.6;
iw_t = 1.4;
gb_gap = 0.3;
standoff_d = 5;
pilot_d = 1.7;
screw_clear_d = 2.4;
screw_head_d = 4.2;
screw_head_recess = 1.4;
lid_lip_h = 2;
lid_lip_t = 1.2;
lid_fit = 0.2;
snap_x = [25, 53];                   // snaps on the rear wall
snap_r = 0.8;
strap_w = 11;
strap_slot = 2;
edge_c = 1.6;                        // stepped 45 degree chamfer, top and bottom
layer_h = 0.2;
notch_x = -3;                        // side intake depth (x of the V-notch tip)

/* [Tail spikes] */
spike_x = [-16, 0, 16];              // relative to the centre line
spike_w = 10;
spike_l = 7;

/* [Wedge + hinge] */
tube_d = 7;                          // hinge tube along the wedge back edge
hinge_gap = 1;                       // tube to body front
hinge_dz = 2.8;                      // pivot above the mid-plane: the plate sits under the pin, evens out both rest angles
pin_pilot_d = 1.7;                   // M2 pivot screw threads into the mandible
pin_clear_d = 2.4;                   // the wedge turns on the screw shank
pin_head_recess = 1.5;
ear_t = 5;                           // mandible thickness
ear_fit = 0.4;                       // per side, wedge to mandible
ear_apex = 8;                        // mandible point ahead of the pivot
ear_r = 4.5;
plate_t = 2.4;
plate_l = 26;                        // pivot to plate edge
plate_corner = 4;
fork_x = [-42, -18, 18, 42];         // wedgelets, relative to the centre line
fork_l = 8;
fork_w0 = 7;
fork_w1 = 2.5;
bevel_l = 12;                        // top bevel back from the fork tips
tip_t = 0.6;
stop_angle = 50;                     // swing limit both ways (from horizontal)
ledge_u = [2.5, 10];                 // stop ledge, distance along the plate from the pivot
ledge_w = 1.5;                       // into the wedge path
ledge_t = 1.5;

/* [Design] */
line_h = 1.0;                        // character line at axle height (slicer height range)
line_d = 0.4;
arch_r = 1.5;
eye_y = 36;
eye_gap = 2;
eye_len = 12;
eye_rise = 5;
eye_w = 3.5;
chev_w = 2.5;
chev_pitch = 5.5;
chev_rise = 0.5;                     // rise per mm half-width (tan of the chevron angle)
floor_chevrons = [[[31, 6], [47, 27]], [[17, 33], [61, 57]]];
lid_vents = [[28, 61], [50, 80]];    // over DRV #2

/* [Colours] */
palette = "stealth_copper"; // [stealth_copper, gunmetal_silver, hazard_red]
// [tub, character line, lid + clamps, eyes, wedge], each [filament, preview hex]
pal = palette == "gunmetal_silver"
        ? [["dark gray", "#3C3F41"], ["copper", "#B87333"], ["silver", "#C4C7CA"], ["transparent", "#E8F0F2"], ["silver", "#C4C7CA"]]
    : palette == "hazard_red"
        ? [["dark gray", "#3C3F41"], ["red", "#C8102E"], ["red", "#C8102E"], ["transparent", "#E8F0F2"], ["silver", "#C4C7CA"]]
        : [["dark gray", "#3C3F41"], ["copper", "#B87333"], ["dark gray", "#2E3032"], ["transparent", "#E8F0F2"], ["copper", "#B87333"]];

// ---------- derived ----------
H = wheel_d - 2 * ground_clearance;
axle_z = H / 2;
clamp_top_z = axle_z + gb_h / 2 - clamp_squeeze + clamp_top_t;
pcb_z = clamp_top_z + pcb_gap;
tub_h = H - lid_t;
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
front_y = in_y0 - front_t;
battery_pos = [cx - battery[0] / 2, battery_y0];
// rear wheel well must clear the PCB corner chamfer (0,48)-(8,56) at the well wall, and the battery
axle2_y = max(48 + iw_x1 + pcb_clear + iw_t + wheel_gap + wheel_d / 2,
              battery_y0 + battery[1] + 0.5 + clamp_wall + cw / 2);
axles = [axle_y, axle2_y];
well_y = [for (a = axles) [a - wheel_d / 2 - wheel_gap, a + wheel_d / 2 + wheel_gap]];
pod1_y1 = well_y[0][1] + iw_t;
pod2_y0 = well_y[1][0] - iw_t;
rear_y = well_y[1][1] + wall;
in_y1 = rear_y - wall;
drv2_c = [cx, axle2_y + drv2_dy];
arch_half = sqrt(pow(wheel_d / 2, 2) - pow(axle_z - floor_t, 2)) + wheel_gap;
arch_half_lid = sqrt(pow(wheel_d / 2, 2) - pow(tub_h - axle_z, 2)) + wheel_gap;
n_chamf = round(edge_c / layer_h);
line_z0 = round((axle_z - line_h / 2) / layer_h) * layer_h;

r_k = tube_d / 2;
L_tot = plate_l + fork_l;
z_h = axle_z + hinge_dz;
y_h = front_y - r_k - hinge_gap;
ear_in = ow_x0 + ear_t;
wx0 = ear_in + ear_fit;
wx1 = pcb_w - wx0;
// 4WD: body level on the wheels
h_up = z_h + ground_clearance;
h_dn = H - z_h + ground_clearance;
rest_up = asin(h_up / sqrt(pow(L_tot, 2) + pow(r_k, 2))) - atan2(r_k, L_tot);
rest_dn = asin(h_dn / sqrt(pow(L_tot, 2) + pow(r_k - tip_t, 2))) + atan2(r_k - tip_t, L_tot);
tip_y = y_h - (L_tot * cos(rest_up) + r_k * sin(rest_up));

echo(str("PA-4WD ", width, " x ", rear_y + spike_l - tip_y, " x ", H, " mm (wedge resting), body ",
         rear_y + spike_l - front_y, " mm, axles y ", axles, ", wheelbase ", axle2_y - axle_y, " mm"));
echo(str("Wedge rest angle upright ", rest_up, " deg, inverted ", rest_dn, " deg, stops +-", stop_angle, " deg"));
echo(str("PCB bottom z = ", pcb_z, ", ESP top ", pcb_z + pcb_t + esp_h, " / lid underside ", tub_h,
         ", lid boss ", tub_h - pcb_z - pcb_t, " mm"));
echo(str("Colours: tub ", pal[0][0], " with ", pal[1][0], " band z ", line_z0, "-", line_z0 + line_h,
         "; lid + clamps ", pal[2][0], "; eyes ", pal[3][0], "; wedge ", pal[4][0]));
echo("Screws: 2x M2x10 pivot (threadlock), 2x M2x16 lid, 2x M2x6 rear PCB, 8x M2x10 motor clamps");

assert(well_y[0][1] + iw_t <= 41 - pcb_clear, "front wheel well hits wide PCB part");
assert(gf_x - shaft_l > ow_x1, "motor shaft hits outer wall");
assert(pcb_z + pcb_t + esp_h <= tub_h - 0.5, "ESP32 hits the lid");
assert(floor_t + battery[2] < pcb_z - drv_h, "battery hits the DRV8833 under the PCB");
assert(battery_y0 > axle_y + cw / 2 + clamp_wall + 0.5, "battery hits front motor cradle");
assert(gf_x + clamp_screw_x + clamp_boss_d / 2 < battery_pos[0] - 0.3, "clamp boss hits battery");
assert(drv2_c[0] - drv2_size[0] / 2 - drv2_fit > gf_x + motor_total + 0.5, "DRV #2 hits rear motor terminals");
assert(drv2_c[1] - drv2_size[1] / 2 - drv2_fit - drv2_frame[0] > battery_y0 + battery[1] + 0.3, "DRV #2 hits battery");
assert(rest_up < stop_angle - 5 && rest_dn < stop_angle - 5, "wedge rests on its stop, not on the floor");

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

module spikes2d()
    for (dx = spike_x) translate([cx + dx, rear_y - 0.01])
        polygon([[-spike_w / 2, 0], [spike_w / 2, 0], [0, spike_l]]);

// full-width wheel pods front and rear, V-notch intakes between them
module body2d() {
    ym = (pod1_y1 + pod2_y0) / 2;
    xr = pcb_w - ow_x0;
    polygon([[ow_x0, front_y], [xr, front_y], [xr, pod1_y1], [pcb_w - notch_x, ym], [xr, pod2_y0], [xr, rear_y],
             [ow_x0, rear_y], [ow_x0, pod2_y0], [notch_x, ym], [ow_x0, pod1_y1]]);
}

module plan() union() { body2d(); spikes2d(); }

module outline_inner() intersection() {
    offset(delta = -wall) body2d();
    translate([-50, in_y0]) square([200, in_y1 - in_y0]);
}

module well_rect(i) translate([ow_x1, well_y[i][0]]) square([iw_x0 - ow_x1, well_y[i][1] - well_y[i][0]]);

module well_zone2d() {
    translate([-50, -50]) square([iw_x1 + lid_fit + 50, pod1_y1 + lid_fit + 50]);
    translate([-50, pod2_y0 - lid_fit]) square([iw_x1 + lid_fit + 50, 200]);
}

module arch_opening(a, half) rrect([ow_x1, a - half], [iw_x0, a + half], arch_r);

// chevrons pointing forward (-y), clipped to the rectangle
module chevrons2d(r0, r1) intersection() {
    translate(r0 + [chev_w / 2, chev_w / 2]) square(r1 - r0 - [chev_w, chev_w]);
    mx = (r0[0] + r1[0]) / 2;
    rise = (mx - r0[0]) * chev_rise;
    for (y = [r0[1] - rise : chev_pitch : r1[1]]) {
        slot2d([r0[0], y + rise], [mx, y], chev_w);
        slot2d([mx, y], [r1[0], y + rise], chev_w);
    }
}

// angry eyes: slits rising to the outside, inner ends forward
module eyes2d()
    for (s = [-1, 1])
        polygon([[cx + s * eye_gap, eye_y], [cx + s * (eye_gap + eye_len), eye_y + eye_rise],
                 [cx + s * (eye_gap + eye_len), eye_y + eye_rise + eye_w * 0.7], [cx + s * eye_gap, eye_y + eye_w]]);

module switch_win2d() {
    translate(switch_slot[0]) square(switch_slot[1] - switch_slot[0]);
    for (p = switch_holes) translate(p) circle(d = switch_hole_d);
}

module strap_slots2d()
    for (x = [battery_pos[0] - 1 - strap_slot, battery_pos[0] + battery[0] + 1])
        translate([x, battery_y0 + battery[1] / 2 - strap_w / 2]) square([strap_slot, strap_w]);

// ---------- 3D helpers ----------
module yz_extrude(x0, w) translate([x0, 0, 0]) rotate([90, 0, 90]) linear_extrude(w) children();

// body slab with stepped 45 degree chamfers top and bottom, clipped to z0..z1
module body_solid(z0 = 0, z1 = H) intersection() {
    union() {
        for (i = [0 : n_chamf - 1]) {
            d = edge_c - (i + 0.5) * layer_h;
            translate([0, 0, i * layer_h]) linear_extrude(layer_h + 0.01) offset(delta = -d) plan();
            translate([0, 0, H - (i + 1) * layer_h]) linear_extrude(layer_h + 0.01) offset(delta = -d) plan();
        }
        translate([0, 0, edge_c]) linear_extrude(H - 2 * edge_c + 0.01) plan();
    }
    translate([-100, -100, z0]) cube([400, 400, z1 - z0]);
}

// (y, z) side view around the pivot: u forward (-y), v up
module hinge2d() translate([y_h, z_h]) mirror([1, 0]) children();

// ---------- motor mount (one per corner, a = axle y) ----------
module motor_cavity(a) {
    translate([0, a, axle_z]) {
        yz_extrude(iw_x1 - 0.01, gf_x - iw_x1 + gearbox_l + motor_fit + 0.01)
            translate([-cw / 2, -gb_h / 2]) square([cw, gb_h - clamp_squeeze]);
        yz_extrude(gf_x + gearbox_l, motor_l + motor_term_l + 2) intersection() {
            circle(d = motor_d + 2 * motor_fit);
            translate([-cw / 2, -gb_h / 2]) square([cw, gb_h - clamp_squeeze]);
        }
    }
}

module clamp_screws(a, z, h, d) {
    for (s = [-1, 1]) translate([gf_x + clamp_screw_x, a + s * clamp_screw_y, z]) cylinder(d = d, h = h);
}

module clamp_ears(a, z, h) {
    for (s = [-1, 1]) hull() {
        translate([gf_x + clamp_screw_x, a + s * clamp_screw_y, z]) cylinder(d = clamp_boss_d, h = h);
        translate([gf_x + clamp_screw_x - clamp_boss_d / 2, a + (s < 0 ? -cw / 2 - clamp_wall : cw / 2), z])
            cube([clamp_boss_d, clamp_wall, h]);
    }
}

module motor_cradle(a) {
    x0 = iw_x1 - 0.01;
    l = gf_x + mount_l - x0;
    difference() {
        union() {
            translate([x0, a - cw / 2 - clamp_wall, axle_z - gb_h / 2 - 1.2])
                cube([l, cw + 2 * clamp_wall, gb_h / 2 + 1.2]);
            for (s = [-1, 1]) translate([x0, a + (s < 0 ? -cw / 2 - clamp_wall : cw / 2), 0])
                cube([l, clamp_wall, axle_z]);
            for (rx = [x0, gf_x + gearbox_l - 1, gf_x + mount_l - clamp_wall])
                translate([rx, a - cw / 2, 0]) cube([clamp_wall, cw, axle_z]);
            clamp_ears(a, 0, axle_z);
        }
        motor_cavity(a);
        clamp_screws(a, axle_z - 8, 9, pilot_d);
    }
}

module motor_clamp(a) {
    x0 = iw_x1 + 0.2;
    difference() {
        union() {
            translate([x0, a - cw / 2 - clamp_wall, axle_z])
                cube([gf_x + mount_l - x0, cw + 2 * clamp_wall, clamp_top_z - axle_z]);
            clamp_ears(a, axle_z, clamp_top_z - axle_z);
        }
        motor_cavity(a);
        clamp_screws(a, axle_z - 1, clamp_top_z - axle_z + 2, screw_clear_d);
        clamp_screws(a, clamp_top_z - clamp_head_recess, clamp_head_recess + 1, clamp_head_d);
    }
}

// corner brackets on the floor for DRV #2 (glue or tape the module in)
module drv2_frame() {
    s = [drv2_size[0], drv2_size[1]] + 2 * [drv2_fit, drv2_fit];
    t = drv2_frame[0];
    l = drv2_frame[2];
    translate([drv2_c[0], drv2_c[1], floor_t - 0.01]) linear_extrude(drv2_frame[1] + 0.01)
        for (mx = [0, 1], my = [0, 1]) mirror([mx, 0]) mirror([0, my]) translate(s / 2) difference() {
            translate([-l, -l]) square([l + t, l + t]);
            translate([-l - 1, -l - 1]) square([l + 1, l + 1]);
        }
}

// ---------- mandibles (wedge hinge ears with swing stops) ----------
module ledges2d() hinge2d() {
    rotate(-stop_angle) translate([ledge_u[0], -r_k - ledge_t]) square([ledge_u[1] - ledge_u[0], ledge_t]);
    rotate(stop_angle) translate([ledge_u[0], -r_k + plate_t]) square([ledge_u[1] - ledge_u[0], ledge_t]);
}

module ear2d() union() {
    hull() {
        translate([front_y - 0.01, 0]) square([0.01, H]);
        translate([y_h - 2, 0]) square([0.01, H]);
        translate([y_h, z_h]) circle(r = ear_r);
        translate([y_h - ear_apex, z_h]) circle(r = 0.5);
        ledges2d();
    }
    // root into the front wall, below the lid
    translate([front_y - 0.01, 0]) square([edge_c + 0.01, tub_h]);
}

module ears() for (m = [0, 1]) mirror_x(m) difference() {
    union() {
        yz_extrude(ow_x0, ear_t) ear2d();
        yz_extrude(ear_in - 0.01, ledge_w + 0.01) ledges2d();
    }
    translate([ow_x0 - 1, y_h, z_h]) rotate([0, 90, 0]) cylinder(d = pin_pilot_d, h = ear_t + 2);
    translate([ow_x0 - 1, y_h, z_h]) rotate([0, 90, 0]) cylinder(d = screw_head_d, h = pin_head_recess + 1);
}

// ---------- tub ----------
module well_boxes()
    intersection() {
        body_solid(0, tub_h);
        for (m = [0, 1], i = [0, 1]) mirror_x(m) linear_extrude(tub_h) difference() {
            offset(delta = iw_t) well_rect(i);
            well_rect(i);
        }
    }

module character_line()
    translate([0, 0, line_z0]) linear_extrude(line_h) difference() {
        offset(delta = 1) plan();
        offset(delta = -line_d) plan();
    }

module tub() {
    difference() {
        union() {
            difference() {
                union() {
                    difference() {
                        body_solid(0, tub_h);
                        translate([0, 0, floor_t]) linear_extrude(tub_h) outline_inner();
                    }
                    well_boxes();
                }
                character_line();
            }
            ears();
            for (m = [0, 1], a = axles) mirror_x(m) motor_cradle(a);
            for (p = pcb_holes) translate([p[0], p[1], 0]) cylinder(d = standoff_d, h = pcb_z);
            drv2_frame();
        }
        for (m = [0, 1], a = axles) mirror_x(m) {
            translate([0, 0, -1]) linear_extrude(floor_t + 2) arch_opening(a, arch_half);
            // open-top shaft slot: motor with wheel fitted drops straight in
            hull() for (z = [axle_z, tub_h + 1])
                translate([iw_x0 - 1, a, z]) rotate([0, 90, 0]) cylinder(d = shaft_boss_d, h = iw_t + 2);
        }
        for (p = pcb_holes) translate([p[0], p[1], pcb_z - 8]) cylinder(d = pilot_d, h = 9);
        translate([0, 0, -1]) linear_extrude(floor_t + 2) {
            strap_slots2d();
            for (r = floor_chevrons) chevrons2d(r[0], r[1]);
        }
        for (sx = snap_x) translate([sx, in_y1, tub_h - lid_lip_h / 2]) sphere(r = snap_r);
    }
}

// ---------- lid (prints upside down) ----------
module lid() {
    difference() {
        union() {
            body_solid(tub_h, H);
            translate([0, 0, tub_h - lid_lip_h]) linear_extrude(lid_lip_h + 0.01) difference() {
                offset(delta = -lid_fit) outline_inner();
                offset(delta = -lid_fit - lid_lip_t) outline_inner();
                for (m = [0, 1]) mirror_x(m) well_zone2d();
            }
            for (i = lid_screw_holes) translate([pcb_holes[i][0], pcb_holes[i][1], pcb_z + pcb_t])
                cylinder(d = standoff_d, h = tub_h - pcb_z - pcb_t + 0.01);
            for (sx = snap_x) translate([sx, in_y1 - lid_fit, tub_h - lid_lip_h / 2]) sphere(r = snap_r - 0.2);
        }
        for (m = [0, 1], a = axles) mirror_x(m)
            translate([0, 0, tub_h - 1]) linear_extrude(lid_t + 2) arch_opening(a, arch_half_lid);
        for (i = lid_screw_holes) {
            translate([pcb_holes[i][0], pcb_holes[i][1], pcb_z]) cylinder(d = screw_clear_d, h = H);
            translate([pcb_holes[i][0], pcb_holes[i][1], H - screw_head_recess]) cylinder(d = screw_head_d, h = 2);
        }
        translate([0, 0, tub_h - 1]) linear_extrude(lid_t + 2) {
            switch_win2d();
            chevrons2d(lid_vents[0], lid_vents[1]);
            eyes2d();
        }
    }
}

// transparent inlay for the eyes: load as extra part of the lid (same transform)
module lid_glass() translate([0, 0, tub_h]) linear_extrude(lid_t) eyes2d();

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

module wedge_placed(a) translate([pcb_w, y_h, z_h]) rotate([0, 0, 180]) rotate([a, 0, 0]) wedge_local();

// ---------- ghost components ----------
module ghosts() {
    for (m = [0, 1], a = axles) mirror_x(m) {
        color("black", 0.6) translate([wheel_x0, a, axle_z]) rotate([0, 90, 0]) cylinder(d = wheel_d, h = wheel_w);
        color("silver", 0.6) translate([0, a, axle_z]) {
            yz_extrude(gf_x, gearbox_l) square([gb_w, gb_h], center = true);
            yz_extrude(gf_x + gearbox_l, motor_l) intersection() {
                circle(d = motor_d);
                square([gb_w, gb_h], center = true);
            }
        }
        color("silver", 0.6) translate([gf_x - shaft_l, a, axle_z]) rotate([0, 90, 0]) cylinder(d = shaft_d, h = shaft_l);
    }
    color("green", 0.5) translate([0, 0, pcb_z]) linear_extrude(pcb_t) pcb_outline();
    color("purple", 0.6) translate([drv_body[0][0], drv_body[0][1], pcb_z - drv_h])
        cube([drv_body[1][0] - drv_body[0][0], drv_body[1][1] - drv_body[0][1], drv_h]);
    color("purple", 0.6) translate([drv2_c[0], drv2_c[1], floor_t + drv2_size[2] / 2]) cube(drv2_size, center = true);
    color("blue", 0.6) translate([esp_body[0][0], esp_body[0][1], pcb_z + pcb_t])
        cube([esp_body[1][0] - esp_body[0][0], esp_body[1][1] - esp_body[0][1], esp_h]);
    color("orange", 0.6) translate([battery_pos[0], battery_pos[1], floor_t]) cube(battery);
}

module assembly() {
    color(pal[0][1]) render() tub();
    color(pal[1][1]) translate([0, 0, line_z0 + 0.01]) linear_extrude(line_h - 0.02) difference() {
        offset(delta = -line_d + 0.05) plan();
        offset(delta = -wall) plan();
    }
    color(pal[2][1]) render() lid();
    color(pal[3][1], 0.6) lid_glass();
    color(pal[2][1]) for (m = [0, 1], a = axles) mirror_x(m) motor_clamp(a);
    color(pal[4][1]) wedge_placed(-rest_up);
    ghosts();
}

// shown level on four wheels, the wedge on the floor
// lid + lid_glass share one transform: load them together as one multi-part object
if (part == "assembly") assembly();
else if (part == "tub") color(pal[0][1]) tub();
else if (part == "lid") color(pal[2][1]) translate([0, 0, H]) rotate([180, 0, 0]) lid();
else if (part == "lid_glass") color(pal[3][1]) translate([0, 0, H]) rotate([180, 0, 0]) lid_glass();
// prints flat on the plate underside, tube on the bed
else if (part == "wedge") color(pal[4][1]) translate([0, 0, r_k]) wedge_local();
// print 4x; the same part fits every corner (rotate 180 deg)
else if (part == "motor_clamp") color(pal[2][1]) translate([0, 0, clamp_top_z]) rotate([180, 0, 0]) translate([-gf_x, -axle_y, 0]) motor_clamp(axle_y);
else if (part == "test_motor")
    intersection() { tub(); translate([iw_x0 - 1, axle_y - clamp_screw_y - clamp_boss_d, -1]) cube([mount_l + 4, 2 * (clamp_screw_y + clamp_boss_d), axle_z + 1]); }
// left mandible + front corner, and the matching left end of the wedge
else if (part == "test_hinge")
    intersection() { tub(); translate([ow_x0 - 1, y_h - ear_apex - 2, -1]) cube([ear_t + 14, front_y - y_h + ear_apex + 8, H + 2]); }
else if (part == "test_wedge")
    intersection() { translate([0, 0, r_k]) wedge_local(); translate([wx0 - 1, -r_k - 1, -1]) cube([16, L_tot + r_k + 2, tube_d + 2]); }
