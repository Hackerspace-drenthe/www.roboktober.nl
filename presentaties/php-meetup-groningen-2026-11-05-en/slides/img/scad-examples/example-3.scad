difference() {
  union() {
    cube([40, 30, 2]);
    translate([20, 15, 2]) cylinder(d=10, h=6);
  }
  translate([20, 15, -1]) cylinder(d=3, h=10);
}
