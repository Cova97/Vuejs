<script setup lang="ts">
import { ref } from 'vue'
import PartList from '@/components/PartList.vue'
import PartForm from '@/components/PartForm.vue'

const list = ref<InstanceType<typeof PartList> | null>(null)

// El formulario de "Nueva pieza" se muestra solo cuando el usuario lo pide.
const showForm = ref(false)

function onSaved() {
  showForm.value = false
  list.value?.load()
}
</script>

<template>
  <header class="page-header">
    <div>
      <h1>Piezas</h1>
      <p>Catálogo de piezas. La cantidad es la suma del stock de todas las empresas.</p>
    </div>
    <button v-if="!showForm" class="btn btn-primary" @click="showForm = true">+ Nueva pieza</button>
  </header>

  <div v-if="showForm" class="card">
    <PartForm @saved="onSaved">
      <button type="button" class="btn btn-secondary" @click="showForm = false">Cancelar</button>
    </PartForm>
  </div>

  <PartList ref="list" />
</template>
