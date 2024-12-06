<template>
  <div class="relative store-selector">
    <!-- Botão principal -->
    <button 
      @click="isOpen = !isOpen"
      class="w-full flex items-center px-4 py-2 bg-[#F3F4F6] focus:outline-none"
    >
      <div class="flex items-center flex-1">
        <div class="w-8 h-8 bg-orange-100 rounded-md flex items-center justify-center">
          <span class="text-sm font-medium text-orange-600">{{ currentStore?.name?.charAt(0) || 'L' }}</span>
        </div>
        <div class="ml-3 text-left">
          <p class="text-sm font-medium text-gray-900">{{ currentStore?.name || 'Loja' }}</p>
          <p class="text-xs text-gray-500">{{ currentStore?.type || 'Selecione uma loja' }}</p>
        </div>
      </div>
      <svg 
        class="w-5 h-5 text-gray-400 transition-transform duration-200"
        :class="{ 'transform rotate-180': isOpen }"
        fill="none" 
        stroke="currentColor" 
        viewBox="0 0 24 24"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
      </svg>
    </button>

    <!-- Menu de Seleção de Loja -->
    <div 
      v-if="isOpen"
      class="absolute left-0 right-0 mt-2 bg-white rounded-lg shadow-xl p-2 z-50"
    >
      <div v-for="store in stores" :key="store.id">
        <button 
          @click="selectStore(store)"
          class="w-full flex items-center px-4 py-2.5 text-sm rounded-md hover:bg-[#F3F4F6]"
        >
          <div class="w-8 h-8 bg-orange-100 rounded-md flex items-center justify-center">
            <span class="text-sm font-medium text-orange-600">{{ store.name.charAt(0) }}</span>
          </div>
          <div class="ml-3 text-left">
            <p class="text-sm font-medium text-gray-900">{{ store.name }}</p>
            <p class="text-xs text-gray-500">{{ store.type }}</p>
          </div>
        </button>
      </div>
      <div class="border-t border-gray-100 mt-1">
        <button
          @click="addNewStore"
          class="w-full px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center space-x-2"
        >
          <svg
            class="w-5 h-5 text-gray-400"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
              clip-rule="evenodd"
            />
          </svg>
          <span>Adicionar nova loja</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from '../plugins/axios'

const router = useRouter()
const isOpen = ref(false)
const stores = ref([])
const currentStore = ref({})

// Carregar lojas do usuário
const loadStores = async () => {
  try {
    const response = await axios.get('/stores')
    stores.value = response.data
    
    // Se não houver loja selecionada, seleciona a primeira
    if (!currentStore.value.id && stores.value.length > 0) {
      currentStore.value = stores.value[0]
    }
  } catch (error) {
    console.error('Erro ao carregar lojas:', error)
  }
}

// Selecionar uma loja
const selectStore = (store) => {
  currentStore.value = store
  localStorage.setItem('currentStoreId', store.id)
  isOpen.value = false
  
  // Recarregar os dados da página atual com a nova loja
  router.go()
}

// Adicionar nova loja
const addNewStore = () => {
  router.push('/stores/new')
  isOpen.value = false
}

// Fecha o menu quando clicar fora
const handleClickOutside = (event) => {
  const storeSelector = event.target.closest('.store-selector')
  if (!storeSelector) {
    isOpen.value = false
  }
}

onMounted(() => {
  loadStores()
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<style>
.store-selector button {
  -webkit-tap-highlight-color: transparent;
}

.store-selector button:focus,
.store-selector button:hover,
.store-selector button:active {
  outline: none !important;
  box-shadow: none !important;
  border: none !important;
}

/* Remove a borda azul em navegadores Webkit (Chrome, Safari) */
.store-selector button:focus-visible {
  outline: none !important;
}

/* Remove a borda azul em navegadores Firefox */
.store-selector button::-moz-focus-inner {
  border: 0;
}
</style>
