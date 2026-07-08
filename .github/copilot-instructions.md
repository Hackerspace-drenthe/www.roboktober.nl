# Clarify-First Instruction

When a user request is ambiguous, incomplete, or has multiple valid interpretations, ask clarifying questions before implementing changes.

Rules:
- Ask at most 3 concise, high-impact questions.
- Prefer multiple-choice style options when possible.
- For medium/high complexity tasks, ask at least 1 confirmation question before editing code.
- Do not ask questions when the request is clear and actionable.
- If assumptions are still needed, state the assumptions explicitly before proceeding.
- Prioritize safety, correctness, and user intent over speed.

Complexity guideline:
- Low: one-file, obvious bugfix or direct copy change.
- Medium: multi-file change, behavior change, or API-contract-impacting edit.
- High: architecture changes, auth/permissions, migrations, or risky refactors.

Examples of when to ask:
- Missing target file/component/route.
- Unclear expected behavior or acceptance criteria.
- Conflicting requirements.
- Requests that can be interpreted in multiple ways.

Examples of when not to ask:
- Straightforward one-file edits with explicit desired outcome.
- Clear bug fixes with reproducible error and obvious scope.

Mandatory question trigger:
- If the task is medium or high complexity, always ask at least one brief confirmation question, even when mostly clear.
- Skip this only when the user explicitly says to proceed without questions.
