<script setup lang="ts">
import { useRoute } from 'vue-router'

// Menú lateral. Para agregar una sección nueva (por ejemplo Empresas)
// basta con crear su ruta en router/index.ts y agregar un elemento aquí.
interface NavItem {
  label: string
  icon: string
  to?: string // si no tiene ruta, se muestra como "Próximamente"
}

const navItems: NavItem[] = [
  { label: 'Piezas', icon: '🔩', to: '/parts' },
  { label: 'Empresas', icon: '🏢', to: '/companies' },
]

// Marca la sección activa: /parts y también /parts/5/edit iluminan "Piezas".
const route = useRoute()
const isActive = (item: NavItem) => !!item.to && route.path.startsWith(item.to)
</script>

<template>
  <div class="layout">
    <aside class="sidebar">
      <div class="brand">
        <span class="brand-logo">VL</span>
        <span>Vuejs lab</span>
      </div>

      <nav>
        <template v-for="item in navItems" :key="item.label">
          <RouterLink
            v-if="item.to"
            :to="item.to"
            class="nav-link"
            :class="{ active: isActive(item) }"
          >
            <span class="nav-icon">{{ item.icon }}</span>
            {{ item.label }}
          </RouterLink>
          <span v-else class="nav-link disabled" title="Próximamente">
            <span class="nav-icon">{{ item.icon }}</span>
            {{ item.label }}
            <small>Pronto</small>
          </span>
        </template>
      </nav>
    </aside>

    <main class="content">
      <RouterView />
    </main>
  </div>
</template>

<style scoped>
.layout {
  display: flex;
  min-height: 100vh;
}

.sidebar {
  width: 230px;
  flex-shrink: 0;
  background: var(--color-sidebar);
  color: var(--color-sidebar-text);
  padding: 1.25rem 0.75rem;
}

.brand {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  color: #fff;
  font-weight: 600;
  font-size: 1.05rem;
  padding: 0 0.5rem 1.5rem;
}
.brand-logo {
  display: grid;
  place-items: center;
  width: 32px;
  height: 32px;
  border-radius: 8px;
  background: var(--color-primary);
  font-size: 0.8rem;
}

nav {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.nav-link {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  padding: 0.6rem 0.75rem;
  border-radius: var(--radius-sm);
  color: inherit;
  text-decoration: none;
  font-size: 0.92rem;
  transition: background 0.15s, color 0.15s;
}
.nav-link:hover:not(.disabled) {
  background: rgb(255 255 255 / 0.06);
  color: #fff;
}
.nav-link.active {
  background: rgb(255 255 255 / 0.1);
  color: #fff;
}
.nav-link.disabled {
  opacity: 0.5;
  cursor: default;
}
.nav-link small {
  margin-left: auto;
  font-size: 0.65rem;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.content {
  flex: 1;
  min-width: 0;
  padding: 2rem 2.5rem;
  max-width: 1200px;
}

/* En pantallas chicas el menú pasa arriba */
@media (max-width: 768px) {
  .layout {
    flex-direction: column;
  }
  .sidebar {
    width: auto;
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.75rem 1rem;
  }
  .brand {
    padding: 0;
  }
  nav {
    flex-direction: row;
  }
  .content {
    padding: 1.25rem 1rem;
  }
}
</style>
