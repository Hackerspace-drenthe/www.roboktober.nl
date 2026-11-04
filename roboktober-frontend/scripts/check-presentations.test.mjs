import assert from 'node:assert/strict'
import { execFileSync } from 'node:child_process'
import { createHash } from 'node:crypto'
import { existsSync, readFileSync, readdirSync } from 'node:fs'
import { join, resolve } from 'node:path'
import test from 'node:test'
import { fileURLToPath } from 'node:url'
import { runInNewContext } from 'node:vm'
import { JSDOM } from 'jsdom'

const root = fileURLToPath(new URL('../public/presentaties/php-meetup-groningen-2026-11-05/', import.meta.url))

test('overview links to both decks, review instructions and full downloads', () => {
  const html = readFileSync(join(root, 'index.html'), 'utf8')
  const links = [...html.matchAll(/href="([^"]+)"/g)].map((match) => match[1])
  assert.equal(links.length, 6)
  for (const link of links) assert.ok(existsSync(join(root, link)), `Missing overview link: ${link}`)
  assert.match(html, /Wallie's video remains in Dutch/)
})

for (const language of ['nl', 'en']) {
  test(`${language}: speaker notes cover each click, including generated and grouped fragments`, () => {
    const slides = join(root, language, 'presentatie')
    const html = readFileSync(join(slides, 'index.html'), 'utf8')
    assert.match(html, /plugins: \[RevealHighlight, RoboStepNotes, RevealNotes\]/)
    const dom = new JSDOM(html, { runScripts: 'outside-only' })
    const scripts = ['scad-designs', 'visuals', 'firmware-source', 'step-notes']
      .map((name) => readFileSync(join(slides, `js/${name}.js`), 'utf8')).join('\n')
    dom.window.eval(scripts)
    const sections = [...dom.window.document.querySelectorAll('.slides > section')]
    const untouched = sections.filter((section) => !section.querySelector('.fragment, code[data-line-numbers*="|"]'))
      .map((section) => [section, section.querySelector('aside.notes')?.innerHTML])
    // Reproduce highlight.js's fragment clones before Reveal assigns group indices.
    for (const section of sections) {
      for (const code of section.querySelectorAll('code[data-line-numbers*="|"]')) {
        const [first, ...steps] = code.dataset.lineNumbers.split('|')
        const start = code.hasAttribute('data-fragment-index') ? Number(code.dataset.fragmentIndex) : null
        code.dataset.lineNumbers = first
        code.removeAttribute('data-fragment-index')
        steps.forEach((range, index) => {
          const clone = code.cloneNode(true)
          clone.classList.add('fragment')
          clone.dataset.lineNumbers = range
          if (start !== null) clone.dataset.fragmentIndex = start + index
          code.parentNode.append(clone)
        })
      }
      const fragments = [...section.querySelectorAll('.fragment')]
      const explicit = fragments.filter((fragment) => fragment.hasAttribute('data-fragment-index'))
      let next = explicit.length ? Math.max(...explicit.map((fragment) => Number(fragment.dataset.fragmentIndex))) + 1 : 0
      for (const fragment of fragments) {
        if (!fragment.hasAttribute('data-fragment-index')) fragment.dataset.fragmentIndex = next++
      }
    }
    let ready
    dom.window.RoboStepNotes.init({
      on: (event, callback) => {
        assert.equal(event, 'ready')
        ready = callback
      },
      getSlides: () => sections,
    })
    ready()
    const firstFirmwareNote = sections[19].querySelector('aside.notes')
    assert.ok([...firstFirmwareNote.children].some((paragraph) => paragraph.textContent === (language === 'nl' ? 'Welke hardware gebruiken we?' : 'Which hardware are we using?')))
    let stepCount = 0
    for (const [index, section] of sections.entries()) {
      const fragments = [...section.querySelectorAll('.fragment')]
      if (!fragments.length) continue
      const notes = new Map()
      assert.match(section.querySelector('aside.notes').textContent, /Start \//)
      for (const fragment of fragments) {
        const step = Number(fragment.dataset.fragmentIndex)
        assert.ok(fragment.dataset.notes?.length > 80, `Missing notes on slide ${index + 1}`)
        assert.match(fragment.dataset.notes, new RegExp(`${language === 'nl' ? 'Klik' : 'Click'} ${step + 1} /`))
        if (notes.has(step)) assert.equal(fragment.dataset.notes, notes.get(step), 'Grouped fragments share notes')
        notes.set(step, fragment.dataset.notes)
      }
      stepCount += notes.size
    }
    assert.equal(stepCount, 81)
    for (const [section, original] of untouched) assert.equal(section.querySelector('aside.notes')?.innerHTML, original)
    const unexpected = dom.window.document.createElement('span')
    unexpected.className = 'fragment'
    unexpected.dataset.fragmentIndex = '999'
    sections[2].append(unexpected)
    assert.throws(ready, /Speaker notes do not match click steps on slide 3/)
    dom.window.close()
  })

  test(`${language}: deck, video, gallery sources and complete archive`, () => {
    const slides = join(root, language, 'presentatie')
    const html = readFileSync(join(slides, 'index.html'), 'utf8')
    assert.match(html, new RegExp(`<html lang="${language}">`))
    assert.match(html, /Rein Velt/)
    assert.match(html, /drenthenoodnetwerk\.nl/)
    assert.equal([...html.matchAll(/<section(?:\s|>)/g)].length, 45)
    const webui = html.split(/<section(?:\s[^>]*)?>/)[16].split('</section>')[0]
    assert.match(webui, /href="https:\/\/github\.com\/wallieonline"/)
    assert.match(webui, language === 'nl' ? /5 november 2026/ : /5 November 2026/)
    assert.doesNotMatch(webui, /Geen datum noemen|Do not promise a date/)
    const validation = html.split(/<section(?:\s[^>]*)?>/)[30].split('</section>')[0]
    assert.match(validation, /ESP32-C3 Super Mini Plus V2/)
    assert.match(validation, language === 'nl' ? /externe antenne — dezelfde footprint, iets duurder/ : /external antenna — same footprint, slightly more expensive/)
    const recap = html.split(/<section(?:\s[^>]*)?>/)[43].split('</section>')[0]
    assert.equal([...recap.matchAll(/<li>/g)].length, 4)
    for (const point of language === 'nl'
      ? ['Van joystick tot wiel', 'Open software en standaarden als basis.', 'Toetsbare eisen als contract.', 'Code als gereedschap — óók voor hardware.', 'Tests als bewijs — óók in het echt.', 'AI helpt bij elke stap. Jij bepaalt, controleert en valideert.']
      : ['From joystick to wheel', 'Open software and standards as foundations.', 'Testable requirements as contracts.', 'Code as a tool — for hardware too.', 'Tests as proof — in the real world too.', 'AI assists at every step. You define, check and validate.']) {
      assert.ok(recap.includes(point), `Missing recap lesson: ${point}`)
    }
    assert.match(html, /class="cols firmware-full"/)
    const resources = new JSDOM(html).window.document.querySelector('.getting-started')
    assert.equal(resources.querySelectorAll('.cols.c3 .card').length, 3)
    assert.equal(resources.querySelectorAll('.resource-brands > figure').length, 3)
    for (const brand of resources.querySelectorAll('.resource-brands > figure')) {
      assert.ok(brand.querySelector('.resource-image img[alt]'))
      assert.ok(brand.querySelector('figcaption').textContent.trim())
    }
    const ranges = '19-30|32-50|51-73|152-167|170-189|129-150|93-105|106-127|191-205'
    assert.ok(html.includes(`data-line-numbers="${ranges}"`))
    const explanations = [...html.matchAll(/<article class="([^"]*firmware-explanation[^"]*)"([^>]*)>([\s\S]*?)<\/article>/g)]
    assert.equal(explanations.length, 9, 'Explain each highlighted firmware block')
    explanations.forEach(([, classes, attributes, body], index) => {
      assert.ok(attributes.includes(`data-lines="${ranges.split('|')[index]}"`))
      if (index === 0) assert.match(classes, /firmware-initial/)
      else assert.ok(attributes.includes(`data-fragment-index="${index - 1}"`))
      assert.match(body, /<h2>[^<]+<\/h2>/)
      assert.match(body, language === 'nl' ? /<b>Wat:<\/b>/ : /<b>What:<\/b>/)
      assert.match(body, language === 'nl' ? /<b>Waarom:<\/b>/ : /<b>Why:<\/b>/)
      assert.match(body, /class="firmware-result"/)
    })
    assert.match(explanations[7][3], /ledcWrite\((?:kanaal, waarde|channel, value)\)/)
    assert.match(explanations[7][3], /Arduino-ESP32/)
    assert.match(explanations[7][3], /LEDC = LED Control/)
    assert.ok(html.indexOf('js/firmware-source.js') < html.indexOf('Reveal.initialize('))
    const firmware = {}
    runInNewContext(readFileSync(join(slides, 'js/firmware-source.js'), 'utf8'), {
      document: { querySelector: (selector) => {
        assert.equal(selector, '.firmware-full code')
        return firmware
      } },
    })
    const source = firmware.textContent
    assert.equal(source.split('\n').length, 205, 'Include every source line')
    assert.equal(createHash('sha256').update(source.split('\n').map((line) => line.trimEnd()).join('\n')).digest('hex'),
      'd7596879a9e4ad269b62722fa1a0a1008967eb0cd5230c3866b1297c58a47bca',
      'Match pinned upstream firmware, ignoring trailing whitespace on blank lines')
    assert.match(readFileSync(join(slides, 'js/firmware-LICENSE.txt'), 'utf8'), /GNU GENERAL PUBLIC LICENSE/)
    const overview = html.match(/<section class="pcbflow-slide"[^>]*>([\s\S]*?)<\/section>/)?.[1]
    assert.ok(overview, 'Missing robot overview')
    const parts = [...overview.matchAll(/<article class="(?:fragment )?pf-part">([\s\S]*?)<\/article>/g)]
    assert.equal(parts.length, 7, 'Show the seven robot parts separately')
    assert.equal([...overview.matchAll(/class="fragment pf-part"/g)].length, 6)
    assert.equal([...overview.matchAll(/class="fragment pf-step"/g)].length, 5)
    for (const [, part] of parts) {
      assert.match(part, /<(?:img|svg)\s/, 'Each part needs its own visual')
      assert.match(part, /<h2>[^<]+<\/h2>/, 'Each part needs its own title')
    }
    assert.match(parts[2][1], /<h2>(?:Aan\/uit|On\/off)<\/h2>/)
    assert.match(parts[2][1], /<img src="img\/part-power-disconnect\.svg"/)
    assert.match(parts[3][1], /<h2>(?:Printplaat|Circuit board) · PCB<\/h2>/)
    assert.match(parts[4][1], /<h2>ESP32-C3 SuperMini<\/h2>/)
    assert.match(parts[4][1], /<img src="img\/esp32-c3-supermini-photo\.png"/)
    assert.match(parts[4][1], /Tobias Weltner · done\.land/)
    assert.match(parts[4][1], /https:\/\/creativecommons\.org\/licenses\/by-nd\/4\.0\//)
    assert.match(parts[5][1], /<img src="img\/drv8833-module-photo\.jpg"/)
    assert.match(parts[5][1], /AliExpress/)
    assert.match(parts[6][1], /<img src="img\/n20-motor-photo\.jpg"/)
    assert.match(parts[6][1], /<img src="img\/n20-wheel-photo\.jpg"/)
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
    const gallery = runInNewContext(`${readFileSync(join(slides, 'js/scad-designs.js'), 'utf8')}\nSCAD_DESIGN_GROUPS`)
    assert.equal(gallery.length, 10)
    assert.equal(gallery.reduce((sum, group) => sum + group.designs.length, 0), 24)
    for (const index of [1, 2, 4, 7]) assert.equal(gallery[index].lesson.example, true)
    for (const index of [1, 2, 4, 7]) {
      const base = join(slides, `img/scad-examples/example-${index + 1}`)
      assert.equal(readFileSync(`${base}.scad`, 'utf8'), `${gallery[index].lesson.code}\n`,
        'Rendered example must match the displayed code')
      const png = readFileSync(`${base}.png`)
      assert.equal(png.readUInt32BE(16), 900)
      assert.equal(png.readUInt32BE(20), 600)
    }
    assert.match(readFileSync(join(slides, 'img/scad-examples/profile.dxf'), 'utf8'), /ENTITIES/)
    assert.match(gallery[1].lesson.code, /pcb_w \+ 2\*/)
    assert.match(gallery[2].lesson.code, /difference\(\)[\s\S]*union\(\)/)
    assert.match(gallery[4].lesson.code, /module [\s\S]*difference\(\)[\s\S]*for \(p =/)
    assert.match(gallery[7].lesson.code, /linear_extrude\(height=2\)[\s\S]*import\("[^"]+\.dxf"\)/)
    assert.match(gallery[8].lesson.code, /assert\(/)
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
