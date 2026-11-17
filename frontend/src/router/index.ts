import { createRouter, createWebHistory } from 'vue-router'
import PartsView from '../views/PartsView.vue'
import PartEditView from '../views/PartEditView.vue'
import CompaniesView from '../views/CompaniesView.vue'
import CompanyDetailView from '../views/CompanyDetailView.vue'
import CompanyEditView from '../views/CompanyEditView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    // La página de inicio manda directo a Piezas.
    { path: '/', redirect: '/parts' },

    { path: '/parts', name: 'parts', component: PartsView },
    { path: '/parts/:id/edit', name: 'parts.edit', component: PartEditView },

    { path: '/companies', name: 'companies', component: CompaniesView },
    { path: '/companies/:id', name: 'companies.show', component: CompanyDetailView },
    { path: '/companies/:id/edit', name: 'companies.edit', component: CompanyEditView },
  ],
})

export default router
