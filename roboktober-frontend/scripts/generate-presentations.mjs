import { execFileSync } from 'node:child_process'
import { cpSync, mkdirSync, mkdtempSync, readdirSync, renameSync, rmSync, statSync, writeFileSync } from 'node:fs'
import { dirname, join } from 'node:path'
import { fileURLToPath } from 'node:url'

const slug = 'php-meetup-groningen-2026-11-05'
const repository = fileURLToPath(new URL('../../', import.meta.url))
const destination = fileURLToPath(new URL(`../public/presentaties/${slug}/`, import.meta.url))
mkdirSync(dirname(destination), { recursive: true })
const temporary = mkdtempSync(join(dirname(destination), '.generating-'))
const output = join(temporary, slug)

try {
  mkdirSync(output)
  for (const language of ['nl', 'en']) {
    const source = join(repository, 'presentaties', `${slug}${language === 'en' ? '-en' : ''}`)
    const archiveDirectory = join(temporary, `archive-${language}`)
    const extracted = join(temporary, `extracted-${language}`)
    execFileSync('bash', [
      join(source, 'slides/scripts/build-review-package.sh'),
      '--full',
      archiveDirectory,
    ], { stdio: 'inherit' })
    const archives = readdirSync(archiveDirectory).filter((file) => file.endsWith('.zip'))
    if (archives.length !== 1) throw new Error(`Expected one ${language} presentation ZIP`)
    const archive = join(archiveDirectory, archives[0])
    execFileSync('unzip', ['-q', archive, '-d', extracted], { stdio: 'inherit' })
    const roots = readdirSync(extracted)
    if (roots.length !== 1) throw new Error(`Expected one ${language} package root`)
    cpSync(join(extracted, roots[0]), join(output, language), { recursive: true })
    cpSync(archive, join(output, `roboktober-${language}-full.zip`))
    console.log(`${language}: ${Math.round(statSync(archive).size / 1024 / 1024)} MiB, full offline package`)
  }

  writeFileSync(join(output, 'index.html'), `<!doctype html>
<html lang="nl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Van joystick tot wiel / From joystick to wheel — Roboktober</title>
  <style>
    body { font: 18px/1.6 system-ui, sans-serif; max-width: 52rem; margin: 3rem auto; padding: 0 1.2rem; background: #0b0f19; color: #e2e8f0; }
    h1 { line-height: 1.2; } a { color: #fb923c; } section { border: 1px solid #475569; border-radius: 12px; padding: 1.2rem; margin: 1rem 0; }
    .button { display: inline-block; padding: .5rem 1rem; border-radius: 6px; background: #f97316; color: #0b0f19; font-weight: bold; text-decoration: none; }
    :focus-visible { outline: 3px solid #e2e8f0; outline-offset: 4px; }
  </style>
</head>
<body>
  <h1>Van joystick tot wiel<br><span lang="en">From joystick to wheel</span></h1>
  <p>Rein Velt · PHP Meetup Groningen · 5 November 2026</p>
  <section aria-labelledby="nl">
    <h2 id="nl">Nederlands</h2>
    <p><a class="button" href="nl/presentatie/index.html">Bekijk de presentatie</a></p>
    <p><a href="nl/00-LEES-MIJ-EERST.html">Lees eerst de reviewinstructies</a></p>
    <p><a href="roboktober-nl-full.zip" download>Download het volledige offline pakket (±112 MB)</a></p>
    <p>Inclusief video's, 3D-bronnen en sprekersnotities. Pak de ZIP volledig uit en open de LEESMIJ.</p>
  </section>
  <section lang="en" aria-labelledby="en">
    <h2 id="en">English</h2>
    <p><a class="button" href="en/presentatie/index.html">View the presentation</a></p>
    <p><a href="en/00-READ-ME-FIRST.html">Read the review instructions first</a></p>
    <p><a href="roboktober-en-full.zip" download>Download the complete offline package (approx. 112 MB)</a></p>
    <p>Includes videos, 3D sources and speaker notes. Extract the entire ZIP and open READ-ME-FIRST.
    Wallie's video remains in Dutch, without subtitles.</p>
  </section>
  <p>← → / spatie / <span lang="en">space</span>: navigeren / navigate · S: sprekersnotities / speaker notes · F: fullscreen</p>
</body>
</html>
`)

  rmSync(destination, { recursive: true, force: true })
  renameSync(output, destination)
  console.log(`Published presentations to ${destination}`)
} finally {
  rmSync(temporary, { recursive: true })
}
