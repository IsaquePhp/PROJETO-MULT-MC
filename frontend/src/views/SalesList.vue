<template>
  <div class="container mx-auto p-4">
    <div class="bg-white rounded-lg shadow p-6">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Histórico de Vendas</h1>
        <div class="flex gap-4">
          <input
            type="text"
            v-model="searchQuery"
            placeholder="Buscar por cliente ou número do pedido..."
            class="px-4 py-2 border rounded-lg"
          />
          <select
            v-model="statusFilter"
            class="px-4 py-2 border rounded-lg"
          >
            <option value="">Todos os status</option>
            <option value="pending">Pendente</option>
            <option value="completed">Concluído</option>
            <option value="cancelled">Cancelado</option>
          </select>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="text-center py-8">
        <p class="text-gray-600">Carregando vendas...</p>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="text-center py-8">
        <p class="text-red-600">{{ error }}</p>
      </div>

      <!-- Empty State -->
      <div v-else-if="filteredSales.length === 0" class="text-center py-8">
        <p class="text-gray-600">Nenhuma venda encontrada</p>
      </div>

      <!-- Sales Table -->
      <div v-else class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Nº Pedido
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Data
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Cliente
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Método de Pagamento
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Status
              </th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                Total
              </th>
              <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                Ações
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="sale in filteredSales" :key="sale.id">
              <td class="px-6 py-4 whitespace-nowrap">
                #{{ sale.id }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                {{ formatDate(sale.created_at) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                {{ sale.customer?.name }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                {{ formatPaymentMethod(sale.payment_method) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  :class="{
                    'px-2 py-1 text-xs rounded-full': true,
                    'bg-yellow-100 text-yellow-800': sale.status === 'pending',
                    'bg-green-100 text-green-800': sale.status === 'completed',
                    'bg-red-100 text-red-800': sale.status === 'cancelled'
                  }"
                >
                  {{ formatStatus(sale.status) }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right">
                R$ {{ formatPrice(sale.total_amount) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-center">
                <button
                  @click="showSaleDetails(sale)"
                  class="text-indigo-600 hover:text-indigo-900"
                >
                  Ver Detalhes
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal de Detalhes -->
    <div v-if="selectedSale" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg w-full max-w-2xl mx-4 p-6">
        <div class="flex justify-between items-start mb-6">
          <h2 class="text-xl font-bold">Detalhes do Pedido #{{ selectedSale.id }}</h2>
          <div class="flex gap-2">
            <button @click="printSaleDetails" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
              Imprimir
            </button>
            <button @click="selectedSale = null" class="text-gray-500 hover:text-gray-700">
              ×
            </button>
          </div>
        </div>

        <div class="space-y-4">
          <!-- Cabeçalho do Pedido -->
          <div class="grid grid-cols-3 gap-4">
            <div>
              <p class="text-sm text-gray-600">Data: {{ formatDate(selectedSale.created_at) }}</p>
              <p class="text-sm text-gray-600">Status: {{ formatStatus(selectedSale.status) }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-600">Cliente: {{ selectedSale.customer?.name }}</p>
              <p class="text-sm text-gray-600">End. {{ selectedSale.customer?.address }}</p>
              <p class="text-sm text-gray-600">Telefone: {{ selectedSale.customer?.phone }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-600">Método: {{ formatPaymentMethod(selectedSale.payment_method) }}</p>
            </div>
          </div>

          <!-- Lista de Itens -->
          <div class="mt-6">
            <h3 class="font-medium mb-2">Lista de itens</h3>
            <table class="w-full">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">#</th>
                  <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">PRODUTO</th>
                  <th class="px-4 py-2 text-center text-sm font-medium text-gray-600">QUANTIDADE</th>
                  <th class="px-4 py-2 text-right text-sm font-medium text-gray-600">PREÇO UNIT.</th>
                  <th class="px-4 py-2 text-right text-sm font-medium text-gray-600">TOTAL</th>
                </tr>
              </thead>
              <tbody class="divide-y">
                <tr v-for="(item, index) in selectedSale.items" :key="index">
                  <td class="px-4 py-2 text-sm">{{ index + 1 }}</td>
                  <td class="px-4 py-2 text-sm">{{ item.product?.name }}</td>
                  <td class="px-4 py-2 text-sm text-center">{{ item.quantity }}</td>
                  <td class="px-4 py-2 text-sm text-right">R$ {{ formatPrice(item.unit_price) }}</td>
                  <td class="px-4 py-2 text-sm text-right">R$ {{ formatPrice(item.total) }}</td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Total -->
          <div class="flex justify-end mt-4 pt-4 border-t">
            <div class="text-right">
              <p class="font-medium">Total</p>
              <p class="text-xl font-bold">R$ {{ calculateTotal(selectedSale.items) }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import axios from '../plugins/axios'

export default {
  name: 'SalesList',
  setup() {
    const sales = ref([])
    const loading = ref(true)
    const error = ref(null)
    const searchQuery = ref('')
    const statusFilter = ref('')
    const selectedSale = ref(null)

    // Carregar vendas
    async function loadSales() {
      try {
        loading.value = true
        error.value = null
        console.log('Iniciando carregamento das vendas...')
        
        const response = await axios.get('/sales')
        console.log('Resposta da API:', response)
        
        if (response.data?.data?.data) {
          // Acessando o array de vendas dentro do objeto paginado
          sales.value = response.data.data.data
          console.log('Vendas carregadas:', sales.value)
        } else {
          error.value = 'Formato de resposta inválido da API'
          console.error('Resposta inesperada:', response.data)
        }
      } catch (err) {
        console.error('Erro detalhado:', {
          message: err.message,
          response: err.response?.data,
          status: err.response?.status,
          config: {
            url: err.config?.url,
            method: err.config?.method,
            headers: err.config?.headers
          }
        })
        error.value = err.response?.data?.message || 'Erro ao carregar as vendas. Verifique o console para mais detalhes.'
      } finally {
        loading.value = false
      }
    }

    // Filtrar vendas
    const filteredSales = computed(() => {
      return sales.value.filter(sale => {
        const matchesSearch = searchQuery.value === '' ||
          sale.id.toString().includes(searchQuery.value) ||
          sale.customer?.name.toLowerCase().includes(searchQuery.value.toLowerCase())
        
        const matchesStatus = statusFilter.value === '' || sale.status === statusFilter.value
        
        return matchesSearch && matchesStatus
      })
    })

    // Formatadores e Cálculos
    function calculateTotal(items) {
      if (!items) return '0.00'
      const total = items.reduce((sum, item) => {
        return sum + (parseFloat(item.unit_price) * parseInt(item.quantity))
      }, 0)
      return formatPrice(total)
    }

    function formatDate(dateString) {
      if (!dateString) return ''
      const date = new Date(dateString)
      return date.toLocaleDateString('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      })
    }

    function formatPrice(value) {
      if (!value || isNaN(value)) return '0.00'
      return Number(value).toLocaleString('pt-BR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      })
    }

    function formatPaymentMethod(method) {
      const methods = {
        cash: 'Dinheiro',
        credit_card: 'Cartão de Crédito',
        debit_card: 'Cartão de Débito',
        pix: 'PIX'
      }
      return methods[method] || method
    }

    function formatStatus(status) {
      const statuses = {
        pending: 'Pendente',
        completed: 'Concluído',
        cancelled: 'Cancelado'
      }
      return statuses[status] || status
    }

    // Ações
    function showSaleDetails(sale) {
      selectedSale.value = sale
    }

    async function printSaleDetails() {
      try {
        // Usar o modelo de PDF que criamos
        window.open(`${axios.defaults.baseURL}/sales/${selectedSale.value.id}/pdf`, '_blank')
      } catch (error) {
        console.error('Erro ao gerar PDF:', error)
        alert('Erro ao gerar o PDF do pedido')
      }
    }

    // Carregar dados iniciais
    onMounted(() => {
      loadSales()
    })

    return {
      sales,
      loading,
      error,
      searchQuery,
      statusFilter,
      selectedSale,
      filteredSales,
      formatDate,
      formatPrice,
      formatPaymentMethod,
      formatStatus,
      calculateTotal,
      showSaleDetails,
      printSaleDetails
    }
  }
}
</script>
