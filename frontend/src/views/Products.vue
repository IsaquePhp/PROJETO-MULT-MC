<template>
  <div>
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center mb-6">
          <h1 class="text-2xl font-semibold text-gray-900">Produtos</h1>
          <div class="flex gap-2">
            <button @click="showImportModal = true" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
              Importar do Magalu
            </button>
            <button @click="showAddModal = true" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
              Adicionar Produto
            </button>
          </div>
        </div>

        <!-- Filtros -->
        <div class="card mb-6">
          <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
            <div>
              <label class="form-label">Buscar</label>
              <input 
                type="text" 
                v-model="filters.search" 
                class="input-field" 
                placeholder="Nome, SKU ou código"
                @input="debouncedLoadProducts"
              />
            </div>
            <div>
              <label class="form-label">Categoria</label>
              <select v-model="filters.category" class="input-field" @change="loadProducts">
                <option value="">Todas</option>
                <option v-for="category in categories" :key="category" :value="category">
                  {{ category }}
                </option>
              </select>
            </div>
            <div>
              <label class="form-label">Status</label>
              <select v-model="filters.status" class="input-field" @change="loadProducts">
                <option value="">Todos</option>
                <option value="active">Ativo</option>
                <option value="inactive">Inativo</option>
              </select>
            </div>
            <div>
              <label class="form-label">Estoque</label>
              <select v-model="filters.stock" class="input-field" @change="loadProducts">
                <option value="">Todos</option>
                <option value="low">Baixo</option>
                <option value="normal">Normal</option>
                <option value="high">Alto</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Lista de Produtos -->
        <div class="card">
          <!-- Loading state -->
          <div v-if="loading" class="py-12 text-center text-gray-500">
            Carregando produtos...
          </div>

          <!-- Empty state -->
          <div v-else-if="products.length === 0" class="py-12 text-center text-gray-500">
            Nenhum produto encontrado.
          </div>

          <!-- Products table -->
          <table v-else class="min-w-full divide-y divide-gray-200">
            <thead>
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Produto
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  SKU
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Preço
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Estoque
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Status
                </th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Ações
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="product in products" :key="product.id">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div>
                      <div class="text-sm font-medium text-gray-900">
                        {{ product.name }}
                      </div>
                      <div class="text-sm text-gray-500">
                        {{ product.category }}
                      </div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ product.sku }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  R$ {{ product.price }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900">{{ product.stock }}</div>
                  <div class="text-sm text-gray-500">
                    Min: {{ product.min_stock }}
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="getStatusClass(product.status)">
                    {{ product.status === 'active' ? 'Ativo' : 'Inativo' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                  <button 
                    @click="editProduct(product)"
                    class="text-blue-600 hover:text-blue-900 mr-4"
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
        </div>
      </div>
    </main>

    <!-- Modais e Toast -->
    <ProductImportModal 
      v-if="showImportModal"
      @close="showImportModal = false"
      @product-imported="onProductImported"
    />

    <!-- Modal de Adicionar/Editar Produto -->
    <div v-if="showAddModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center">
      <div class="bg-white rounded-lg p-6 max-w-md w-full">
        <h2 class="text-xl font-semibold mb-4">
          {{ editingProduct ? 'Editar Produto' : 'Novo Produto' }}
        </h2>
        <form @submit.prevent="saveProduct" class="space-y-4">
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
            <label class="form-label">Descrição</label>
            <textarea 
              v-model="productForm.description" 
              class="input-field" 
              rows="3"
              maxlength="500"
            ></textarea>
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

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="form-label">Preço de Venda *</label>
              <div class="relative">
                <span class="absolute left-3 top-2">R$</span>
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
                <span class="absolute left-3 top-2">R$</span>
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

          <div class="grid grid-cols-2 gap-4">
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
          </div>

          <div>
            <label class="form-label">Unidade *</label>
            <input 
              type="text" 
              v-model="productForm.unit" 
              class="input-field" 
              required
            />
          </div>

          <div>
            <label class="form-label">Categoria *</label>
            <select 
              v-model="productForm.category" 
              class="input-field" 
              required
              :class="{'border-red-500': errors.category}"
              @change="validateCategory"
            >
              <option value="">Selecione uma categoria</option>
              <option v-for="category in defaultCategories" :key="category" :value="category">
                {{ category }}
              </option>
              <option value="other">Outra...</option>
            </select>
            <input 
              v-if="productForm.category === 'other'"
              type="text"
              v-model="newCategory"
              class="input-field mt-2"
              placeholder="Digite a nova categoria"
              @input="validateCategory"
              :class="{'border-red-500': errors.category}"
            />
            <span v-if="errors.category" class="text-red-500 text-sm">{{ errors.category }}</span>
          </div>

          <div class="flex justify-end space-x-4">
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
              {{ isSaving ? 'Salvando...' : (editingProduct ? 'Atualizar' : 'Salvar') }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Toast de Sucesso -->
    <div 
      v-if="showToast" 
      class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg"
    >
      {{ toastMessage }}
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from '../plugins/axios'
import ProductImportModal from '../components/ProductImportModal.vue'

// Refs
const products = ref([])
const categories = ref([])
const loading = ref(false)
const showAddModal = ref(false)
const showImportModal = ref(false)
const showToast = ref(false)
const toastMessage = ref('')
const editingProduct = ref(null)
const isSaving = ref(false)
const errors = ref({})

// Form state
const productForm = ref({
  name: '',
  description: '',
  sku: '',
  category: '',
  price: '',
  stock: '',
  min_stock: '',
  status: 'active'
})

const filters = ref({
  search: '',
  category: '',
  status: '',
  stock: ''
})

// Carregar produtos quando o componente é montado
onMounted(async () => {
  await Promise.all([
    loadProducts(),
    loadCategories()
  ])
})

// Debounce para a busca
let searchTimeout
const debouncedLoadProducts = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    loadProducts()
  }, 300)
}

// Carregar produtos com loading state
async function loadProducts() {
  loading.value = true
  try {
    const response = await axios.get('/products', {
      params: filters.value
    })
    products.value = response.data
  } catch (error) {
    console.error('Error loading products:', error)
    showToast.value = true
    toastMessage.value = 'Erro ao carregar produtos'
  } finally {
    loading.value = false
  }
}

// Carregar categorias
async function loadCategories() {
  try {
    const response = await axios.get('/categories')
    categories.value = response.data
  } catch (error) {
    console.error('Error loading categories:', error)
  }
}

// Validar SKU
function validateSku() {
  const skuPattern = /^[A-Za-z0-9-]+$/
  if (!skuPattern.test(productForm.value.sku)) {
    errors.value.sku = 'SKU deve conter apenas letras, números e hífen'
  } else {
    delete errors.value.sku
  }
}

// Salvar produto
async function saveProduct() {
  if (Object.keys(errors.value).length > 0) return

  isSaving.value = true
  try {
    const endpoint = editingProduct.value ? `/products/${editingProduct.value.id}` : '/products'
    const method = editingProduct.value ? 'put' : 'post'
    
    const response = await axios[method](endpoint, productForm.value)
    
    showToast.value = true
    toastMessage.value = editingProduct.value ? 'Produto atualizado com sucesso!' : 'Produto criado com sucesso!'
    
    await loadProducts()
    closeModal()
  } catch (error) {
    console.error('Error saving product:', error)
    showToast.value = true
    toastMessage.value = 'Erro ao salvar produto'
  } finally {
    isSaving.value = false
  }
}

// Editar produto
function editProduct(product) {
  editingProduct.value = product
  productForm.value = { ...product }
  showAddModal.value = true
}

// Fechar modal
function closeModal() {
  showAddModal.value = false
  showImportModal.value = false
  editingProduct.value = null
  productForm.value = {
    name: '',
    description: '',
    sku: '',
    category: '',
    price: '',
    stock: '',
    min_stock: '',
    status: 'active'
  }
  errors.value = {}
}

// Deletar produto
async function deleteProduct(id) {
  if (!confirm('Tem certeza que deseja excluir este produto?')) return
  
  try {
    await axios.delete(`/products/${id}`)
    showToast.value = true
    toastMessage.value = 'Produto excluído com sucesso!'
    await loadProducts()
  } catch (error) {
    console.error('Error deleting product:', error)
    showToast.value = true
    toastMessage.value = 'Erro ao excluir produto'
  }
}

// Callback quando um produto é importado
function onProductImported(product) {
  showToast.value = true
  toastMessage.value = 'Produto importado com sucesso!'
  loadProducts()
  showImportModal.value = false
}

// Estilo do status
function getStatusClass(status) {
  return status === 'active'
    ? 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800'
    : 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800'
}
</script>
