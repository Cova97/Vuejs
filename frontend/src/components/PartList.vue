<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { partsApi } from '@/api/parts'
import type { Part } from '@/types/part'

const parts = ref<Part[]>([])
const loading = ref(true)
const error = ref<string | null>(null)
const deleting = ref<number | null>(null)

// Texto del buscador. "computed" recalcula la lista filtrada cada vez que cambia.
const search = ref('')
const filtered = computed(() => {
  const q = search.value.trim().toLowerCase()
  if (!q) return parts.value
  return parts.value.filter(
    (p) => p.sku.toLowerCase().includes(q) || p.name.toLowerCase().includes(q),
  )
})

// Formatea el precio como moneda: 12.5 -> "$12.50"
const money = new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' })

async function load() {
  loading.value = true
  error.value = null
  try {
    parts.value = await partsApi.list()
  } catch (e) {
    error.value = String(e)
  } finally {
    loading.value = false
  }
}

async function remove(part: Part) {
  if (!confirm(`¿Borrar ${part.name}?`)) return

  deleting.value = part.id
  error.value = null
  try {
    await partsApi.remove(part.id)

    parts.value = parts.value.filter((p) => p.id !== part.id)
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
        <span v-if="!loading" class="badge badge-muted">{{ parts.length }}</span>
      </h2>
      <input v-model="search" class="input search" type="search" placeholder="Buscar por SKU o nombre..." />
    </div>

    <div v-if="error" class="alert alert-danger">{{ error }}</div>

    <p v-if="loading" class="muted">Cargando...</p>

    <div v-else-if="parts.length === 0" class="empty-state">
      Aún no hay piezas registradas. Crea la primera con <strong>+ Nueva pieza</strong>.
    </div>

    <div v-else-if="filtered.length === 0" class="empty-state">
      Ninguna pieza coincide con "{{ search }}".
    </div>

    <div v-else class="table-wrap">
      <table class="table">
        <thead>
          <tr>
            <th>SKU</th>
            <th>Nombre</th>
            <th class="num">Cantidad</th>
            <th class="num">Precio</th>
            <th>Estado</th>
            <th class="actions">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="part in filtered" :key="part.id">
            <td class="mono">{{ part.sku }}</td>
            <td>
              <div class="name">{{ part.name }}</div>
              <div v-if="part.description" class="desc">{{ part.description }}</div>
            </td>
            <td class="num">
              <span v-if="part.quantity === 0" class="badge badge-danger">Sin stock</span>
              <template v-else>{{ part.quantity }}</template>
            </td>
            <td class="num">{{ money.format(Number(part.price)) }}</td>
            <td>
              <span class="badge" :class="part.active ? 'badge-success' : 'badge-muted'">
                {{ part.active ? 'Activa' : 'Inactiva' }}
              </span>
            </td>
            <td class="actions">
              <RouterLink
                :to="{ name: 'parts.edit', params: { id: part.id } }"
                class="btn btn-secondary btn-sm"
              >
                Editar
              </RouterLink>
              <button
                class="btn btn-danger btn-sm"
                @click="remove(part)"
                :disabled="deleting === part.id"
              >
                {{ deleting === part.id ? 'Borrando...' : 'Borrar' }}
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
}
.desc {
  color: var(--color-muted);
  font-size: 0.8rem;
  max-width: 340px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
</style>
