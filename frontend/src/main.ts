import { createApp } from 'vue'
import App from './App.vue'
import router from './router'

// Estilos globales (colores, botones, tablas, formularios...)
import './assets/main.css'

const app = createApp(App)

app.use(router)

app.mount('#app')
