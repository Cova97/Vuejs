<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import CompanyStock from '@/components/CompanyStock.vue'
import { companiesApi } from '@/api/companies'
import { NotFoundError } from '@/api/http'
import type { Company } from '@/types/company'

const route = useRoute()

const company = ref<Company | null>(null)
const loading = ref(true)
const notFound = ref(false)
const error = ref<string | null>(null)

onMounted(async () => {
  try {
    // GET /api/companies/{id} ya trae las piezas con su stock.
    company.value = await companiesApi.get(route.params.id as string)
  } catch (e) {
    if (e instanceof NotFoundError) notFound.value = true
    else error.value = String(e)
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <RouterLink :to="{ name: 'companies' }" class="back-link">← Volver a empresas</RouterLink>

  <p v-if="loading" class="muted">Cargando...</p>

  <div v-else-if="notFound" class="card empty-state">
    Esa empresa no existe.
    <p><RouterLink :to="{ name: 'companies' }">Volver a la lista</RouterLink></p>
  </div>

  <div v-else-if="error" class="alert alert-danger">Error: {{ error }}</div>

  <template v-else-if="company">
    <header class="page-header">
      <div>
        <h1>{{ company.name }}</h1>
        <p class="mono">{{ company.rfc }}</p>
      </div>
      <RouterLink
        :to="{ name: 'companies.edit', params: { id: company.id } }"
        class="btn btn-secondary"
      >
        Editar datos
      </RouterLink>
    </header>

    <div class="card info">
      <div>
        <span class="label">Teléfono</span>
        {{ company.phone || '—' }}
      </div>
      <div>
        <span class="label">Dirección</span>
        {{ company.address || '—' }}
      </div>
    </div>

    <!-- v-model: CompanyStock puede reemplazar "company" cuando cambia el stock -->
    <CompanyStock v-model="company" />
  </template>
</template>

<style scoped>
.back-link {
  display: inline-block;
  margin-bottom: 0.5rem;
  font-size: 0.85rem;
  text-decoration: none;
}
.info {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
}
.label {
  display: block;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  color: var(--color-muted);
  margin-bottom: 0.25rem;
}
</style>
