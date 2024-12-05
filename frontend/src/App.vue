<template>
  <div class="flex h-screen w-screen overflow-hidden">
    <!-- Sidebar (apenas para rotas autenticadas) -->
    <Sidebar v-if="showSidebar" />

    <!-- Conteúdo Principal -->
    <div class="flex-1 overflow-auto bg-gray-50">
      <router-view></router-view>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import Sidebar from './components/Sidebar.vue'

const route = useRoute()

// Mostra a sidebar apenas se não estiver nas páginas de auth (login, registro, reset senha)
const showSidebar = computed(() => {
  const authRoutes = ['/login', '/register', '/reset-password']
  return !authRoutes.includes(route.path)
})
</script>

<style>
/* Reset CSS para garantir que não haja margens ou padding indesejados */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  margin: 0;
  padding: 0;
  overflow: hidden;
}

#app {
  width: 100vw;
  height: 100vh;
  overflow: hidden;
}

.btn-primary {
  @apply inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500;
}

.btn-secondary {
  @apply inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500;
}

.form-label {
  @apply block text-sm font-medium text-gray-700;
}

.input-field {
  @apply mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm;
}
</style>
