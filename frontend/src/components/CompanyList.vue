<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { companiesApi } from '@/api/companies'
import type { Company } from '@/types/company'

const companies = ref<Company[]>([])
const loading = ref(true)
const error = ref<string | null>(null)
const deleting = ref<number | null>(null)

const search = ref('')
const filtered = computed(() => {
  const q = search.value.trim().toLowerCase()
  if (!q) return companies.value
  return companies.value.filter(
    (c) => c.name.toLowerCase().includes(q) || c.rfc.toLowerCase().includes(q),
  )
})

async function load() {
  loading.value = true
  error.value = null
  try {
    companies.value = await companiesApi.list()
  } catch (e) {
    error.value = String(e)
  } finally {
    loading.value = false
  }
}

async function remove(company: Company) {
  if (!confirm(`¿Borrar ${company.name}? También se borrará su stock de piezas.`)) return

  deleting.value = company.id
  error.value = null
  try {
    await companiesApi.remove(company.id)
    companies.value = companies.value.filter((c) => c.id !== company.id)
  } catch (e) {
    error.value = `No se pudo borrar: ${e}`
  } finally {
    deleting.value = null
  }
}

onMounted(load)

defineExpose({ load })
</script>

<template>
  <section class="card">
    <div class="card-header">
      <h2>
        Listado
        <span v-if="!loading" class="badge badge-muted">{{ companies.length }}</span>
      </h2>
      <input v-model="search" class="input search" type="search" placeholder="Buscar por nombre o RFC..." />
    </div>

    <div v-if="error" class="alert alert-danger">{{ error }}</div>

    <p v-if="loading" class="muted">Cargando...</p>

    <div v-else-if="companies.length === 0" class="empty-state">
      Aún no hay empresas registradas. Crea la primera con <strong>+ Nueva empresa</strong>.
    </div>

    <div v-else-if="filtered.length === 0" class="empty-state">
      Ninguna empresa coincide con "{{ search }}".
    </div>

    <div v-else class="table-wrap">
      <table class="table">
        <thead>
          <tr>
            <th>Nombre</th>
            <th>RFC</th>
            <th>Teléfono</th>
            <th>Dirección</th>
            <th class="actions">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="company in filtered" :key="company.id">
            <td>
              <RouterLink :to="{ name: 'companies.show', params: { id: company.id } }" class="name">
                {{ company.name }}
              </RouterLink>
            </td>
            <td class="mono">{{ company.rfc }}</td>
            <td>{{ company.phone || '—' }}</td>
            <td class="muted">{{ company.address || '—' }}</td>
            <td class="actions">
              <RouterLink
                :to="{ name: 'companies.show', params: { id: company.id } }"
                class="btn btn-secondary btn-sm"
              >
                Ver stock
              </RouterLink>
              <RouterLink
                :to="{ name: 'companies.edit', params: { id: company.id } }"
                class="btn btn-secondary btn-sm"
              >
                Editar
              </RouterLink>
              <button
                class="btn btn-danger btn-sm"
                @click="remove(company)"
                :disabled="deleting === company.id"
              >
                {{ deleting === company.id ? 'Borrando...' : 'Borrar' }}
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>
</template>

<style scoped>
.card-header h2 {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}
.search {
  max-width: 280px;
}
.name {
  font-weight: 500;
  text-decoration: none;
}
.name:hover {
  text-decoration: underline;
}
</style>
