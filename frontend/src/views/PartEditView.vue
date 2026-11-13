<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()

const id = route.params.id as string

const form = reactive({
  sku: '',
  name: '',
  description: '',
  quantity: 0,
  price: 0,
  active: true,
})

const errors = ref<Record<string, string[]>>({})
const loading = ref(true)
const saving = ref(false)
const notFound = ref(false)

onMounted(async () => {
  try {
    const res = await fetch(`/api/parts/${id}`, {
      headers: { Accept: 'application/json' },
    })

    if (res.status === 404) {
      notFound.value = true
      return
    }

    if (!res.ok) throw new Error(`HTTP ${res.status}`)

    const part = await res.json()
    Object.assign(form, {
      sku: part.sku,
      name: part.name,
      description: part.description ?? '',
      quantity: part.quantity,
      price: Number(part.price),
      active: part.active,
    })
  } catch (e) {
    alert(e)
  } finally {
    loading.value = false
  }
})

async function submit() {
  saving.value = true
  errors.value = {}

  try {
    const res = await fetch(`/api/parts/${id}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
      },
      body: JSON.stringify(form),
    })

    if (res.status === 422) {
      errors.value = (await res.json()).errors
      return
    }

    if (!res.ok) throw new Error(`HTTP ${res.status}`)

    router.push({ name: 'parts' })
  } catch (e) {
    alert(e)
  } finally {
    saving.value = false
  }
}
</script>

<template>
  <p v-if="loading">Cargando...</p>

  <p v-else-if="notFound">
    Esa pieza no existe. <RouterLink to="/">Volver</RouterLink>
  </p>

  <form v-else @submit.prevent="submit">
    <h2>Editar pieza</h2>

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
      {{ saving ? 'Guardando...' : 'Actualizar' }}
    </button>
    <RouterLink to="/">Cancelar</RouterLink>
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
button {
  margin-right: 0.75rem;
}
</style>