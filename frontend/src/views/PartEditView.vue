<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import PartForm from '@/components/PartForm.vue'
import { partsApi, NotFoundError } from '@/api/parts'
import type { Part } from '@/types/part'

const route = useRoute()
const router = useRouter()

const part = ref<Part | null>(null)
const loading = ref(true)
const notFound = ref(false)
const error = ref<string | null>(null)

onMounted(async () => {
  try {
    part.value = await partsApi.get(route.params.id as string)
  } catch (e) {
    if (e instanceof NotFoundError) notFound.value = true
    else error.value = String(e)
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <header class="page-header">
    <div>
      <RouterLink :to="{ name: 'parts' }" class="back-link">← Volver a piezas</RouterLink>
      <h1>{{ part ? part.name : 'Editar pieza' }}</h1>
      <p v-if="part" class="mono">{{ part.sku }}</p>
    </div>
  </header>

  <div class="card">
    <p v-if="loading" class="muted">Cargando...</p>

    <div v-else-if="notFound" class="empty-state">
      Esa pieza no existe.
      <p><RouterLink :to="{ name: 'parts' }">Volver a la lista</RouterLink></p>
    </div>

    <div v-else-if="error" class="alert alert-danger">Error: {{ error }}</div>

    <PartForm v-else-if="part" :part="part" @saved="router.push({ name: 'parts' })">
      <RouterLink :to="{ name: 'parts' }" class="btn btn-secondary">Cancelar</RouterLink>
    </PartForm>
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
