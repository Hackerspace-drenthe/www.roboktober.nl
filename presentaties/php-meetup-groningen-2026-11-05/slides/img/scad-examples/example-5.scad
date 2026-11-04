module busje(h=6, d=6, gat=2.2) {
  difference() {
    cylinder(h=h, d=d);
    translate([0,0,-1]) cylinder(h=h+2, d=gat);
  } }
for (p = [[5,5], [35,5], [5,25], [35,25]])
  translate([p[0], p[1], 0]) busje();
