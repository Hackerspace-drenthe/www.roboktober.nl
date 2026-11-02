#!/usr/bin/env bash
# Bouwt een zelfstandig reviewpakket (ZIP) van de presentatie.
# Gebruik: bash build-review-package.sh [--full] [uitvoermap]   (standaard: ~/Desktop)
#   standaard: alleen wat de dia's gebruiken
#   --full:    complete slides-map (incl. video's en scripts) en volledige 3D-bronmappen
set -euo pipefail

FULL=0
if [ "${1:-}" = "--full" ]; then FULL=1; shift; fi

SLIDES="$(cd "$(dirname "$0")/.." && pwd)"
TALK="$(dirname "$SLIDES")"
REPO="$(cd "$TALK/../.." && pwd)"
OUT_DIR="${1:-$HOME/Desktop}"
mkdir -p "$OUT_DIR"
OUT_DIR="$(cd "$OUT_DIR" && pwd)"
NAME="roboktober-php-meetup-2026-11-05-review"
[ "$FULL" -eq 1 ] && NAME="$NAME-volledig"
STAGE="$(mktemp -d)"
PKG="$STAGE/$NAME"
trap 'rm -rf "$STAGE"' EXIT

mkdir -p "$PKG/presentatie/img" "$PKG/3dprinter"

if [ "$FULL" -eq 1 ]; then
  cp -r "$SLIDES/." "$PKG/presentatie/"
  for dir in reinsdesigns "Wallieonline+Mini+Robot+chassis"; do
    mkdir -p "$PKG/3dprinter/$dir"
    find "$REPO/3dprinter/$dir" -maxdepth 1 -type f -exec cp {} "$PKG/3dprinter/$dir/" \;
  done
else
  cp "$SLIDES/index.html" "$PKG/presentatie/"
  cp -r "$SLIDES/css" "$SLIDES/js" "$SLIDES/vendor" "$PKG/presentatie/"
  find "$SLIDES/img" -maxdepth 1 -type f -exec cp {} "$PKG/presentatie/img/" \;
  mkdir -p "$PKG/presentatie/img/scad-steps"
  cp -r "$SLIDES/img/scad-steps/"*/ "$PKG/presentatie/img/scad-steps/"
fi

# Galerijlinks wijzen in de repo naar ../../../3dprinter; in het pakket naar ../3dprinter.
sed -i 's|\.\./\.\./\.\./3dprinter/|../3dprinter/|g' "$PKG/presentatie/js/visuals.js"

# Bronbestanden uit de galerij plus hun use/include/import-afhankelijkheden.
{
  node -e "require('$SLIDES/js/scad-designs.js').forEach(d => console.log(d[1]))"
  grep -o "source: '[^']*'" "$SLIDES/js/scad-designs.js" | sed "s/source: '//;s/'$//"
} | sort -u > "$STAGE/sources.txt"

copy_source() {
  local rel="$1"
  if [ -f "$PKG/3dprinter/$rel" ]; then return 0; fi
  mkdir -p "$PKG/3dprinter/$(dirname "$rel")"
  cp "$REPO/3dprinter/$rel" "$PKG/3dprinter/$rel"
  case "$rel" in
    *.scad)
      { grep -hoE '^\s*(use|include) *<[^>]+>|import *\( *"[^"]+"' "$REPO/3dprinter/$rel" || true; } \
        | sed -E 's/.*[<"]([^>"]+)[>"].*/\1/' \
        | while read -r dep; do copy_source "$(dirname "$rel")/$dep"; done
      ;;
  esac
}
while read -r src; do copy_source "$src"; done < "$STAGE/sources.txt"

cp "$TALK/PLAN.md" "$PKG/"
README="$PKG/00-LEES-MIJ-EERST.md"
cp "$SLIDES/scripts/LEESMIJ-review.md" "$README"
if [ "$FULL" -eq 1 ]; then
  cat >> "$README" <<'EOF'

## Volledige versie

Deze ZIP bevat naast de dia's ook alle bronmateriaal:

- `presentatie/video/` — de volledige kick-off-video en het Wallie-fragment
  (0:26–1:50) als losse mp4. Dia 8 speelt het fragment af vanuit
  `js/wallie-video.js`, dus de presentatie heeft deze map zelf niet nodig.
- `presentatie/scripts/` — de scripts waarmee renders, video en dit pakket
  gemaakt zijn.
- `presentatie/img/` — alle afbeeldingen, ook die niet (meer) in de dia's staan.
- `3dprinter/` — de complete ontwerpmappen (OpenSCAD, STL, STEP).
EOF
fi

# Browserversie van de LEESMIJ met startknop, zodat reviewers die als eerste openen.
python3 - "$README" "$PKG/00-LEES-MIJ-EERST.html" <<'PY'
import markdown, sys
body = markdown.markdown(open(sys.argv[1], encoding="utf-8").read(), extensions=["tables"])
start = '<p class="start"><a href="presentatie/index.html">▶ Start de presentatie</a></p>'
body = body.replace("</h1>", "</h1>" + start, 1)
open(sys.argv[2], "w", encoding="utf-8").write(f"""<!doctype html>
<html lang="nl"><head><meta charset="utf-8"><title>Lees mij eerst · Roboktober reviewpakket</title>
<style>
body{{font:17px/1.6 system-ui,sans-serif;max-width:46em;margin:2em auto;padding:0 1.2em;color:#1d2433;background:#f7f8fb}}
h1{{color:#0b6e4f;line-height:1.25}} h2{{margin-top:1.6em;border-bottom:2px solid #dfe4ec}}
code{{background:#e9edf3;padding:.1em .35em;border-radius:4px}}
table{{border-collapse:collapse}} td,th{{border:1px solid #cfd6e0;padding:.3em .7em;text-align:left}}
.start a{{display:inline-block;background:#0b6e4f;color:#fff;text-decoration:none;font-weight:700;font-size:1.2em;padding:.6em 1.2em;border-radius:8px}}
.start a:hover{{background:#095a41}}
</style></head><body>
{body}
{start}
</body></html>
""")
PY

# PLAN.md verwijst naar de LEESMIJ voor wie daar begint.
sed -i '1a\
\
> **Reviewer?** Begin bij `00-LEES-MIJ-EERST.html` (of `.md`) in de hoofdmap van dit pakket.' "$PKG/PLAN.md"
sed -i \
  -e 's|](slides/|](presentatie/|g' \
  -e 's|](../../|](https://github.com/Hackerspace-drenthe/www.roboktober.nl/blob/master/|g' \
  "$PKG/PLAN.md"

# Controle: elke lokale verwijzing in de presentatie moet in het pakket bestaan.
missing=0
while read -r ref; do
  [ -e "$PKG/presentatie/$ref" ] || { echo "Ontbreekt: $ref" >&2; missing=1; }
done < <(grep -oE '(src|href)="[^"#:$]+"' "$PKG/presentatie/index.html" | sed -E 's/^[a-z]+="//;s/"$//' | sort -u)
while read -r src; do
  [ -e "$PKG/3dprinter/$src" ] || { echo "Ontbreekt: 3dprinter/$src" >&2; missing=1; }
done < "$STAGE/sources.txt"
while read -r src; do
  [ -e "$PKG/presentatie/img/scad-steps/$src.png" ] || { echo "Ontbreekt: render $src.png" >&2; missing=1; }
done < <(node -e "require('$SLIDES/js/scad-designs.js').forEach(d => console.log(d[1]))")
[ "$missing" -eq 0 ] || { echo "Pakket onvolledig, afgebroken." >&2; exit 1; }

mkdir -p "$OUT_DIR"
rm -f "$OUT_DIR/$NAME.zip"
(cd "$STAGE" && zip -qr -X "$OUT_DIR/$NAME.zip" "$NAME")
echo "$OUT_DIR/$NAME.zip ($(du -h "$OUT_DIR/$NAME.zip" | cut -f1))"
