<script setup lang="ts">
import { ref, reactive } from 'vue'
import { partsApi, ValidationError } from '@/api/parts'
import type { Part, PartInput, ValidationErrors } from '@/types/part'

// Si recibe una pieza, el formulario está en modo "editar"; si no, en modo "crear".
const props = defineProps<{ part?: Part }>()
const emit = defineEmits<{ saved: [part: Part] }>()

const isEdit = !!props.part

function emptyForm(): PartInput {
  return { sku: '', name: '', description: '', quantity: 0, price: 0, active: true }
}

function fromPart(part: Part): PartInput {
  return {
    sku: part.sku,
    name: part.name,
    description: part.description ?? '',
    quantity: part.quantity,
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
    <h2>{{ isEdit ? 'Editar pieza' : 'Nueva pieza' }}</h2>

    <div>
      <label for="sku">SKU</label>
      <input id="sku" v-model="form.sku" />
      <small v-if="errors.sku">{{ errors.sku[0] }}</small>
    </div>

    <div>
      <label for="name">Nombre</label>
      <input id="name" v-model="form.name" />
      <small v-if="errors.name">{{ errors.name[0] }}</small>
    </div>

    <div>
      <label for="description">Descripción</label>
      <textarea id="description" v-model="form.description"></textarea>
      <small v-if="errors.description">{{ errors.description[0] }}</small>
    </div>

    <div>
      <label for="quantity">Cantidad</label>
      <input id="quantity" type="number" v-model.number="form.quantity" />
      <small v-if="errors.quantity">{{ errors.quantity[0] }}</small>
    </div>

    <div>
      <label for="price">Precio</label>
      <input id="price" type="number" step="0.01" v-model.number="form.price" />
      <small v-if="errors.price">{{ errors.price[0] }}</small>
    </div>

    <div>
      <label>
        <input type="checkbox" v-model="form.active" />
        Activo
      </label>
    </div>

    <small v-if="error">{{ error }}</small>

    <button type="submit" :disabled="saving">
      {{ saving ? 'Guardando...' : isEdit ? 'Actualizar' : 'Guardar' }}
    </button>
    <!-- El slot permite que quien use el formulario agregue botones extra (como "Cancelar"). -->
    <slot />
  </form>
</template>

<style scoped>
form {
  margin-bottom: 2rem;
}
div {
  margin-bottom: 0.75rem;
}
label {
  display: block;
  font-size: 0.85rem;
}
small {
  color: crimson;
  display: block;
}
button {
  margin-right: 0.75rem;
}
</style>
