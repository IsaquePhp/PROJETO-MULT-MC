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
            <span class="ml-auto text-xs text-gray-400">⌘2</span>
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
            <span class="ml-auto text-xs text-gray-400">⌘3</span>
          </router-link>

          <router-link
            to="/sales"
            class="flex items-center px-3 py-2 text-sm font-medium rounded-lg"
            :class="[$route.path === '/sales' ? 'text-indigo-600 bg-indigo-50' : 'text-gray-700 hover:bg-gray-100']"
          >
            <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            Vendas
            <span class="ml-auto text-xs text-gray-400">⌘4</span>
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
        </div>
      </div>
    </nav>

    <!-- Perfil do Usuário -->
    <div class="relative mt-auto bg-white">
      <div class="h-px bg-gray-100"></div>
      <button 
        @click="toggleProfileMenu"
        class="profile-button w-full p-4 flex items-center bg-white"
      >
        <img
          :src="'https://ui-avatars.com/api/?name=' + userName"
          alt="User avatar"
          class="w-8 h-8 rounded-full"
        >
        <div class="ml-3 flex-1 text-left">
          <p class="text-sm font-medium text-gray-700">{{ userName }}</p>
          <p class="text-xs text-gray-500">{{ userEmail }}</p>
        </div>
        <svg 
          class="w-5 h-5 text-gray-400 transition-transform duration-200"
          :class="{ 'transform rotate-180': isProfileOpen }"
          fill="none" 
          stroke="currentColor" 
          viewBox="0 0 24 24"
        >
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
      </button>

      <!-- Menu Flutuante -->
      <div 
        v-if="isProfileOpen"
        class="profile-menu absolute bottom-full left-0 right-0 mx-4 mb-2 bg-white rounded-lg shadow-xl border border-gray-50 p-2"
      >
        <a href="#" class="flex items-center px-4 py-2.5 text-sm text-gray-700 rounded-md hover:bg-gray-100">
          <svg class="mr-3 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
          Perfil
        </a>
        <a href="#" class="flex items-center px-4 py-2.5 text-sm text-gray-700 rounded-md hover:bg-gray-100">
          <svg class="mr-3 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
          Configurações
        </a>
        <div class="h-px bg-gray-100 my-2"></div>
        <button 
          @click="handleLogout"
          class="w-full flex items-center px-4 py-2.5 text-sm text-red-600 rounded-md hover:bg-gray-100"
        >
          <svg class="mr-3 h-5 w-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
          </svg>
          Logout
        </button>
      </div>
    </div>
  </aside>
</template>

<script setup>
import { computed, ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import StoreSelector from './StoreSelector.vue'

const router = useRouter()
const auth = useAuthStore()

const userName = computed(() => auth.user?.name || 'Usuário')
const userEmail = computed(() => auth.user?.email || 'usuario@exemplo.com')

const isProfileOpen = ref(false)

const handleLogout = async () => {
  await auth.logout()
  router.push('/login')
}

const toggleProfileMenu = () => {
  isProfileOpen.value = !isProfileOpen.value
}

const handleClickOutside = (event) => {
  const profileButton = document.querySelector('.profile-button')
  const profileMenu = document.querySelector('.profile-menu')
  
  if (!profileButton?.contains(event.target) && !profileMenu?.contains(event.target)) {
    isProfileOpen.value = false
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
