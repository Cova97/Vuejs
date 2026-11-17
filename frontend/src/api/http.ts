import type { ValidationErrors } from '@/types/part'

// Código compartido por todas las APIs (piezas, empresas...).
// Antes vivía en api/parts.ts; lo movimos aquí para no repetirlo en api/companies.ts.

// Error especial para cuando Laravel responde 422 (datos inválidos).
export class ValidationError extends Error {
  constructor(public errors: ValidationErrors) {
    super('Datos inválidos')
  }
}

// Error para cuando el registro no existe (404).
export class NotFoundError extends Error {}

// Función única que hace todas las llamadas a la API.
// Así los headers y el manejo de errores viven en un solo lugar.
export async function request<T>(url: string, options: RequestInit = {}): Promise<T> {
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

// Laravel (con los Resources) envuelve las respuestas en { "data": ... }.
// Este tipo describe esa "caja" y en cada API sacamos lo que viene dentro con .data
export interface ApiResponse<T> {
  data: T
}
