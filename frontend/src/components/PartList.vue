<script setup lang="ts">
import { ref, onMounted } from 'vue'

interface Part {
  id: number
  sku: string
  name: string
  description: string | null
  quantity: number
  price: string
  active: boolean
}

const parts = ref<Part[]>([])
const loading = ref(true)
const error = ref<string | null>(null)
const deleting = ref<number | null>(null)

async function load() {
  loading.value = true
  error.value = null
  try {
    const res = await fetch('/api/parts', {
      headers: { Accept: 'application/json' },
    })
    if (!res.ok) throw new Error(`HTTP ${res.status}`)
    parts.value = await res.json()
  } catch (e) {
    error.value = String(e)
  } finally {
    loading.value = false
  }
}

async function remove(part: Part) {
  if (!confirm(`¿Borrar ${part.name}?`)) return

  deleting.value = part.id
  try {
    const res = await fetch(`/api/parts/${part.id}`, {
      method: 'DELETE',
      headers: { Accept: 'application/json' },
    })
    if (!res.ok) throw new Error(`HTTP ${res.status}`)

    parts.value = parts.value.filter((p) => p.id !== part.id)
  } catch (e) {
    alert(`No se pudo borrar: ${e}`)
  } finally {
    deleting.value = null
  }
}

onMounted(load)

defineExpose({ load })
</script>

<template>
  <section>
    <h2>Piezas</h2>

    <p v-if="loading">Cargando...</p>
    <p v-else-if="error">Error: {{ error }}</p>
    <p v-else-if="parts.length === 0">No hay piezas registradas.</p>

    <table v-else>
      <thead>
        <tr>
          <th>SKU</th>
          <th>Nombre</th>
          <th>Cantidad</th>
          <th>Precio</th>
          <th>Activo</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="part in parts" :key="part.id">
          <td>{{ part.sku }}</td>
          <td>{{ part.name }}</td>
          <td>{{ part.quantity }}</td>
          <td>{{ Number(part.price).toFixed(2) }}</td>
          <td>{{ part.active ? 'Sí' : 'No' }}</td>
          <td>
            <RouterLink :to="{ name: 'parts.edit', params: { id: part.id } }">
              Editar
            </RouterLink>
            <button @click="remove(part)" :disabled="deleting === part.id">
              {{ deleting === part.id ? 'Borrando...' : 'Borrar' }}
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </section>
</template>

<style scoped>
table {
  border-collapse: collapse;
  width: 100%;
}
th,
td {
  border: 1px solid #ddd;
  padding: 0.4rem 0.6rem;
  text-align: left;
}
td a {
  margin-right: 0.5rem;
}
</style>