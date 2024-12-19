<template>
  <div class="container mx-auto px-4 py-8">
    <!-- Cabeçalho -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Produtos</h1>
      <button
        @click="openAddModal"
        class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
      >
        Adicionar Produto
      </button>
    </div>

    <!-- Filtros -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
      <!-- Busca -->
      <div class="form-group">
        <label class="form-label">Buscar</label>
        <input
          v-model="filters.search"
          type="text"
          placeholder="Nome do produto"
          class="form-input"
        />
      </div>

      <!-- Categoria -->
      <div class="form-group">
        <label class="form-label">Categoria</label>
        <select v-model="filters.category_id" class="form-input">
          <option value="">Todas</option>
          <option v-for="category in categories" :key="category.id" :value="category.id">
            {{ category.name }}
          </option>
        </select>
      </div>

      <!-- Status -->
      <div class="form-group">
        <label class="form-label">Status</label>
        <select v-model="filters.status" class="form-input">
          <option value="">Todos</option>
          <option value="active">Ativo</option>
          <option value="inactive">Inativo</option>
        </select>
      </div>

      <!-- Estoque -->
      <div class="form-group">
        <label class="form-label">Estoque</label>
        <select v-model="filters.stock" class="form-input">
          <option value="">Todos</option>
          <option value="in">Em Estoque</option>
          <option value="low">Estoque Baixo</option>
          <option value="out">Sem Estoque</option>
        </select>
      </div>
    </div>

    <!-- Botão Resetar Filtros -->
    <div class="flex justify-end mb-4">
      <button
        @click="resetFilters"
        class="text-gray-600 hover:text-gray-800 text-sm"
      >
        Limpar Filtros
      </button>
    </div>

    <!-- Tabela de Produtos -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Imagem</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Categoria</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Preço</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Estoque</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Ações</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="product in products" :key="product.id">
            <td class="px-6 py-4">
              <div class="w-16 h-16 rounded-lg bg-gray-100 flex items-center justify-center overflow-hidden">
                <img 
                  v-if="product.image"
                  :src="product.image"
                  :alt="product.name"
                  class="w-full h-full object-cover"
                  @error="handleImageError"
                />
                <div v-else class="text-2xl text-gray-400">📦</div>
              </div>
            </td>
            <td class="px-6 py-4">{{ product.name }}</td>
            <td class="px-6 py-4">{{ product.sku }}</td>
            <td class="px-6 py-4">{{ getCategoryName(product.category_id) }}</td>
            <td class="px-6 py-4 text-right">R$ {{ product.price.toFixed(2) }}</td>
            <td class="px-6 py-4 text-right">{{ product.stock }}</td>
            <td class="px-6 py-4 text-center">
              <span :class="getStatusClass(product.status)">
                {{ product.status === 'active' ? 'Ativo' : 'Inativo' }}
              </span>
            </td>
            <td class="px-6 py-4 text-right space-x-2">
              <button 
                @click="editProduct(product)"
                class="text-blue-600 hover:text-blue-900"
              >
                Editar
              </button>
              <button 
                @click="deleteProduct(product)"
                class="text-red-600 hover:text-red-900"
              >
                Excluir
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal Adicionar/Editar -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <h2 class="text-2xl font-bold mb-4">
          {{ editingProduct ? 'Editar Produto' : 'Adicionar Produto' }}
        </h2>

        <form @submit.prevent="saveProduct" class="space-y-4">
          <!-- Nome -->
          <div class="form-group">
            <label class="form-label">Nome *</label>
            <input
              v-model="productForm.name"
              type="text"
              class="form-input"
              :class="{ 'border-red-500': errors.name }"
            />
            <span v-if="errors.name" class="text-red-500 text-sm">{{ errors.name }}</span>
          </div>

          <!-- SKU -->
          <div class="form-group">
            <label class="form-label">SKU *</label>
            <input
              v-model="productForm.sku"
              type="text"
              class="form-input"
              :class="{ 'border-red-500': errors.sku }"
            />
            <span class="text-sm text-gray-500">Apenas letras, números e hífen</span>
            <span v-if="errors.sku" class="text-red-500 text-sm">{{ errors.sku }}</span>
          </div>

          <!-- Descrição -->
          <div class="form-group">
            <label class="form-label">Descrição</label>
            <textarea
              v-model="productForm.description"
              class="form-input"
              rows="3"
            ></textarea>
          </div>

          <!-- Preços -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-group">
              <label class="form-label">Preço de Venda *</label>
              <input
                v-model="productForm.price"
                type="number"
                step="0.01"
                class="form-input"
                :class="{ 'border-red-500': errors.price }"
              />
              <span v-if="errors.price" class="text-red-500 text-sm">{{ errors.price }}</span>
            </div>

            <div class="form-group">
              <label class="form-label">Preço de Custo *</label>
              <input
                v-model="productForm.cost_price"
                type="number"
                step="0.01"
                class="form-input"
                :class="{ 'border-red-500': errors.cost_price }"
              />
              <span v-if="errors.cost_price" class="text-red-500 text-sm">{{ errors.cost_price }}</span>
            </div>
          </div>

          <!-- Estoque -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="form-group">
              <label class="form-label">Estoque *</label>
              <input
                v-model="productForm.stock"
                type="number"
                class="form-input"
                :class="{ 'border-red-500': errors.stock }"
              />
              <span v-if="errors.stock" class="text-red-500 text-sm">{{ errors.stock }}</span>
            </div>

            <div class="form-group">
              <label class="form-label">Estoque Mínimo *</label>
              <input
                v-model="productForm.min_stock"
                type="number"
                class="form-input"
                :class="{ 'border-red-500': errors.min_stock }"
              />
              <span v-if="errors.min_stock" class="text-red-500 text-sm">{{ errors.min_stock }}</span>
            </div>

            <div class="form-group">
              <label class="form-label">Unidade *</label>
              <select
                v-model="productForm.unit"
                class="form-input"
                :class="{ 'border-red-500': errors.unit }"
              >
                <option value="un">Unidade (un)</option>
                <option value="kg">Quilograma (kg)</option>
                <option value="g">Grama (g)</option>
                <option value="l">Litro (l)</option>
                <option value="ml">Mililitro (ml)</option>
              </select>
              <span v-if="errors.unit" class="text-red-500 text-sm">{{ errors.unit }}</span>
            </div>
          </div>

          <!-- Categoria -->
          <div class="form-group">
            <label class="form-label">Categoria *</label>
            <select
              v-model="productForm.category_id"
              class="form-input"
              :class="{ 'border-red-500': errors.category_id }"
            >
              <option value="">Selecione uma categoria</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }}
              </option>
            </select>
            <span v-if="errors.category_id" class="text-red-500 text-sm">{{ errors.category_id }}</span>
          </div>

          <!-- Status -->
          <div class="form-group">
            <label class="form-label">Status *</label>
            <select
              v-model="productForm.status"
              class="form-input"
              :class="{ 'border-red-500': errors.status }"
            >
              <option value="active">Ativo</option>
              <option value="inactive">Inativo</option>
            </select>
            <span v-if="errors.status" class="text-red-500 text-sm">{{ errors.status }}</span>
          </div>

          <!-- Imagem -->
          <div class="form-group">
            <label class="form-label">Imagem</label>
            <div class="mt-2">
              <div class="flex items-start space-x-4">
                <div class="w-32 h-32 border-2 border-gray-200 rounded-lg overflow-hidden bg-gray-50">
                  <img 
                    v-if="previewImage || productForm.image"
                    :src="previewImage || productForm.image"
                    class="w-full h-full object-cover"
                    alt="Preview do produto"
                    @error="handleImageError"
                  />
                  <div v-else class="w-full h-full flex items-center justify-center text-4xl text-gray-400">
                    📦
                  </div>
                </div>
                <div class="flex flex-col space-y-2">
                  <label class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 cursor-pointer inline-block">
                    <span>Selecionar Imagem</span>
                    <input 
                      type="file" 
                      @change="handleImageUpload" 
                      accept="image/*"
                      class="hidden" 
                    />
                  </label>
                  <button 
                    v-if="previewImage || productForm.image"
                    type="button" 
                    @click="removeImage" 
                    class="text-red-600 hover:text-red-800 text-sm"
                  >
                    Remover
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Botões -->
          <div class="flex justify-end space-x-2 mt-6">
            <button
              type="button"
              @click="closeModal"
              class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600"
              :disabled="isLoading"
            >
              Cancelar
            </button>
            <button
              type="submit"
              class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
              :disabled="isLoading"
            >
              {{ isLoading ? 'Salvando...' : (editingProduct ? 'Atualizar' : 'Salvar') }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal de Confirmação de Exclusão -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <div class="text-center">
          <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
          </div>
          <h3 class="text-lg font-medium text-gray-900 mb-2">Confirmar Exclusão</h3>
          <p class="text-sm text-gray-500 mb-6">
            Tem certeza que deseja excluir este produto? Esta ação não pode ser desfeita.
          </p>
          <div class="flex justify-center space-x-3">
            <button
              @click="confirmDelete"
              class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-red-500"
            >
              Sim, excluir
            </button>
            <button
              @click="cancelDelete"
              class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-gray-500"
            >
              Cancelar
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import axios from '../plugins/axios'
import { useToast } from '../composables/useToast'

const { showSuccessToast, showErrorToast, showToast, toastMessage, toastType } = useToast()

// Estados
const products = ref([])
const categories = ref([])
const showModal = ref(false)
const editingProduct = ref(null)
const isLoading = ref(false)
const previewImage = ref(null)
const imageFile = ref(null)
const errors = ref({})

// Estados dos filtros
const filters = ref({
  search: '',
  category_id: '',
  status: '',
  stock: ''
})

// Estados para o modal de exclusão
const showDeleteModal = ref(false)
const productToDelete = ref(null)

// Formulário
const productForm = ref({
  name: '',
  description: '',
  sku: '',
  price: '',
  cost_price: '',
  stock: '',
  min_stock: '',
  unit: 'un',
  category_id: '',
  status: 'active',
  image: null
})

// Carregar dados iniciais
onMounted(async () => {
  try {
    await Promise.all([
      loadProducts(),
      loadCategories()
    ])
  } catch (error) {
    console.error('Erro ao carregar dados iniciais:', error)
    showErrorToast('Erro ao carregar dados iniciais')
  }
})

// Funções CRUD
async function loadProducts() {
  try {
    isLoading.value = true
    
    // Construir parâmetros de consulta
    const params = {}
    if (filters.value.search) params.search = filters.value.search
    if (filters.value.category_id) params.category_id = filters.value.category_id
    if (filters.value.status) params.status = filters.value.status
    if (filters.value.stock) params.stock = filters.value.stock

    console.log('Filtros aplicados:', params)
    
    const response = await axios.get('/products', { params })
    console.log('Resposta da API:', response.data)
    
    products.value = response.data.data || []
  } catch (error) {
    console.error('Erro ao carregar produtos:', error)
    showErrorToast('Erro ao carregar produtos')
  } finally {
    isLoading.value = false
  }
}

async function loadCategories() {
  try {
    const response = await axios.get('/categories')
    if (response.data && Array.isArray(response.data.data)) {
      categories.value = response.data.data
    } else if (Array.isArray(response.data)) {
      categories.value = response.data
    } else {
      console.error('Formato de resposta inesperado:', response.data)
      categories.value = []
    }
  } catch (error) {
    console.error('Erro ao carregar categorias:', error)
    showErrorToast('Erro ao carregar categorias')
    categories.value = []
  }
}

function openAddModal() {
  editingProduct.value = null
  resetForm()
  showModal.value = true
}

async function saveProduct() {
  if (!validateForm()) return

  try {
    isLoading.value = true
    const formData = new FormData()

    // Adicionar campos do formulário
    Object.keys(productForm.value).forEach(key => {
      if (key !== 'image') {
        formData.append(key, productForm.value[key])
      }
    })

    // Adicionar imagem se houver uma nova
    if (imageFile.value) {
      formData.append('image', imageFile.value)
    }

    const config = {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    }

    let response
    if (editingProduct.value) {
      // Para edição, usamos o método PUT
      response = await axios.post(`/products/${editingProduct.value.id}`, formData, {
        ...config,
        params: { _method: 'PUT' }
      })
      showSuccessToast('Produto atualizado com sucesso!')
    } else {
      // Para criação, usamos POST normal
      response = await axios.post('/products', formData, config)
      showSuccessToast('Produto cadastrado com sucesso!')
    }

    console.log('Resposta da API:', response)
    await loadProducts() // Recarrega a lista de produtos
    closeModal()
  } catch (error) {
    console.error('Erro ao salvar produto:', error.response || error)
    showErrorToast(error.response?.data?.message || 'Erro ao salvar produto. Tente novamente.')
  } finally {
    isLoading.value = false
  }
}

function validateForm() {
  errors.value = {}
  
  if (!productForm.value.name?.trim()) errors.value.name = 'Nome é obrigatório'
  if (!productForm.value.sku?.trim()) errors.value.sku = 'SKU é obrigatório'
  if (!productForm.value.price) errors.value.price = 'Preço de venda é obrigatório'
  if (!productForm.value.cost_price) errors.value.cost_price = 'Preço de custo é obrigatório'
  if (productForm.value.stock === undefined || productForm.value.stock === '') errors.value.stock = 'Estoque é obrigatório'
  if (productForm.value.min_stock === undefined || productForm.value.min_stock === '') errors.value.min_stock = 'Estoque mínimo é obrigatório'
  if (!productForm.value.unit) errors.value.unit = 'Unidade é obrigatória'
  if (!productForm.value.category_id) errors.value.category_id = 'Categoria é obrigatória'
  if (!productForm.value.status) errors.value.status = 'Status é obrigatório'

  return Object.keys(errors.value).length === 0
}

function editProduct(product) {
  console.log('Editando produto:', product)
  editingProduct.value = product
  productForm.value = {
    name: product.name,
    description: product.description || '',
    sku: product.sku,
    price: product.price?.toString(),
    cost_price: product.cost_price?.toString(),
    stock: product.stock?.toString(),
    min_stock: product.min_stock?.toString() || '0',
    unit: product.unit,
    category_id: product.category_id,
    status: product.status || 'active',
    image: product.image
  }
  previewImage.value = product.image
  imageFile.value = null
  showModal.value = true
}

function deleteProduct(product) {
  productToDelete.value = product
  showDeleteModal.value = true
}

async function confirmDelete() {
  if (!productToDelete.value) return

  try {
    isLoading.value = true
    await axios.delete(`/products/${productToDelete.value.id}`)
    await loadProducts()
    showSuccessToast('Produto excluído com sucesso!')
  } catch (error) {
    console.error('Erro ao excluir produto:', error)
    showErrorToast('Erro ao excluir produto')
  } finally {
    isLoading.value = false
    cancelDelete()
  }
}

function cancelDelete() {
  showDeleteModal.value = false
  productToDelete.value = null
}

// Funções auxiliares
function handleImageUpload(event) {
  const file = event.target.files[0]
  if (!file) return

  if (!file.type.startsWith('image/')) {
    showErrorToast('Por favor, selecione uma imagem válida')
    return
  }

  if (file.size > 2 * 1024 * 1024) {
    showErrorToast('A imagem deve ter no máximo 2MB')
    return
  }

  imageFile.value = file
  previewImage.value = URL.createObjectURL(file)
  productForm.value.image = null
}

function handleImageError() {
  previewImage.value = null
  productForm.value.image = null
}

function removeImage() {
  imageFile.value = null
  previewImage.value = null
  productForm.value.image = null
}

function closeModal() {
  showModal.value = false
  editingProduct.value = null
  resetForm()
}

function resetForm() {
  productForm.value = {
    name: '',
    description: '',
    sku: '',
    price: '',
    cost_price: '',
    stock: '',
    min_stock: '',
    unit: 'un',
    category_id: '',
    status: 'active',
    image: null
  }
  previewImage.value = null
  imageFile.value = null
  errors.value = {}
}

function resetFilters() {
  filters.value = {
    search: '',
    category_id: '',
    status: '',
    stock: ''
  }
  loadProducts()
}

watch(filters, () => {
  loadProducts()
}, { deep: true })

function getCategoryName(categoryId) {
  const category = categories.value.find(c => c.id === categoryId)
  return category ? category.name : ''
}

function getStatusClass(status) {
  return status === 'active'
    ? 'px-2 py-1 text-xs rounded-full bg-green-100 text-green-800'
    : 'px-2 py-1 text-xs rounded-full bg-red-100 text-red-800'
}
</script>

<style scoped>
.form-group {
  @apply mb-4;
}

.form-label {
  @apply block text-sm font-medium text-gray-700 mb-1;
}

.form-input {
  @apply mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm;
}
</style>
