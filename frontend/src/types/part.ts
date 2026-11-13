// Forma de una pieza tal como la devuelve la API de Laravel.
export interface Part {
  id: number
  sku: string
  name: string
  description: string | null
  quantity: number
  price: string // Laravel envía los decimales como texto, por ejemplo "12.50"
  active: boolean
  created_at: string | null // fecha en formato ISO, ej. "2026-11-13T20:00:00+00:00"
  updated_at: string | null
}

// Datos que enviamos al crear o editar (sin id ni fechas).
export interface PartInput {
  sku: string
  name: string
  description: string
  quantity: number
  price: number
  active: boolean
}

// Errores de validación que devuelve Laravel con estado 422:
// { "sku": ["El sku ya existe."], "name": [...] }
export type ValidationErrors = Record<string, string[]>
