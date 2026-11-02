use <wallieonline-mini-robot.scad>

// Measured from STL bounding boxes
MAIN_BBOX_MIN = [3, 0, 0];
MAIN_BBOX_SIZE = [94, 20, 105];

// Base dimensions (mm)
main_size = MAIN_BBOX_SIZE;

show_main = true;
center_models = false;

module _place_mesh(bbox_min = [0, 0, 0], base_size = [1, 1, 1], target_size = [1, 1, 1], center_model = false) {
    sx = target_size[0] / base_size[0];
    sy = target_size[1] / base_size[1];
    sz = target_size[2] / base_size[2];

    if (center_model) {
        translate([-target_size[0] / 2, -target_size[1] / 2, -target_size[2] / 2])
            scale([sx, sy, sz])
                translate([-bbox_min[0], -bbox_min[1], -bbox_min[2]])
                    wallieonline_mini_robot_mesh();
    } else {
        scale([sx, sy, sz])
            translate([-bbox_min[0], -bbox_min[1], -bbox_min[2]])
                wallieonline_mini_robot_mesh();
    }
}

module wallie_main_parametric(size = MAIN_BBOX_SIZE, center_model = false) {
    _place_mesh(
        bbox_min = MAIN_BBOX_MIN,
        base_size = MAIN_BBOX_SIZE,
        target_size = size,
        center_model = center_model
    );
}

module wallie_assembly_parametric(main_size = MAIN_BBOX_SIZE, center_model = false, show_main_part = true) {
    if (show_main_part)
        wallie_main_parametric(size = main_size, center_model = center_model);
}

wallie_assembly_parametric(
    main_size = main_size,
    center_model = center_models,
    show_main_part = show_main
);
