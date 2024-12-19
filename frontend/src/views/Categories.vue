<template>
  <div class="p-6">
    <!-- Título e Subtítulo -->
    <div class="mb-6">
      <h2 class="text-2xl font-bold text-gray-900">Categorias</h2>
      <p class="text-sm text-gray-600">Gerencie as categorias de produtos</p>
    </div>

    <!-- Busca e Botão Novo -->
    <div class="mb-6">
      <div class="flex justify-between items-center">
        <div class="w-72">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Buscar categorias..."
            class="w-full h-[45px] px-4 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
          />
        </div>
        <button
          @click="openNewCategoryModal"
          class="h-[45px] bg-indigo-600 text-white px-6 rounded-lg hover:bg-indigo-700 flex items-center gap-2 transition-colors"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
          </svg>
          Nova Categoria
        </button>
      </div>
    </div>

    <!-- Tabela -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full">
        <thead>
          <tr class="border-b border-gray-200">
            <th class="w-10 px-6 py-3">
              <input type="checkbox" class="rounded border-gray-300" />
            </th>
            <th class="w-16 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
              Imagem
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
              Nome
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
              Descrição
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
              Status
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">
              Ações
            </th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="category in filteredCategories" :key="category.id" class="hover:bg-gray-50">
            <td class="px-6 py-4">
              <input type="checkbox" class="rounded border-gray-300" />
            </td>
            <td class="px-6 py-4">
              <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                <span class="text-2xl">📁</span>
              </div>
            </td>
            <td class="px-6 py-4">
              <div>
                <div class="font-medium text-gray-900">{{ category.name }}</div>
                <div v-if="isNewCategory(category)" class="text-xs text-green-600 font-medium">Novo</div>
              </div>
            </td>
            <td class="px-6 py-4 text-gray-500">
              {{ category.description || '-' }}
            </td>
            <td class="px-6 py-4">
              <span
                :class="{
                  'px-2 py-1 text-xs font-medium rounded-full': true,
                  'bg-green-100 text-green-800': category.active,
                  'bg-red-100 text-red-800': !category.active
                }"
              >
                {{ category.active ? 'Ativo' : 'Inativo' }}
              </span>
            </td>
            <td class="px-6 py-4 text-right space-x-3">
              <button
                @click="editCategory(category)"
                class="text-indigo-600 hover:text-indigo-900"
                title="Editar"
              >
                ✏️
              </button>
              <button
                class="text-gray-600 hover:text-gray-900"
                title="Visualizar"
              >
                👁️
              </button>
              <button
                @click="deleteCategory(category)"
                class="text-red-600 hover:text-red-900"
                title="Excluir"
              >
                🗑️
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modals -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-8 w-full max-w-md">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">
          {{ selectedCategory ? 'Editar Categoria' : 'Nova Categoria' }}
        </h2>

        <form @submit.prevent="submitCategory" class="space-y-6">
          <!-- Nome -->
          <div class="form-group">
            <label class="block text-sm font-medium text-gray-700 mb-2">Nome *</label>
            <input
              v-model="form.name"
              type="text"
              class="w-full h-[45px] px-4 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
              required
            />
          </div>

          <!-- Descrição -->
          <div class="form-group">
            <label class="block text-sm font-medium text-gray-700 mb-2">Descrição</label>
            <textarea
              v-model="form.description"
              class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
              rows="3"
            ></textarea>
          </div>

          <!-- Upload de Imagem -->
          <div class="flex items-center space-x-6">
            <div class="w-24 h-24 rounded-lg bg-gray-50 flex items-center justify-center overflow-hidden border border-gray-200">
              <img 
                v-if="previewImage"
                :src="previewImage"
                class="w-full h-full object-cover"
              />
              <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
            </div>
            <div class="flex-1">
              <label class="block text-sm font-medium text-gray-700 mb-2">Imagem da Categoria</label>
              <input
                type="file"
                @change="handleImageUpload"
                accept="image/*"
                class="block w-full text-sm text-gray-500
                  file:mr-4 file:py-2 file:px-4
                  file:rounded-full file:border-0
                  file:text-sm file:font-semibold
                  file:bg-indigo-50 file:text-indigo-700
                  hover:file:bg-indigo-100
                  transition-colors"
              />
              <p class="mt-2 text-sm text-gray-500">
                PNG, JPG ou GIF até 2MB
              </p>
            </div>
          </div>

          <!-- Botões -->
          <div class="flex justify-end space-x-4 mt-8">
            <button
              type="button"
              @click="closeModal"
              class="h-[45px] px-6 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors"
            >
              Cancelar
            </button>
            <button
              type="submit"
              :disabled="loading"
              class="h-[45px] px-6 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors disabled:opacity-50"
            >
              {{ selectedCategory ? 'Atualizar' : 'Criar' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal de Confirmação de Exclusão -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-8 w-full max-w-md">
        <div class="text-center">
          <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
          </div>
          <h3 class="text-lg font-medium text-gray-900 mb-2">Confirmar Exclusão</h3>
          <p class="text-sm text-gray-500 mb-6">
            Tem certeza que deseja excluir esta categoria? Esta ação não pode ser desfeita.
          </p>
          <div class="flex justify-end space-x-4">
            <button
              @click="showDeleteModal = false"
              class="h-[45px] px-6 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors"
            >
              Cancelar
            </button>
            <button
              @click="confirmDelete"
              class="h-[45px] px-6 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
            >
              Excluir
            </button>
          </div>
        </div>
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
const showDeleteModal = ref(false)
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