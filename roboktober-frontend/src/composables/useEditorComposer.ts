import { type Ref } from 'vue'
import { useContentInsertion } from '@/composables/useContentInsertion'

export type ContentFormat = 'html' | 'markdown'

export type EditorAction =
  | 'bold'
  | 'italic'
  | 'h2'
  | 'h3'
  | 'ul'
  | 'ol'
  | 'link'
  | 'quote'
  | 'code'
  | 'divider'

export function useEditorComposer(
  contentModel: Ref<string>,
  textareaRef: Ref<HTMLTextAreaElement | null>,
  contentFormat: Ref<ContentFormat>,
) {
  const {
    insertSnippet,
    wrapSelection,
    formatHeading,
    formatList,
    formatLink,
    formatQuote,
    formatCode,
    insertDivider,
  } = useContentInsertion(contentModel, textareaRef)

  function applyAction(action: EditorAction): void {
    if (action === 'bold') {
      if (contentFormat.value === 'html') {
        wrapSelection('<strong>', '</strong>', 'vetgedrukte tekst')
      } else {
        wrapSelection('**', '**', 'vetgedrukte tekst')
      }

      return
    }

    if (action === 'italic') {
      if (contentFormat.value === 'html') {
        wrapSelection('<em>', '</em>', 'cursieve tekst')
      } else {
        wrapSelection('*', '*', 'cursieve tekst')
      }

      return
    }

    if (action === 'h2') {
      formatHeading(2, contentFormat.value)
      return
    }

    if (action === 'h3') {
      formatHeading(3, contentFormat.value)
      return
    }

    if (action === 'ul') {
      formatList('ul', contentFormat.value)
      return
    }

    if (action === 'ol') {
      formatList('ol', contentFormat.value)
      return
    }

    if (action === 'link') {
      const url = window.prompt('Voer de URL in', 'https://')

      if (url && url.trim().length > 0) {
        formatLink(url.trim(), contentFormat.value)
      }

      return
    }

    if (action === 'quote') {
      formatQuote(contentFormat.value)
      return
    }

    if (action === 'code') {
      formatCode(contentFormat.value)
      return
    }

    if (action === 'divider') {
      insertDivider(contentFormat.value)
    }
  }

  function insert(snippet: string): void {
    insertSnippet(snippet)
  }

  return {
    applyAction,
    insert,
  }
}