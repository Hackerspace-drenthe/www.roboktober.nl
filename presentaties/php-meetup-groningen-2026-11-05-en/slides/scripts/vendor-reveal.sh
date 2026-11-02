#!/usr/bin/env bash
# Downloads reveal.js into slides/vendor/reveal.js so the deck works offline (file://, no CDN).
set -euo pipefail

VERSION="${1:-5}"
SLIDES_DIR="$(cd "$(dirname "$0")/.." && pwd)"
TMP="$(mktemp -d)"
trap 'rm -rf "$TMP"' EXIT

cd "$TMP"
npm pack "reveal.js@${VERSION}" --silent >/dev/null
tar xzf reveal.js-*.tgz

rm -rf "$SLIDES_DIR/vendor/reveal.js"
mkdir -p "$SLIDES_DIR/vendor/reveal.js"
cp -r package/dist package/plugin package/LICENSE "$SLIDES_DIR/vendor/reveal.js/"
echo "reveal.js $(node -p "require('./package/package.json').version") -> $SLIDES_DIR/vendor/reveal.js"
