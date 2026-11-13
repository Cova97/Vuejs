import type { Part, PartInput, ValidationErrors } from '@/types/part'

// Error especial para cuando Laravel responde 422 (datos inválidos).
export class ValidationError extends Error {
  constructor(public errors: ValidationErrors) {
    super('Datos inválidos')
  }
}

// Error para cuando la pieza no existe (404).
export class NotFoundError extends Error {}

// Función única que hace todas las llamadas a la API.
// Así los headers y el manejo de errores viven en un solo lugar.
async function request<T>(url: string, options: RequestInit = {}): Promise<T> {
  const res = await fetch(url, {
    ...options,
    headers: {
      Accept: 'application/json',
      'Content-Type': 'application/json',
      ...options.headers,
    },
  })

  if (res.status === 422) throw new ValidationError((await res.json()).errors)
  if (res.status === 404) throw new NotFoundError('No encontrado')
  if (!res.ok) throw new Error(`HTTP ${res.status}`)

  // 204 No Content (por ejemplo al borrar) no trae cuerpo.
  return res.status === 204 ? (undefined as T) : res.json()
}

export const partsApi = {
  list: () => request<Part[]>('/api/parts'),
  get: (id: number | string) => request<Part>(`/api/parts/${id}`),
  create: (data: PartInput) =>
    request<Part>('/api/parts', { method: 'POST', body: JSON.stringify(data) }),
  update: (id: number | string, data: PartInput) =>
    request<Part>(`/api/parts/${id}`, { method: 'PUT', body: JSON.stringify(data) }),
  remove: (id: number | string) => request<void>(`/api/parts/${id}`, { method: 'DELETE' }),
}
