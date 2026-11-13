import { createRouter, createWebHistory } from 'vue-router'
import PartsView from '../views/PartsView.vue'
import PartEditView from '../views/PartEditView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    { path: '/', name: 'parts', component: PartsView },
    { path: '/parts/:id/edit', name: 'parts.edit', component: PartEditView },
  ],
})

export default router