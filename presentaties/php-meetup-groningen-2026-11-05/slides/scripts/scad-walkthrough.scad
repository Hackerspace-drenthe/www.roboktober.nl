// Teaching model, not a production chassis.
step = 1;
$fn = 32;
pcb_w = 78;
pcb_l = 56;
wall = 2;
floor_t = 2;
height = 20;
holes = [[9, 8], [69, 8], [9, 48], [69, 48]];

module outline() {
    if (step < 6) square([pcb_w, pcb_l]);
    else hull()
        for (x = [4, pcb_w - 4], y = [4, pcb_l - 4])
            translate([x, y]) circle(r = 4);
}

module body() {
    difference() {
        union() {
            difference() {
                linear_extrude(height) outline();
                translate([wall, wall, step >= 5 ? floor_t : -1])
                    cube([pcb_w - 2 * wall, pcb_l - 2 * wall, height + 2]);
            }
            if (step >= 8)
                for (p = holes)
                    translate([p[0], p[1], 0]) cylinder(h = 8, d = 7);
        }
        if (step >= 7)
            for (p = holes)
                translate([p[0], p[1], -1]) cylinder(h = height + 2, d = 2.4);
        if (step >= 9)
            for (x = [-1, pcb_w - 9])
                translate([x, 18, -1]) cube([10, 22, height + 2]);
    }
}

if (step == 1) color("orange") cube([40, 30, 10]);
else if (step == 2) color("orange") cube([pcb_w, pcb_l, height]);
else if (step == 3) {
    color("orange") cube([pcb_w, pcb_l, height]);
    color("cyan", 0.6)
        translate([wall, wall, height + 8])
            cube([pcb_w - 2 * wall, pcb_l - 2 * wall, height]);
} else {
    color("orange") body();
    if (step >= 10)
        color("green", 0.6)
            translate([8, 8, step == 12 ? 34 : 9])
                cube([pcb_w - 16, pcb_l - 16, 1.6]);
    if (step >= 11)
        color("slategray", 0.8)
            translate([0, 0, step == 12 ? 52 : height + 1])
                linear_extrude(2) outline();
}
