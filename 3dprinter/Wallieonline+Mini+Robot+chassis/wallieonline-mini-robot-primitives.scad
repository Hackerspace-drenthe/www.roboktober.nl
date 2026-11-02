$fn = 64;

// Primitive-based reconstruction scaffold for wallieonline-mini-robot.STL.
// This model is intentionally feature-editable: every major region is a module
// with dimensions exposed as parameters.

show_reference_mesh = false;
reference_mesh_alpha = 0.22;

// Thin slab thickness used for lofting between measured sections.
loft_slice_thickness = 0.01;

// Sections are [x_min, x_max, y_depth, z_height].
// Values are measured from mesh slices and can be tuned.
sec_0 = [21.512, 78.488, 19.000, 0.000];
sec_1 = [3.000, 97.000, 20.000, 15.000];
sec_2 = [16.000, 84.000, 20.000, 20.000];
sec_3 = [16.000, 84.000, 20.000, 50.000];
sec_4 = [3.000, 97.000, 20.000, 55.000];
sec_5 = [6.330, 93.670, 20.000, 70.000];
sec_6 = [8.000, 92.000, 13.330, 75.000];
sec_7 = [13.000, 87.000, 6.670, 90.000];
sec_8 = [21.120, 78.880, 2.220, 100.000];
sec_9 = [25.265, 74.735, 0.450, 105.000];

// Measured wheel/motor features.
enable_wheel_motor_features = true;
enable_upper_mast_features = true;
enable_rear_top_trim = true;
enable_front_slice_relief = true;

// Large side opening seen for y ~= 2.5..12.
motor_window_xmin = 3.0;
motor_window_xmax = 97.0;
motor_window_ymin = 2.05;
motor_window_ymax = 12.0;
motor_window_zmin = 29.8;
motor_window_zmax = 42.2;

// Inner cavity seen for y ~= 12.5..20 (top edge tapers down).
inner_cavity_xmin = 18.97;
inner_cavity_xmax = 81.03;
inner_cavity_ymin = 12.5;
inner_cavity_ymax = 20.0;
inner_cavity_zmin = 3.97;
inner_cavity_zmax_front = 61.53;
inner_cavity_zmax_back = 53.80;

// Sloped trim to better match the measured upper rear profile at deep y slices.
rear_trim_xmin = 0.0;
rear_trim_xmax = 100.0;
rear_trim_y0 = 14.8;
rear_trim_z0 = 71.25;
rear_trim_y1 = 18.0;
rear_trim_z1 = 69.17;
rear_trim_y2 = 20.0;
rear_trim_z2 = 70.00;
rear_trim_height = 45.0;

// Smooth local relief to match the y=2 slice while keeping neighboring slices continuous.
front_relief_xmin = 0.0;
front_relief_xmax = 100.0;
front_relief_y_start = 1.95;
front_relief_y_peak = 2.00;
front_relief_y_end = 2.05;
front_relief_z_peak = 3.97;

mount_hole_d = 2.0;
mount_hole_ymin = 2.0;
mount_hole_ymax = 20.0;
mount_hole_positions = [
    [14.95, 13.45],
    [85.05, 13.45],
    [20.14, 54.86],
    [79.86, 54.86]
];

front_hole_d = 2.0;
front_hole_ymin = -0.05;
front_hole_depth = 2.1;
front_hole_positions = [
    [26.50, 27.00],
    [26.50, 45.00],
    [73.50, 27.00],
    [73.50, 45.00]
];

// Upper mast/taper control sections [x_min, x_max, y_depth, z_height].
mast_sec_0 = [3.000, 97.000, 20.000, 60.000];
mast_sec_1 = [6.333, 93.667, 20.000, 70.000];
mast_sec_2 = [8.000, 92.000, 13.333, 75.000];
mast_sec_3 = [13.000, 87.000, 6.667, 90.000];
mast_sec_4 = [21.119, 78.881, 2.222, 100.000];
mast_sec_5 = [25.265, 74.735, 0.450, 105.000];

// Fine tuning offsets for quick calibration without rewriting section arrays.
mast_width_offset = 0.0;
mast_depth_offset = 0.0;
mast_x_center = 50.0;

// Optional custom cuts for further manual tuning.
enable_custom_cuts = false;
custom_cut_xmin = 23;
custom_cut_xmax = 77;
custom_cut_ymin = 3;
custom_cut_ymax = 17;
custom_cut_zmin = 22;
custom_cut_zmax = 54;

module section_slab(sec, t = loft_slice_thickness) {
    x_min = sec[0];
    x_max = sec[1];
    y_depth = sec[2];
    z_height = sec[3];

    translate([x_min, 0, z_height])
        cube([x_max - x_min, y_depth, t]);
}

module section_slab_adjusted(sec, width_offset = 0, depth_offset = 0, x_center = 50, t = loft_slice_thickness) {
    x_min = sec[0];
    x_max = sec[1];
    y_depth = sec[2];
    z_height = sec[3];

    width = (x_max - x_min) + width_offset;
    x_start = x_center - width / 2;
    y_adj = max(0.1, y_depth + depth_offset);

    translate([x_start, 0, z_height])
        cube([width, y_adj, t]);
}

module loft_between(sec_a, sec_b) {
    hull() {
        section_slab(sec_a);
        section_slab(sec_b);
    }
}

module lower_keel() {
    loft_between(sec_0, sec_1);
    loft_between(sec_1, sec_2);
}

module mid_body() {
    loft_between(sec_2, sec_3);
}

module shoulder_transition() {
    loft_between(sec_3, sec_4);
    loft_between(sec_4, sec_5);
}

module upper_taper() {
    loft_between(sec_5, sec_6);
    loft_between(sec_6, sec_7);
    loft_between(sec_7, sec_8);
    loft_between(sec_8, sec_9);
}

module upper_mast_taper() {
    hull() {
        section_slab_adjusted(mast_sec_0, mast_width_offset, mast_depth_offset, mast_x_center);
        section_slab_adjusted(mast_sec_1, mast_width_offset, mast_depth_offset, mast_x_center);
    }
    hull() {
        section_slab_adjusted(mast_sec_1, mast_width_offset, mast_depth_offset, mast_x_center);
        section_slab_adjusted(mast_sec_2, mast_width_offset, mast_depth_offset, mast_x_center);
    }
    hull() {
        section_slab_adjusted(mast_sec_2, mast_width_offset, mast_depth_offset, mast_x_center);
        section_slab_adjusted(mast_sec_3, mast_width_offset, mast_depth_offset, mast_x_center);
    }
    hull() {
        section_slab_adjusted(mast_sec_3, mast_width_offset, mast_depth_offset, mast_x_center);
        section_slab_adjusted(mast_sec_4, mast_width_offset, mast_depth_offset, mast_x_center);
    }
    hull() {
        section_slab_adjusted(mast_sec_4, mast_width_offset, mast_depth_offset, mast_x_center);
        section_slab_adjusted(mast_sec_5, mast_width_offset, mast_depth_offset, mast_x_center);
    }
}

module chassis_outer() {
    if (enable_upper_mast_features)
        union() {
            lower_keel();
            mid_body();
            shoulder_transition();
            upper_mast_taper();
        }
    else
        union() {
            lower_keel();
            mid_body();
            shoulder_transition();
            upper_taper();
        }
}

module y_axis_hole(x_pos, z_pos, y_start, y_end, d) {
    translate([x_pos, y_start, z_pos])
        rotate([-90, 0, 0])
            cylinder(h = y_end - y_start, d = d);
}

module tapered_inner_cavity() {
    hull() {
        translate([inner_cavity_xmin, inner_cavity_ymin, inner_cavity_zmin])
            cube([
                inner_cavity_xmax - inner_cavity_xmin,
                loft_slice_thickness,
                inner_cavity_zmax_front - inner_cavity_zmin
            ]);

        translate([inner_cavity_xmin, inner_cavity_ymax, inner_cavity_zmin])
            cube([
                inner_cavity_xmax - inner_cavity_xmin,
                loft_slice_thickness,
                inner_cavity_zmax_back - inner_cavity_zmin
            ]);
    }
}

module wheel_motor_zone_cuts() {
    // Side opening around the motor/wheel belt region.
    translate([motor_window_xmin, motor_window_ymin, motor_window_zmin])
        cube([
            motor_window_xmax - motor_window_xmin,
            motor_window_ymax - motor_window_ymin,
            motor_window_zmax - motor_window_zmin
        ]);

    // Rear-inner cavity starts where section loops indicate hollow shell.
    tapered_inner_cavity();

    // Through mounting holes.
    for (p = mount_hole_positions)
        y_axis_hole(p[0], p[1], mount_hole_ymin, mount_hole_ymax, mount_hole_d);

    // Shallow front holes visible only in y ~= 0..2 region.
    for (p = front_hole_positions)
        y_axis_hole(p[0], p[1], front_hole_ymin, front_hole_ymin + front_hole_depth, front_hole_d);
}

module rear_top_trim_cut() {
    x_span = rear_trim_xmax - rear_trim_xmin;

    hull() {
        translate([rear_trim_xmin, rear_trim_y0, rear_trim_z0])
            cube([x_span, loft_slice_thickness, rear_trim_height]);
        translate([rear_trim_xmin, rear_trim_y1, rear_trim_z1])
            cube([x_span, loft_slice_thickness, rear_trim_height]);
    }

    hull() {
        translate([rear_trim_xmin, rear_trim_y1, rear_trim_z1])
            cube([x_span, loft_slice_thickness, rear_trim_height]);
        translate([rear_trim_xmin, rear_trim_y2, rear_trim_z2])
            cube([x_span, loft_slice_thickness, rear_trim_height]);
    }
}

module front_slice_relief_cut() {
    polyhedron(
        points = [
            [front_relief_xmin, front_relief_y_start, 0],
            [front_relief_xmax, front_relief_y_start, 0],
            [front_relief_xmax, front_relief_y_peak, front_relief_z_peak],
            [front_relief_xmin, front_relief_y_peak, front_relief_z_peak],
            [front_relief_xmin, front_relief_y_end, 0],
            [front_relief_xmax, front_relief_y_end, 0]
        ],
        faces = [
            [0, 1, 2, 3],
            [3, 2, 5, 4],
            [0, 4, 5, 1],
            [0, 3, 4],
            [1, 5, 2]
        ],
        convexity = 4
    );
}

module custom_cuts() {
    translate([custom_cut_xmin, custom_cut_ymin, custom_cut_zmin])
        cube([
            custom_cut_xmax - custom_cut_xmin,
            custom_cut_ymax - custom_cut_ymin,
            custom_cut_zmax - custom_cut_zmin
        ]);
}

module wheel_motor_zone_only() {
    difference() {
        chassis_outer();
        wheel_motor_zone_cuts();
    }
}

module mast_zone_only() {
    if (enable_upper_mast_features)
        upper_mast_taper();
}

module wallie_chassis_primitives() {
    difference() {
        chassis_outer();

        if (enable_wheel_motor_features)
            wheel_motor_zone_cuts();

        if (enable_rear_top_trim)
            rear_top_trim_cut();

        if (enable_front_slice_relief)
            front_slice_relief_cut();

        if (enable_custom_cuts)
            custom_cuts();
    }
}

wallie_chassis_primitives();

if (show_reference_mesh)
    color([0.95, 0.1, 0.1, reference_mesh_alpha])
        import("wallieonline-mini-robot.STL");
