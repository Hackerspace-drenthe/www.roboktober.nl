pcb_w = 78;
pcb_l = 56;
clearance = 0.3;
inner_size = [pcb_w + 2*clearance, pcb_l + 2*clearance];
cube([inner_size[0], inner_size[1], 2]);
