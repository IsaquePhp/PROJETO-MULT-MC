<template>
  <div class="flex h-screen bg-gray-100">
    <!-- Sidebar -->
    <Sidebar />

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
      <!-- Top Navigation -->
      <header class="bg-white shadow">
        <div class="px-4 py-3 flex justify-between items-center">
          <h2 class="text-xl font-semibold text-gray-800">
            {{ currentRouteName }}
          </h2>
          <div class="flex items-center space-x-4">
            <button @click="refreshPage" class="text-gray-600 hover:text-gray-900">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
            </button>
            <div class="relative">
              <button @click="toggleUserMenu" class="flex items-center space-x-2 text-gray-600 hover:text-gray-900">
                <span>{{ userName }}</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </button>
              <div v-if="showUserMenu" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1">
                <router-link to="/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                  Perfil
                </router-link>
                <button @click="logout" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                  Sair
                </button>
              </div>
            </div>
          </div>
        </div>
      </header>

      <!-- Page Content -->
      <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
        <router-view></router-view>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import Sidebar from '../components/Sidebar.vue'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

const showUserMenu = ref(false)
const userName = computed(() => authStore.user?.name || 'Usuário')
const currentRouteName = computed(() => {
  const routeNames = {
    'Dashboard': 'Dashboard',
    'Customers': 'Clientes',
    'Products': 'Produtos',
    'Categories': 'Categorias',
    'PDV': 'PDV',
    'SalesHistory': 'Histórico de Vendas',
    'StockEntries': 'Lançamentos de Estoque',
    'Deliveries': 'Entregas',
    'LogisticsKanban': 'Kanban de Logística',
    'Routes': 'Rotas',
    'Accounts': 'Contas',
    'Bills': 'Faturas',
    'Budgets': 'Orçamentos',
    'Reports': 'Relatórios',
    'Profile': 'Perfil'
  }
  return routeNames[route.name] || route.name
})

function toggleUserMenu() {
  showUserMenu.value = !showUserMenu.value
}

function refreshPage() {
  window.location.reload()
}

async function logout() {
  await authStore.logout()
  router.push('/login')
}
</script>

<style scoped>
.router-link-active {
  @apply bg-gray-100 text-gray-900;
}
</style>
