<script setup lang="ts">
import { ref } from 'vue'
import PartList from './components/PartList.vue'
import PartForm from './components/PartForm.vue'

const list = ref<InstanceType<typeof PartList> | null>(null)
</script>

<template>
  <main style="font-family: system-ui; padding: 2rem; max-width: 800px">
    <h1>Vuejs lab</h1>
    <RouterView />
  </main>
</template>