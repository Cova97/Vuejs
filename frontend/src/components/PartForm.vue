<script setup lang="ts">
import { ref, reactive } from 'vue'
import { partsApi, ValidationError } from '@/api/parts'
import type { Part, PartInput, ValidationErrors } from '@/types/part'

// Si recibe una pieza, el formulario está en modo "editar"; si no, en modo "crear".
const props = defineProps<{ part?: Part }>()
const emit = defineEmits<{ saved: [part: Part] }>()

const isEdit = !!props.part

function emptyForm(): PartInput {
  return { sku: '', name: '', description: '', price: 0, active: true }
}

function fromPart(part: Part): PartInput {
  return {
    sku: part.sku,
    name: part.name,
    description: part.description ?? '',
    price: Number(part.price),
    active: part.active,
  }
}

const form = reactive<PartInput>(props.part ? fromPart(props.part) : emptyForm())
const errors = ref<ValidationErrors>({})
const saving = ref(false)
const error = ref<string | null>(null)

async function submit() {
  saving.value = true
  errors.value = {}
  error.value = null

  try {
    const saved = props.part
      ? await partsApi.update(props.part.id, form)
      : await partsApi.create(form)

    if (!isEdit) Object.assign(form, emptyForm())
    emit('saved', saved)
  } catch (e) {
    if (e instanceof ValidationError) errors.value = e.errors
    else error.value = String(e)
  } finally {
    saving.value = false
  }
}
</script>

<template>
  <form @submit.prevent="submit">
    <div class="card-header">
      <h2>{{ isEdit ? 'Editar pieza' : 'Nueva pieza' }}</h2>
    </div>

    <div v-if="error" class="alert alert-danger">{{ error }}</div>

    <div class="form-grid">
      <div class="field">
        <label for="sku">SKU</label>
        <input
          id="sku"
          v-model="form.sku"
          class="input mono"
          :class="{ 'has-error': errors.sku }"
          placeholder="Ej. TOR-001"
        />
        <small v-if="errors.sku" class="field-error">{{ errors.sku[0] }}</small>
      </div>

      <div class="field">
        <label for="name">Nombre</label>
        <input
          id="name"
          v-model="form.name"
          class="input"
          :class="{ 'has-error': errors.name }"
          placeholder="Ej. Tornillo hexagonal 1/4"
        />
        <small v-if="errors.name" class="field-error">{{ errors.name[0] }}</small>
      </div>

      <div class="field full">
        <label for="description">Descripción</label>
        <textarea
          id="description"
          v-model="form.description"
          class="input"
          :class="{ 'has-error': errors.description }"
          placeholder="Opcional"
        ></textarea>
        <small v-if="errors.description" class="field-error">{{ errors.description[0] }}</small>
      </div>

      <div class="field">
        <label for="price">Precio</label>
        <input
          id="price"
          type="number"
          step="0.01"
          min="0"
          v-model.number="form.price"
          class="input"
          :class="{ 'has-error': errors.price }"
        />
        <small v-if="errors.price" class="field-error">{{ errors.price[0] }}</small>
      </div>

      <!-- La cantidad no se captura: se calcula sumando el stock de cada empresa. -->
      <div v-if="props.part" class="field">
        <label>Cantidad total</label>
        <input class="input" :value="props.part.quantity" disabled />
        <small class="field-hint">Se calcula con el stock de cada empresa.</small>
      </div>

      <div class="field full">
        <label class="checkbox">
          <input type="checkbox" v-model="form.active" />
          Pieza activa
        </label>
      </div>
    </div>

    <div class="form-actions">
      <button type="submit" class="btn btn-primary" :disabled="saving">
        {{ saving ? 'Guardando...' : isEdit ? 'Guardar cambios' : 'Crear pieza' }}
      </button>
      <!-- El slot permite que quien use el formulario agregue botones extra (como "Cancelar"). -->
      <slot />
    </div>
  </form>
</template>
