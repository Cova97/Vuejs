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
  <p v-if="loading">Cargando...</p>

  <p v-else-if="notFound">
    Esa pieza no existe. <RouterLink to="/">Volver</RouterLink>
  </p>

  <p v-else-if="error">Error: {{ error }}</p>

  <PartForm v-else-if="part" :part="part" @saved="router.push({ name: 'parts' })">
    <RouterLink to="/">Cancelar</RouterLink>
  </PartForm>
</template>
