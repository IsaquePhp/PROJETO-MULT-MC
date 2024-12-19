<template>
  <div>
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center mb-6">
          <h1 class="text-2xl font-semibold text-gray-900">Produtos</h1>
          <button @click="showAddModal = true" class="btn-primary">
            Adicionar Produto
          </button>
        </div>

        <!-- Filtros -->
        <div class="card mb-6 p-4">
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Campo de Busca -->
            <div class="col-span-1 sm:col-span-2 lg:col-span-1">
              <label class="form-label">Buscar</label>
              <input 
                type="text" 
                v-model="filters.search" 
                class="input-field w-full" 
                placeholder="Nome do produto"
              />
            </div>
            <!-- Filtro de Categoria -->
            <div>
              <label class="form-label">Categoria</label>
              <select v-model="filters.category_id" class="input-field w-full">
                <option value="">Todas</option>
                <option 
                  v-for="category in categories" 
                  :key="category.id" 
                  :value="category.id"
                >
                  {{ category.name }}
                </option>
              </select>
            </div>
            <!-- Filtro de Status -->
            <div>
              <label class="form-label">Status</label>
              <select v-model="filters.status" class="input-field w-full">
                <option value="">Todos</option>
                <option value="active">Ativo</option>
                <option value="inactive">Inativo</option>
              </select>
            </div>
            <!-- Filtro de Estoque -->
            <div>
              <label class="form-label">Estoque</label>
              <select v-model="filters.stock" class="input-field w-full">
                <option value="">Todos</option>
                <option value="low">Estoque Baixo</option>
                <option value="out">Sem Estoque</option>
                <option value="in">Em Estoque</option>
              </select>
            </div>
          </div>
          <!-- Botão Limpar Filtros -->
          <div v-if="hasActiveFilters" class="mt-4 flex justify-end">
            <button 
              @click="resetFilters" 
              class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
              Limpar Filtros
            </button>
          </div>
        </div>

        <!-- Lista de Produtos -->
        <div class="card overflow-x-auto">
          <div v-if="isLoading" class="flex justify-center items-center py-4">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
          </div>
          <template v-else>
            <table class="min-w-full divide-y divide-gray-200">
              <thead>
                <tr>
                  <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Imagem
                  </th>
                  <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Nome
                  </th>
                  <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">
                    SKU
                  </th>
                  <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">
                    Categoria
                  </th>
                  <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Preço
                  </th>
                  <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">
                    Estoque
                  </th>
                  <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Status
                  </th>
                  <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Ações
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="product in products" :key="product.id" class="hover:bg-gray-50">
                  <td class="px-4 py-2 whitespace-nowrap">
                    <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center overflow-hidden">
                      <img 
                        v-if="product.image" 
                        :src="product.image" 
                        :alt="product.name"
                        class="w-full h-full object-cover"
                      />
                      <div v-else class="text-2xl text-gray-400">📦</div>
                    </div>
                  </td>
                  <td class="px-4 py-2 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ product.name }}</div>
                  </td>
                  <td class="px-4 py-2 whitespace-nowrap hidden sm:table-cell">
                    <div class="text-sm text-gray-500">{{ product.sku }}</div>
                  </td>
                  <td class="px-4 py-2 whitespace-nowrap hidden md:table-cell">
                    <div class="text-sm text-gray-500">{{ getCategoryName(product.category_id) }}</div>
                  </td>
                  <td class="px-4 py-2 whitespace-nowrap text-right">
                    <div class="text-sm text-gray-900">R$ {{ product.price.toFixed(2) }}</div>
                  </td>
                  <td class="px-4 py-2 whitespace-nowrap text-right hidden sm:table-cell">
                    <div class="text-sm text-gray-500">{{ product.stock }}</div>
                  </td>
                  <td class="px-4 py-2 whitespace-nowrap text-center">
                    <span :class="getStatusClass(product.status)">
                      {{ product.status === 'active' ? 'Ativo' : 'Inativo' }}
                    </span>
                  </td>
                  <td class="px-4 py-2 whitespace-nowrap text-right text-sm font-medium space-x-2">
                    <button 
                      @click="editProduct(product)"
                      class="text-blue-600 hover:text-blue-900"
                    >
                      Editar
                    </button>
                    <button 
                      @click="deleteProduct(product.id)"
                      class="text-red-600 hover:text-red-900"
                    >
                      Excluir
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>

            <!-- Paginação -->
            <div v-if="pagination.total > 0" class="px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
              <div class="flex-1 flex justify-between sm:hidden">
                <button
                  @click="changePage(pagination.current_page - 1)"
                  :disabled="pagination.current_page === 1"
                  class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  Anterior
                </button>
                <button
                  @click="changePage(pagination.current_page + 1)"
                  :disabled="pagination.current_page === pagination.last_page"
                  class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  Próxima
                </button>
              </div>
              <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                  <p class="text-sm text-gray-700">
                    Mostrando
                    <span class="font-medium">{{ pagination.from }}</span>
                    até
                    <span class="font-medium">{{ pagination.to }}</span>
                    de
                    <span class="font-medium">{{ pagination.total }}</span>
                    resultados
                  </p>
                </div>
                <div>
                  <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                    <button
                      @click="changePage(pagination.current_page - 1)"
                      :disabled="pagination.current_page === 1"
                      class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                      <span class="sr-only">Anterior</span>
                      <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                      </svg>
                    </button>
                    
                    <template v-for="page in paginationRange" :key="page">
                      <button
                        v-if="page !== '...'"
                        @click="changePage(page)"
                        :class="[
                          page === pagination.current_page
                            ? 'z-10 bg-blue-50 border-blue-500 text-blue-600'
                            : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                          'relative inline-flex items-center px-4 py-2 border text-sm font-medium'
                        ]"
                      >
                        {{ page }}
                      </button>
                      <span
                        v-else
                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700"
                      >
                        ...
                      </span>
                    </template>

                    <button
                      @click="changePage(pagination.current_page + 1)"
                      :disabled="pagination.current_page === pagination.last_page"
                      class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                      <span class="sr-only">Próxima</span>
                      <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                      </svg>
                    </button>
                  </nav>
                </div>
              </div>
            </div>
          </template>
        </div>
      </div>
    </main>

    <!-- Modal de Adicionar/Editar Produto -->
    <TransitionRoot appear :show="showAddModal" as="template">
      <Dialog as="div" @close="closeModal" class="relative z-10">
        <TransitionChild
          as="template"
          enter="duration-300 ease-out"
          enter-from="opacity-0"
          enter-to="opacity-100"
          leave="duration-200 ease-in"
          leave-from="opacity-100"
          leave-to="opacity-0"
        >
          <div class="fixed inset-0 bg-black/25" />
        </TransitionChild>

        <div class="fixed inset-0 overflow-y-auto">
          <div class="flex min-h-full items-center justify-center p-4">
            <TransitionChild
              as="template"
              enter="duration-300 ease-out"
              enter-from="opacity-0 scale-95"
              enter-to="opacity-100 scale-100"
              leave="duration-200 ease-in"
              leave-from="opacity-100 scale-100"
              leave-to="opacity-0 scale-95"
            >
              <DialogPanel class="w-full max-w-2xl transform overflow-hidden rounded-2xl bg-white p-6 shadow-xl transition-all">
                <DialogTitle as="h3" class="text-xl font-semibold leading-6 text-gray-900 mb-4">
                  {{ editingProduct ? 'Editar Produto' : 'Novo Produto' }}
                </DialogTitle>

                <form @submit.prevent="saveProduct" class="space-y-4">
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                      <label class="form-label">Nome *</label>
                      <input 
                        type="text" 
                        v-model="productForm.name" 
                        class="input-field" 
                        required 
                        minlength="3"
                        maxlength="100"
                        :class="{'border-red-500': errors.name}"
                        @input="() => delete errors.name"
                      />
                      <span v-if="errors.name" class="text-red-500 text-sm">{{ errors.name }}</span>
                    </div>

                    <div>
                      <label class="form-label">SKU *</label>
                      <input 
                        type="text" 
                        v-model="productForm.sku" 
                        class="input-field" 
                        required
                        pattern="[A-Za-z0-9-]+"
                        :class="{'border-red-500': errors.sku}"
                        @input="validateSku"
                      />
                      <span v-if="errors.sku" class="text-red-500 text-sm">{{ errors.sku }}</span>
                      <span class="text-gray-500 text-sm">Apenas letras, números e hífen</span>
                    </div>
                  </div>

                  <div>
                    <label class="form-label">Descrição</label>
                    <textarea 
                      v-model="productForm.description" 
                      class="input-field" 
                      rows="3"
                      maxlength="500"
                    ></textarea>
                  </div>

                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                      <label class="form-label">Preço de Venda *</label>
                      <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500">R$</span>
                        <input 
                          type="text" 
                          v-model="productForm.price" 
                          class="input-field pl-8" 
                          required
                          @input="formatCurrency($event, 'price')"
                          :class="{'border-red-500': errors.price}"
                        />
                      </div>
                      <span v-if="errors.price" class="text-red-500 text-sm">{{ errors.price }}</span>
                    </div>
                    <div>
                      <label class="form-label">Preço de Custo *</label>
                      <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500">R$</span>
                        <input 
                          type="text" 
                          v-model="productForm.cost_price" 
                          class="input-field pl-8" 
                          required
                          @input="formatCurrency($event, 'cost_price')"
                          :class="{'border-red-500': errors.cost_price}"
                        />
                      </div>
                      <span v-if="errors.cost_price" class="text-red-500 text-sm">{{ errors.cost_price }}</span>
                    </div>
                  </div>

                  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                      <label class="form-label">Estoque *</label>
                      <input 
                        type="number" 
                        v-model="productForm.stock" 
                        class="input-field" 
                        required
                        min="0"
                        step="1"
                        @input="validateStock"
                        :class="{'border-red-500': errors.stock}"
                      />
                      <span v-if="errors.stock" class="text-red-500 text-sm">{{ errors.stock }}</span>
                    </div>
                    <div>
                      <label class="form-label">Estoque Mínimo *</label>
                      <input 
                        type="number" 
                        v-model="productForm.min_stock" 
                        class="input-field" 
                        required
                        min="0"
                        step="1"
                        @input="validateStock"
                        :class="{'border-red-500': errors.min_stock}"
                      />
                      <span v-if="errors.min_stock" class="text-red-500 text-sm">{{ errors.min_stock }}</span>
                    </div>
                    <div>
                      <label class="form-label">Unidade *</label>
                      <select 
                        v-model="productForm.unit" 
                        class="input-field" 
                        required
                      >
                        <option value="un">Unidade (un)</option>
                        <option value="kg">Quilograma (kg)</option>
                        <option value="g">Grama (g)</option>
                        <option value="l">Litro (l)</option>
                        <option value="ml">Mililitro (ml)</option>
                        <option value="m">Metro (m)</option>
                        <option value="cm">Centímetro (cm)</option>
                        <option value="m2">Metro Quadrado (m²)</option>
                        <option value="m3">Metro Cúbico (m³)</option>
                        <option value="cx">Caixa (cx)</option>
                        <option value="pct">Pacote (pct)</option>
                      </select>
                    </div>
                  </div>

                  <div>
                    <label class="form-label">Categoria *</label>
                    <select 
                      v-model="productForm.category_id" 
                      class="input-field" 
                      required
                      :class="{'border-red-500': errors.category_id}"
                    >
                      <option value="">Selecione uma categoria</option>
                      <option 
                        v-for="category in categories" 
                        :key="category.id" 
                        :value="category.id"
                        :disabled="!category.active"
                      >
                        {{ category.name }}
                        <span v-if="!category.active" class="text-gray-400">(Inativa)</span>
                      </option>
                    </select>
                    <span v-if="errors.category_id" class="text-red-500 text-sm">{{ errors.category_id }}</span>
                  </div>

                  <div>
                    <label class="form-label">Status *</label>
                    <select 
                      v-model="productForm.status" 
                      class="input-field" 
                      required
                    >
                      <option value="active">Ativo</option>
                      <option value="inactive">Inativo</option>
                    </select>
                  </div>

                  <div class="flex justify-end space-x-4 mt-6">
                    <button 
                      type="button" 
                      @click="closeModal" 
                      class="btn-secondary"
                      :disabled="isSaving"
                    >
                      Cancelar
                    </button>
                    <button 
                      type="submit" 
                      class="btn-primary" 
                      :disabled="isSaving || Object.keys(errors).length > 0"
                    >
                      <span v-if="isSaving" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Salvando...
                      </span>
                      <span v-else>
                        {{ editingProduct ? 'Atualizar' : 'Salvar' }}
                      </span>
                    </button>
                  </div>
                </form>
              </DialogPanel>
            </TransitionChild>
          </div>
        </div>
      </Dialog>
    </TransitionRoot>

    <!-- Toast de Sucesso -->
    <div 
      v-if="showToast" 
      class="fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded shadow-lg"
    >
      {{ toastMessage }}
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, computed } from 'vue'
import axios from '../plugins/axios'
import { useToast } from '../composables/useToast'
import {
  Dialog,
  DialogPanel,
  DialogTitle,
  TransitionChild,
  TransitionRoot,
} from '@headlessui/vue'

const products = ref([])
const categories = ref([])
const showAddModal = ref(false)
const editingProduct = ref(null)
const errors = ref({})
const isSaving = ref(false)
const isLoading = ref(false)
const { showToast, toastMessage, showSuccessToast } = useToast()

const defaultCategories = [
  'Roupas',
  'Calçados',
  'Acessórios',
  'Eletrônicos',
  'Móveis',
  'Decoração',
  'Brinquedos',
  'Livros',
  'Outros'
]

const filters = ref({
  search: '',
  category_id: '',
  status: '',
  stock: ''
})

// Observar mudanças nos filtros
watch(filters, () => {
  pagination.value.current_page = 1
  loadProducts()
}, { deep: true })

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
  status: 'active'
})

const isFormValid = computed(() => {
  return productForm.value.name &&
    productForm.value.sku &&
    productForm.value.price &&
    productForm.value.cost_price &&
    productForm.value.stock &&
    productForm.value.min_stock &&
    productForm.value.category_id &&
    productForm.value.unit &&
    Object.keys(errors.value).length === 0
})

const hasActiveFilters = computed(() => {
  return filters.value.search !== '' || 
         filters.value.category_id !== '' || 
         filters.value.status !== '' || 
         filters.value.stock !== ''
})

const pagination = ref({
  current_page: 1,
  from: 0,
  to: 0,
  total: 0,
  last_page: 1
})

const paginationRange = computed(() => {
  const range = []
  const maxVisiblePages = 7
  const currentPage = pagination.value.current_page
  const lastPage = pagination.value.last_page

  if (lastPage <= maxVisiblePages) {
    // Se tiver menos páginas que o máximo, mostra todas
    for (let i = 1; i <= lastPage; i++) {
      range.push(i)
    }
  } else {
    // Sempre mostra a primeira página
    range.push(1)

    // Calcula o início e fim do range central
    let start = Math.max(currentPage - 2, 2)
    let end = Math.min(currentPage + 2, lastPage - 1)

    // Ajusta se estiver muito próximo do início ou fim
    if (currentPage <= 3) {
      end = 5
    } else if (currentPage >= lastPage - 2) {
      start = lastPage - 4
    }

    // Adiciona ellipsis no início se necessário
    if (start > 2) {
      range.push('...')
    }

    // Adiciona as páginas do meio
    for (let i = start; i <= end; i++) {
      range.push(i)
    }

    // Adiciona ellipsis no final se necessário
    if (end < lastPage - 1) {
      range.push('...')
    }

    // Sempre mostra a última página
    range.push(lastPage)
  }

  return range
})

onMounted(() => {
  loadProducts()
  loadCategories()
})

function formatCurrency(event, field) {
  let value = event.target.value.replace(/\D/g, '')
  if (value === '') {
    productForm.value[field] = ''
    return
  }
  value = (parseFloat(value) / 100).toFixed(2)
  if (isNaN(value)) {
    productForm.value[field] = ''
    return
  }
  productForm.value[field] = value
  validatePrice(field)
}

function validatePrice(field) {
  const value = parseFloat(productForm.value[field])
  if (isNaN(value) || value < 0) {
    errors.value[field] = 'O valor deve ser maior que zero'
    return false
  }
  delete errors.value[field]
  return true
}

function validateStock() {
  const stock = parseInt(productForm.value.stock)
  const minStock = parseInt(productForm.value.min_stock)

  if (isNaN(stock) || stock < 0) {
    errors.value.stock = 'O estoque deve ser maior ou igual a zero'
    return false
  }
  delete errors.value.stock

  if (isNaN(minStock) || minStock < 0) {
    errors.value.min_stock = 'O estoque mínimo deve ser maior ou igual a zero'
    return false
  }
  
  if (minStock > stock) {
    errors.value.min_stock = 'O estoque mínimo não pode ser maior que o estoque atual'
    return false
  }
  delete errors.value.min_stock
  return true
}

async function validateSku() {
  if (!productForm.value.sku) {
    errors.value.sku = 'SKU é obrigatório'
    return
  }

  if (!/^[A-Za-z0-9-]+$/.test(productForm.value.sku)) {
    errors.value.sku = 'SKU deve conter apenas letras, números e hífen'
    return
  }

  try {
    const response = await axios.get(`/products/check-sku/${productForm.value.sku}`)
    if (response.data.exists && (!editingProduct.value || editingProduct.value.sku !== productForm.value.sku)) {
      errors.value.sku = 'SKU já existe'
    } else {
      delete errors.value.sku
    }
  } catch (error) {
    console.error('Error checking SKU:', error)
  }
}

function closeModal() {
  showAddModal.value = false
  editingProduct.value = null
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
    status: 'active'
  }
  errors.value = {}
}

async function saveProduct() {
  errors.value = {}
  
  // Validações antes de enviar
  if (!productForm.value.name || productForm.value.name.length < 3) {
    errors.value.name = 'O nome deve ter pelo menos 3 caracteres'
    return
  }

  if (!productForm.value.sku) {
    errors.value.sku = 'O SKU é obrigatório'
    return
  }

  if (!validatePrice('price')) return
  if (!validatePrice('cost_price')) return
  if (!validateStock()) return

  if (!productForm.value.category_id) {
    errors.value.category_id = 'A categoria é obrigatória'
    return
  }

  if (!productForm.value.unit) {
    errors.value.unit = 'A unidade é obrigatória'
    return
  }

  isSaving.value = true
  try {
    const formData = { ...productForm.value }
    
    // Converter valores monetários para números
    formData.price = parseFloat(formData.price)
    formData.cost_price = parseFloat(formData.cost_price)
    
    // Converter valores de estoque para inteiros
    formData.stock = parseInt(formData.stock)
    formData.min_stock = parseInt(formData.min_stock)
    
    if (editingProduct.value) {
      await axios.put(`/products/${editingProduct.value.id}`, formData)
      showSuccessToast('Produto atualizado com sucesso!')
    } else {
      await axios.post('/products', formData)
      showSuccessToast('Produto criado com sucesso!')
    }
    
    await loadProducts()
    closeModal()
  } catch (error) {
    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors
    } else {
      toastMessage.value = 'Erro ao salvar produto. Tente novamente.'
      showToast.value = true
    }
  } finally {
    isSaving.value = false
  }
}

async function loadProducts() {
  isLoading.value = true
  try {
    const response = await axios.get('/products', {
      params: {
        ...filters.value,
        page: pagination.value.current_page
      }
    })
    products.value = response.data.data
    pagination.value = {
      current_page: response.data.current_page,
      from: response.data.from,
      to: response.data.to,
      total: response.data.total,
      last_page: response.data.last_page
    }
  } catch (error) {
    console.error('Error loading products:', error)
    toastMessage.value = 'Erro ao carregar produtos'
    showToast.value = true
  } finally {
    isLoading.value = false
  }
}

function changePage(page) {
  if (page >= 1 && page <= pagination.value.last_page) {
    pagination.value.current_page = page
    loadProducts()
  }
}

async function loadCategories() {
  try {
    const response = await axios.get('/categories')
    categories.value = response.data
  } catch (error) {
    console.error('Error loading categories:', error)
    showToast('Erro ao carregar categorias')
  }
}

function editProduct(product) {
  editingProduct.value = product
  productForm.value = {
    name: product.name,
    description: product.description || '',
    sku: product.sku,
    price: product.price.toString(),
    cost_price: product.cost_price.toString(),
    stock: product.stock.toString(),
    min_stock: product.min_stock.toString(),
    unit: product.unit,
    category_id: product.category_id,
    status: product.status || 'active'
  }
  showAddModal.value = true
}

async function deleteProduct(id) {
  if (!confirm('Tem certeza que deseja excluir este produto?')) return
  
  try {
    await axios.delete(`/products/${id}`)
    await loadProducts()
  } catch (error) {
    console.error('Error deleting product:', error)
  }
}

function getStatusClass(status) {
  const classes = {
    active: 'bg-green-100 text-green-800',
    inactive: 'bg-red-100 text-red-800'
  }
  return `px-2 py-1 text-xs font-medium rounded-full ${classes[status] || ''}`
}

function getCategoryName(categoryId) {
  const category = categories.value.find(category => category.id === categoryId)
  return category ? category.name : ''
}

function resetFilters() {
  filters.value = {
    search: '',
    category_id: '',
    status: '',
    stock: ''
  }
}
</script>
