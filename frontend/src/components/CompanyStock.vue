<script setup lang="ts">
import { ref, reactive, computed, watch, onMounted } from 'vue'
import { companiesApi } from '@/api/companies'
import { partsApi } from '@/api/parts'
import { ValidationError } from '@/api/http'
import type { Company, CompanyPart } from '@/types/company'
import type { Part, ValidationErrors } from '@/types/part'

// defineModel = "v-model" del componente: el padre le pasa la empresa
// y cuando aquí la reemplazamos, el padre recibe la versión nueva automáticamente.
const company = defineModel<Company>({ required: true })

const rows = computed(() => company.value.parts ?? [])
const error = ref<string | null>(null)

// Bajo stock = hay menos unidades que el mínimo configurado.
const isLow = (row: CompanyPart) => row.stock < row.min_stock
const lowCount = computed(() => rows.value.filter(isLow).length)

// ---------- Editar stock en la tabla ----------
// "drafts" guarda lo que el usuario escribe en cada fila antes de guardar.
const drafts = reactive<Record<number, { stock: number; min_stock: number }>>({})
const savingId = ref<number | null>(null)

// Cada vez que llegan piezas nuevas de la API, reiniciamos los borradores.
watch(
  rows,
  (list) => {
    for (const key of Object.keys(drafts)) delete drafts[Number(key)]
    for (const r of list) drafts[r.id] = { stock: r.stock, min_stock: r.min_stock }
  },
  { immediate: true },
)

const isDirty = (row: CompanyPart) => {
  const d = drafts[row.id]
  return !!d && (d.stock !== row.stock || d.min_stock !== row.min_stock)
}

async function saveRow(row: CompanyPart) {
  const d = drafts[row.id]
  if (!d) return
  savingId.value = row.id
  error.value = null
  try {
    company.value = await companiesApi.setStock(company.value.id, row.id, d)
  } catch (e) {
    error.value = e instanceof ValidationError ? firstError(e.errors) : String(e)
  } finally {
    savingId.value = null
  }
}

async function removeRow(row: CompanyPart) {
  if (!confirm(`¿Quitar ${row.name} de ${company.value.name}? La pieza no se borra del catálogo.`))
    return
  savingId.value = row.id
  error.value = null
  try {
    await companiesApi.removePart(company.value.id, row.id)
    // La API responde 204 (sin cuerpo), así que quitamos la fila nosotros.
    company.value = { ...company.value, parts: rows.value.filter((p) => p.id !== row.id) }
  } catch (e) {
    error.value = `No se pudo quitar: ${e}`
  } finally {
    savingId.value = null
  }
}

// ---------- Asignar una pieza nueva ----------
const allParts = ref<Part[]>([])
// Solo ofrecemos las piezas que esta empresa todavía no tiene.
const availableParts = computed(() => {
  const assigned = new Set(rows.value.map((r) => r.id))
  return allParts.value.filter((p) => !assigned.has(p.id))
})

const newRow = reactive({ part_id: null as number | null, stock: 0, min_stock: 0 })
const adding = ref(false)
const addErrors = ref<ValidationErrors>({})

async function addPart() {
  if (newRow.part_id === null) {
    addErrors.value = { part_id: ['Elige una pieza.'] }
    return
  }
  adding.value = true
  addErrors.value = {}
  error.value = null
  try {
    company.value = await companiesApi.setStock(company.value.id, newRow.part_id, {
      stock: newRow.stock,
      min_stock: newRow.min_stock,
    })
    Object.assign(newRow, { part_id: null, stock: 0, min_stock: 0 })
  } catch (e) {
    if (e instanceof ValidationError) addErrors.value = e.errors
    else error.value = String(e)
  } finally {
    adding.value = false
  }
}

function firstError(errors: ValidationErrors) {
  return Object.values(errors)[0]?.[0] ?? 'Datos inválidos'
}

onMounted(async () => {
  try {
    allParts.value = await partsApi.list()
  } catch (e) {
    error.value = `No se pudieron cargar las piezas: ${e}`
  }
})
</script>

<template>
  <section class="card">
    <div class="card-header">
      <h2>
        Stock de piezas
        <span class="badge badge-muted">{{ rows.length }}</span>
        <span v-if="lowCount" class="badge badge-warning">{{ lowCount }} con bajo stock</span>
      </h2>
    </div>

    <div v-if="error" class="alert alert-danger">{{ error }}</div>

    <!-- Formulario para asignar una pieza a esta empresa -->
    <form class="add-row" @submit.prevent="addPart">
      <div class="field grow">
        <label for="new-part">Pieza</label>
        <select
          id="new-part"
          v-model="newRow.part_id"
          class="input"
          :class="{ 'has-error': addErrors.part_id }"
        >
          <option :value="null" disabled>
            {{ availableParts.length ? 'Elige una pieza...' : 'Ya tiene todas las piezas' }}
          </option>
          <option v-for="p in availableParts" :key="p.id" :value="p.id">
            {{ p.sku }} — {{ p.name }}
          </option>
        </select>
        <small v-if="addErrors.part_id" class="field-error">{{ addErrors.part_id[0] }}</small>
      </div>
      <div class="field small">
        <label for="new-stock">Stock</label>
        <input
          id="new-stock"
          v-model.number="newRow.stock"
          type="number"
          min="0"
          class="input"
          :class="{ 'has-error': addErrors.stock }"
        />
        <small v-if="addErrors.stock" class="field-error">{{ addErrors.stock[0] }}</small>
      </div>
      <div class="field small">
        <label for="new-min">Mínimo</label>
        <input
          id="new-min"
          v-model.number="newRow.min_stock"
          type="number"
          min="0"
          class="input"
          :class="{ 'has-error': addErrors.min_stock }"
        />
        <small v-if="addErrors.min_stock" class="field-error">{{ addErrors.min_stock[0] }}</small>
      </div>
      <button type="submit" class="btn btn-primary" :disabled="adding || !availableParts.length">
        {{ adding ? 'Agregando...' : '+ Agregar' }}
      </button>
    </form>

    <div v-if="rows.length === 0" class="empty-state">
      Esta empresa todavía no tiene piezas. Agrega la primera con el formulario de arriba.
    </div>

    <div v-else class="table-wrap">
      <table class="table">
        <thead>
          <tr>
            <th>SKU</th>
            <th>Pieza</th>
            <th class="num">Stock</th>
            <th class="num">Mínimo</th>
            <th>Estado</th>
            <th class="actions">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="row in rows" :key="row.id" :class="{ low: isLow(row) }">
            <td class="mono">{{ row.sku }}</td>
            <td>{{ row.name }}</td>
            <td class="num">
              <input
                v-if="drafts[row.id]"
                v-model.number="drafts[row.id]!.stock"
                type="number"
                min="0"
                class="input cell-input"
              />
            </td>
            <td class="num">
              <input
                v-if="drafts[row.id]"
                v-model.number="drafts[row.id]!.min_stock"
                type="number"
                min="0"
                class="input cell-input"
              />
            </td>
            <td>
              <span v-if="isLow(row)" class="badge badge-warning">Bajo stock</span>
              <span v-else-if="row.stock === 0" class="badge badge-danger">Sin stock</span>
              <span v-else class="badge badge-success">OK</span>
            </td>
            <td class="actions">
              <button
                v-if="isDirty(row)"
                class="btn btn-primary btn-sm"
                :disabled="savingId === row.id"
                @click="saveRow(row)"
              >
                {{ savingId === row.id ? 'Guardando...' : 'Guardar' }}
              </button>
              <button
                class="btn btn-danger btn-sm"
                :disabled="savingId === row.id"
                @click="removeRow(row)"
              >
                Quitar
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
  flex-wrap: wrap;
}
.add-row {
  display: flex;
  align-items: flex-start;
  gap: 0.75rem;
  flex-wrap: wrap;
  padding: 1rem;
  margin-bottom: 1rem;
  background: var(--color-bg);
  border-radius: var(--radius-sm);
}
.add-row .grow {
  flex: 1 1 240px;
}
.add-row .small {
  width: 110px;
}
/* El botón se alinea con los inputs (no con las etiquetas) */
.add-row .btn {
  margin-top: 1.55rem;
}
.cell-input {
  width: 90px;
  padding: 0.35rem 0.5rem;
  text-align: right;
}
tr.low {
  background: var(--color-warning-soft);
}
</style>
