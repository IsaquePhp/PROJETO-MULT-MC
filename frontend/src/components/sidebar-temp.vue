<template>
  <aside class="h-screen w-64 flex-shrink-0 border-r border-gray-200 bg-white flex flex-col">
    <!-- Seção do Cabeçalho -->
    <div class="border-b border-gray-200">
      <!-- Logo -->
      <div class="p-4">
        <div class="flex items-center">
          <h1 class="text-xl font-bold text-indigo-600">MC</h1>
          <span class="ml-2 text-sm text-gray-600">Team Plan</span>
        </div>
      </div>

      <!-- Seletor de Lojas -->
      <div class="store-selector relative mx-3.5 mb-3.5">
        <StoreSelector />
      </div>
    </div>

    <!-- Menu Principal -->
    <nav class="flex-1 overflow-y-auto">
      <div class="px-3 py-2">
        <div class="space-y-1">
          <!-- Menu Items -->
          <router-link
            to="/dashboard"
            class="flex items-center px-3 py-2 text-sm font-medium rounded-lg"
            :class="[$route.path === '/dashboard' ? 'text-indigo-600 bg-indigo-50' : 'text-gray-700 hover:bg-gray-100']"
          >
            <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            Dashboard
          </router-link>

          <router-link
            to="/customers"
            class="flex items-center px-3 py-2 text-sm font-medium rounded-lg"
            :class="[$route.path === '/customers' ? 'text-indigo-600 bg-indigo-50' : 'text-gray-700 hover:bg-gray-100']"
          >
            <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            Clientes
          </router-link>

          <router-link
            to="/categories"
            class="flex items-center px-3 py-2 text-sm font-medium rounded-lg"
            :class="[$route.path === '/categories' ? 'text-indigo-600 bg-indigo-50' : 'text-gray-700 hover:bg-gray-100']"
          >
            <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
            </svg>
            Categorias
          </router-link>

          <router-link
            to="/products"
            class="flex items-center px-3 py-2 text-sm font-medium rounded-lg"
            :class="[$route.path === '/products' ? 'text-indigo-600 bg-indigo-50' : 'text-gray-700 hover:bg-gray-100']"
          >
            <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
            Produtos
          </router-link>

          <router-link
            to="/orders"
            class="flex items-center px-3 py-2 text-sm font-medium rounded-lg"
            :class="[$route.path === '/orders' ? 'text-indigo-600 bg-indigo-50' : 'text-gray-700 hover:bg-gray-100']"
          >
            <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            Pedidos
          </router-link>

          <router-link
            to="/sales"
            class="flex items-center px-3 py-2 text-sm font-medium rounded-lg"
            :class="[$route.path === '/sales' ? 'text-indigo-600 bg-indigo-50' : 'text-gray-700 hover:bg-gray-100']"
          >
            <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h14a2 2 0 002-2V5a2 2 0 00-2-2H9a2 2 0 00-2 2v14a2 2 0 01-2 2z" />
            </svg>
            Vendas
          </router-link>

          <router-link
            to="/inventory"
            class="flex items-center px-3 py-2 text-sm font-medium rounded-lg"
            :class="[$route.path === '/inventory' ? 'text-indigo-600 bg-indigo-50' : 'text-gray-700 hover:bg-gray-100']"
          >
            <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
            </svg>
            Estoque
          </router-link>

          <!-- Menu Logística -->
          <div class="relative">
            <div class="flex items-center px-3 py-2 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-100 cursor-pointer"
                 @click="toggleLogisticsMenu">
              <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3V4" />
              </svg>
              <span class="flex-1">Logística</span>
              <svg 
                class="ml-auto h-5 w-5 transform transition-transform duration-200"
                :class="{ 'rotate-180': isLogisticsOpen }"
                fill="none" 
                stroke="currentColor" 
                viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </div>
            
            <!-- Submenu -->
            <div 
              v-show="isLogisticsOpen"
              class="mt-1"
            >
              <router-link
                to="/logistics/deliveries"
                class="flex items-center pl-10 pr-3 py-2 text-sm font-medium text-gray-700 hover:bg-indigo-50 hover:text-indigo-600"
                :class="{ 'bg-indigo-50 text-indigo-600': $route.path === '/logistics/deliveries' }"
              >
                <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Entregas
              </router-link>
              
              <router-link
                to="/logistics/returns"
                class="flex items-center pl-10 pr-3 py-2 text-sm font-medium text-gray-700 hover:bg-indigo-50 hover:text-indigo-600"
                :class="{ 'bg-indigo-50 text-indigo-600': $route.path === '/logistics/returns' }"
              >
                <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l3-3m-3 3L9 7m0 0l-3 3 3 3" />
                </svg>
                Devoluções
              </router-link>
            </div>
          </div>

          <!-- Menu Financeiro -->
          <div class="relative">
            <div class="flex items-center px-3 py-2 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-100 cursor-pointer"
                 @click="toggleFinancialMenu">
              <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span class="flex-1">Financeiro</span>
              <svg 
                class="ml-auto h-5 w-5 transform transition-transform duration-200"
                :class="{ 'rotate-180': isFinancialOpen }"
                fill="none" 
                stroke="currentColor" 
                viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </div>
            
            <!-- Submenu Financeiro -->
            <div 
              v-show="isFinancialOpen"
              class="mt-1"
            >
              <router-link
                to="/financial/pagamentos"
                class="flex items-center pl-10 pr-3 py-2 text-sm font-medium text-gray-700 hover:bg-indigo-50 hover:text-indigo-600"
                :class="{ 'bg-indigo-50 text-indigo-600': $route.path === '/financial/pagamentos' }"
              >
                <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Pagamentos
              </router-link>

              <router-link
                to="/financial/fluxo-caixa"
                class="flex items-center pl-10 pr-3 py-2 text-sm font-medium text-gray-700 hover:bg-indigo-50 hover:text-indigo-600"
                :class="{ 'bg-indigo-50 text-indigo-600': $route.path === '/financial/fluxo-caixa' }"
              >
                <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h14a2 2 0 002-2V5a2 2 0 00-2-2H9a2 2 0 00-2 2v14a2 2 0 01-2 2z" />
                </svg>
                Fluxo de Caixa
              </router-link>

              <router-link
                to="/financial/centro-custos"
                class="flex items-center pl-10 pr-3 py-2 text-sm font-medium text-gray-700 hover:bg-indigo-50 hover:text-indigo-600"
                :class="{ 'bg-indigo-50 text-indigo-600': $route.path === '/financial/centro-custos' }"
              >
                <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Centro de Custos
              </router-link>
            </div>
          </div>

          <router-link
            to="/budgets"
            class="flex items-center px-3 py-2 text-sm font-medium rounded-lg"
            :class="[$route.path === '/budgets' ? 'text-indigo-600 bg-indigo-50' : 'text-gray-700 hover:bg-gray-100']"
          >
            <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
            </svg>
            Orçamentos
          </router-link>

          <router-link
            to="/reports"
            class="flex items-center px-3 py-2 text-sm font-medium rounded-lg"
            :class="[$route.path === '/reports' ? 'text-indigo-600 bg-indigo-50' : 'text-gray-700 hover:bg-gray-100']"
          >
            <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Relatórios
          </router-link>
        </div>
      </div>

      <!-- Rodapé com Admin -->
      <div class="border-t border-gray-200 p-4">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center">
              <span class="text-sm font-medium text-gray-500">AD</span>
            </div>
          </div>
          <div class="ml-3">
            <p class="text-sm font-medium text-gray-700">Admin</p>
            <p class="text-xs text-gray-500">admin@admin.com</p>
          </div>
        </div>
      </div>
    </nav>
  </aside>
</template>

<script setup>
import { ref } from 'vue'
import StoreSelector from './StoreSelector.vue'

const isLogisticsOpen = ref(false)
const isFinancialOpen = ref(false)

const toggleLogisticsMenu = () => {
  isLogisticsOpen.value = !isLogisticsOpen.value
}

const toggleFinancialMenu = () => {
  isFinancialOpen.value = !isFinancialOpen.value
}
</script>
