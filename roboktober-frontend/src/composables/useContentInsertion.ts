import { nextTick, type Ref } from 'vue'

export function useContentInsertion(
  contentModel: Ref<string>,
  textareaRef: Ref<HTMLTextAreaElement | null>,
) {
  function replaceSelection(buildReplacement: (selection: string) => string): void {
    const textarea = textareaRef.value

    if (!textarea) {
      return
    }

    const start = textarea.selectionStart ?? 0
    const end = textarea.selectionEnd ?? start
    const before = contentModel.value.slice(0, start)
    const selection = contentModel.value.slice(start, end)
    const after = contentModel.value.slice(end)
    const replacement = buildReplacement(selection)

    contentModel.value = `${before}${replacement}${after}`

    void nextTick(() => {
      const newCaret = before.length + replacement.length
      textarea.focus()
      textarea.setSelectionRange(newCaret, newCaret)
    })
  }

  function insertSnippet(snippet: string): void {
    const textarea = textareaRef.value

    if (!textarea) {
      const prefix = contentModel.value.trim() === '' ? '' : '\n'
      contentModel.value += `${prefix}${snippet}`
      return
    }

    const start = textarea.selectionStart ?? contentModel.value.length
    const end = textarea.selectionEnd ?? start
    const before = contentModel.value.slice(0, start)
    const after = contentModel.value.slice(end)
    const needsLeadingBreak = before !== '' && !before.endsWith('\n')
    const needsTrailingBreak = after !== '' && !after.startsWith('\n')
    const insertion = `${needsLeadingBreak ? '\n' : ''}${snippet}${needsTrailingBreak ? '\n' : ''}`

    contentModel.value = `${before}${insertion}${after}`

    void nextTick(() => {
      const caret = before.length + insertion.length
      textarea.focus()
      textarea.setSelectionRange(caret, caret)
    })
  }

  function wrapSelection(before: string, after: string, fallback = 'tekst'): void {
    replaceSelection((selection) => {
      const target = selection.length > 0 ? selection : fallback
      return `${before}${target}${after}`
    })
  }

  function formatHeading(level: 2 | 3, contentFormat: 'html' | 'markdown'): void {
    if (contentFormat === 'html') {
      wrapSelection(`<h${level}>`, `</h${level}>`, 'Koptekst')
      return
    }

    replaceSelection((selection) => {
      const target = selection.length > 0 ? selection : 'Koptekst'
      return `${'#'.repeat(level)} ${target}`
    })
  }

  function formatList(type: 'ul' | 'ol', contentFormat: 'html' | 'markdown'): void {
    replaceSelection((selection) => {
      const lines = (selection.length > 0 ? selection : 'Item')
        .split('\n')
        .map((line) => line.trim())
        .filter((line) => line.length > 0)

      if (lines.length === 0) {
        return ''
      }

      if (contentFormat === 'html') {
        const tag = type === 'ul' ? 'ul' : 'ol'
        const items = lines.map((line) => `  <li>${line}</li>`).join('\n')
        return `<${tag}>\n${items}\n</${tag}>`
      }

      if (type === 'ul') {
        return lines.map((line) => `- ${line}`).join('\n')
      }

      return lines.map((line, index) => `${index + 1}. ${line}`).join('\n')
    })
  }

  function formatLink(url: string, contentFormat: 'html' | 'markdown'): void {
    replaceSelection((selection) => {
      const target = selection.length > 0 ? selection : 'link'

      if (contentFormat === 'html') {
        return `<a href="${url}">${target}</a>`
      }

      return `[${target}](${url})`
    })
  }

  function formatQuote(contentFormat: 'html' | 'markdown'): void {
    if (contentFormat === 'html') {
      wrapSelection('<blockquote>', '</blockquote>', 'Citaat')
      return
    }

    replaceSelection((selection) => {
      const target = selection.length > 0 ? selection : 'Citaat'
      return target
        .split('\n')
        .map((line) => `> ${line}`)
        .join('\n')
    })
  }

  function formatCode(contentFormat: 'html' | 'markdown'): void {
    if (contentFormat === 'html') {
      wrapSelection('<pre><code>', '</code></pre>', 'code')
      return
    }

    replaceSelection((selection) => {
      const target = selection.length > 0 ? selection : 'code'
      return `\`\`\`\n${target}\n\`\`\``
    })
  }

  function insertDivider(contentFormat: 'html' | 'markdown'): void {
    insertSnippet(contentFormat === 'html' ? '<hr />' : '---')
  }

  return {
    insertSnippet,
    wrapSelection,
    formatHeading,
    formatList,
    formatLink,
    formatQuote,
    formatCode,
    insertDivider,
  }
}
