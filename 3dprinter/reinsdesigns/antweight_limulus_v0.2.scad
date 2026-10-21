// Limulus v0.2: raised ESP cover and gravity-operated roll-hinged telson.
// Reuses v0.1 PCB, drive, shield, clamps and part selector (default: tub).
// Additional parts: tail, test_tail, esp_clearance, tail_clearance.
// Self-righting is a prototype feature, not a verified guarantee.
include <antweight_limulus_v0.1.scad>

/* [Raised ESP cover] */
esp_stack_h = 17;
esp_margin = 0.8;
canopy_wall = 1.2;
canopy_rx = 24;
canopy_rz = 23;
canopy_y = 40;
canopy_length = 30;
canopy_offset_x = -4;
canopy_center_z = H - 6;

/* [Passive roll tail] */
tail_angle = -65; // [-65:1:65]
tail_limit = 65;
tail_fit = 0.4;
tail_pin_d = 2.4;
tail_hub_d = 10;
tail_hub_length = 6;
tail_pivot_y = rear_y + 10;
tail_pivot_z = axle_z;
tail_ear_t = 2.4;
tail_blade_t = 5.2;
tail_reach = 24;
tail_rise = 38;
tail_round = 3;

function canopy_room(x) = canopy_center_z +
    (canopy_rz - canopy_wall) * sqrt(1 - pow((x - cx - canopy_offset_x) /
    (canopy_rx - canopy_wall), 2));

assert(esp_stack_h > 0 && canopy_wall >= 1.2, "invalid ESP cover dimensions");
assert(max(abs(esp_body[0][0] - esp_margin - cx - canopy_offset_x),
           abs(esp_body[1][0] + esp_margin - cx - canopy_offset_x)) < canopy_rx - canopy_wall,
       "ESP extends outside the canopy width");
assert(min(canopy_room(esp_body[0][0] - esp_margin),
           canopy_room(esp_body[1][0] + esp_margin)) >= tub_h + esp_stack_h + esp_margin,
       "ESP stack hits the rounded canopy");
assert(esp_body[0][1] - esp_margin >= canopy_y - canopy_length / 2 + canopy_wall &&
       esp_body[1][1] + esp_margin <= canopy_y + canopy_length / 2 - canopy_wall,
       "ESP hits a canopy end wall");
assert(cx + canopy_offset_x + canopy_rx < switch_slot[0][0],
    "canopy obstructs the PCB switch");
assert(abs(tail_angle) <= tail_limit && tail_limit > 0 && tail_limit < 90,
       "tail angle outside mechanical stops");
assert(tail_pivot_z - tail_hub_d / 2 > 10.8, "tail hub hits the lower fork rail");
assert(tail_pivot_z + tail_rise > canopy_center_z + canopy_rz + 2,
    "tail cannot contact the floor above the canopy when inverted");
assert(tail_pivot_y + tail_reach + tail_round - tip_y <= 150,
       "body and tail exceed the 150 mm footprint");

echo(str("Limulus v0.2: ESP stack ", esp_stack_h, " mm + ", esp_margin,
         " mm clearance; cover height ", canopy_center_z + canopy_rz, " mm"));
echo(str("Tail envelope at centre angle: ", tail_pivot_z + tail_rise + tail_round,
         " mm high; footprint length <= ", tail_pivot_y + tail_reach + tail_round - tip_y, " mm"));
echo(str("Tail: free roll hinge +/-", tail_limit,
         " deg; 1x M2x20 bolt, washers and locknut. Do not clamp the moving hub."));
echo("Lid prints right side up and needs internal supports; tail prints on its broad side. Keep pivot surfaces clean.");
echo("Self-righting depends on mass distribution, hinge friction and floor contact; validate on the assembled robot.");

module canopy_solid(inset = 0) {
    translate([cx + canopy_offset_x, canopy_y, canopy_center_z])
        scale([1, 1, (canopy_rz - inset) / (canopy_rx - inset)])
            rotate([90, 0, 0]) cylinder(r = canopy_rx - inset,
                                      h = canopy_length - 2 * inset, center = true);
}

module canopy_opening() {
    translate([0, 0, H - top_t - 0.1]) linear_extrude(top_t + 0.2)
        intersection() {
            translate([cx + canopy_offset_x - canopy_rx + canopy_wall,
                       canopy_y - canopy_length / 2 + canopy_wall])
                square([2 * (canopy_rx - canopy_wall), canopy_length - 2 * canopy_wall]);
            outline_inner();
            translate([0, pcb_holes[0][1] + standoff_d])
                square([pcb_w, pcb_l]);
        }
}

module esp_envelope() {
    translate([esp_body[0][0] - esp_margin, esp_body[0][1] - esp_margin, tub_h])
        cube([esp_body[1][0] - esp_body[0][0] + 2 * esp_margin,
              esp_body[1][1] - esp_body[0][1] + 2 * esp_margin,
              esp_stack_h + esp_margin]);
}

module tail_axis(y0, length, diameter) {
    translate([cx, y0, tail_pivot_z]) rotate([-90, 0, 0])
        cylinder(d = diameter, h = length);
}

module tail_placed(angle = tail_angle) {
    translate([cx, tail_pivot_y, tail_pivot_z]) rotate([0, angle, 0]) tail_local();
}

module tail_local() {
    difference() {
        union() {
            intersection() {
                rotate([90, 0, 0]) cylinder(d = tail_hub_d, h = tail_hub_length, center = true);
                translate([-tail_blade_t / 2, -tail_hub_length / 2, -tail_hub_d / 2])
                    cube([tail_blade_t, tail_hub_length, tail_hub_d]);
            }
            translate([-tail_blade_t / 2, -tail_hub_length / 2, 4])
                cube([tail_blade_t, tail_hub_length, 6]);
            yz_extrude(-tail_blade_t / 2, tail_blade_t) {
                hull() {
                    translate([0, 8]) circle(r = tail_round);
                    translate([11, 20]) circle(r = tail_round);
                }
                hull() {
                    translate([11, 20]) circle(r = tail_round);
                    translate([tail_reach, tail_rise]) circle(r = tail_round);
                }
            }
        }
        rotate([90, 0, 0]) cylinder(d = tail_pin_d, h = tail_hub_length + 2, center = true);
    }
}

module telson() {
    front_ear_y = tail_pivot_y - tail_hub_length / 2 - tail_fit - tail_ear_t;
    rear_ear_y = tail_pivot_y + tail_hub_length / 2 + tail_fit;
    difference() {
        union() {
            translate([cx - 13, rear_y - wall, 4])
                cube([26, front_ear_y + tail_ear_t - rear_y + wall, 6]);
            tail_axis(front_ear_y, tail_ear_t, 26);
            tail_axis(rear_ear_y, tail_ear_t, 8);
            translate([cx - 2, rear_ear_y, 10])
                cube([4, tail_ear_t, tail_pivot_z - 10]);
            translate([cx - 7, rear_y - wall, 8])
                cube([14, rear_ear_y + tail_ear_t - rear_y + wall, 2.8]);
            for (direction = [-1, 1])
                translate([cx, tail_pivot_y, tail_pivot_z]) rotate([0, direction * tail_limit, 0])
                    translate([direction < 0 ? -tail_blade_t / 2 - 3 : tail_blade_t / 2,
                               -tail_hub_length / 2 - tail_fit - 0.1, 6])
                        cube([3, tail_hub_length + tail_fit + 0.1, 5]);
        }
        tail_axis(front_ear_y - 1, rear_ear_y + tail_ear_t - front_ear_y + 2, tail_pin_d);
    }
}

module lid() {
    if (part == "lid")
        translate([0, 0, H + tub_h]) rotate([180, 0, 0]) lid_geometry();
    else lid_geometry();
}

module lid_glass() {
    if (part == "lid_glass")
        translate([0, 0, H + tub_h]) rotate([180, 0, 0]) lid_glass_geometry();
    else lid_glass_geometry();
}

module lid_glass_geometry() {
    translate([0, 0, H - top_t]) linear_extrude(top_t) lid_eyes2d();
}

module lid_geometry() {
    difference() {
        union() {
            difference() {
                body_solid(tub_h, H);
                translate([0, 0, tub_h - 1]) linear_extrude(H - top_t - tub_h + 1) outline_inner();
                canopy_opening();
            }
            intersection() { body_solid(tub_h, H); well_rings(); }
            for (position = pcb_holes) translate([position[0], position[1], tub_h])
                cylinder(d = standoff_d, h = H - tub_h - 0.01);
            intersection() {
                difference() { canopy_solid(); canopy_solid(canopy_wall); }
                translate([-100, -100, H - top_t]) cube([300, 300, 100]);
            }
        }
        for (side = [0, 1]) mirror_x(side) translate([0, 0, H - top_t - 1])
            linear_extrude(top_t + 2) arch_opening(arch_half_top);
        for (position = pcb_holes) {
            translate([position[0], position[1], tub_h - 1]) cylinder(d = screw_clear_d, h = H);
            translate([position[0], position[1], H - screw_head_recess]) cylinder(d = screw_head_d, h = 2);
        }
        translate([0, 0, H - top_t - 1]) linear_extrude(top_t + 2) {
            switch_win2d();
            lid_eyes2d();
        }
        translate([0, 0, H - groove_d]) linear_extrude(groove_d + 1) sutures2d();
    }
}

module assembly() {
    color(pal[0][1]) render() tub();
    color(pal[1][1]) render() lid();
    color(pal[3][1], 0.8) lid_glass();
    color(pal[1][1]) for (side = [0, 1]) mirror_x(side) motor_clamp();
    color(pal[2][1]) shield_placed(-rest_up);
    color(pal[2][1]) tail_placed();
    ghosts();
    color("blue", 0.35) esp_envelope();
}

if (part == "tail")
    color(pal[2][1]) translate([0, 0, tail_blade_t / 2]) rotate([0, -90, 0]) tail_local();
else if (part == "test_tail")
    intersection() {
        tub();
        translate([cx - 15, rear_y - wall - 2, -1]) cube([30, 30, H + 2]);
    }
else if (part == "esp_clearance") intersection() { lid(); esp_envelope(); }
else if (part == "tail_clearance") intersection() { tub(); tail_placed(); }