<script setup lang="ts">
import { ref, reactive } from 'vue'

const emit = defineEmits<{ created: [] }>()

const form = reactive({
  sku: '',
  name: '',
  description: '',
  quantity: 0,
  price: 0,
  active: true,
})

const errors = ref<Record<string, string[]>>({})
const saving = ref(false)

function reset() {
  form.sku = ''
  form.name = ''
  form.description = ''
  form.quantity = 0
  form.price = 0
  form.active = true
}

async function submit() {
  saving.value = true
  errors.value = {}

  try {
    const res = await fetch('/api/parts', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
      },
      body: JSON.stringify(form),
    })

    if (res.status === 422) {
      const data = await res.json()
      errors.value = data.errors
      return
    }

    if (!res.ok) throw new Error(`HTTP ${res.status}`)

    reset()
    emit('created')
  } catch (e) {
    alert(e)
  } finally {
    saving.value = false
  }
}
</script>

<template>
  <form @submit.prevent="submit" style="margin-bottom: 2rem">
    <h2>Nueva pieza</h2>

    <div>
      <label>SKU</label>
      <input v-model="form.sku" />
      <small v-if="errors.sku">{{ errors.sku[0] }}</small>
    </div>

    <div>
      <label>Nombre</label>
      <input v-model="form.name" />
      <small v-if="errors.name">{{ errors.name[0] }}</small>
    </div>

    <div>
      <label>Descripción</label>
      <textarea v-model="form.description"></textarea>
      <small v-if="errors.description">{{ errors.description[0] }}</small>
    </div>

    <div>
      <label>Cantidad</label>
      <input type="number" v-model.number="form.quantity" />
      <small v-if="errors.quantity">{{ errors.quantity[0] }}</small>
    </div>

    <div>
      <label>Precio</label>
      <input type="number" step="0.01" v-model.number="form.price" />
      <small v-if="errors.price">{{ errors.price[0] }}</small>
    </div>

    <div>
      <label>
        <input type="checkbox" v-model="form.active" />
        Activo
      </label>
    </div>

    <button type="submit" :disabled="saving">
      {{ saving ? 'Guardando...' : 'Guardar' }}
    </button>
  </form>
</template>

<style scoped>
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
</style>