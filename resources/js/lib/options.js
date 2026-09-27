/**
 * Normalize Laravel option payloads (string[] or { name }[]) into string names.
 */
export function toOptionNames(items = [], fallback = []) {
  if (!Array.isArray(items) || items.length === 0) return [...fallback]
  return items
    .map((item) => (typeof item === "string" ? item : item?.name))
    .filter(Boolean)
}
