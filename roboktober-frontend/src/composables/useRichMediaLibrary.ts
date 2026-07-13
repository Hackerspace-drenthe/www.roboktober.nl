import { getRichMediaLibrary, uploadRichMedia } from '@/api'
import type { RichMediaItem, RichMediaUploadPayload } from '@/types/api'
import { ref } from 'vue'

export type ContentFormat = 'html' | 'markdown'

export function useRichMediaLibrary() {
  const items = ref<RichMediaItem[]>([])
  const loading = ref(false)
  const uploading = ref(false)
  const errorMessage = ref<string | null>(null)
  const currentPage = ref(1)
  const lastPage = ref(1)
  const totalItems = ref(0)
  const activeQuery = ref('')

  async function load(query?: string, page = 1): Promise<void> {
    loading.value = true
    errorMessage.value = null

    try {
      const response = await getRichMediaLibrary({
        q: query || undefined,
        page,
      })

      currentPage.value = response.meta.current_page
      lastPage.value = response.meta.last_page
      totalItems.value = response.meta.total
      activeQuery.value = query ?? ''

      if (page === 1) {
        items.value = response.data
      } else {
        items.value = [...items.value, ...response.data]
      }
    } catch {
      errorMessage.value = 'Media laden mislukt.'
    } finally {
      loading.value = false
    }
  }

  async function loadNextPage(): Promise<void> {
    if (loading.value || currentPage.value >= lastPage.value) {
      return
    }

    await load(activeQuery.value, currentPage.value + 1)
  }

  function hasMorePages(): boolean {
    return currentPage.value < lastPage.value
  }

  async function upload(payload: RichMediaUploadPayload): Promise<RichMediaItem | null> {
    uploading.value = true
    errorMessage.value = null

    try {
      const response = await uploadRichMedia(payload)
      items.value = [response.data, ...items.value]
      totalItems.value += 1
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
    currentPage,
    lastPage,
    totalItems,
    load,
    loadNextPage,
    hasMorePages,
    upload,
    snippetFor,
  }
}
