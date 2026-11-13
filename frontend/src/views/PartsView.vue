<script setup lang="ts">
import { ref } from 'vue'
import PartList from '../components/PartList.vue'
import PartForm from '../components/PartForm.vue'

const list = ref<InstanceType<typeof PartList> | null>(null)
</script>

<template>
  <PartForm @created="list?.load()" />
  <PartList ref="list" />
</template>