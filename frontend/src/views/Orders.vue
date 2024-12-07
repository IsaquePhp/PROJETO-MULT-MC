<template>
  <div class="p-6">
    <header class="mb-8">
      <h1 class="text-2xl font-bold text-gray-900">Vendas</h1>
      <p class="mt-2 text-sm text-gray-700">Acompanhamento de vendas</p>
    </header>

    <div class="bg-white rounded-lg shadow">
      <!-- Filtros -->
      <div class="p-4 border-b border-gray-200">
        <div class="flex flex-wrap gap-4">
          <div class="flex-1 min-w-[200px]">
            <input
              v-model="filters.search"
              type="text"
              placeholder="Buscar venda por cliente ou número..."
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
              @input="debouncedLoadOrders"
            >
          </div>
          <div>
            <select 
              v-model="filters.status"
              class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
              @change="loadOrders"
            >
              <option value="">Todos os Status</option>
              <option value="pending">Aguardando Pagamento</option>
              <option value="processing">Em Processamento</option>
              <option value="completed">Concluída</option>
              <option value="cancelled">Cancelada</option>
            </select>
          </div>
          <div>
            <select 
              v-model="filters.dateRange"
              class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
              @change="loadOrders"
            >
              <option value="today">Hoje</option>
              <option value="yesterday">Ontem</option>
              <option value="week">Últimos 7 dias</option>
              <option value="month">Este mês</option>
              <option value="all">Todos</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Loading state -->
      <div v-if="loading" class="p-8 text-center text-gray-500">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600 mx-auto mb-4"></div>
        Carregando vendas...
      </div>

      <!-- Lista de Vendas -->
      <div v-else class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nº Venda</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data/Hora</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Itens</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="sale in sales" :key="sale.id" :class="{'bg-yellow-50': isNewSale(sale)}">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                #{{ sale.id }}
                <span v-if="isNewSale(sale)" class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                  Novo
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ formatDateTime(sale.created_at) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ sale.customer }}</td>
              <td class="px-6 py-4 text-sm text-gray-500">
                <div class="space-y-1">
                  <div v-for="item in sale.items" :key="item.id" class="flex items-center gap-1">
                    <span class="font-medium">{{ item.quantity }}x</span>
                    <span>{{ item.product }}</span>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                R$ {{ formatPrice(sale.total) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="getStatusClass(sale.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                  {{ getStatusText(sale.status) }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <button 
                  @click="showSaleDetails(sale)" 
                  class="text-indigo-600 hover:text-indigo-900 mr-4"
                >
                  Detalhes
                </button>
                <button 
                  v-if="canUpdateStatus(sale.status)"
                  @click="showUpdateStatus(sale)" 
                  class="text-green-600 hover:text-green-900"
                >
                  Atualizar Status
                </button>
              </td>
            </tr>
            <!-- Empty state -->
            <tr v-if="sales.length === 0">
              <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                Nenhuma venda encontrada
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal de Detalhes da Venda -->
    <div v-if="showModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center">
      <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4">
        <div class="flex justify-between items-center mb-6">
          <h3 class="text-lg font-medium text-gray-900">
            Detalhes da Venda #{{ selectedSale?.id }}
          </h3>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-500">
            <span class="sr-only">Fechar</span>
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <div class="space-y-6">
          <!-- Informações do Cliente -->
          <div>
            <h4 class="text-sm font-medium text-gray-900 mb-2">Informações do Cliente</h4>
            <div class="bg-gray-50 rounded-lg p-4">
              <p class="text-sm text-gray-600"><span class="font-medium">Nome:</span> {{ selectedSale?.customer }}</p>
              <p class="text-sm text-gray-600 mt-1"><span class="font-medium">Data da Venda:</span> {{ formatDateTime(selectedSale?.created_at) }}</p>
            </div>
          </div>

          <!-- Itens da Venda -->
          <div>
            <h4 class="text-sm font-medium text-gray-900 mb-2">Itens da Venda</h4>
            <div class="bg-gray-50 rounded-lg p-4">
              <div v-for="item in selectedSale?.items" :key="item.id" class="flex justify-between items-center py-2">
                <div>
                  <p class="text-sm font-medium text-gray-900">{{ item.product }}</p>
                  <p class="text-sm text-gray-500">Quantidade: {{ item.quantity }}</p>
                </div>
                <p class="text-sm font-medium text-gray-900">R$ {{ formatPrice(item.price * item.quantity) }}</p>
              </div>
              <div class="border-t border-gray-200 mt-4 pt-4 flex justify-between items-center">
                <p class="text-sm font-medium text-gray-900">Total da Venda</p>
                <p class="text-lg font-medium text-gray-900">R$ {{ formatPrice(selectedSale?.total) }}</p>
              </div>
            </div>
          </div>

          <!-- Status da Venda -->
          <div>
            <h4 class="text-sm font-medium text-gray-900 mb-2">Status da Venda</h4>
            <div class="bg-gray-50 rounded-lg p-4">
              <span :class="getStatusClass(selectedSale?.status)" class="px-2 py-1 text-sm font-medium rounded-full">
                {{ getStatusText(selectedSale?.status) }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de Atualização de Status -->
    <div v-if="showStatusModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center">
      <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <div class="flex justify-between items-center mb-6">
          <h3 class="text-lg font-medium text-gray-900">
            Atualizar Status da Venda #{{ selectedSale?.id }}
          </h3>
          <button @click="closeStatusModal" class="text-gray-400 hover:text-gray-500">
            <span class="sr-only">Fechar</span>
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">Status Atual</label>
            <p class="mt-1 text-sm text-gray-500">{{ getStatusText(selectedSale?.status) }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Novo Status</label>
            <select 
              v-model="newStatus"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >
              <option v-for="status in availableNextStatuses" :key="status.value" :value="status.value">
                {{ status.label }}
              </option>
            </select>
          </div>

          <div class="flex justify-end gap-3 mt-6">
            <button 
              type="button"
              @click="closeStatusModal"
              class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50"
            >
              Cancelar
            </button>
            <button 
              type="button"
              @click="updateSaleStatus"
              :disabled="updating"
              class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md shadow-sm hover:bg-indigo-700 disabled:opacity-50"
            >
              {{ updating ? 'Atualizando...' : 'Atualizar Status' }}
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
import { format } from 'date-fns'

// Estado
const sales = ref([])
const loading = ref(false)
const showModal = ref(false)
const showStatusModal = ref(false)
const selectedSale = ref(null)
const updating = ref(false)
const newStatus = ref('')
const error = ref(null)

const filters = ref({
  search: '',
  status: '',
  dateRange: 'today'
})

// Status da venda e suas transições permitidas
const statusFlow = {
  pending: ['processing', 'cancelled'],
  processing: ['completed', 'cancelled'],
  completed: [],
  cancelled: []
}

const statusLabels = {
  pending: 'Aguardando Pagamento',
  processing: 'Em Processamento',
  completed: 'Concluída',
  cancelled: 'Cancelada'
}

const statusClasses = {
  pending: 'bg-yellow-100 text-yellow-800',
  processing: 'bg-blue-100 text-blue-800',
  completed: 'bg-green-100 text-green-800',
  cancelled: 'bg-red-100 text-red-800'
}

// Computed
const availableNextStatuses = computed(() => {
  if (!selectedSale.value) return []
  
  const currentStatus = selectedSale.value.status
  return statusFlow[currentStatus].map(status => ({
    value: status,
    label: statusLabels[status]
  }))
})

// Methods
const loadSales = async () => {
  try {
    loading.value = true
    error.value = null
    console.log('Carregando vendas...')
    
    const response = await axios.get('http://localhost/VERSAO%20DIEGO/api-loja-mc/public/api/sales', { 
      params: {
        ...filters.value,
        per_page: 50
      }
    })
    
    console.log('Resposta da API:', response.data)

    if (!response.data?.data) {
      throw new Error('Dados não recebidos da API')
    }

    sales.value = response.data.data.map(sale => ({
      id: sale.id,
      customer: sale.client?.name || 'Cliente não encontrado',
      customer_id: sale.client_id,
      items: (sale.items || []).map(item => ({
        id: item.id,
        product: item.product?.name || 'Produto não encontrado',
        product_id: item.product_id,
        quantity: item.quantity,
        price: parseFloat(item.price || 0)
      })),
      status: sale.sale_status || 'pending',
      created_at: sale.created_at,
      total: parseFloat(sale.total || 0)
    }))

    console.log('Vendas processadas:', sales.value)
  } catch (err) {
    console.error('Erro ao carregar vendas:', err)
    error.value = 'Erro ao carregar vendas: ' + (err.response?.data?.message || err.message)
  } finally {
    loading.value = false
  }
}

const showSaleDetails = (sale) => {
  selectedSale.value = sale
  showModal.value = true
}

const showUpdateStatus = (sale) => {
  selectedSale.value = sale
  newStatus.value = ''
  showStatusModal.value = true
}

const closeModal = () => {
  showModal.value = false
  selectedSale.value = null
}

const closeStatusModal = () => {
  showStatusModal.value = false
  newStatus.value = ''
}

const updateSaleStatus = async () => {
  if (!newStatus.value || !selectedSale.value) return

  try {
    updating.value = true
    await axios.put(`http://localhost/VERSAO%20DIEGO/api-loja-mc/public/api/sales/${selectedSale.value.id}/status`, {
      status: newStatus.value
    })
    
    await loadSales()
    closeStatusModal()
  } catch (error) {
    console.error('Error updating sale status:', error)
    alert('Erro ao atualizar o status da venda. Por favor, tente novamente.')
  } finally {
    updating.value = false
  }
}

const formatDateTime = (date) => {
  if (!date) return ''
  try {
    return format(new Date(date), 'dd/MM/yyyy HH:mm')
  } catch (error) {
    console.error('Error formatting date:', error)
    return date
  }
}

const formatPrice = (price) => {
  return Number(price).toLocaleString('pt-BR', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  })
}

const getStatusText = (status) => {
  return statusLabels[status] || status
}

const getStatusClass = (status) => {
  return statusClasses[status] || 'bg-gray-100 text-gray-800'
}

const canUpdateStatus = (status) => {
  return statusFlow[status] && statusFlow[status].length > 0
}

const isNewSale = (sale) => {
  const saleDate = new Date(sale.created_at)
  const now = new Date()
  const diffInHours = (now - saleDate) / (1000 * 60 * 60)
  return diffInHours <= 1 // Considera novo se foi criado na última hora
}

// Debounce para a busca
let searchTimeout
const debouncedLoadSales = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    loadSales()
  }, 300)
}

// Lifecycle
onMounted(() => {
  loadSales()
})
</script>
