<script setup lang="ts">
import { ref } from 'vue'
import media from '@/data/event-media.json'
import KickoffRecording from '@/components/KickoffRecording.vue'

defineProps<{ embedded?: boolean }>()

type Photo = (typeof media.categories)[number]['photos'][number]
const dialog = ref<HTMLDialogElement | null>(null)
const selectedPhoto = ref<Photo | null>(null)
const mediaUrl = (file: string) => `${import.meta.env.BASE_URL}media/evenement/${file}`
const photoCount = media.categories.reduce((total, category) => total + category.photos.length, 0)

function openPhoto(photo: Photo): void {
  selectedPhoto.value = photo
  dialog.value?.showModal()
}
</script>

<template>
  <section id="galerij" aria-labelledby="galerij-title" class="scroll-mt-28">
    <section class="bg-robo-dark py-16 text-white md:py-24">
      <div class="mx-auto max-w-6xl px-6">
        <p class="mb-3 text-sm font-bold uppercase tracking-widest text-robo-orange">Uit de werkplaats</p>
        <component :is="embedded ? 'h2' : 'h1'" id="galerij-title" class="text-4xl font-black md:text-6xl">Foto's en video's</component>
        <p class="mt-5 max-w-2xl text-lg text-slate-300">
          Zo ziet Roboktober eruit: eigen robots, samen bouwen en de techniek achter de besturing.
          Een kijkje bij Hackerspace Drenthe.
        </p>
        <p class="mt-5 text-sm text-slate-300">{{ photoCount }} foto's · {{ media.videos.length }} video</p>
        <nav aria-label="Galerijonderwerpen" class="mt-8 flex flex-wrap gap-3">
          <a
            v-for="category in media.categories"
            :key="category.id"
            :href="`#${category.id}`"
            class="rounded-full border border-white/25 px-4 py-2 text-sm font-bold transition hover:bg-white/10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-robo-orange"
          >{{ category.title }} ({{ category.photos.length }})</a>
          <a href="#video" class="rounded-full border border-white/25 px-4 py-2 text-sm font-bold transition hover:bg-white/10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-robo-orange">Video</a>
        </nav>
      </div>
    </section>

    <div class="bg-slate-50 py-14">
      <div class="mx-auto max-w-6xl space-y-16 px-6">
        <section
          v-for="category in media.categories"
          :id="category.id"
          :key="category.id"
          :aria-labelledby="`${category.id}-title`"
          class="scroll-mt-28"
        >
          <component :is="embedded ? 'h3' : 'h2'" :id="`${category.id}-title`" class="text-3xl font-black text-robo-dark">{{ category.title }}</component>
          <p class="mb-6 mt-3 text-slate-600">{{ category.description }} Klik op een foto om deze te vergroten.</p>
          <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <figure
              v-for="photo in category.photos"
              :key="photo.id"
              class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
            >
              <button
                type="button"
                :aria-label="`Vergroot ${photo.title}`"
                aria-haspopup="dialog"
                class="group block w-full bg-slate-100 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-inset focus-visible:ring-robo-orange"
                @click="openPhoto(photo)"
              >
                <img
                  :src="mediaUrl(photo.thumbnail)"
                  :alt="photo.title"
                  loading="lazy"
                  decoding="async"
                  width="640"
                  height="480"
                  class="aspect-4/3 w-full object-contain transition group-hover:brightness-110"
                />
              </button>
              <figcaption class="px-4 py-3 text-sm font-semibold text-slate-600">{{ photo.title }}</figcaption>
            </figure>
          </div>
        </section>

        <section id="video" aria-labelledby="video-title" class="scroll-mt-28">
          <component :is="embedded ? 'h3' : 'h2'" id="video-title" class="text-3xl font-black text-robo-dark">Video</component>
          <p class="mb-6 mt-3 text-slate-600">Roboktober in bewegend beeld. Start de video zelf; er wordt niets automatisch afgespeeld.</p>
          <div class="grid gap-6">
            <KickoffRecording :heading-level="embedded ? 'h4' : 'h3'" />
            <figure v-for="video in media.videos" :key="video.file" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
              <video
                controls
                playsinline
                preload="none"
                :poster="mediaUrl(video.poster)"
                :aria-label="video.title"
                class="max-h-[70vh] w-full bg-black"
              >
                <source :src="mediaUrl(video.file)" type="video/mp4" />
                Je browser kan deze video niet afspelen.
                <a :href="mediaUrl(video.file)">Open de video als MP4</a>.
              </video>
              <figcaption class="flex flex-wrap items-center justify-between gap-3 px-5 py-4">
                <span class="font-semibold text-robo-dark">{{ video.title }}</span>
                <a :href="mediaUrl(video.file)" download class="text-sm font-bold text-robo-dark underline underline-offset-4 hover:text-robo-orange-dark">Download video (MP4)</a>
              </figcaption>
            </figure>
          </div>
        </section>
      </div>
    </div>

    <dialog
      ref="dialog"
      aria-labelledby="photo-dialog-title"
      class="fixed inset-0 m-auto max-h-[95dvh] w-[calc(100%-2rem)] max-w-5xl overflow-auto rounded-2xl bg-robo-dark p-4 text-white backdrop:bg-black/80"
      @click.self="dialog?.close()"
      @close="selectedPhoto = null"
    >
      <template v-if="selectedPhoto">
        <div class="mb-4 flex items-center justify-between gap-4">
          <h2 id="photo-dialog-title" class="font-bold">{{ selectedPhoto.title }}</h2>
          <button type="button" autofocus class="rounded-lg border border-white/30 px-4 py-2 font-bold hover:bg-white/10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-robo-orange" @click="dialog?.close()">Sluiten</button>
        </div>
        <img :src="mediaUrl(selectedPhoto.image)" :alt="selectedPhoto.title" class="mx-auto max-h-[70dvh] max-w-full object-contain" />
        <a :href="mediaUrl(selectedPhoto.image)" download class="mt-4 inline-block font-bold underline underline-offset-4">Download foto (WebP)</a>
      </template>
    </dialog>
  </section>
</template>
