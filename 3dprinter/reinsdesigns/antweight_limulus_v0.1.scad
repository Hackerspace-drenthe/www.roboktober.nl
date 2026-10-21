// Antweight "Limulus" v0.1 - biomimicry: horseshoe crab (Limulus polyphemus). Invertible 2WD, passive.
// Prosoma: a horseshoe-shaped shield on a gravity hinge (like the crab's prosoma/opisthosoma joint).
//   It drops onto the floor in both orientations; median keel + lateral ridges stiffen the plate the way the
//   crab's carapace ridges do, with compound-eye bumps on the lateral ridges. Hinge ears with swing stops.
// Opisthosoma: hexagonal body tapering to the rear with marginal spines on the flanks.
// Telson: long diamond-section tail spike at mid-height (rear deterrent, same in both orientations).
// Underside "book gills": rows of lamella slots in the floor (lightening). Sutures as grooves top and bottom.
// Internals as Rhomboid: 1S 300 mAh LiPo between the motors, ESP32-C3 + DRV8833 soldered flat, 34x10 wheels.
// Coordinates = PCB-local: x = width, y = length (y = 0 = narrow PCB edge = front), z = 0 = tub bottom.
// Driving direction is -y.

part = "lid"; // [assembly, tub, lid, lid_glass, shield, motor_clamp, test_motor, test_hinge, test_shield]
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

/* [Body (opisthosoma)] */
wall = 1.6;
side_t = 1.6;                        // outer wall beside the wheel
front_t = 2.0;
floor_t = 1.2;
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
edge_c = 1.6;                        // stepped 45 degree chamfer, top and bottom
layer_h = 0.2;
spine_t = [0.25, 0.5, 0.75];         // marginal spines along each flank
spine_w = 6;
spine_l = 5;
spine_sweep = 25;                    // spines lean backwards
telson_l = 40;
telson_w = 12;                       // diamond section at the root (width = height)
telson_tip = 1;

/* [Prosoma shield + hinge] */
tube_d = 7;
hinge_gap = 1;
hinge_dz = 2.8;                      // pivot above the mid-plane: the plate sits under the pin, evens out both rest angles
pin_pilot_d = 1.7;                   // M2 pivot screw threads into the ear
pin_clear_d = 2.4;                   // the shield turns on the screw shank
pin_head_recess = 1.5;
ear_t = 5;
ear_fit = 0.4;
ear_apex = 8;
ear_r = 4.5;
plate_t = 2.4;
shield_l = 34;                       // pivot to the front of the horseshoe
shield_sweep = 18;                   // horseshoe: the corners sit this far behind the front centre
shield_p = 3;                        // front curve exponent (higher = flatter front)
bevel_l = 8;                         // thin edge all around the horseshoe
tip_t = 0.6;
keel_w = 2.4;
keel_h = 1.6;
ridge_w = 2;
ridge_pts = [[20, 4], [27, 22]];     // lateral ridge, x from the centre line, u from the pivot
eye_size = [6, 3.5, 1.2];            // compound-eye bump on each lateral ridge
eye_u = 17;
stop_angle = 50;
ledge_u = [2.5, 10];
ledge_w = 1.5;
ledge_t = 1.5;

/* [Details] */
groove_w = 1.2;                      // sutures, top and bottom
groove_d = 0.4;
gill_dx = [6, 22];                   // book gills: two columns, x from the centre line
gill_y = [40, 55];
gill_w = 1.5;
gill_pitch = 3.2;
lid_eyes = [[26, 12], [6, 5]];       // lateral compound eyes, median ocelli (x from the centre line, y)

/* [Colours] */
palette = "limulus"; // [limulus, deep_sea, ams_loaded]
// [tub, lid + clamps, shield, eyes], each [filament, preview hex]
pal = palette == "deep_sea"
        ? [["dark gray", "#3C3F41"], ["dark gray", "#2E3032"], ["copper", "#B87333"], ["transparent", "#E8F0F2"]]
    : palette == "ams_loaded"
        ? [["dark gray", "#3C3F41"], ["silver", "#C4C7CA"], ["copper", "#B87333"], ["transparent", "#E8F0F2"]]
        : [["coffee brown", "#6F4E37"], ["coffee brown", "#7B5A3E"], ["coffee brown", "#6F4E37"], ["gold", "#D4AF37"]];

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
front_y = in_y0 - front_t;
well_y0 = axle_y - wheel_d / 2 - wheel_gap;
well_y1 = axle_y + wheel_d / 2 + wheel_gap;
pod_y1 = well_y1 + iw_t;
battery_pos = [cx - battery[0] / 2, axle_y - battery[1] / 2];
dd = pcb_clear + wall;
rear_y = pcb_l + dd;
flank = [[ow_x0, pod_y1], [8 - dd * tan(22.5), rear_y]];   // left flank, pod corner to rear corner
arch_half = sqrt(pow(wheel_d / 2, 2) - pow(axle_z - floor_t, 2)) + wheel_gap;
arch_half_top = sqrt(pow(wheel_d / 2, 2) - pow(H - top_t - axle_z, 2)) + wheel_gap;
n_chamf = round(edge_c / layer_h);

r_k = tube_d / 2;
z_h = axle_z + hinge_dz;
y_h = front_y - r_k - hinge_gap;
ear_in = ow_x0 + ear_t;
wx0 = ear_in + ear_fit;
wx1 = pcb_w - wx0;
n_bev = round((plate_t - tip_t) / layer_h);
function u_front(x) = shield_l - shield_sweep * pow(abs(2 * (x - cx) / (wx1 - wx0)), shield_p);
// resting on the wheels and the rear edge: body bottom height above the floor at the pivot
lift = ground_clearance * (1 + (axle_y - y_h) / (rear_y - axle_y));
h_up = z_h + lift;
h_dn = H - z_h + lift;
rest_up = asin(h_up / sqrt(pow(shield_l, 2) + pow(r_k, 2))) - atan2(r_k, shield_l);
rest_dn = asin(h_dn / sqrt(pow(shield_l, 2) + pow(r_k - tip_t, 2))) + atan2(r_k - tip_t, shield_l);
tip_y = y_h - (shield_l * cos(rest_up) + r_k * sin(rest_up));
length = rear_y + telson_l - tip_y;

echo(str("Limulus ", width, " x ", length, " x ", H, " mm (shield resting, incl. telson), body ", rear_y - front_y, " mm"));
echo(str("Shield rest angle upright ", rest_up, " deg, inverted ", rest_dn, " deg, stops +-", stop_angle, " deg"));
echo(str("PCB bottom z = ", pcb_z, ", split z = ", tub_h, ", ESP top ", tub_h + esp_h, " / lid underside ", H - top_t));
echo(str("Colours: tub ", pal[0][0], "; lid + clamps ", pal[1][0], "; shield ", pal[2][0], "; eyes ", pal[3][0]));
echo("Screws: 2x M2x10 pivot (threadlock), 4x M2x14 (lid + PCB into tub), 4x M2x10 motor clamps");

assert(width <= 150 && length <= 150, "does not fit the 150 x 150 mm square");
assert(well_y1 + iw_t <= 41 - pcb_clear, "wheel well hits wide PCB part");
assert(gf_x - shaft_l > ow_x1, "motor shaft hits outer wall");
assert(tub_h + esp_h <= H - top_t - 0.5, "ESP32 hits the lid");
assert(floor_t + battery[2] < pcb_z - max(drv_h, jst_h) - 0.5, "battery hits parts under the PCB");
assert(battery_pos[0] - strap_gap - strap_slot > gf_x + mount_l, "battery / strap slot hits motor cradle");
assert(axle_z + telson_w / 2 < tub_h, "telson reaches into the lid");
assert(u_front(wx0) > ledge_u[1] + 3, "horseshoe corners too short for the swing stops");
assert(rest_up < stop_angle - 5 && rest_dn < stop_angle - 5, "shield rests on its stop, not on the floor");

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

module pod2d() translate([ow_x0, front_y]) square([width, pod_y1 - front_y]);

module hull2d() hull() { pod2d(); offset(delta = dd) pcb_outline(); }

module spines2d() for (m = [0, 1]) mirror_x(m) {
    d = flank[1] - flank[0];
    e = d / norm(d);
    n = [-e[1], e[0]];                               // outward on the left flank
    dir = [n[0] * cos(spine_sweep) - n[1] * sin(spine_sweep), n[0] * sin(spine_sweep) + n[1] * cos(spine_sweep)];
    for (t = spine_t) {
        b = flank[0] + t * d;
        polygon([b - e * spine_w / 2 - n * 0.5, b + e * spine_w / 2 - n * 0.5, b + dir * spine_l]);
    }
}

module plan() union() { hull2d(); spines2d(); }

module outline_inner() intersection() {
    offset(delta = -wall) hull2d();
    translate([-50, in_y0]) square([200, 200]);
}

module well_rect() translate([ow_x1, well_y0]) square([iw_x0 - ow_x1, well_y1 - well_y0]);

module arch_opening(half) rrect([ow_x1, axle_y - half], [iw_x0, axle_y + half], 1.5);

module sutures2d() {
    slot2d([cx, 2], [cx, rear_y - 4], groove_w);
    for (s = [-1, 1]) {
        slot2d([cx + s * 24, 1], [cx + s * 13, rear_y - 4], groove_w);
        slot2d([cx + s * 13, pod_y1], [cx + s * 34, pod_y1], groove_w);
    }
}

module gills2d()
    for (s = [-1, 1], y = [gill_y[0] : gill_pitch : gill_y[1]])
        slot2d([cx + s * gill_dx[0], y], [cx + s * gill_dx[1], y], gill_w);

module lid_eyes2d() for (s = [-1, 1]) {
    translate([cx + s * lid_eyes[0][0], lid_eyes[0][1]]) rotate(s * -20) scale([1, 0.55]) circle(d = 8);
    translate([cx + s * lid_eyes[1][0] / 2, lid_eyes[1][1]]) circle(d = 3);
}

module strap_slots2d()
    for (x = [battery_pos[0] - strap_gap - strap_slot, battery_pos[0] + battery[0] + strap_gap])
        translate([x, axle_y - strap_w / 2]) square([strap_slot, strap_w]);

module switch_win2d() {
    translate(switch_slot[0]) square(switch_slot[1] - switch_slot[0]);
    for (p = switch_holes) translate(p) circle(d = switch_hole_d);
}

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

module well_rings() for (m = [0, 1]) mirror_x(m) linear_extrude(H) difference() {
    offset(delta = iw_t) well_rect();
    well_rect();
}

// (y, z) side view around the pivot: u forward (-y), v up
module hinge2d() translate([y_h, z_h]) mirror([1, 0]) children();

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

// ---------- hinge ears with swing stops ----------
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

// ---------- telson ----------
module telson() translate([cx, rear_y - wall, axle_z]) rotate([-90, 0, 0])
    linear_extrude(telson_l + wall, scale = telson_tip / telson_w) rotate(45) square(telson_w / sqrt(2), center = true);

// ---------- tub ----------
module tub() {
    difference() {
        union() {
            difference() {
                body_solid(0, tub_h);
                translate([0, 0, floor_t]) linear_extrude(tub_h) outline_inner();
            }
            intersection() { body_solid(0, tub_h); well_rings(); }
            ears();
            telson();
            for (m = [0, 1]) mirror_x(m) motor_cradle();
            for (p = pcb_holes) translate([p[0], p[1], 0]) cylinder(d = standoff_d, h = pcb_z);
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
            gills2d();
        }
        translate([0, 0, -1]) linear_extrude(groove_d + 1) sutures2d();
    }
}

// ---------- lid (prints upside down) ----------
module lid() {
    difference() {
        union() {
            difference() {
                body_solid(tub_h, H);
                translate([0, 0, tub_h - 1]) linear_extrude(H - top_t - tub_h + 1) outline_inner();
            }
            intersection() { body_solid(tub_h, H); well_rings(); }
            for (p = pcb_holes) translate([p[0], p[1], tub_h]) cylinder(d = standoff_d, h = H - tub_h - 0.01);
        }
        for (m = [0, 1]) mirror_x(m) translate([0, 0, H - top_t - 1]) linear_extrude(top_t + 2) arch_opening(arch_half_top);
        for (p = pcb_holes) {
            translate([p[0], p[1], tub_h - 1]) cylinder(d = screw_clear_d, h = H);
            translate([p[0], p[1], H - screw_head_recess]) cylinder(d = screw_head_d, h = 2);
        }
        translate([0, 0, H - top_t - 1]) linear_extrude(top_t + 2) {
            switch_win2d();
            lid_eyes2d();
        }
        translate([0, 0, H - groove_d]) linear_extrude(groove_d + 1) sutures2d();
    }
}

// transparent compound eyes + ocelli: load as extra part of the lid (same transform)
module lid_glass() translate([0, 0, H - top_t]) linear_extrude(top_t) lid_eyes2d();

// ---------- prosoma shield ----------
module shield_plan()
    polygon(concat([[wx0, 0], [wx1, 0]],
                   [for (i = [0 : 48]) let(x = wx1 - (wx1 - wx0) * i / 48) [x, u_front(x)]]));

// local frame: x = width, y = u (forward from the pivot), z = v; pivot on the x axis, plate under the pin
module shield_local() difference() {
    union() {
        translate([0, 0, -r_k]) linear_extrude(tip_t + 0.01) shield_plan();
        // thin edge all around the horseshoe, full thickness at the hinge and the stop ledges
        for (i = [0 : n_bev - 1]) translate([0, 0, -r_k + tip_t + i * layer_h]) linear_extrude(layer_h + 0.01) union() {
            offset(delta = -bevel_l * (i + 1) / n_bev) shield_plan();
            intersection() { shield_plan(); translate([wx0 - 1, -1]) square([wx1 - wx0 + 2, ledge_u[1] + 3]); }
        }
        translate([0, 0, -r_k + plate_t - 0.01]) linear_extrude(keel_h) intersection() {
            offset(delta = -bevel_l) shield_plan();
            union() {
                slot2d([cx, 0], [cx, shield_l], keel_w);
                for (s = [-1, 1]) slot2d([cx + s * ridge_pts[0][0], ridge_pts[0][1]], [cx + s * ridge_pts[1][0], ridge_pts[1][1]], ridge_w);
            }
        }
        for (s = [-1, 1]) let(t = (eye_u - ridge_pts[0][1]) / (ridge_pts[1][1] - ridge_pts[0][1]))
            translate([cx + s * (ridge_pts[0][0] + t * (ridge_pts[1][0] - ridge_pts[0][0])), eye_u, -r_k + plate_t + keel_h - 0.5])
                scale([eye_size[0] / 2, eye_size[1] / 2, eye_size[2] + 0.5]) intersection() {
                    sphere(r = 1, $fn = 32);
                    translate([-1, -1, 0]) cube(2);
                }
        translate([wx0, 0, 0]) rotate([0, 90, 0]) cylinder(d = tube_d, h = wx1 - wx0);
    }
    translate([wx0 - 1, 0, 0]) rotate([0, 90, 0]) cylinder(d = pin_clear_d, h = wx1 - wx0 + 2);
}

module shield_placed(a) translate([pcb_w, y_h, z_h]) rotate([0, 0, 180]) rotate([a, 0, 0]) shield_local();

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
    color(pal[3][1], 0.8) lid_glass();
    color(pal[1][1]) for (m = [0, 1]) mirror_x(m) motor_clamp();
    color(pal[2][1]) shield_placed(-rest_up);
    ghosts();
}

// lid + lid_glass share one transform: load them together as one multi-part object
if (part == "assembly") assembly();
else if (part == "tub") color(pal[0][1]) tub();
else if (part == "lid") color(pal[1][1]) translate([0, 0, H]) rotate([180, 0, 0]) lid();
else if (part == "lid_glass") color(pal[3][1]) translate([0, 0, H]) rotate([180, 0, 0]) lid_glass();
// prints flat on the plate underside, tube on the bed
else if (part == "shield") color(pal[2][1]) translate([0, 0, r_k]) shield_local();
// print 2x; the same part fits left and right (rotate 180 deg)
else if (part == "motor_clamp") color(pal[1][1]) translate([0, 0, clamp_top_z]) rotate([180, 0, 0]) translate([-gf_x, -axle_y, 0]) motor_clamp();
else if (part == "test_motor")
    intersection() { tub(); translate([iw_x0 - 1, axle_y - clamp_screw_y - clamp_boss_d, -1]) cube([mount_l + 4, 2 * (clamp_screw_y + clamp_boss_d), axle_z + 1]); }
else if (part == "test_hinge")
    intersection() { tub(); translate([ow_x0 - 1, y_h - ear_apex - 2, -1]) cube([ear_t + 14, front_y - y_h + ear_apex + 8, H + 2]); }
else if (part == "test_shield")
    intersection() { translate([0, 0, r_k]) shield_local(); translate([wx0 - 1, -r_k - 1, -1]) cube([16, shield_l + r_k + 2, tube_d + 2]); }
