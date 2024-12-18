<template>
  <aside class="h-screen w-64 flex-shrink-0 bg-[#4338CA] flex flex-col">
    <!-- Logo e Team Plan -->
    <div class="p-4 border-b border-white/10">
      <div class="flex items-center justify-center">
        <img src="../assets/Logo.png" alt="MC Logo" class="h-10" />
      </div>
    </div>

    <!-- Store Selector -->
    <div class="p-4">
      <StoreSelector />
    </div>

    <!-- Menu Principal -->
    <nav class="flex-1 overflow-y-auto px-3 py-2">
      <div class="space-y-1">
        <!-- Menu Items -->
        <router-link
          to="/dashboard"
          class="flex items-center px-3 py-2 text-sm font-medium rounded-lg"
          :class="[$route.path === '/dashboard' ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/5 hover:text-white']"
        >
          <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
          </svg>
          Dashboard
        </router-link>

        <router-link
          to="/customers"
          class="flex items-center px-3 py-2 text-sm font-medium rounded-lg"
          :class="[$route.path === '/customers' ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/5 hover:text-white']"
        >
          <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 00-3-3H4a3 3 0 013-3h10a3 3 0 013 3v10a3 3 0 01-3 3z" />
          </svg>
          Clientes
        </router-link>

        <router-link
          to="/products"
          class="flex items-center px-3 py-2 text-sm font-medium rounded-lg"
          :class="[$route.path === '/products' ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/5 hover:text-white']"
        >
          <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
          </svg>
          Produtos
        </router-link>

        <router-link
          to="/categories"
          class="flex items-center px-3 py-2 text-sm font-medium rounded-lg"
          :class="[$route.path === '/categories' ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/5 hover:text-white']"
        >
          <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
          </svg>
          Categorias
        </router-link>

        <router-link
          to="/sales"
          class="flex items-center px-3 py-2 text-sm font-medium rounded-lg"
          :class="[$route.path === '/sales' ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/5 hover:text-white']"
        >
          <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
          </svg>
          PDV
        </router-link>

        <router-link
          to="/sales-history"
          class="flex items-center px-3 py-2 text-sm font-medium rounded-lg"
          :class="[$route.path === '/sales-history' ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/5 hover:text-white']"
        >
          <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
          </svg>
          Histórico de Vendas
        </router-link>

        <router-link
          to="/inventory"
          class="flex items-center px-3 py-2 text-sm font-medium rounded-lg"
          :class="[$route.path === '/inventory' ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/5 hover:text-white']"
        >
          <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
          </svg>
          Estoque
        </router-link>

        <router-link
          to="/lancamentos"
          class="flex items-center px-3 py-2 text-sm font-medium rounded-lg"
          :class="[$route.path === '/lancamentos' ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/5 hover:text-white']"
        >
          <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
          </svg>
          Lançamentos
        </router-link>

        <!-- Menu Logística -->
        <div class="relative">
          <div class="flex items-center px-3 py-2 text-sm font-medium rounded-lg text-white/80 hover:bg-white/5 hover:text-white cursor-pointer"
               @click="toggleLogisticsMenu">
            <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
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
          
          <!-- Submenu Logística -->
          <div 
            v-show="isLogisticsOpen"
            class="mt-1"
          >
            <router-link
              to="/logistics/entregas"
              class="flex items-center pl-10 pr-3 py-2 text-sm font-medium text-white/80 hover:bg-white/5 hover:text-white"
              :class="{ 'bg-white/10 text-white': $route.path === '/logistics/entregas' }"
            >
              <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
              </svg>
              Entregas
            </router-link>

            <router-link
              to="/logistics/devolucoes"
              class="flex items-center pl-10 pr-3 py-2 text-sm font-medium text-white/80 hover:bg-white/5 hover:text-white"
              :class="{ 'bg-white/10 text-white': $route.path === '/logistics/devolucoes' }"
            >
              <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21l-7-7 7-7" />
              </svg>
              Devoluções
            </router-link>
          </div>
        </div>

        <!-- Menu Financeiro -->
        <div class="relative">
          <div class="flex items-center px-3 py-2 text-sm font-medium rounded-lg text-white/80 hover:bg-white/5 hover:text-white cursor-pointer"
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
              class="flex items-center pl-10 pr-3 py-2 text-sm font-medium text-white/80 hover:bg-white/5 hover:text-white"
              :class="{ 'bg-white/10 text-white': $route.path === '/financial/pagamentos' }"
            >
              <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
              Pagamentos
            </router-link>

            <router-link
              to="/financial/fluxo-caixa"
              class="flex items-center pl-10 pr-3 py-2 text-sm font-medium text-white/80 hover:bg-white/5 hover:text-white"
              :class="{ 'bg-white/10 text-white': $route.path === '/financial/fluxo-caixa' }"
            >
              <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h14a2 2 0 002-2V5a2 2 0 00-2-2H9a2 2 0 00-2 2v14a2 2 0 01-2 2z" />
              </svg>
              Fluxo de Caixa
            </router-link>

            <router-link
              to="/financial/centro-custos"
              class="flex items-center pl-10 pr-3 py-2 text-sm font-medium text-white/80 hover:bg-white/5 hover:text-white"
              :class="{ 'bg-white/10 text-white': $route.path === '/financial/centro-custos' }"
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
          :class="[$route.path === '/budgets' ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/5 hover:text-white']"
        >
          <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
          </svg>
          Orçamentos
        </router-link>

        <router-link
          to="/reports"
          class="flex items-center px-3 py-2 text-sm font-medium rounded-lg"
          :class="[$route.path === '/reports' ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/5 hover:text-white']"
        >
          <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4V9m-6 4h-2m-6 4h-2m6-4h12a2 2 0 012 2v3m-7 4h3m-6 4h12a2 2 0 012 2v3" />
          </svg>
          Relatórios
        </router-link>
      </div>
    </nav>

    <!-- Perfil do Usuário -->
    <div class="p-4 border-t border-white/10">
      <div class="flex items-center">
        <img
          :src="'https://ui-avatars.com/api/?name=' + userName + '&background=fff&color=4338CA'"
          alt="User avatar"
          class="h-8 w-8 rounded-full"
        />
        <span class="ml-3 text-sm font-medium text-white">{{ userName }}</span>
        <button
          @click="handleLogout"
          class="ml-auto text-sm text-white/60 hover:text-white"
        >
          Sair
        </button>
      </div>
    </div>
  </aside>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const authStore = useAuthStore()
const isProfileOpen = ref(false)
const isLogisticsOpen = ref(false)
const isFinancialOpen = ref(false)

// Computed properties for user info
const userName = computed(() => {
  const user = authStore.getUser
  return user ? user.name : 'Usuário'
})

const userEmail = computed(() => {
  const user = authStore.getUser
  return user ? user.email : 'usuario@exemplo.com'
})

const toggleProfileMenu = () => {
  isProfileOpen.value = !isProfileOpen.value
}

const toggleLogisticsMenu = () => {
  isLogisticsOpen.value = !isLogisticsOpen.value
}

const toggleFinancialMenu = () => {
  isFinancialOpen.value = !isFinancialOpen.value
}

const closeLogisticsMenu = () => {
  isLogisticsOpen.value = false
}

const handleClickOutside = (event) => {
  const profile = document.querySelector('.profile-menu')
  if (profile && !profile.contains(event.target)) {
    isProfileOpen.value = false
  }
}

const handleLogout = async () => {
  try {
    await authStore.logout()
    router.push('/login')
  } catch (error) {
    console.error('Error during logout:', error)
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<style>
.profile-button {
  -webkit-tap-highlight-color: transparent;
}

.profile-button:focus,
.profile-button:hover,
.profile-button:active {
  outline: none !important;
  box-shadow: none !important;
  border: none !important;
}

/* Remove a borda azul em navegadores Webkit (Chrome, Safari) */
.profile-button:focus-visible {
  outline: none !important;
}

/* Remove a borda azul em navegadores Firefox */
.profile-button::-moz-focus-inner {
  border: 0;
}
</style>
