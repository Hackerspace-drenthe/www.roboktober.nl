import { getRichMediaLibrary, uploadRichMedia } from '@/api'
import type { RichMediaItem, RichMediaUploadPayload } from '@/types/api'
import { ref } from 'vue'

export type ContentFormat = 'html' | 'markdown'

export function useRichMediaLibrary() {
  const items = ref<RichMediaItem[]>([])
  const loading = ref(false)
  const uploading = ref(false)
  const errorMessage = ref<string | null>(null)

  async function load(query?: string): Promise<void> {
    loading.value = true
    errorMessage.value = null

    try {
      const response = await getRichMediaLibrary({ q: query || undefined })
      items.value = response.data
    } catch {
      errorMessage.value = 'Media laden mislukt.'
    } finally {
      loading.value = false
    }
  }

  async function upload(payload: RichMediaUploadPayload): Promise<RichMediaItem | null> {
    uploading.value = true
    errorMessage.value = null

    try {
      const response = await uploadRichMedia(payload)
      items.value = [response.data, ...items.value]
      return response.data
    } catch {
      errorMessage.value = 'Upload mislukt. Controleer bestandstype, grootte of rechten.'
      return null
    } finally {
      uploading.value = false
    }
  }

  function snippetFor(item: RichMediaItem, format: ContentFormat): string {
    return format === 'markdown' ? item.markdown_snippet : item.html_snippet
  }

  return {
    items,
    loading,
    uploading,
    errorMessage,
    load,
    upload,
    snippetFor,
  }
}
