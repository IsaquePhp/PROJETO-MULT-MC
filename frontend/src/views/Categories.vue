<template>
  <div class="p-6">
    <header class="mb-8">
      <h1 class="text-2xl font-bold text-gray-900">CATEGORIAS</h1>
      <p class="mt-2 text-sm text-gray-700">Gerencie as categorias de produtos</p>
    </header>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-4">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600 mx-auto"></div>
      <p class="mt-2 text-gray-600">Carregando categorias...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-red-50 p-4 rounded-md">
      <p class="text-red-700">{{ error }}</p>
    </div>

    <!-- Empty State -->
    <div v-else-if="categories.length === 0" class="text-center py-4">
      <p class="text-gray-600">Nenhuma categoria encontrada</p>
    </div>

    <!-- Tabela de Categorias -->
    <div v-else class="bg-white shadow rounded-lg overflow-hidden">
      <!-- Cabeçalho da Tabela -->
      <div class="p-6 border-b border-gray-200">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
          <div class="flex-1 min-w-0">
            <h2 class="text-lg font-medium text-gray-900">Lista de Categorias</h2>
          </div>
          <div class="mt-4 md:mt-0">
            <button 
              @click="openNewCategoryModal"
              class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              Nova Categoria
            </button>
          </div>
        </div>
      </div>

      <!-- Tabela -->
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descrição</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="category in categories" :key="category.id">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">#{{ category.id }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ category.name }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ category.description }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span 
                  class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                  :class="{
                    'bg-green-100 text-green-800': category.status === 'active',
                    'bg-red-100 text-red-800': category.status === 'inactive'
                  }"
                >
                  {{ category.status === 'active' ? 'Ativo' : 'Inativo' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <button 
                  @click="editCategory(category)"
                  class="text-indigo-600 hover:text-indigo-900 mr-2"
                >
                  Editar
                </button>
                <button 
                  @click="toggleCategoryStatus(category)"
                  :class="{
                    'text-red-600 hover:text-red-900': category.status === 'active',
                    'text-green-600 hover:text-green-900': category.status === 'inactive'
                  }"
                >
                  {{ category.status === 'active' ? 'Desativar' : 'Ativar' }}
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center">
      <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-medium text-gray-900">
            {{ selectedCategory ? 'Editar Categoria' : 'Nova Categoria' }}
          </h3>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-500">
            <span class="sr-only">Fechar</span>
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <form @submit.prevent="submitCategory" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">Nome</label>
            <input 
              v-model="categoryForm.name" 
              type="text" 
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              required
            >
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Descrição</label>
            <textarea 
              v-model="categoryForm.description" 
              rows="3" 
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            ></textarea>
          </div>
          <div class="flex justify-end space-x-3">
            <button 
              type="button"
              @click="closeModal"
              class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              Cancelar
            </button>
            <button 
              type="submit"
              class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              {{ selectedCategory ? 'Salvar' : 'Criar' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from '../plugins/axios'

// State for modal and form
const showModal = ref(false)
const selectedCategory = ref(null)
const categories = ref([])
const loading = ref(false)
const error = ref(null)

const categoryForm = ref({
  name: '',
  description: ''
})

// Fetch categories from API
const fetchCategories = async () => {
  loading.value = true
  error.value = null
  try {
    const response = await axios.get('/categories')
    categories.value = response.data.map(category => ({
      ...category,
      status: category.active ? 'active' : 'inactive'
    }))
  } catch (err) {
    error.value = 'Erro ao carregar categorias'
    console.error('Error fetching categories:', err)
  } finally {
    loading.value = false
  }
}

// Open modal for new category
const openNewCategoryModal = () => {
  selectedCategory.value = null
  categoryForm.value = {
    name: '',
    description: ''
  }
  showModal.value = true
}

// Edit existing category
const editCategory = (category) => {
  selectedCategory.value = category
  categoryForm.value = {
    name: category.name,
    description: category.description
  }
  showModal.value = true
}

// Toggle category status
const toggleCategoryStatus = async (category) => {
  try {
    await axios.put(`/categories/${category.id}/toggle-status`)
    await fetchCategories() // Refresh the list
  } catch (error) {
    console.error('Error toggling category status:', error)
  }
}

// Close modal
const closeModal = () => {
  showModal.value = false
  selectedCategory.value = null
  categoryForm.value = {
    name: '',
    description: ''
  }
}

// Submit category form
const submitCategory = async () => {
  try {
    if (selectedCategory.value) {
      // Update existing category
      await axios.put(`/categories/${selectedCategory.value.id}`, categoryForm.value)
    } else {
      // Create new category
      await axios.post('/categories', categoryForm.value)
    }
    
    await fetchCategories() // Refresh the list
    closeModal()
  } catch (error) {
    console.error('Error submitting category:', error)
  }
}

// Load categories when component mounts
onMounted(() => {
  fetchCategories()
})
</script>