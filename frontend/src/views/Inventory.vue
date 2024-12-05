<template>
  <div>
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center mb-6">
          <h1 class="text-2xl font-semibold text-gray-900">Controle de Estoque</h1>
          <button @click="showMovementModal = true" class="btn-primary">
            Registrar Movimento
          </button>
        </div>

        <!-- Alertas de Estoque -->
        <div class="mb-6 grid gap-4 sm:grid-cols-3">
          <div class="card bg-red-50">
            <h3 class="text-lg font-medium text-red-800">Estoque Crítico</h3>
            <p class="mt-1 text-3xl font-semibold text-red-900">{{ lowStockCount }}</p>
            <p class="text-sm text-red-700">Produtos abaixo do estoque mínimo</p>
          </div>
          <div class="card bg-yellow-50">
            <h3 class="text-lg font-medium text-yellow-800">Atenção</h3>
            <p class="mt-1 text-3xl font-semibold text-yellow-900">{{ warningStockCount }}</p>
            <p class="text-sm text-yellow-700">Produtos próximos ao estoque mínimo</p>
          </div>
          <div class="card bg-green-50">
            <h3 class="text-lg font-medium text-green-800">Estoque Normal</h3>
            <p class="mt-1 text-3xl font-semibold text-green-900">{{ normalStockCount }}</p>
            <p class="text-sm text-green-700">Produtos com estoque adequado</p>
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
                placeholder="Nome ou código do produto"
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
              <label class="form-label">Status do Estoque</label>
              <select v-model="filters.stockStatus" class="input-field">
                <option value="">Todos</option>
                <option value="low">Crítico</option>
                <option value="warning">Atenção</option>
                <option value="normal">Normal</option>
              </select>
            </div>
            <div>
              <label class="form-label">Ordenar por</label>
              <select v-model="filters.sortBy" class="input-field">
                <option value="name">Nome</option>
                <option value="stock">Quantidade em Estoque</option>
                <option value="min_stock">Estoque Mínimo</option>
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
                  Categoria
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Estoque Atual
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Estoque Mínimo
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
              <tr v-for="product in filteredProducts" :key="product.id">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-medium text-gray-900">{{ product.name }}</div>
                  <div class="text-sm text-gray-500">SKU: {{ product.sku }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ product.category }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ product.stock }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ product.min_stock }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="getStockStatusClass(product)">
                    {{ getStockStatusText(product) }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <button 
                    @click="viewHistory(product)" 
                    class="text-blue-600 hover:text-blue-900 mr-4"
                  >
                    Histórico
                  </button>
                  <button 
                    @click="openMovementModal(product)" 
                    class="text-green-600 hover:text-green-900"
                  >
                    Ajustar
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </main>

    <!-- Modal de Movimento de Estoque -->
    <div v-if="showMovementModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center">
      <div class="bg-white rounded-lg p-6 max-w-md w-full">
        <h2 class="text-xl font-semibold mb-4">
          {{ selectedProduct ? 'Ajustar Estoque' : 'Novo Movimento' }}
        </h2>
        <form @submit.prevent="saveMovement" class="space-y-4">
          <div v-if="!selectedProduct">
            <label class="form-label">Produto</label>
            <select v-model="movementForm.product_id" class="input-field" required>
              <option value="">Selecione um produto</option>
              <option v-for="product in products" :key="product.id" :value="product.id">
                {{ product.name }} (Atual: {{ product.stock }})
              </option>
            </select>
          </div>
          
          <div>
            <label class="form-label">Tipo de Movimento</label>
            <select v-model="movementForm.type" class="input-field" required>
              <option value="add">Entrada</option>
              <option value="remove">Saída</option>
              <option value="adjust">Ajuste</option>
            </select>
          </div>

          <div>
            <label class="form-label">Quantidade</label>
            <input 
              type="number" 
              v-model="movementForm.quantity" 
              class="input-field" 
              required 
              min="1"
            />
          </div>

          <div>
            <label class="form-label">Observação</label>
            <textarea 
              v-model="movementForm.notes" 
              class="input-field" 
              rows="3"
              placeholder="Motivo do movimento"
            ></textarea>
          </div>

          <div class="flex justify-end space-x-4">
            <button type="button" @click="showMovementModal = false" class="btn-secondary">
              Cancelar
            </button>
            <button type="submit" class="btn-primary">
              Salvar
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal de Histórico -->
    <div v-if="showHistoryModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center">
      <div class="bg-white rounded-lg p-6 max-w-4xl w-full">
        <h2 class="text-xl font-semibold mb-4">
          Histórico de Movimentações - {{ selectedProduct?.name }}
        </h2>
        
        <div class="space-y-4">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead>
                <tr>
                  <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                  <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                  <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Quantidade</th>
                  <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Saldo</th>
                  <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Observação</th>
                  <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Usuário</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <tr v-for="movement in productHistory" :key="movement.id">
                  <td class="px-4 py-2 text-sm text-gray-900">
                    {{ formatDate(movement.created_at) }}
                  </td>
                  <td class="px-4 py-2">
                    <span :class="getMovementTypeClass(movement.type)">
                      {{ getMovementTypeText(movement.type) }}
                    </span>
                  </td>
                  <td class="px-4 py-2 text-sm text-gray-900">
                    {{ movement.quantity }}
                  </td>
                  <td class="px-4 py-2 text-sm text-gray-900">
                    {{ movement.balance }}
                  </td>
                  <td class="px-4 py-2 text-sm text-gray-500">
                    {{ movement.notes }}
                  </td>
                  <td class="px-4 py-2 text-sm text-gray-900">
                    {{ movement.user_name }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="flex justify-end">
            <button @click="showHistoryModal = false" class="btn-secondary">
              Fechar
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const products = ref([])
const categories = ref([])
const showMovementModal = ref(false)
const showHistoryModal = ref(false)
const selectedProduct = ref(null)
const productHistory = ref([])

const filters = ref({
  search: '',
  category: '',
  stockStatus: '',
  sortBy: 'name'
})

const movementForm = ref({
  product_id: '',
  type: 'add',
  quantity: 1,
  notes: ''
})

// Contadores de status de estoque
const lowStockCount = computed(() => 
  products.value.filter(p => p.stock < p.min_stock).length
)

const warningStockCount = computed(() => 
  products.value.filter(p => p.stock >= p.min_stock && p.stock <= p.min_stock * 1.2).length
)

const normalStockCount = computed(() => 
  products.value.filter(p => p.stock > p.min_stock * 1.2).length
)

// Produtos filtrados
const filteredProducts = computed(() => {
  let filtered = [...products.value]

  // Filtro de busca
  if (filters.value.search) {
    const search = filters.value.search.toLowerCase()
    filtered = filtered.filter(p => 
      p.name.toLowerCase().includes(search) || 
      p.sku.toLowerCase().includes(search)
    )
  }

  // Filtro de categoria
  if (filters.value.category) {
    filtered = filtered.filter(p => p.category === filters.value.category)
  }

  // Filtro de status de estoque
  if (filters.value.stockStatus) {
    switch (filters.value.stockStatus) {
      case 'low':
        filtered = filtered.filter(p => p.stock < p.min_stock)
        break
      case 'warning':
        filtered = filtered.filter(p => 
          p.stock >= p.min_stock && p.stock <= p.min_stock * 1.2
        )
        break
      case 'normal':
        filtered = filtered.filter(p => p.stock > p.min_stock * 1.2)
        break
    }
  }

  // Ordenação
  filtered.sort((a, b) => {
    switch (filters.value.sortBy) {
      case 'stock':
        return b.stock - a.stock
      case 'min_stock':
        return b.min_stock - a.min_stock
      default:
        return a.name.localeCompare(b.name)
    }
  })

  return filtered
})

onMounted(async () => {
  await loadProducts()
  await loadCategories()
})

async function loadProducts() {
  try {
    const token = localStorage.getItem('token')
    const response = await axios.get('http://localhost:8000/api/products', {
      headers: { Authorization: `Bearer ${token}` }
    })
    products.value = response.data
  } catch (error) {
    console.error('Erro ao carregar produtos:', error)
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
    console.error('Erro ao carregar categorias:', error)
  }
}

function openMovementModal(product = null) {
  selectedProduct.value = product
  if (product) {
    movementForm.value.product_id = product.id
  }
  showMovementModal.value = true
}

async function saveMovement() {
  try {
    const token = localStorage.getItem('token')
    await axios.post('http://localhost:8000/api/inventory/movements', movementForm.value, {
      headers: { Authorization: `Bearer ${token}` }
    })
    showMovementModal.value = false
    await loadProducts()
    movementForm.value = {
      product_id: '',
      type: 'add',
      quantity: 1,
      notes: ''
    }
  } catch (error) {
    console.error('Erro ao salvar movimento:', error)
  }
}

async function viewHistory(product) {
  selectedProduct.value = product
  try {
    const token = localStorage.getItem('token')
    const response = await axios.get(`http://localhost:8000/api/inventory/movements/${product.id}`, {
      headers: { Authorization: `Bearer ${token}` }
    })
    productHistory.value = response.data
    showHistoryModal.value = true
  } catch (error) {
    console.error('Erro ao carregar histórico:', error)
  }
}

function getStockStatusClass(product) {
  if (product.stock < product.min_stock) {
    return 'bg-red-100 text-red-800 px-2 py-1 text-xs font-medium rounded-full'
  }
  if (product.stock <= product.min_stock * 1.2) {
    return 'bg-yellow-100 text-yellow-800 px-2 py-1 text-xs font-medium rounded-full'
  }
  return 'bg-green-100 text-green-800 px-2 py-1 text-xs font-medium rounded-full'
}

function getStockStatusText(product) {
  if (product.stock < product.min_stock) {
    return 'Crítico'
  }
  if (product.stock <= product.min_stock * 1.2) {
    return 'Atenção'
  }
  return 'Normal'
}

function getMovementTypeClass(type) {
  const classes = {
    add: 'bg-green-100 text-green-800',
    remove: 'bg-red-100 text-red-800',
    adjust: 'bg-blue-100 text-blue-800'
  }
  return `px-2 py-1 text-xs font-medium rounded-full ${classes[type] || ''}`
}

function getMovementTypeText(type) {
  const texts = {
    add: 'Entrada',
    remove: 'Saída',
    adjust: 'Ajuste'
  }
  return texts[type] || type
}

function formatDate(date) {
  return new Date(date).toLocaleDateString('pt-BR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}
</script>
