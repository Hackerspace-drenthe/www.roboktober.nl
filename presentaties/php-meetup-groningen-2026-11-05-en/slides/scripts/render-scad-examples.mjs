import { readFileSync, writeFileSync, mkdirSync } from 'node:fs'
import { fileURLToPath } from 'node:url'
import { join } from 'node:path'
import { runInNewContext } from 'node:vm'
import { spawnSync } from 'node:child_process'

const slides = fileURLToPath(new URL('../', import.meta.url))
const groups = runInNewContext(`${readFileSync(join(slides, 'js/scad-designs.js'), 'utf8')}\nSCAD_DESIGN_GROUPS`)
const output = join(slides, 'img/scad-examples')
mkdirSync(output, { recursive: true })
// Closed, chamfered 40 x 30 mm profile using DXF line entities.
const points = [[0, 0], [40, 0], [40, 20], [30, 30], [0, 30]]
const dxf = ['0', 'SECTION', '2', 'ENTITIES']
points.forEach(([x, y], index) => {
  const [nextX, nextY] = points[(index + 1) % points.length]
  dxf.push('0', 'LINE', '8', '0', '10', `${x}`, '20', `${y}`, '30', '0',
    '11', `${nextX}`, '21', `${nextY}`, '31', '0')
})
dxf.push('0', 'ENDSEC', '0', 'EOF', '')
writeFileSync(join(output, 'profile.dxf'), dxf.join('\n'))

for (const [index, { lesson }] of groups.entries()) {
  if (!lesson.example) continue
  const file = join(output, `example-${index + 1}.scad`)
  writeFileSync(file, `${lesson.code}\n`)
  const result = spawnSync('openscad', [
    '--render', '--imgsize=900,600', '--viewall', '--autocenter',
    '--camera=0,0,0,55,0,35,200', '--colorscheme=Tomorrow',
    '-o', join(output, `example-${index + 1}.png`), file,
  ], { encoding: 'utf8' })
  if (result.error) throw result.error
  process.stdout.write(result.stdout)
  process.stderr.write(result.stderr)
  if (result.status !== 0) throw new Error(`OpenSCAD failed for example ${index + 1}`)
  const modelWarnings = result.stderr.split('\n').filter(line =>
    /ERROR|WARNING/.test(line) && !/^WARNING: (CPU random generator|RDRND generated:)/.test(line))
  if (modelWarnings.length || !result.stderr.includes('Top level object is a 3D object')) {
    throw new Error(`Invalid example ${index + 1}: ${modelWarnings.join('\n')}`)
  }
}
