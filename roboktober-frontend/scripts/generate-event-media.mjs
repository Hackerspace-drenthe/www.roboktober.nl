import { execFileSync } from 'node:child_process'
import { copyFileSync, mkdirSync, readdirSync, writeFileSync } from 'node:fs'
import { fileURLToPath } from 'node:url'

const source = new URL('../../fotos/', import.meta.url)
const destination = new URL('../public/media/evenement/', import.meta.url)
const categories = [
  { id: 'robots', title: 'Robots', description: 'Eigen ontwerpen, geprinte chassis en robots van dichtbij.' },
  { id: 'mensenbezig', title: 'Samen bouwen', description: 'Aan de werktafel bij Hackerspace Drenthe: bouwen, uitproberen en elkaar helpen.' },
  { id: 'controller', title: 'Controllers', description: 'Een kijkje bij de besturing van de robots.' },
]

function convert(input, output, filter) {
  execFileSync('ffmpeg', [
    '-hide_banner', '-loglevel', 'error', '-y', '-i', input,
    '-frames:v', '1', '-vf', filter, '-map_metadata', '-1',
    '-c:v', 'libwebp', '-quality', '82', output,
  ], { stdio: 'inherit' })
}

const gallery = categories.map((category) => {
  const files = readdirSync(new URL(`${category.id}/`, source))
    .filter((file) => /\.(jpe?g|png|webp)$/i.test(file)).sort()
  const output = new URL(`${category.id}/`, destination)
  mkdirSync(output, { recursive: true })
  const photos = files.map((file, index) => {
    const name = file.replace(/\.[^.]+$/, '')
    const input = fileURLToPath(new URL(`${category.id}/${file}`, source))
    convert(input, fileURLToPath(new URL(`${name}.webp`, output)), "scale='min(1600,iw)':-2")
    convert(input, fileURLToPath(new URL(`${name}-thumb.webp`, output)), "scale='min(640,iw)':-2")
    return {
      id: `${category.id}-${name}`,
      title: `${category.title} — foto ${index + 1}`,
      image: `${category.id}/${name}.webp`,
      thumbnail: `${category.id}/${name}-thumb.webp`,
    }
  })
  return { ...category, photos }
})

const videoSource = new URL('video/', source)
const videoOutput = new URL('video/', destination)
mkdirSync(videoOutput, { recursive: true })
const videos = readdirSync(videoSource).filter((file) => /\.mp4$/i.test(file)).sort().map((file, index) => {
  const input = fileURLToPath(new URL(file, videoSource))
  const name = file.replace(/\.mp4$/i, '')
  copyFileSync(input, new URL(file, videoOutput))
  convert(input, fileURLToPath(new URL(`${name}-poster.webp`, videoOutput)), "scale='min(1280,iw)':-2")
  return {
    title: `Roboktober in beeld — video ${index + 1}`,
    file: `video/${file}`,
    poster: `video/${name}-poster.webp`,
  }
})

writeFileSync(new URL('../src/data/event-media.json', import.meta.url), `${JSON.stringify({ categories: gallery, videos }, null, 2)}\n`)
console.log(`Generated ${gallery.reduce((total, category) => total + category.photos.length, 0)} photos and ${videos.length} videos.`)
