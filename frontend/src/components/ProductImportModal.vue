<template>
  <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-4xl w-full">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold">Importar Produtos do Magalu</h2>
        <button @click="$emit('close')" class="text-gray-500 hover:text-gray-700">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Busca -->
      <div class="mb-6">
        <div class="flex gap-4">
          <input 
            type="text" 
            v-model="searchQuery" 
            class="input-field flex-1" 
            placeholder="Digite o nome do produto para buscar..."
            @keyup.enter="handleSearch"
          />
          <button @click="handleSearch" class="btn-primary" :disabled="loading">
            <span v-if="!loading">Buscar</span>
            <span v-else>Buscando...</span>
          </button>
        </div>
      </div>

      <!-- Resultados -->
      <div v-if="products.length > 0" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div v-for="product in products" :key="product.id" class="border rounded-lg p-4">
            <img 
              :src="product.image_url" 
              :alt="product.name" 
              class="w-full h-48 object-contain mb-4" 
              @error="handleImageError"
            >
            <h3 class="font-semibold text-lg mb-2">{{ product.name }}</h3>
            <p class="text-gray-600 text-sm mb-2">{{ product.description }}</p>
            <div class="flex justify-between items-center mb-2">
              <span class="text-lg font-bold">R$ {{ formatPrice(product.price) }}</span>
              <button 
                @click="handleImport(product)" 
                class="btn-secondary"
                :disabled="importing === product.id"
              >
                <span v-if="importing !== product.id">Importar</span>
                <span v-else>Importando...</span>
              </button>
            </div>
          </div>
        </div>

        <!-- Paginação -->
        <div class="flex justify-center mt-4">
          <button 
            v-if="page > 1" 
            @click="handlePreviousPage" 
            class="btn-secondary mr-2"
            :disabled="loading"
          >
            Anterior
          </button>
          <button 
            @click="handleNextPage" 
            class="btn-secondary"
            :disabled="loading"
          >
            Próxima
          </button>
        </div>
      </div>

      <!-- Mensagem de nenhum resultado -->
      <div v-else-if="searched" class="text-center py-8 text-gray-500">
        Nenhum produto encontrado com este termo.
      </div>

      <!-- Loading state -->
      <div v-if="loading" class="absolute inset-0 bg-white bg-opacity-50 flex items-center justify-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import axios from '../plugins/axios'

const emit = defineEmits(['close', 'product-imported'])

const searchQuery = ref('')
const products = ref([])
const loading = ref(false)
const importing = ref(null)
const page = ref(1)
const searched = ref(false)

// Limpar estado ao desmontar o componente
onBeforeUnmount(() => {
  searchQuery.value = ''
  products.value = []
  loading.value = false
  importing.value = null
  page.value = 1
  searched.value = false
})

const handleSearch = async () => {
  if (!searchQuery.value.trim()) return
  
  loading.value = true
  page.value = 1
  
  try {
    const response = await axios.get('/products/magalu/search', {
      params: {
        query: searchQuery.value,
        page: page.value
      }
    })
    products.value = response.data.products || []
    searched.value = true
  } catch (error) {
    console.error('Erro ao buscar produtos:', error)
    products.value = []
  } finally {
    loading.value = false
  }
}

const handleImport = async (product) => {
  if (importing.value) return // Previne múltiplos cliques
  
  importing.value = product.id
  try {
    const response = await axios.post('/products/magalu/import', {
      product_id: product.id
    })
    if (response.data) {
      emit('product-imported', response.data)
    }
  } catch (error) {
    console.error('Erro ao importar produto:', error)
  } finally {
    importing.value = null
  }
}

const handleNextPage = async () => {
  if (loading.value) return
  page.value++
  await handleSearch()
}

const handlePreviousPage = async () => {
  if (loading.value || page.value <= 1) return
  page.value--
  await handleSearch()
}

const handleImageError = (event) => {
  event.target.src = '/placeholder-image.png'
}

const formatPrice = (price) => {
  return Number(price).toLocaleString('pt-BR', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  })
}
</script>
