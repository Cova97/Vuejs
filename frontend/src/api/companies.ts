import type { Company, CompanyInput, StockInput } from '@/types/company'
import { request, type ApiResponse } from './http'

// Igual que partsApi, pero para /api/companies.
export const companiesApi = {
  list: async () => (await request<ApiResponse<Company[]>>('/api/companies')).data,
  get: async (id: number | string) =>
    (await request<ApiResponse<Company>>(`/api/companies/${id}`)).data,
  create: async (data: CompanyInput) =>
    (
      await request<ApiResponse<Company>>('/api/companies', {
        method: 'POST',
        body: JSON.stringify(data),
      })
    ).data,
  update: async (id: number | string, data: CompanyInput) =>
    (
      await request<ApiResponse<Company>>(`/api/companies/${id}`, {
        method: 'PUT',
        body: JSON.stringify(data),
      })
    ).data,
  remove: (id: number | string) => request<void>(`/api/companies/${id}`, { method: 'DELETE' }),

  // Stock: "en la empresa X, la pieza Y tiene N unidades".
  // Si la pieza no estaba en la empresa, la API la agrega. Devuelve la empresa con sus piezas.
  setStock: async (companyId: number, partId: number, data: StockInput) =>
    (
      await request<ApiResponse<Company>>(`/api/companies/${companyId}/parts/${partId}`, {
        method: 'PUT',
        body: JSON.stringify(data),
      })
    ).data,
  removePart: (companyId: number, partId: number) =>
    request<void>(`/api/companies/${companyId}/parts/${partId}`, { method: 'DELETE' }),
}
