import { spawnSync } from 'node:child_process'
import { copyFileSync, mkdirSync, mkdtempSync, readFileSync, rmSync, writeFileSync } from 'node:fs'
import { tmpdir } from 'node:os'
import { join } from 'node:path'
import { fileURLToPath } from 'node:url'

const source = new URL('../../3dprinter/reinsdesigns/', import.meta.url)
const output = new URL('../public/downloads/designs/rein/', import.meta.url)
const designs = JSON.parse(readFileSync(new URL('../src/data/rein-designs.json', import.meta.url), 'utf8'))
const selectedIds = process.argv.slice(2)
for (const id of selectedIds) {
  if (!designs.some((design) => design.id === id)) throw new Error(`Unknown design: ${id}`)
}
mkdirSync(output, { recursive: true })

for (const design of designs.filter((design) => !selectedIds.length || selectedIds.includes(design.id))) {
  for (const version of design.versions) {
    copyFileSync(new URL(version.file, source), new URL(version.file, output))
    if (version.dependency) {
      copyFileSync(new URL(version.dependency, source), new URL(version.dependency, output))
    }
  }

  const model = fileURLToPath(new URL(design.versions[0].file, source))
  const args = [
    '-o', fileURLToPath(new URL(`${design.id}.png`, output)),
    '--imgsize=960,720', '--autocenter', '--viewall',
    '--camera=0,0,0,55,0,35,250', '--projection=o', '--colorscheme=Tomorrow',
  ]

  const temp = model.endsWith('.stl') ? mkdtempSync(join(tmpdir(), 'rein-design-')) : undefined
  try {
    if (temp) {
      const wrapper = join(temp, 'preview.scad')
      writeFileSync(wrapper, `import(${JSON.stringify(model)});`)
      args.push('--render=true', wrapper)
    } else {
      args.push('-D', `part="${design.previewPart ?? 'assembly'}"`, model)
    }

    console.log(`Rendering ${design.title}`)
    const result = spawnSync('xvfb-run', ['-a', 'openscad', ...args], {
      encoding: 'utf8',
      stdio: ['ignore', 'pipe', 'pipe'],
    })
    if (result.error) throw result.error
    process.stdout.write(result.stdout)
    process.stderr.write(result.stderr)
    if (result.status !== 0 || /^ERROR:/m.test(result.stderr)) {
      throw new Error(`OpenSCAD could not render ${design.title} (exit ${result.status})`)
    }
  } finally {
    if (temp) rmSync(temp, { recursive: true })
  }
}
