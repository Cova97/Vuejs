<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import CompanyForm from '@/components/CompanyForm.vue'
import { companiesApi } from '@/api/companies'
import { NotFoundError } from '@/api/http'
import type { Company } from '@/types/company'

const route = useRoute()
const router = useRouter()

const company = ref<Company | null>(null)
const loading = ref(true)
const notFound = ref(false)
const error = ref<string | null>(null)

onMounted(async () => {
  try {
    company.value = await companiesApi.get(route.params.id as string)
  } catch (e) {
    if (e instanceof NotFoundError) notFound.value = true
    else error.value = String(e)
  } finally {
    loading.value = false
  }
})

// Al guardar, regresamos al detalle de la empresa.
function onSaved(saved: Company) {
  router.push({ name: 'companies.show', params: { id: saved.id } })
}
</script>

<template>
  <header class="page-header">
    <div>
      <RouterLink :to="{ name: 'companies' }" class="back-link">← Volver a empresas</RouterLink>
      <h1>{{ company ? company.name : 'Editar empresa' }}</h1>
      <p v-if="company" class="mono">{{ company.rfc }}</p>
    </div>
  </header>

  <div class="card">
    <p v-if="loading" class="muted">Cargando...</p>

    <div v-else-if="notFound" class="empty-state">
      Esa empresa no existe.
      <p><RouterLink :to="{ name: 'companies' }">Volver a la lista</RouterLink></p>
    </div>

    <div v-else-if="error" class="alert alert-danger">Error: {{ error }}</div>

    <CompanyForm v-else-if="company" :company="company" @saved="onSaved">
      <RouterLink
        :to="{ name: 'companies.show', params: { id: company.id } }"
        class="btn btn-secondary"
      >
        Cancelar
      </RouterLink>
    </CompanyForm>
  </div>
</template>

<style scoped>
.back-link {
  display: inline-block;
  margin-bottom: 0.5rem;
  font-size: 0.85rem;
  text-decoration: none;
}
</style>
