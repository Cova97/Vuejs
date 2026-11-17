<script setup lang="ts">
import { ref } from 'vue'
import CompanyList from '@/components/CompanyList.vue'
import CompanyForm from '@/components/CompanyForm.vue'

const list = ref<InstanceType<typeof CompanyList> | null>(null)
const showForm = ref(false)

function onSaved() {
  showForm.value = false
  list.value?.load()
}
</script>

<template>
  <header class="page-header">
    <div>
      <h1>Empresas</h1>
      <p>Empresas y almacenes. Entra a una para ver y ajustar su stock de piezas.</p>
    </div>
    <button v-if="!showForm" class="btn btn-primary" @click="showForm = true">+ Nueva empresa</button>
  </header>

  <div v-if="showForm" class="card">
    <CompanyForm @saved="onSaved">
      <button type="button" class="btn btn-secondary" @click="showForm = false">Cancelar</button>
    </CompanyForm>
  </div>

  <CompanyList ref="list" />
</template>
