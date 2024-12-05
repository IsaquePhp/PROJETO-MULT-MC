<template>
  <nav class="bg-white shadow">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <!-- Logo e Menu Principal -->
        <div class="flex">
          <!-- Logo -->
          <div class="flex-shrink-0 flex items-center">
            <h1 class="text-2xl font-bold text-indigo-600">MC</h1>
          </div>

          <!-- Menu Principal -->
          <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
            <router-link
              to="/dashboard"
              class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
              :class="{ 'border-indigo-500 text-gray-900': $route.path === '/dashboard' }"
            >
              Dashboard
            </router-link>

            <router-link
              to="/products"
              class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
              :class="{ 'border-indigo-500 text-gray-900': $route.path === '/products' }"
            >
              Produtos
            </router-link>

            <router-link
              to="/sales"
              class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
              :class="{ 'border-indigo-500 text-gray-900': $route.path === '/sales' }"
            >
              Vendas
            </router-link>

            <router-link
              to="/inventory"
              class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
              :class="{ 'border-indigo-500 text-gray-900': $route.path === '/inventory' }"
            >
              Estoque
            </router-link>
          </div>
        </div>

        <!-- Menu do Usuário -->
        <div class="hidden sm:ml-6 sm:flex sm:items-center">
          <div class="ml-3 relative">
            <div class="flex items-center">
              <!-- Nome do Usuário -->
              <span class="text-gray-700 text-sm font-medium mr-4">{{ userName }}</span>

              <!-- Botão de Logout -->
              <button
                @click="handleLogout"
                class="bg-white p-1 rounded-full text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                <span class="text-sm text-gray-700">Sair</span>
              </button>
            </div>
          </div>
        </div>

        <!-- Menu Mobile -->
        <div class="-mr-2 flex items-center sm:hidden">
          <button
            @click="mobileMenuOpen = !mobileMenuOpen"
            class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500"
          >
            <span class="sr-only">Abrir menu principal</span>
            <!-- Ícone do Menu (3 barras) -->
            <svg
              class="h-6 w-6"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4 6h16M4 12h16M4 18h16"
              />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Menu Mobile (Expandido) -->
    <div v-if="mobileMenuOpen" class="sm:hidden">
      <div class="pt-2 pb-3 space-y-1">
        <router-link
          to="/dashboard"
          class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium"
          :class="[
            $route.path === '/dashboard'
              ? 'bg-indigo-50 border-indigo-500 text-indigo-700'
              : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700'
          ]"
        >
          Dashboard
        </router-link>

        <router-link
          to="/products"
          class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium"
          :class="[
            $route.path === '/products'
              ? 'bg-indigo-50 border-indigo-500 text-indigo-700'
              : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700'
          ]"
        >
          Produtos
        </router-link>

        <router-link
          to="/sales"
          class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium"
          :class="[
            $route.path === '/sales'
              ? 'bg-indigo-50 border-indigo-500 text-indigo-700'
              : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700'
          ]"
        >
          Vendas
        </router-link>

        <router-link
          to="/inventory"
          class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium"
          :class="[
            $route.path === '/inventory'
              ? 'bg-indigo-50 border-indigo-500 text-indigo-700'
              : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700'
          ]"
        >
          Estoque
        </router-link>

        <!-- Botão de Logout Mobile -->
        <button
          @click="handleLogout"
          class="block w-full text-left pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700"
        >
          Sair
        </button>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const auth = useAuthStore()
const mobileMenuOpen = ref(false)

// Simula o nome do usuário (você pode pegar isso do store de autenticação)
const userName = computed(() => auth.user?.name || 'Usuário')

const handleLogout = async () => {
  await auth.logout()
  router.push('/login')
}
</script>
