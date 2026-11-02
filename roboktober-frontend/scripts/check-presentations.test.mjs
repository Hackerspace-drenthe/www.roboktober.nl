import assert from 'node:assert/strict'
import { execFileSync } from 'node:child_process'
import { existsSync, readFileSync, readdirSync } from 'node:fs'
import { join, resolve } from 'node:path'
import test from 'node:test'
import { fileURLToPath } from 'node:url'

const root = fileURLToPath(new URL('../public/presentaties/php-meetup-groningen-2026-11-05/', import.meta.url))

test('overview links to both decks, review instructions and full downloads', () => {
  const html = readFileSync(join(root, 'index.html'), 'utf8')
  const links = [...html.matchAll(/href="([^"]+)"/g)].map((match) => match[1])
  assert.equal(links.length, 6)
  for (const link of links) assert.ok(existsSync(join(root, link)), `Missing overview link: ${link}`)
  assert.match(html, /Wallie's video remains in Dutch/)
})

for (const language of ['nl', 'en']) {
  test(`${language}: deck, video, gallery sources and complete archive`, () => {
    const slides = join(root, language, 'presentatie')
    const html = readFileSync(join(slides, 'index.html'), 'utf8')
    assert.match(html, new RegExp(`<html lang="${language}">`))
    assert.match(html, /Rein Velt/)
    assert.match(html, /drenthenoodnetwerk\.nl/)
    assert.equal([...html.matchAll(/<section(?:\s|>)/g)].length, 44)
    for (const match of html.matchAll(/(?:src|href)="([^"]+)"/g)) {
      if (/^(?:https?:|#|\$\{)/.test(match[1])) continue
      assert.ok(existsSync(join(slides, match[1])), `Missing resource: ${match[1]}`)
    }

    const sources = [...readFileSync(join(slides, 'js/scad-designs.js'), 'utf8')
      .matchAll(/(?:'|source: ')((?:reinsdesigns|Wallieonline\+Mini\+Robot\+chassis)\/[^']+)'/g)]
      .map((match) => match[1])
    assert.ok(sources.length >= 24)
    for (const source of sources) {
      assert.ok(existsSync(resolve(slides, '../3dprinter', source)), `Missing gallery source: ${source}`)
    }
    assert.match(readFileSync(join(slides, 'js/visuals.js'), 'utf8'), /\.\.\/3dprinter\//)
    assert.doesNotMatch(readFileSync(join(slides, 'js/visuals.js'), 'utf8'), /\.\.\/\.\.\/\.\.\/3dprinter\//)
    assert.ok(readFileSync(join(slides, 'js/wallie-video.js'), 'utf8').includes('data:video/mp4;base64,'))
    assert.equal(readdirSync(join(slides, 'video')).filter((file) => file.endsWith('.mp4')).length, 2)

    const plan = readFileSync(join(root, language, 'PLAN.md'), 'utf8')
    for (const [, link] of plan.matchAll(/\]\(([^)]+)\)/g)) {
      if (link.startsWith('https://')) continue
      assert.ok(existsSync(join(root, language, link)), `Missing PLAN link: ${link}`)
    }
    const archive = join(root, `roboktober-${language}-full.zip`)
    execFileSync('unzip', ['-tq', archive])
    const paths = execFileSync('unzip', ['-Z1', archive], { encoding: 'utf8' })
    assert.match(paths, /\/presentatie\/video\/wallie-intro-00m26-01m50\.mp4/)
    assert.match(paths, /\/3dprinter\/reinsdesigns\//)
    assert.match(paths, language === 'nl' ? /\/00-LEES-MIJ-EERST\.html/ : /\/00-READ-ME-FIRST\.html/)
  })
}
