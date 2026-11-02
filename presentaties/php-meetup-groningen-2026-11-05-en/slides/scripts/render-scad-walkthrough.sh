#!/usr/bin/env bash
set -euo pipefail

SLIDES_DIR="$(cd "$(dirname "$0")/.." && pwd)"
DESIGNS="$SLIDES_DIR/../../../3dprinter"
OUTPUT="$SLIDES_DIR/img/scad-steps"
mkdir -p "$OUTPUT"

render() {
  mkdir -p "$(dirname "$OUTPUT/$1.png")"
  openscad --imgsize=960,720 --viewall --autocenter \
    --camera="$2" --colorscheme=Tomorrow \
    -o "$OUTPUT/$1.png" "${@:3}"
}

CATALOGUE="$(node -e '
  const designs = require(process.argv[1]);
  designs.forEach(([, source, , part = "assembly"]) =>
    console.log([source, part].join("\t")));
' "$SLIDES_DIR/js/scad-designs.js")"

while IFS=$'\t' read -r source part; do
  printf 'Rendering %s\n' "$source"
  camera=0,0,0,55,0,35,200
  if [[ "$source" == Wallieonline* ]]; then
    camera=0,0,0,-35,0,35,200
  fi
  case "$source" in
    *.stl|*.STL)
      render "$source" "$camera" -D "source=\"$DESIGNS/$source\"" "$SLIDES_DIR/scripts/scad-design-import.scad"
      ;;
    *)
      render "$source" "$camera" -D "part=\"$part\"" "$DESIGNS/$source"
      ;;
  esac
done <<< "$CATALOGUE"
