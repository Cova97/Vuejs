import type { Part, PartInput } from '@/types/part'
import { request, type ApiResponse } from './http'

// Para que los imports viejos (`from '@/api/parts'`) sigan funcionando.
export { ValidationError, NotFoundError } from './http'

export const partsApi = {
  list: async () => (await request<ApiResponse<Part[]>>('/api/parts')).data,
  get: async (id: number | string) =>
    (await request<ApiResponse<Part>>(`/api/parts/${id}`)).data,
  create: async (data: PartInput) =>
    (
      await request<ApiResponse<Part>>('/api/parts', {
        method: 'POST',
        body: JSON.stringify(data),
      })
    ).data,
  update: async (id: number | string, data: PartInput) =>
    (
      await request<ApiResponse<Part>>(`/api/parts/${id}`, {
        method: 'PUT',
        body: JSON.stringify(data),
      })
    ).data,
  remove: (id: number | string) => request<void>(`/api/parts/${id}`, { method: 'DELETE' }),
}
