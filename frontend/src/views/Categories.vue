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
    <div v-if="error" class="bg-red-50 p-4 rounded-md mb-4">
      <p class="text-red-700">{{ error }}</p>
      <button 
        @click="fetchCategories" 
        class="mt-2 text-sm text-red-600 hover:text-red-800"
      >
        Tentar novamente
      </button>
    </div>

    <!-- Success Message -->
    <div v-if="successMessage" class="bg-green-50 p-4 rounded-md mb-4">
      <p class="text-green-700">{{ successMessage }}</p>
    </div>

    <!-- Actions Bar -->
    <div class="mb-6 flex justify-between items-center">
      <div class="relative">
        <input
          type="text"
          v-model="searchQuery"
          placeholder="Buscar categorias..."
          class="pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
        >
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
          <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
          </svg>
        </div>
      </div>
      <div class="flex space-x-3">
        <button 
          v-if="selectedCategories.length > 0"
          @click="deleteSelectedCategories"
          class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700"
        >
          <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
          </svg>
          Excluir Selecionados ({{ selectedCategories.length }})
        </button>
        <button 
          @click="openNewCategoryModal"
          class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700"
        >
          <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
          </svg>
          Nova Categoria
        </button>
      </div>
    </div>

    <!-- Empty State -->
    <div v-if="!loading && !error && categories.length === 0" class="text-center py-8 bg-white rounded-lg shadow">
      <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
      </svg>
      <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhuma categoria</h3>
      <p class="mt-1 text-sm text-gray-500">Comece criando uma nova categoria.</p>
    </div>

    <!-- Categories Table -->
    <div v-if="!loading && !error && categories.length > 0" class="bg-white shadow rounded-lg overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-8">
              <input
                type="checkbox"
                :checked="selectedCategories.length === filteredCategories.length"
                :indeterminate="selectedCategories.length > 0 && selectedCategories.length < filteredCategories.length"
                @change="toggleAllCategories"
                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
              >
            </th>
            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Imagem
            </th>
            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Nome
            </th>
            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Descrição
            </th>
            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Status
            </th>
            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Ações
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="category in filteredCategories" :key="category.id" class="hover:bg-gray-50">
            <td class="px-4 py-2 whitespace-nowrap w-8">
              <input
                type="checkbox"
                :checked="selectedCategories.includes(category.id)"
                @change="toggleCategory(category)"
                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
              >
            </td>
            <td class="px-4 py-2 whitespace-nowrap">
              <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center overflow-hidden">
                <img 
                  v-if="category.image" 
                  :src="category.image" 
                  :alt="category.name"
                  class="w-full h-full object-cover"
                />
                <div v-else class="text-2xl text-gray-400">🗂️</div>
              </div>
            </td>
            <td class="px-4 py-2 whitespace-nowrap">
              <div class="text-sm font-medium text-gray-900">{{ category.name }}</div>
              <span v-if="isNewCategory(category)" class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                Novo
              </span>
            </td>
            <td class="px-4 py-2">
              <div class="text-sm text-gray-500">{{ category.description || '-' }}</div>
            </td>
            <td class="px-4 py-2 whitespace-nowrap">
              <span 
                :class="[
                  'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                  category.active 
                    ? 'bg-green-100 text-green-800' 
                    : 'bg-red-100 text-red-800'
                ]"
              >
                {{ category.active ? 'Ativo' : 'Inativo' }}
              </span>
            </td>
            <td class="px-4 py-2 whitespace-nowrap text-sm font-medium space-x-3">
              <button 
                @click="editCategory(category)"
                class="text-indigo-600 hover:text-indigo-900"
              >
                <svg class="inline-block h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                  <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                </svg>
              </button>
              <button 
                @click="toggleCategoryStatus(category)"
                class="text-gray-600 hover:text-gray-900"
              >
                <svg class="inline-block h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                  <path d="M10 2a8 8 0 100 16 8 8 0 000-16zm0 14a6 6 0 110-12 6 6 0 010 12zm-1-5h2v2H9v-2zm0-6h2v4H9V5z" />
                </svg>
              </button>
              <button 
                @click="deleteCategory(category)"
                class="text-red-600 hover:text-red-900"
              >
                <svg class="inline-block h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center">
      <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
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

        <form @submit.prevent="submitCategory">
          <div class="space-y-4">
            <div>
              <label for="name" class="block text-sm font-medium text-gray-700">Nome</label>
              <input
                type="text"
                id="name"
                v-model="form.name"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                required
              >
            </div>

            <div>
              <label for="description" class="block text-sm font-medium text-gray-700">Descrição</label>
              <textarea
                id="description"
                v-model="form.description"
                rows="3"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              ></textarea>
            </div>

            <!-- Upload de Imagem -->
            <div class="flex items-center space-x-6">
              <div class="w-24 h-24 rounded-lg bg-gray-100 flex items-center justify-center overflow-hidden">
                <img 
                  v-if="previewImage"
                  :src="previewImage"
                  class="w-full h-full object-cover"
                />
                <div v-else class="text-4xl text-gray-400">🗂️</div>
              </div>
              <div class="flex-1">
                <label class="form-label">Imagem da Categoria</label>
                <input
                  type="file"
                  @change="handleImageUpload"
                  accept="image/*"
                  class="block w-full text-sm text-gray-500
                    file:mr-4 file:py-2 file:px-4
                    file:rounded-full file:border-0
                    file:text-sm file:font-semibold
                    file:bg-blue-50 file:text-blue-700
                    hover:file:bg-blue-100"
                />
                <p class="mt-1 text-sm text-gray-500">
                  PNG, JPG ou GIF até 2MB
                </p>
              </div>
            </div>
          </div>

          <div class="mt-6 flex justify-end space-x-3">
            <button
              type="button"
              @click="closeModal"
              class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              Cancelar
            </button>
            <button
              type="submit"
              :disabled="loading"
              class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              {{ selectedCategory ? 'Atualizar' : 'Criar' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from '../plugins/axios'

// Estado
const categories = ref([])
const loading = ref(false)
const error = ref(null)
const showModal = ref(false)
const selectedCategory = ref(null)
const successMessage = ref('')
const searchQuery = ref('')
const selectedCategories = ref([])

const form = ref({
  name: '',
  description: ''
})

const previewImage = ref('')
const imageFile = ref(null)

// Computed
const filteredCategories = computed(() => {
  if (!searchQuery.value) return categories.value
  const query = searchQuery.value.toLowerCase()
  return categories.value.filter(category => 
    category.name.toLowerCase().includes(query) ||
    (category.description && category.description.toLowerCase().includes(query))
  )
})

// Verifica se a categoria foi criada nas últimas 24 horas
function isNewCategory(category) {
  const createdAt = new Date(category.created_at)
  const now = new Date()
  const diffInHours = (now - createdAt) / (1000 * 60 * 60)
  return diffInHours < 24
}

// Buscar categorias
const fetchCategories = async () => {
  loading.value = true
  error.value = null
  try {
    const response = await axios.get('/categories')
    categories.value = response.data
  } catch (err) {
    console.error('Erro ao buscar categorias:', err)
    error.value = 'Erro ao carregar categorias. Por favor, tente novamente.'
  } finally {
    loading.value = false
  }
}

// Abrir modal para nova categoria
function openNewCategoryModal() {
  selectedCategory.value = null
  form.value = {
    name: '',
    description: ''
  }
  previewImage.value = ''
  imageFile.value = null
  showModal.value = true
}

// Editar categoria existente
function editCategory(category) {
  selectedCategory.value = category
  form.value = {
    name: category.name,
    description: category.description || ''
  }
  previewImage.value = category.image || ''
  imageFile.value = null
  showModal.value = true
}

// Excluir categoria
async function deleteCategory(category) {
  if (!confirm('Tem certeza que deseja excluir esta categoria?')) return
  
  try {
    await axios.delete(`/categories/${category.id}`)
    successMessage.value = 'Categoria excluída com sucesso'
    await fetchCategories()
    
    setTimeout(() => {
      successMessage.value = ''
    }, 3000)
  } catch (err) {
    error.value = err.response?.data?.message || 'Erro ao excluir categoria'
  }
}

// Excluir categorias selecionadas
async function deleteSelectedCategories() {
  if (!confirm(`Tem certeza que deseja excluir ${selectedCategories.value.length} categorias?`)) return

  try {
    await Promise.all(selectedCategories.value.map(id => 
      axios.delete(`/categories/${id}`)
    ))
    successMessage.value = 'Categorias excluídas com sucesso'
    selectedCategories.value = []
    await fetchCategories()
    
    setTimeout(() => {
      successMessage.value = ''
    }, 3000)
  } catch (err) {
    error.value = err.response?.data?.message || 'Erro ao excluir categorias'
  }
}

// Alternar seleção de categoria
function toggleCategory(category) {
  const index = selectedCategories.value.indexOf(category.id)
  if (index === -1) {
    selectedCategories.value.push(category.id)
  } else {
    selectedCategories.value.splice(index, 1)
  }
}

// Alternar seleção de todas as categorias
function toggleAllCategories() {
  if (selectedCategories.value.length === filteredCategories.value.length) {
    selectedCategories.value = []
  } else {
    selectedCategories.value = filteredCategories.value.map(category => category.id)
  }
}

// Alternar status da categoria
async function toggleCategoryStatus(category) {
  try {
    await axios.put(`/categories/${category.id}/toggle-status`)
    await fetchCategories()
    successMessage.value = `Categoria ${category.active ? 'desativada' : 'ativada'} com sucesso`
    setTimeout(() => {
      successMessage.value = ''
    }, 3000)
  } catch (err) {
    error.value = err.response?.data?.message || 'Erro ao alterar status da categoria'
  }
}

// Fechar modal
function closeModal() {
  showModal.value = false
  selectedCategory.value = null
  form.value = {
    name: '',
    description: ''
  }
  previewImage.value = ''
  imageFile.value = null
}

// Enviar formulário
async function submitCategory() {
  loading.value = true
  error.value = null
  
  try {
    const formData = new FormData()
    
    // Dados básicos da categoria
    const categoryData = {
      name: form.value.name,
      description: form.value.description,
    }

    // Adicionar dados ao FormData
    formData.append('data', JSON.stringify(categoryData))
    
    // Adicionar imagem se houver
    if (imageFile.value) {
      formData.append('image', imageFile.value)
    }

    const url = selectedCategory.value 
      ? `/categories/${selectedCategory.value.id}`
      : '/categories'

    const method = selectedCategory.value ? 'put' : 'post'
    
    await axios({
      method,
      url,
      data: formData,
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })

    successMessage.value = selectedCategory.value ? 'Categoria atualizada com sucesso' : 'Categoria criada com sucesso'
    await fetchCategories()
    closeModal()
    
    setTimeout(() => {
      successMessage.value = ''
    }, 3000)
  } catch (err) {
    error.value = err.response?.data?.message || 'Erro ao salvar categoria'
  } finally {
    loading.value = false
  }
}

// Método para manipular upload de imagem
function handleImageUpload(event) {
  const file = event.target.files[0]
  if (file) {
    if (file.size > 2 * 1024 * 1024) {
      alert('A imagem deve ter no máximo 2MB')
      event.target.value = ''
      return
    }

    imageFile.value = file
    const reader = new FileReader()
    reader.onload = (e) => {
      previewImage.value = e.target.result
    }
    reader.readAsDataURL(file)
  }
}

// Carregar categorias quando o componente é montado
onMounted(() => {
  fetchCategories()
})
</script>