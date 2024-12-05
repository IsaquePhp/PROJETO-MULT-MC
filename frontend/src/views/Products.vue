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
        <div class="card mb-6">
          <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
            <div>
              <label class="form-label">Buscar</label>
              <input 
                type="text" 
                v-model="filters.search" 
                class="input-field" 
                placeholder="Nome, SKU ou código"
              />
            </div>
            <div>
              <label class="form-label">Categoria</label>
              <select v-model="filters.category" class="input-field">
                <option value="">Todas</option>
                <option v-for="category in categories" :key="category" :value="category">
                  {{ category }}
                </option>
              </select>
            </div>
            <div>
              <label class="form-label">Status</label>
              <select v-model="filters.status" class="input-field">
                <option value="">Todos</option>
                <option value="active">Ativo</option>
                <option value="inactive">Inativo</option>
              </select>
            </div>
            <div>
              <label class="form-label">Estoque</label>
              <select v-model="filters.stock" class="input-field">
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
          <table class="min-w-full divide-y divide-gray-200">
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
                    {{ product.status }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
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

    <!-- Modal de Adicionar/Editar Produto -->
    <div v-if="showAddModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center">
      <div class="bg-white rounded-lg p-6 max-w-md w-full">
        <h2 class="text-xl font-semibold mb-4">
          {{ editingProduct ? 'Editar Produto' : 'Novo Produto' }}
        </h2>
        <form @submit.prevent="saveProduct" class="space-y-4">
          <div>
            <label class="form-label">Nome</label>
            <input type="text" v-model="productForm.name" class="input-field" required />
          </div>
          <div>
            <label class="form-label">SKU</label>
            <input type="text" v-model="productForm.sku" class="input-field" required />
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="form-label">Preço</label>
              <input type="number" v-model="productForm.price" class="input-field" required step="0.01" />
            </div>
            <div>
              <label class="form-label">Preço de Custo</label>
              <input type="number" v-model="productForm.cost_price" class="input-field" required step="0.01" />
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="form-label">Estoque</label>
              <input type="number" v-model="productForm.stock" class="input-field" required />
            </div>
            <div>
              <label class="form-label">Estoque Mínimo</label>
              <input type="number" v-model="productForm.min_stock" class="input-field" required />
            </div>
          </div>
          <div>
            <label class="form-label">Categoria</label>
            <input type="text" v-model="productForm.category" class="input-field" required />
          </div>
          <div class="flex justify-end space-x-4">
            <button type="button" @click="showAddModal = false" class="btn-secondary">
              Cancelar
            </button>
            <button type="submit" class="btn-primary">
              Salvar
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const products = ref([])
const categories = ref([])
const showAddModal = ref(false)
const editingProduct = ref(null)
const filters = ref({
  search: '',
  category: '',
  status: '',
  stock: ''
})

const productForm = ref({
  name: '',
  sku: '',
  price: '',
  cost_price: '',
  stock: '',
  min_stock: '',
  category: '',
  status: 'active'
})

onMounted(async () => {
  await loadProducts()
  await loadCategories()
})

async function loadProducts() {
  try {
    const token = localStorage.getItem('token')
    const response = await axios.get('http://localhost:8000/api/products', {
      headers: { Authorization: `Bearer ${token}` },
      params: filters.value
    })
    products.value = response.data
  } catch (error) {
    console.error('Error loading products:', error)
  }
}

async function loadCategories() {
  try {
    const token = localStorage.getItem('token')
    const response = await axios.get('http://localhost:8000/api/products/categories', {
      headers: { Authorization: `Bearer ${token}` }
    })
    categories.value = response.data
  } catch (error) {
    console.error('Error loading categories:', error)
  }
}

function editProduct(product) {
  editingProduct.value = product
  productForm.value = { ...product }
  showAddModal.value = true
}

async function saveProduct() {
  try {
    const token = localStorage.getItem('token')
    const headers = { Authorization: `Bearer ${token}` }
    
    if (editingProduct.value) {
      await axios.put(`http://localhost:8000/api/products/${editingProduct.value.id}`, productForm.value, { headers })
    } else {
      await axios.post('http://localhost:8000/api/products', productForm.value, { headers })
    }
    
    showAddModal.value = false
    await loadProducts()
  } catch (error) {
    console.error('Error saving product:', error)
  }
}

async function deleteProduct(id) {
  if (!confirm('Tem certeza que deseja excluir este produto?')) return
  
  try {
    const token = localStorage.getItem('token')
    await axios.delete(`http://localhost:8000/api/products/${id}`, {
      headers: { Authorization: `Bearer ${token}` }
    })
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
</script>
