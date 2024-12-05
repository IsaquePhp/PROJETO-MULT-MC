<template>
  <div>
    <Navbar />
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center mb-6">
          <h1 class="text-2xl font-semibold text-gray-900">Vendas</h1>
          <button @click="showNewSaleModal = true" class="btn-primary">
            Nova Venda
          </button>
        </div>

        <!-- Filtros -->
        <div class="card mb-6">
          <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
            <div>
              <label class="form-label">Período</label>
              <select v-model="filters.period" class="input-field">
                <option value="today">Hoje</option>
                <option value="week">Última Semana</option>
                <option value="month">Último Mês</option>
                <option value="custom">Personalizado</option>
              </select>
            </div>
            <div v-if="filters.period === 'custom'" class="sm:col-span-2">
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="form-label">Data Inicial</label>
                  <input type="date" v-model="filters.startDate" class="input-field" />
                </div>
                <div>
                  <label class="form-label">Data Final</label>
                  <input type="date" v-model="filters.endDate" class="input-field" />
                </div>
              </div>
            </div>
            <div>
              <label class="form-label">Status</label>
              <select v-model="filters.status" class="input-field">
                <option value="">Todos</option>
                <option value="completed">Concluída</option>
                <option value="pending">Pendente</option>
                <option value="cancelled">Cancelada</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Lista de Vendas -->
        <div class="card">
          <table class="min-w-full divide-y divide-gray-200">
            <thead>
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Código
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Data
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Cliente
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Total
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
              <tr v-for="sale in sales" :key="sale.id">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  #{{ sale.id }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ formatDate(sale.created_at) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900">{{ sale.customer_name }}</div>
                  <div class="text-sm text-gray-500">{{ sale.customer_email }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  R$ {{ formatPrice(sale.total) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="getSaleStatusClass(sale.status)">
                    {{ getSaleStatusText(sale.status) }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <button 
                    @click="viewSaleDetails(sale)" 
                    class="text-blue-600 hover:text-blue-900 mr-4"
                  >
                    Detalhes
                  </button>
                  <button 
                    v-if="sale.status === 'pending'"
                    @click="cancelSale(sale.id)" 
                    class="text-red-600 hover:text-red-900"
                  >
                    Cancelar
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </main>

    <!-- Modal de Nova Venda -->
    <div v-if="showNewSaleModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center">
      <div class="bg-white rounded-lg p-6 max-w-4xl w-full">
        <h2 class="text-xl font-semibold mb-4">Nova Venda</h2>
        <form @submit.prevent="saveSale" class="space-y-4">
          <!-- Informações do Cliente -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="form-label">Nome do Cliente</label>
              <input type="text" v-model="saleForm.customer_name" class="input-field" required />
            </div>
            <div>
              <label class="form-label">Email do Cliente</label>
              <input type="email" v-model="saleForm.customer_email" class="input-field" required />
            </div>
          </div>

          <!-- Produtos -->
          <div>
            <label class="form-label">Produtos</label>
            <div v-for="(item, index) in saleForm.items" :key="index" class="grid grid-cols-12 gap-4 mb-2">
              <div class="col-span-5">
                <select v-model="item.product_id" class="input-field" @change="updateProductDetails(index)">
                  <option value="">Selecione um produto</option>
                  <option v-for="product in products" :key="product.id" :value="product.id">
                    {{ product.name }} - R$ {{ formatPrice(product.price) }}
                  </option>
                </select>
              </div>
              <div class="col-span-2">
                <input 
                  type="number" 
                  v-model="item.quantity" 
                  class="input-field" 
                  min="1" 
                  @change="updateItemTotal(index)"
                  placeholder="Qtd"
                />
              </div>
              <div class="col-span-3">
                <input 
                  type="number" 
                  v-model="item.price" 
                  class="input-field" 
                  step="0.01" 
                  @change="updateItemTotal(index)"
                  placeholder="Preço"
                />
              </div>
              <div class="col-span-1 flex items-center">
                R$ {{ formatPrice(item.total) }}
              </div>
              <div class="col-span-1 flex items-center">
                <button 
                  type="button" 
                  @click="removeItem(index)" 
                  class="text-red-600 hover:text-red-900"
                >
                  <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
            </div>
            <button 
              type="button" 
              @click="addItem" 
              class="mt-2 text-sm text-blue-600 hover:text-blue-900"
            >
              + Adicionar Produto
            </button>
          </div>

          <!-- Total -->
          <div class="flex justify-between items-center pt-4 border-t">
            <span class="text-lg font-semibold">Total:</span>
            <span class="text-lg font-semibold">R$ {{ formatPrice(saleTotal) }}</span>
          </div>

          <div class="flex justify-end space-x-4">
            <button type="button" @click="showNewSaleModal = false" class="btn-secondary">
              Cancelar
            </button>
            <button type="submit" class="btn-primary">
              Finalizar Venda
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal de Detalhes da Venda -->
    <div v-if="selectedSale" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center">
      <div class="bg-white rounded-lg p-6 max-w-2xl w-full">
        <h2 class="text-xl font-semibold mb-4">Detalhes da Venda #{{ selectedSale.id }}</h2>
        
        <div class="space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <p class="text-sm text-gray-500">Cliente</p>
              <p class="font-medium">{{ selectedSale.customer_name }}</p>
              <p class="text-sm text-gray-500">{{ selectedSale.customer_email }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-500">Data</p>
              <p class="font-medium">{{ formatDate(selectedSale.created_at) }}</p>
            </div>
          </div>

          <div class="border-t pt-4">
            <h3 class="font-medium mb-2">Produtos</h3>
            <table class="min-w-full divide-y divide-gray-200">
              <thead>
                <tr>
                  <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Produto</th>
                  <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Qtd</th>
                  <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Preço</th>
                  <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in selectedSale.items" :key="item.id">
                  <td class="px-4 py-2">{{ item.product_name }}</td>
                  <td class="px-4 py-2 text-right">{{ item.quantity }}</td>
                  <td class="px-4 py-2 text-right">R$ {{ formatPrice(item.price) }}</td>
                  <td class="px-4 py-2 text-right">R$ {{ formatPrice(item.total) }}</td>
                </tr>
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="3" class="px-4 py-2 text-right font-medium">Total:</td>
                  <td class="px-4 py-2 text-right font-medium">R$ {{ formatPrice(selectedSale.total) }}</td>
                </tr>
              </tfoot>
            </table>
          </div>

          <div class="flex justify-end">
            <button @click="selectedSale = null" class="btn-secondary">
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
import Navbar from '../components/Navbar.vue'
import axios from 'axios'

const sales = ref([])
const products = ref([])
const showNewSaleModal = ref(false)
const selectedSale = ref(null)
const filters = ref({
  period: 'today',
  startDate: '',
  endDate: '',
  status: ''
})

const saleForm = ref({
  customer_name: '',
  customer_email: '',
  items: [{ product_id: '', quantity: 1, price: 0, total: 0 }]
})

const saleTotal = computed(() => {
  return saleForm.value.items.reduce((total, item) => total + item.total, 0)
})

onMounted(async () => {
  await loadSales()
  await loadProducts()
})

async function loadSales() {
  try {
    const token = localStorage.getItem('token')
    const response = await axios.get('http://localhost:8000/api/sales', {
      headers: { Authorization: `Bearer ${token}` },
      params: filters.value
    })
    sales.value = response.data
  } catch (error) {
    console.error('Erro ao carregar vendas:', error)
  }
}

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

function addItem() {
  saleForm.value.items.push({ product_id: '', quantity: 1, price: 0, total: 0 })
}

function removeItem(index) {
  saleForm.value.items.splice(index, 1)
}

function updateProductDetails(index) {
  const item = saleForm.value.items[index]
  const product = products.value.find(p => p.id === item.product_id)
  if (product) {
    item.price = product.price
    updateItemTotal(index)
  }
}

function updateItemTotal(index) {
  const item = saleForm.value.items[index]
  item.total = item.quantity * item.price
}

async function saveSale() {
  try {
    const token = localStorage.getItem('token')
    await axios.post('http://localhost:8000/api/sales', saleForm.value, {
      headers: { Authorization: `Bearer ${token}` }
    })
    showNewSaleModal.value = false
    await loadSales()
    saleForm.value = {
      customer_name: '',
      customer_email: '',
      items: [{ product_id: '', quantity: 1, price: 0, total: 0 }]
    }
  } catch (error) {
    console.error('Erro ao salvar venda:', error)
  }
}

function viewSaleDetails(sale) {
  selectedSale.value = sale
}

async function cancelSale(id) {
  if (!confirm('Tem certeza que deseja cancelar esta venda?')) return

  try {
    const token = localStorage.getItem('token')
    await axios.put(`http://localhost:8000/api/sales/${id}/cancel`, {}, {
      headers: { Authorization: `Bearer ${token}` }
    })
    await loadSales()
  } catch (error) {
    console.error('Erro ao cancelar venda:', error)
  }
}

function getSaleStatusClass(status) {
  const classes = {
    completed: 'bg-green-100 text-green-800',
    pending: 'bg-yellow-100 text-yellow-800',
    cancelled: 'bg-red-100 text-red-800'
  }
  return `px-2 py-1 text-xs font-medium rounded-full ${classes[status] || ''}`
}

function getSaleStatusText(status) {
  const texts = {
    completed: 'Concluída',
    pending: 'Pendente',
    cancelled: 'Cancelada'
  }
  return texts[status] || status
}

function formatDate(date) {
  return new Date(date).toLocaleDateString('pt-BR')
}

function formatPrice(price) {
  return Number(price).toFixed(2)
}
</script>
