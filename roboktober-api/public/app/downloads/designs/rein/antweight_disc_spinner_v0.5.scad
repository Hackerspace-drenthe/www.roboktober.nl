// Disc spinner v0.5: minimum practical core and long, floor-braced tub spikes.
// The motors now sit under the PCB right up to the narrow PCB edge (wheels 1 mm outside it) and slide under the DRV8833,
// which is raised 0.3 mm above the motor clamps. Wheel reach drops from 52.6 to 47.2 mm, so the disc shrinks from d110 to d104.
// The battery lies across the front (30 x 20 mm), ahead of the motors, which also balances the PCB behind the axle.
// Two levels: N20 motors + wheels low on the disc centre (wheels stick out at the bottom only), PCB + ESP above them.
// Flat front (-y) with a gravity-hinged wedge, radial tub spikes, snap-fit lid. Upright only.
// Coordinates = PCB-local: x = width, y = length (y = 0 = narrow PCB edge = front), z = 0 = tub bottom.
// Axle = disc centre = (cx, axle_y). Driving direction is -y; the ESP / wide PCB end is at the rear.

part = "assembly"; // [assembly, tub, lid, wedge, clamp, test_hinge, esp_clearance, wheel_clearance, pcb_clearance, pcb_wheel_clearance, drv_clearance, motor_clearance, spike_clearance, lid_fit, lid_asm, wedge_asm, clamps_asm]
$fn = 96;

/* [PCB and parts] */
pcb_w = 78;
pcb_l = 56;
pcb_t = 1.6;
pcb_clear = 0.3;
pcb_gap_min = 2.5;                   // PCB underside to the motor clamps: room for wires and solder (asserted)
pcb_narrow_x0 = 6;                   // narrow PCB part (y 0..41) spans x 6..72: the wheels sit outside it
pcb_holes = [[9.14, 3.14], [68.86, 3.14], [3.95, 44.56], [74.05, 44.56]];
pcb_pts = [[6, 0], [72, 0], [72, 41], [78, 41], [78, 48], [70, 56], [8, 56], [0, 48], [0, 41], [6, 41]];
switch_slot = [[63.65, 38.65], [66.65, 45.65]];
switch_holes = [[65.15, 34.7], [65.15, 49.6]];
switch_hole_d = 2;
drv_center = [37.93, 15.5];          // DRV8833 breakout soldered flat under the PCB, above the motor inner ends
drv_len = 6 * 2.54 + (22.5 - 8 * 2.54);
drv_w = 18;
drv_h = 3.5;
drv_clear_z = 0.3;                   // DRV underside to the motor clamps
drv_body = [drv_center - [drv_len / 2, drv_w / 2], drv_center + [drv_len / 2, drv_w / 2]];
esp_body = [[28.1, 31.7], [48.4, 49.5]];
esp_h = 8;                           // ESP32 on headers
jst_h = 6;
battery = [30, 20, 6.5];             // 1S 300 mAh (602030), lying across the front: long side along x

/* [Drive] */
wheel_d = 34;
wheel_w = 10;
wheel_gap = 1;
wheel_pcb_gap = 1;                   // wheel inner face to the narrow PCB edge
ground_clearance = 1.5;              // body to floor: the wheels stick out this far at the bottom
wheel_top_min = 1.5;                 // wheel top to the lid skin, at least
axle_y = 21;
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
rim_r = 51.25;
wall_t = 1.6;
floor_t = 0.8;
top_t = 0.8;
front_t = 1.8;
flat_k = 0.7;                        // flat front at this fraction of the radius ahead of the centre
pilot_d = 1.7;
screw_clear_d = 2.4;
screw_head_d = 4.2;
screw_head_recess = 1.2;
boss_pts = [[14, -8], [64, -8], [27, 63], [51, 63]];   // extra lid screws, clear of the PCB, the battery and the lip
win_r0 = 20;                         // lightening windows sit between the post spokes
win_r0_lid = 38;                     // keeps the lid windows clear of the ESP opening
esp_win_margin = 6;                  // ESP opening in the lid: ESP body + this on every side
win_edge = 6;
win_sectors = [[43, 65], [83, 97], [115, 137]];
win_sectors_lid = [[43, 58], [83, 97], [122, 137]];
ring_t = 0.8;                        // wheel pocket front / back walls (open on the outer side; the inner wall stays iw_t)
post_d = 6.4;                        // screw posts: solid pilot zone on top, tube below
post_bore = 3.2;
post_solid_top = 10;
post_base_d = 10;                    // conical foot: posts snap off at a thin base
post_base_h = 5;
rib_t = 1.2;                         // gusset from every tub post to the wall
rib_end_frac = 0.4;                  // rib height at the wall as a fraction of the post height
spike_rib_t = 2;

/* [Lid click] */
lip_h = 9;                           // lid lip hangs this far into the tub (stays above the post ribs)
lip_t = 1.2;
lip_gap = 0.2;                       // lip to the tub wall
lip_flange_t = 1.6;                  // shelf in the lid that carries the lip
lip_arcs = [[26, 154], [206, 334]];  // skips the wheel pockets
snap_arc_angles = [60, 120, 215, 325];
snap_front_x = [pcb_w / 2 - 16, pcb_w / 2 + 16];   // hooks on the flat front wall
snap_w = 10;                         // flexing tab width
snap_slit = 0.8;
snap_h = 0.8;                        // hook height beyond the lip face
hook_z0 = 0.8;                       // hook base above the lip bottom
hook_catch_z = 2.6;                  // hook height: flat catch face on top
snap_win_w = 12;
snap_win_h = 3;

/* [Spikes] */
spike_angles = [30, 90, 150, 210, 330];   // tub spikes, symmetric, skip the wedge side
spike_envelope_margin = 2;             // reserve 2 mm inside the 150 mm overall length
spike_w_low = 8;
spike_h_low = 9;
spike_tip_h_low = 2.5;
spike_tip_w_low = 1;
spike_root_low = 3.5;
spike_rib_t = 1.6;
spike_gusset_start = 14;
spike_gusset_t = 2.4;

/* [Wedge and hinge] */
ear_x = 8;
ear_t = 5;
ear_fit = 0.4;
ear_r = 4.5;
ear_apex = 8;
tube_d = 7;
hinge_gap = 1;
z_h = 13;                            // pivot height above the tub bottom: keeps the lower stop ledge above the floor
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
stop_up = 50;
stop_down = 50;
ledge_u = [4, 10];                   // starts outside the hinge tube (radius r_k + 0.5)
ledge_w = 1.5;
ledge_t = 1.4;

// ---------- derived ----------
axle_z = wheel_d / 2 - ground_clearance;
cx = pcb_w / 2;
cy = axle_y;
R = rim_r;
cw = gb_w + 2 * motor_fit;
motor_total = gearbox_l + motor_l + motor_term_l;
mount_l = gearbox_l + motor_l + motor_fit;
clamp_top_z = axle_z + gb_h / 2 - clamp_squeeze + clamp_top_t;
pcb_z = clamp_top_z + drv_clear_z + drv_h;    // the DRV8833 passes over the motor inner ends
tub_h = pcb_z + pcb_t;                        // split at the PCB top
H = max(axle_z + wheel_d / 2 + wheel_top_min + top_t, tub_h + esp_h + 0.5 + top_t);
wheel_x0 = pcb_narrow_x0 - wheel_pcb_gap - wheel_w;
ow_x1 = wheel_x0 - wheel_gap;
iw_x0 = wheel_x0 + wheel_w + wheel_gap;
iw_x1 = iw_x0 + iw_t;
gf_x = iw_x1 + gb_gap;
clamp_screw_y = cw / 2 + 2.2;
well_y0 = axle_y - wheel_d / 2 - wheel_gap;
well_y1 = axle_y + wheel_d / 2 + wheel_gap;
arch_half = sqrt(pow(wheel_d / 2, 2) - pow(axle_z - floor_t, 2)) + wheel_gap;
battery_pos = [cx - battery[0] / 2, axle_y - clamp_screw_y - clamp_boss_d / 2 - 0.5 - battery[1]];
flat_y = cy - flat_k * R;
pcb_r = max([for (p = pcb_pts) norm(p - [cx, cy])]);
width = 2 * (cx - wheel_x0);

r_k = tube_d / 2;
y_h = flat_y - r_k - hinge_gap;
L_tot = plate_l + fork_l;
ear_h = z_h + ear_r + 1.5;
wx0 = ear_x + ear_t + ear_fit;
wx1 = pcb_w - wx0;
h_rest = z_h + ground_clearance;
rest = asin(h_rest / sqrt(pow(L_tot, 2) + pow(r_k, 2))) - atan2(r_k, L_tot);
tip_y = y_h - L_tot * cos(rest) - r_k * sin(rest);
spike_reach_low = 150 - spike_envelope_margin - cy + tip_y - R;
tip_r_low = R + spike_reach_low;
spike_y_max = cy + max([for (a = spike_angles) tip_r_low * sin(a)]);
length = spike_y_max - tip_y;
spike_x_max = max([for (a = spike_angles) tip_r_low * abs(cos(a))]);

echo(str("Disc spinner v0.5: compact core d", 2 * R, ", tub spike reach ", spike_reach_low, ", tip circle d", 2 * tip_r_low, ", wheels ", width, " wide, height ", H,
         ", length incl. wedge ", length, " mm"));
echo(str("Minimum core radius: PCB/lip ", pcb_r + pcb_clear + wall_t + lip_gap + lip_t + 0.5,
         " mm; wheel ", norm([cx - wheel_x0, wheel_d / 2]) + wall_t + 0.5, " mm"));
echo(str("Axle on the disc centre (", cx, ", ", cy, ", z ", axle_z, "); motors x ", gf_x, "..", gf_x + motor_total,
         "; PCB z ", pcb_z, " (", pcb_z - clamp_top_z, " mm above the clamps); split z ", tub_h, "; wedge rest ", rest, " deg"));
echo(str("Battery x ", battery_pos[0], "..", battery_pos[0] + battery[0], ", y ", battery_pos[1], "..", battery_pos[1] + battery[1]));
echo(str("Lid click: ", len(snap_sites), " hook tabs, ~", 100 * 3 * lip_t * (snap_h - lip_gap + 0.1) / (2 * pow(lip_h - 1.5, 2)), " % bending strain (PETG recommended; PLA is borderline)"));
echo("Screws: 4x M2x20 (lid posts, PCB, standoffs), 4x M2x16 lid bosses, 2x M2x10 wedge pivots, 4x M2x10 motor clamps");

assert(2 * spike_x_max <= 150 && width <= 150 && length <= 150 - spike_envelope_margin, "exceeds 150 x 150 mm");
assert(R >= pcb_r + pcb_clear + wall_t + lip_gap + lip_t + 0.5, "core is too small for the PCB and lip");
assert(pcb_r + pcb_clear <= R - wall_t - lip_gap - lip_t - 0.5, "PCB corner hits the lid lip");
assert(norm([cx - wheel_x0, wheel_d / 2]) + 0.5 <= R - wall_t, "wheel hits the wall");
assert(ledge_u[0] > sqrt(pow(r_k + 0.5, 2) - pow(r_k - plate_t, 2)), "stop ledge hits the hinge tube");
assert(pcb_z - clamp_top_z >= pcb_gap_min, "less than 2.5 mm between the PCB and the motor clamps");
assert(hook_catch_z + 0.3 <= snap_win_h, "hook does not fit the window");
assert(tub_h + esp_h <= H - top_t - 0.5, "ESP hits the lid");
assert(H - top_t - (axle_z + wheel_d / 2) >= 1, "wheel hits the lid");
assert(well_y1 + iw_t <= 41 - pcb_clear, "wheel pocket hits the wide PCB part");
assert(wheel_x0 + wheel_w + 0.5 <= pcb_narrow_x0, "wheel overlaps the PCB width");
assert(gf_x + motor_total < cx - 1, "the motors touch in the middle");
assert(gf_x - shaft_l > wheel_x0 && gf_x - shaft_l < wheel_x0 + wheel_w, "motor shaft misses the wheel");
assert(floor_t + battery[2] < pcb_z - jst_h - 0.5, "battery hits the parts under the PCB");
assert(battery_pos[1] > flat_y + front_t + 1.5, "battery hits the front wall");
assert(rest < stop_down - 5, "wedge rests on its stop");
assert(z_h - ear_r >= 0, "hinge ear below the tub bottom");
assert(wx1 - wx0 > fork_x[3] * 2 + fork_w0, "forks outside the plate");
assert(cx - R * sqrt(1 - flat_k * flat_k) < ear_x, "hinge ears outside the flat front");

// ---------- 2D / 3D helpers ----------
module mirror_x(m) {
    if (m == 0) children();
    else translate([pcb_w, 0, 0]) mirror([1, 0, 0]) children();
}
module rrect(r0, r1, r) translate([r0[0] + r, r0[1] + r]) offset(r = r) square([r1[0] - r0[0] - 2 * r, r1[1] - r0[1] - 2 * r]);
module pcb_outline() polygon(pcb_pts);
module esp_win2d() rrect(esp_body[0] - [esp_win_margin, esp_win_margin], esp_body[1] + [esp_win_margin, esp_win_margin], 3);
module switch_win2d() {
    translate(switch_slot[0]) square(switch_slot[1] - switch_slot[0]);
    for (p = switch_holes) translate(p) circle(d = switch_hole_d);
}
module yz_extrude(x0, w) translate([x0, 0, 0]) rotate([90, 0, 90]) linear_extrude(w) children();
module slab(z0, z1) translate([-300, -300, z0]) cube([600, 600, z1 - z0]);

module keepouts2d() {
    for (p = pcb_holes) translate(p) circle(d = post_base_d + 1);
    for (p = boss_pts) translate(p) circle(d = post_base_d + 1);
    translate(battery_pos - [1, 1]) square([battery[0] + 2, battery[1] + 2]);
}

module sectors2d(secs, r0, r1) difference() {
    intersection() {
        translate([cx, cy]) for (s = secs)
            polygon(concat([for (k = [0 : 20]) r1 * [cos(s[0] + (s[1] - s[0]) * k / 20), sin(s[0] + (s[1] - s[0]) * k / 20)]],
                           [for (k = [20 : -1 : 0]) r0 * [cos(s[0] + (s[1] - s[0]) * k / 20), sin(s[0] + (s[1] - s[0]) * k / 20)]]));
        translate([-300, flat_y + front_t + 3]) square(600);
    }
    keepouts2d();
}

module outer3d() translate([cx, cy, 0]) cylinder(r = R, h = H);

module shell3d() difference() {
    outer3d();
    translate([cx, cy, floor_t]) cylinder(r = R - wall_t, h = H - floor_t - top_t);
}

module body_shell() {
    intersection() { shell3d(); translate([-300, flat_y, -2]) cube([600, 600, H + 4]); }
    intersection() { outer3d(); translate([-300, flat_y, 0]) cube([600, front_t, H]); }
}

module well_rect() translate([ow_x1, well_y0]) square([iw_x0 - ow_x1, well_y1 - well_y0]);

// the tub pocket stops below the PCB: its inner wall lies under the narrow PCB edge
module well_rings(z0, z1, inner_only = false) for (m = [0, 1]) mirror_x(m) intersection() {
    translate([0, 0, z0]) linear_extrude(z1 - z0) {
        if (inner_only) translate([iw_x0, well_y0 - ring_t]) square([iw_t, well_y1 - well_y0 + 2 * ring_t]);
        else difference() {
            translate([ow_x1, well_y0 - ring_t]) square([iw_x1 - ow_x1, well_y1 - well_y0 + 2 * ring_t]);
            well_rect();
        }
    }
    outer3d();
}

module post(h) difference() {
    union() {
        cylinder(d = post_d, h = h);
        cylinder(d1 = post_base_d, d2 = post_d, h = post_base_h);
    }
    translate([0, 0, floor_t + 0.5]) cylinder(d = post_bore, h = h - post_solid_top - floor_t - 0.5);
}

module inside_disc() intersection() { outer3d(); translate([-300, flat_y, 0]) cube([600, 600, H]); }

// vertical gusset from a post radially out to the wall; the top stays below the PCB
module post_rib(p, h) intersection() {
    a = atan2(p[1] - cy, p[0] - cx);
    r0 = norm(p - [cx, cy]);
    to_front = sin(a) < -0.01 ? (flat_y + front_t - p[1]) / sin(a) : 1e9;
    len = min(R - r0, to_front + 0.5);
    translate([cx, cy, 0]) rotate(a) translate([r0, 0, 0]) rotate([90, 0, 0])
        linear_extrude(rib_t, center = true) polygon([[0, 0], [len, 0], [len, h * rib_end_frac], [0, h]]);
    inside_disc();
}

module lid_post() {
    cylinder(d = post_d, h = H - tub_h - 0.01);
    translate([0, 0, H - tub_h - top_t - post_base_h]) cylinder(d1 = post_d, d2 = post_base_d, h = post_base_h + 0.01);
}

// ---------- lid click: lip with flexing hook tabs, windows in the tub wall ----------
// site frame: origin on the lip outer face, +x outward, y along the wall
snap_sites = concat(
    [for (a = snap_arc_angles) [cx + (R - wall_t - lip_gap) * cos(a), cy + (R - wall_t - lip_gap) * sin(a), a]],
    [for (x = snap_front_x) [x, flat_y + front_t + lip_gap, 270]]);

module cavity2d() intersection() {
    translate([cx, cy]) circle(r = R - wall_t);
    translate([-300, flat_y + front_t]) square(600);
}
module arcs2d(arcs) translate([cx, cy]) for (s = arcs)
    polygon(concat([[0, 0]], [for (k = [0 : 24]) 200 * [cos(s[0] + (s[1] - s[0]) * k / 24), sin(s[0] + (s[1] - s[0]) * k / 24)]]));
module lip_ring2d() intersection() {
    difference() { offset(delta = -lip_gap) cavity2d(); offset(delta = -lip_gap - lip_t) cavity2d(); }
    arcs2d(lip_arcs);
}
module lip_flange2d() intersection() {
    difference() { cavity2d(); offset(delta = -lip_gap - lip_t) cavity2d(); }
    arcs2d(lip_arcs);
}

module lip() {
    translate([0, 0, tub_h - lip_h]) linear_extrude(lip_h + 0.01) lip_ring2d();
    translate([0, 0, tub_h]) linear_extrude(lip_flange_t) lip_flange2d();
}

module snap_hooks() for (s = snap_sites) translate([s[0], s[1], 0]) rotate(s[2])
    translate([0, snap_w / 2, tub_h - lip_h + hook_z0]) rotate([90, 0, 0]) linear_extrude(snap_w)
        polygon([[-0.4, 0], [snap_h, hook_catch_z - 0.8], [snap_h, hook_catch_z], [-0.4, hook_catch_z]]);

module snap_slits() for (s = snap_sites) translate([s[0], s[1], 0]) rotate(s[2]) for (sg = [-1, 1])
    translate([-lip_t - 1, sg * (snap_w / 2 + snap_slit / 2) - snap_slit / 2, tub_h - lip_h - 1])
        cube([lip_t + 2, snap_slit, lip_h - 0.5]);

module snap_windows() for (s = snap_sites) translate([s[0], s[1], 0]) rotate(s[2])
    translate([-1.5, -snap_win_w / 2, tub_h - lip_h + hook_z0 - 0.2]) cube([wall_t + 4.5, snap_win_w, snap_win_h]);

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

module spikes_ring(angles, root, tip, w, h, tip_h, tip_w) for (a = angles)
    translate([cx, cy, 0]) rotate(a) translate([R - root, 0, 0]) spike_local(tip - R + root, w, h, tip_h, tip_w);

module spikes_bottom() spikes_ring(spike_angles, spike_root_low, tip_r_low, spike_w_low, spike_h_low, spike_tip_h_low, spike_tip_w_low);

// rib behind every spike, from the floor into the wall: the impact load goes into the floor and the posts
module spike_ribs() for (a = spike_angles)
    translate([cx, cy, floor_t - 0.2]) rotate(a) translate([R - spike_gusset_start, 0, 0])
        rotate([90, 0, 0]) linear_extrude(spike_gusset_t, center = true)
            polygon([[0, 0], [spike_gusset_start + spike_root_low + spike_reach_low, 0],
                     [spike_gusset_start + spike_root_low + spike_reach_low, spike_tip_h_low],
                     [spike_gusset_start + spike_root_low + 4, spike_h_low * 0.65],
                     [0, spike_h_low * 0.65]]);

// ---------- hinge ears ----------
module hinge2d() translate([y_h, z_h]) mirror([1, 0]) children();

module ledges2d() hinge2d() {
    rotate(-stop_down) translate([ledge_u[0], -r_k - ledge_t]) square([ledge_u[1] - ledge_u[0], ledge_t]);
    rotate(stop_up) translate([ledge_u[0], -r_k + plate_t]) square([ledge_u[1] - ledge_u[0], ledge_t]);
}

module ear2d() union() {
    hull() {
        translate([flat_y, 0]) square([0.2, ear_h]);
        translate([y_h - 2, 0]) square([0.01, ear_h]);
        translate([y_h, z_h]) circle(r = ear_r);
        translate([y_h - ear_apex, z_h]) circle(r = 0.5);
        ledges2d();
    }
    translate([flat_y, 0]) square([front_t + 0.01, ear_h]);
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
            well_rings(0, pcb_z - 0.5);
            for (p = pcb_holes) translate([p[0], p[1], 0]) post(pcb_z);
            for (p = boss_pts) translate([p[0], p[1], 0]) post(tub_h);
            for (p = pcb_holes) post_rib(p, pcb_z - 1);
            for (p = boss_pts) post_rib(p, tub_h - 1);
            spike_ribs();
            for (y = [battery_pos[1] - 1.5, battery_pos[1] + battery[1] + 0.5])
                translate([battery_pos[0], y, 0]) cube([battery[0], 1, 3]);
            for (m = [0, 1]) mirror_x(m) motor_cradle();
            ears();
            spikes_bottom();
        }
        for (m = [0, 1]) mirror_x(m) {
            translate([0, 0, -1]) linear_extrude(floor_t + 2) rrect([ow_x1, axle_y - arch_half], [iw_x0, axle_y + arch_half], 1.5);
            hull() for (z = [axle_z, tub_h + 1])
                translate([iw_x0 - 1, axle_y, z]) rotate([0, 90, 0]) cylinder(d = shaft_boss_d, h = iw_t + 2);
        }
        for (p = pcb_holes) translate([p[0], p[1], pcb_z - 8]) cylinder(d = pilot_d, h = 9);
        for (p = boss_pts) translate([p[0], p[1], tub_h - 8]) cylinder(d = pilot_d, h = 9);
        translate([0, 0, -1]) linear_extrude(floor_t + 1.01) sectors2d(win_sectors, win_r0, R - win_edge);
        snap_windows();
    }
}

// ---------- lid (prints upside down) ----------
module lid() {
    difference() {
        union() {
            intersection() { body_shell(); slab(tub_h, H); }
            well_rings(tub_h, H, true);
            for (p = pcb_holes) translate([p[0], p[1], tub_h]) lid_post();
            for (p = boss_pts) translate([p[0], p[1], tub_h]) lid_post();
            lip();
            snap_hooks();
        }
        snap_slits();
        for (p = concat(pcb_holes, boss_pts)) {
            translate([p[0], p[1], tub_h - 1]) cylinder(d = screw_clear_d, h = H);
            translate([p[0], p[1], H - screw_head_recess]) cylinder(d = screw_head_d, h = 2);
        }
        translate([0, 0, H - top_t - 1]) linear_extrude(top_t + 2) {
            esp_win2d();
            switch_win2d();
        }
        translate([0, 0, H - top_t - 1]) linear_extrude(top_t + 2.01) sectors2d(win_sectors_lid, win_r0_lid, R - win_edge);
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

module wedge_placed(a = -rest) translate([pcb_w, y_h, z_h]) rotate([0, 0, 180]) rotate([a, 0, 0]) wedge_local();

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

module pcb_env() translate([0, 0, pcb_z + 0.02]) linear_extrude(pcb_t - 0.04) difference() {
    pcb_outline();
    for (p = pcb_holes) translate(p) circle(d = screw_clear_d);
}

module drv_env(dz = 0) translate([drv_body[0][0], drv_body[0][1], pcb_z - drv_h - dz]) cube([drv_len, drv_w, drv_h + dz]);

module parts_env() {
    pcb_env();
    drv_env();
    translate([esp_body[0][0], esp_body[0][1], pcb_z + pcb_t])
        cube([esp_body[1][0] - esp_body[0][0], esp_body[1][1] - esp_body[0][1], esp_h]);
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
else if (part == "lid_fit") intersection() { tub(); lid(); }
else if (part == "lid_asm") lid();
else if (part == "wedge_asm") wedge_placed();
else if (part == "clamps_asm") for (m = [0, 1]) mirror_x(m) motor_clamp();
else if (part == "test_hinge")
    intersection() { tub(); translate([ear_x - 1, y_h - ear_apex - 3, -1]) cube([ear_t + 14, flat_y - y_h + ear_apex + 8, H + 2]); }
else if (part == "esp_clearance") intersection() {
    lid();
    translate([esp_body[0][0] - 0.5, esp_body[0][1] - 0.5, tub_h])
        cube([esp_body[1][0] - esp_body[0][0] + 1, esp_body[1][1] - esp_body[0][1] + 1, esp_h + 0.5]);
}
else if (part == "wheel_clearance") intersection() {
    wheels_env(wheel_d + 0.2);
    union() { tub(); lid(); wedge_placed(); }
}
else if (part == "pcb_clearance") intersection() { parts_env(); union() { tub(); lid(); for (m = [0, 1]) mirror_x(m) motor_clamp(); } }
else if (part == "pcb_wheel_clearance") intersection() { wheels_env(wheel_d + 0.2); translate([0, 0, pcb_z]) linear_extrude(pcb_t) pcb_outline(); }
else if (part == "drv_clearance") intersection() {
    drv_env(0.2);
    union() { for (m = [0, 1]) mirror_x(m) { motor_clamp(); motor_cradle(); } }
}
else if (part == "motor_clearance") intersection() {
    motors_env();
    union() { tub(); lid(); for (m = [0, 1]) mirror_x(m) motor_clamp(); }
}
else if (part == "spike_clearance") intersection() {
    union() { spikes_bottom(); }
    union() { wheels_env(wheel_d + 0.2); for (a = [-rest, stop_up, -stop_down]) wedge_placed(a); }
}
