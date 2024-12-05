<template>
  <nav class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <div class="flex">
          <div class="flex-shrink-0 flex items-center">
            <router-link to="/dashboard" class="text-xl font-bold text-gray-900">
              Sistema de Gestão
            </router-link>
          </div>
          <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
            <router-link 
              to="/dashboard" 
              class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
              :class="{ 'border-blue-500 text-gray-900': $route.path === '/dashboard' }"
            >
              Dashboard
            </router-link>

            <router-link 
              to="/products" 
              class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
              :class="{ 'border-blue-500 text-gray-900': $route.path === '/products' }"
            >
              Produtos
            </router-link>

            <router-link 
              to="/sales" 
              class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
              :class="{ 'border-blue-500 text-gray-900': $route.path === '/sales' }"
            >
              Vendas
            </router-link>

            <router-link 
              to="/inventory" 
              class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
              :class="{ 'border-blue-500 text-gray-900': $route.path === '/inventory' }"
            >
              Estoque
            </router-link>
          </div>
        </div>
        <div class="hidden sm:ml-6 sm:flex sm:items-center">
          <button 
            @click="handleLogout" 
            class="ml-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
          >
            Sair
          </button>
        </div>
        
        <!-- Menu Mobile -->
        <div class="-mr-2 flex items-center sm:hidden">
          <button 
            @click="mobileMenuOpen = !mobileMenuOpen" 
            type="button" 
            class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500"
          >
            <span class="sr-only">Abrir menu</span>
            <svg 
              class="h-6 w-6" 
              xmlns="http://www.w3.org/2000/svg" 
              fill="none" 
              viewBox="0 0 24 24" 
              stroke="currentColor"
            >
              <path 
                v-if="!mobileMenuOpen" 
                stroke-linecap="round" 
                stroke-linejoin="round" 
                stroke-width="2" 
                d="M4 6h16M4 12h16M4 18h16"
              />
              <path 
                v-else 
                stroke-linecap="round" 
                stroke-linejoin="round" 
                stroke-width="2" 
                d="M6 18L18 6M6 6l12 12"
              />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Menu Mobile -->
    <div class="sm:hidden" v-show="mobileMenuOpen">
      <div class="pt-2 pb-3 space-y-1">
        <router-link 
          to="/dashboard" 
          class="text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800 block pl-3 pr-4 py-2 border-l-4 text-base font-medium"
          :class="{ 'bg-blue-50 border-blue-500 text-blue-700': $route.path === '/dashboard' }"
        >
          Dashboard
        </router-link>

        <router-link 
          to="/products" 
          class="text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800 block pl-3 pr-4 py-2 border-l-4 text-base font-medium"
          :class="{ 'bg-blue-50 border-blue-500 text-blue-700': $route.path === '/products' }"
        >
          Produtos
        </router-link>

        <router-link 
          to="/sales" 
          class="text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800 block pl-3 pr-4 py-2 border-l-4 text-base font-medium"
          :class="{ 'bg-blue-50 border-blue-500 text-blue-700': $route.path === '/sales' }"
        >
          Vendas
        </router-link>

        <router-link 
          to="/inventory" 
          class="text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800 block pl-3 pr-4 py-2 border-l-4 text-base font-medium"
          :class="{ 'bg-blue-50 border-blue-500 text-blue-700': $route.path === '/inventory' }"
        >
          Estoque
        </router-link>

        <button 
          @click="handleLogout" 
          class="text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800 block w-full text-left pl-3 pr-4 py-2 border-l-4 text-base font-medium"
        >
          Sair
        </button>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const authStore = useAuthStore()
const mobileMenuOpen = ref(false)

async function handleLogout() {
  await authStore.logout()
  router.push('/login')
}
</script>
