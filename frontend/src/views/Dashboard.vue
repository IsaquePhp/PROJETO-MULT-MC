<template>
  <div class="dashboard p-6">
    <!-- Cabeçalho com Filtros -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-semibold text-gray-900">Dashboard</h1>
      
      <div class="flex items-center gap-4">
        <div class="flex items-center">
          <span class="text-sm text-gray-500 mr-2">De:</span>
          <input 
            type="date" 
            v-model="filters.startDate" 
            @change="loadDashboardData" 
            class="date-filter"
          >
        </div>
        <div class="flex items-center">
          <span class="text-sm text-gray-500 mr-2">Até:</span>
          <input 
            type="date" 
            v-model="filters.endDate" 
            @change="loadDashboardData" 
            class="date-filter"
          >
        </div>
        <button 
          @click="loadDashboardData" 
          :disabled="loading"
          class="filter-button"
        >
          <span v-if="loading">Carregando...</span>
          <span v-else>Filtrar</span>
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center items-center h-64">
      <div class="loading-spinner"></div>
    </div>

    <div v-else>
      <!-- Cards de Métricas -->
      <div class="grid grid-cols-1 gap-6 mb-6 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total de Vendas -->
        <div class="metric-card">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-medium text-gray-500">Total de Vendas</h3>
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
            </svg>
          </div>
          <div class="flex items-baseline">
            <p class="text-2xl font-semibold text-gray-900">{{ metrics.current.totalSales }}</p>
            <p class="ml-2 text-sm font-medium" :class="metrics.growth.sales >= 0 ? 'text-green-600' : 'text-red-600'">
              <span>{{ metrics.growth.sales >= 0 ? '+' : '' }}{{ metrics.growth.sales }}%</span>
            </p>
          </div>
        </div>

        <!-- Receita Total -->
        <div class="metric-card">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-medium text-gray-500">Receita Total</h3>
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div class="flex items-baseline">
            <p class="text-2xl font-semibold text-gray-900">{{ formatPrice(metrics.current.totalRevenue) }}</p>
            <p class="ml-2 text-sm font-medium" :class="metrics.growth.revenue >= 0 ? 'text-green-600' : 'text-red-600'">
              <span>{{ metrics.growth.revenue >= 0 ? '+' : '' }}{{ metrics.growth.revenue }}%</span>
            </p>
          </div>
        </div>

        <!-- Ticket Médio -->
        <div class="metric-card">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-medium text-gray-500">Ticket Médio</h3>
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v4m-6-4v4m6-11v-4m-6 4v-4" />
            </svg>
          </div>
          <div class="flex items-baseline">
            <p class="text-2xl font-semibold text-gray-900">{{ formatPrice(metrics.current.averageTicket) }}</p>
            <p class="ml-2 text-sm font-medium" :class="metrics.growth.averageTicket >= 0 ? 'text-green-600' : 'text-red-600'">
              <span>{{ metrics.growth.averageTicket >= 0 ? '+' : '' }}{{ metrics.growth.averageTicket }}%</span>
            </p>
          </div>
        </div>

        <!-- Média Diária -->
        <div class="metric-card">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-medium text-gray-500">Média Diária</h3>
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
          </div>
          <div class="flex items-baseline">
            <p class="text-2xl font-semibold text-gray-900">{{ Math.round(metrics.current.averageDailySales) }}</p>
            <p class="ml-2 text-sm text-gray-500">vendas/dia</p>
          </div>
        </div>
      </div>

      <!-- Gráficos -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Gráfico de Vendas -->
        <div class="chart-container">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Vendas por Dia</h3>
          <canvas ref="salesChart"></canvas>
        </div>

        <!-- Gráfico de Status -->
        <div class="chart-container">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Vendas por Status</h3>
          <canvas ref="statusChart"></canvas>
        </div>
      </div>

      <!-- Rankings -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Produtos -->
        <div class="table-container">
          <h3 class="text-lg font-medium text-gray-900 p-6 border-b">Produtos Mais Vendidos</h3>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="table-header">Produto</th>
                  <th class="table-header text-right">Quantidade</th>
                  <th class="table-header text-right">Total</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="product in charts.topProducts" :key="product.name">
                  <td class="table-cell">{{ product.name }}</td>
                  <td class="table-cell text-right">{{ product.total_quantity }}</td>
                  <td class="table-cell text-right">{{ formatPrice(product.total_revenue) }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Top Clientes -->
        <div class="table-container">
          <h3 class="text-lg font-medium text-gray-900 p-6 border-b">Melhores Clientes</h3>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="table-header">Cliente</th>
                  <th class="table-header text-right">Compras</th>
                  <th class="table-header text-right">Total</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="client in charts.topClients" :key="client.name">
                  <td class="table-cell">{{ client.name }}</td>
                  <td class="table-cell text-right">{{ client.total_purchases }}</td>
                  <td class="table-cell text-right">{{ formatPrice(client.total_spent) }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import Chart from 'chart.js/auto'
import { format, subDays } from 'date-fns'
import { ptBR } from 'date-fns/locale'

export default {
  name: 'Dashboard',
  setup() {
    const loading = ref(false)
    const salesChart = ref(null)
    const statusChart = ref(null)
    let salesChartInstance = null
    let statusChartInstance = null

    const filters = ref({
      startDate: format(subDays(new Date(), 30), 'yyyy-MM-dd'),
      endDate: format(new Date(), 'yyyy-MM-dd')
    })

    const metrics = ref({
      current: {
        totalSales: 0,
        totalRevenue: 0,
        averageTicket: 0,
        averageDailySales: 0
      },
      growth: {
        sales: 0,
        revenue: 0,
        averageTicket: 0
      }
    })

    const charts = ref({
      salesByDay: [],
      salesByStatus: [],
      topProducts: [],
      topClients: []
    })

    const loadDashboardData = async () => {
      try {
        loading.value = true
        const response = await axios.get('/api/dashboard', {
          params: {
            start_date: filters.value.startDate,
            end_date: filters.value.endDate
          }
        })

        const { data } = response.data
        
        // Atualizar métricas
        metrics.value = data.metrics
        charts.value = data.charts

        // Atualizar gráficos
        updateCharts()
      } catch (error) {
        console.error('Erro ao carregar dados do dashboard:', error)
      } finally {
        loading.value = false
      }
    }

    const updateCharts = () => {
      // Destruir instâncias anteriores dos gráficos
      if (salesChartInstance) salesChartInstance.destroy()
      if (statusChartInstance) statusChartInstance.destroy()

      // Gráfico de vendas por dia
      const salesCtx = salesChart.value.getContext('2d')
      salesChartInstance = new Chart(salesCtx, {
        type: 'line',
        data: {
          labels: charts.value.salesByDay.map(item => 
            format(new Date(item.date), 'dd/MM', { locale: ptBR })
          ),
          datasets: [{
            label: 'Vendas',
            data: charts.value.salesByDay.map(item => item.total_sales),
            borderColor: '#4F46E5',
            backgroundColor: 'rgba(79, 70, 229, 0.1)',
            tension: 0.4,
            fill: true
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false
            },
            tooltip: {
              callbacks: {
                label: function(context) {
                  const value = context.raw
                  return `Vendas: ${value}`
                }
              }
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                stepSize: 1
              }
            }
          }
        }
      })

      // Gráfico de vendas por status
      const statusCtx = statusChart.value.getContext('2d')
      const statusColors = {
        'pending': '#F59E0B',    // Amber
        'completed': '#10B981',  // Emerald
        'cancelled': '#EF4444',  // Red
        'processing': '#4F46E5', // Indigo
        'shipped': '#6B7280'     // Gray
      }

      statusChartInstance = new Chart(statusCtx, {
        type: 'doughnut',
        data: {
          labels: charts.value.salesByStatus.map(item => {
            const statusLabels = {
              'pending': 'Pendente',
              'completed': 'Concluída',
              'cancelled': 'Cancelada',
              'processing': 'Em Processamento',
              'shipped': 'Enviada'
            }
            return statusLabels[item.sale_status] || item.sale_status
          }),
          datasets: [{
            data: charts.value.salesByStatus.map(item => item.total),
            backgroundColor: charts.value.salesByStatus.map(item => statusColors[item.sale_status] || '#6B7280')
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'bottom'
            }
          }
        }
      })
    }

    const formatPrice = (value) => {
      return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
      }).format(value)
    }

    onMounted(() => {
      loadDashboardData()
    })

    return {
      loading,
      filters,
      metrics,
      charts,
      salesChart,
      statusChart,
      loadDashboardData,
      formatPrice
    }
  }
}
</script>

<style>
.dashboard {
  @apply bg-gray-100 min-h-screen;
}

.metric-card {
  @apply bg-white rounded-lg shadow p-6;
  transition: transform 0.2s ease-in-out;
}

.metric-card:hover {
  transform: translateY(-2px);
}

.chart-container {
  @apply bg-white rounded-lg shadow p-6;
  height: 400px;
}

.table-container {
  @apply bg-white rounded-lg shadow overflow-hidden;
}

.table-header {
  @apply bg-gray-50 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider;
}

.table-cell {
  @apply px-6 py-4 whitespace-nowrap text-sm text-gray-900;
}

.loading-spinner {
  @apply animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600;
}

.date-filter {
  @apply px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500;
}

.filter-button {
  @apply px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50;
}
</style>
