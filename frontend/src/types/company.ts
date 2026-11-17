// Forma de una empresa tal como la devuelve la API (CompanyResource).
export interface Company {
  id: number
  name: string
  address: string | null
  phone: string | null
  rfc: string
  // Solo viene al pedir UNA empresa (GET /api/companies/{id}); en la lista no viene.
  parts?: CompanyPart[]
}

// Una pieza dentro de una empresa, con su stock en esa empresa (tabla company_part).
export interface CompanyPart {
  id: number // id de la pieza
  sku: string
  name: string
  stock: number
  min_stock: number
}

// Datos que enviamos al crear o editar una empresa.
export interface CompanyInput {
  name: string
  address: string
  phone: string
  rfc: string
}

// Datos para PUT /api/companies/{company}/parts/{part}
export interface StockInput {
  stock: number
  min_stock?: number
}
