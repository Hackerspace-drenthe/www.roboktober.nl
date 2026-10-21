import { readFileSync, readdirSync } from 'node:fs'
import { resolve } from 'node:path'
import { mount } from '@vue/test-utils'
import { describe, expect, it } from 'vitest'
import designs from '@/data/rein-designs.json'
import ReinDesignGallery from './ReinDesignGallery.vue'

const source = resolve(process.cwd(), '../3dprinter/reinsdesigns')
const published = resolve(process.cwd(), 'public/downloads/designs/rein')

describe('ReinDesignGallery', () => {
  it('groups designs and keeps every version and STL part available', () => {
    const wrapper = mount(ReinDesignGallery)
    const cards = wrapper.findAll('article')

    expect(cards).toHaveLength(13)
    for (const [index, design] of designs.entries()) {
      const card = cards[index]!
      expect(card.find('h3').text()).toBe(design.title)
      expect(card.find('img').attributes('src')).toBe(
        `${import.meta.env.BASE_URL}downloads/designs/rein/${design.id}.png`,
      )
      expect(card.find('img').attributes('alt')).toContain(design.title)
      expect(card.findAll('ul > li')).toHaveLength(design.versions.length)
      for (const version of design.versions) {
        const link = card.find(`li > a[download="${version.file}"]`)
        expect(link.attributes('href')).toBe(
          `${import.meta.env.BASE_URL}downloads/designs/rein/${version.file}`,
        )
        expect(link.attributes('aria-label')).toContain(version.label)
        if (version.dependency) {
          expect(card.find(`a[download="${version.dependency}"]`).exists()).toBe(true)
          expect(card.text()).toContain('bewaar beide bestanden in dezelfde map')
        }
      }
    }
  })

  it('publishes every source model without changing its contents or filename', () => {
    const models = readdirSync(source).filter((name) => /\.(scad|stl)$/.test(name)).sort()
    const listed = [...new Set(designs.flatMap((design) =>
      design.versions.flatMap((version) => version.dependency ? [version.file, version.dependency] : [version.file]),
    ))].sort()

    expect(listed).toEqual(models)
    for (const file of models) {
      expect(
        readFileSync(resolve(published, file)).equals(readFileSync(resolve(source, file))),
        file,
      ).toBe(true)
    }
  })

  it('includes a rendered PNG preview for each design', () => {
    for (const design of designs) {
      const preview = readFileSync(resolve(published, `${design.id}.png`))
      expect(preview.subarray(0, 8).toString('hex')).toBe('89504e470d0a1a0a')
      expect(preview.readUInt32BE(16)).toBe(960)
      expect(preview.readUInt32BE(20)).toBe(720)
      expect(preview.length).toBeGreaterThan(5000)
    }
  })
})
