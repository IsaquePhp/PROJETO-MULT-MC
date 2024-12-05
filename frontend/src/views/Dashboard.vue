<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-semibold text-gray-900">Dashboard</h1>
      
      <div class="flex items-center gap-4">
        <div class="flex items-center">
          <span class="text-sm text-gray-500 mr-2">De:</span>
          <input 
            type="date" 
            v-model="filters.startDate" 
            @change="loadDashboardData" 
            class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
          >
        </div>
        <div class="flex items-center">
          <span class="text-sm text-gray-500 mr-2">Até:</span>
          <input 
            type="date" 
            v-model="filters.endDate" 
            @change="loadDashboardData" 
            class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
          >
        </div>
        <button 
          @click="loadDashboardData" 
          class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          Filtrar
        </button>
      </div>
    </div>

    <!-- Cards de Métricas -->
    <div class="grid grid-cols-1 gap-6 mb-6 sm:grid-cols-2 lg:grid-cols-4">
      <!-- Visualizações -->
      <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-sm font-medium text-gray-500">Visualizações</h3>
          <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <div class="flex items-baseline">
          <p class="text-2xl font-semibold text-gray-900">{{ pageViews }}</p>
          <p class="ml-2 text-sm font-medium text-green-600">
            <span>+{{ pageViewsGrowth }}%</span>
          </p>
        </div>
      </div>

      <!-- Receita Total -->
      <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-sm font-medium text-gray-500">Receita Total</h3>
          <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <div class="flex items-baseline">
          <p class="text-2xl font-semibold text-gray-900">{{ formatPrice(totalRevenue) }}</p>
          <p class="ml-2 text-sm font-medium text-green-600">
            <span>+{{ revenueGrowth }}%</span>
          </p>
        </div>
      </div>

      <!-- Despesas -->
      <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-sm font-medium text-gray-500">Despesas</h3>
          <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <div class="flex items-baseline">
          <p class="text-2xl font-semibold text-gray-900">{{ formatPrice(totalExpenses) }}</p>
          <p class="ml-2 text-sm font-medium" :class="expensesGrowth > 0 ? 'text-red-600' : 'text-green-600'">
            <span>{{ expensesGrowth > 0 ? '+' : '' }}{{ expensesGrowth }}%</span>
          </p>
        </div>
      </div>

      <!-- Vendas -->
      <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-sm font-medium text-gray-500">Vendas</h3>
          <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <div class="flex items-baseline">
          <p class="text-2xl font-semibold text-gray-900">{{ totalSales }}</p>
          <p class="ml-2 text-sm font-medium text-green-600">
            <span>+{{ salesGrowth }}%</span>
          </p>
        </div>
      </div>
    </div>

    <!-- Gráfico de Vendas -->
    <div class="bg-white p-6 rounded-lg shadow mb-6">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-medium text-gray-900">Vendas por Período</h3>
      </div>
      <div class="h-80">
        <canvas ref="salesChart"></canvas>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Chart from 'chart.js/auto'

// Referências para os gráficos
const salesChart = ref(null)

// Filtros
const filters = ref({
  startDate: '',
  endDate: ''
})

// Dados do dashboard
const pageViews = ref(0)
const pageViewsGrowth = ref(0)
const totalRevenue = ref(0)
const revenueGrowth = ref(0)
const totalExpenses = ref(0)
const expensesGrowth = ref(0)
const totalSales = ref(0)
const salesGrowth = ref(0)
const fixedExpenses = ref(0)
const variableExpenses = ref(0)

// Função para formatar preços
function formatPrice(value) {
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL'
  }).format(value)
}

// Função para carregar dados do dashboard
async function loadDashboardData() {
  try {
    // Em um ambiente real, você enviaria os filtros para a API
    // const params = new URLSearchParams({
    //   startDate: filters.value.startDate,
    //   endDate: filters.value.endDate
    // })
    // const response = await axios.get(`http://localhost:8000/api/dashboard?${params}`)
    
    // Simulando dados da API
    pageViews.value = 12890
    pageViewsGrowth.value = 12
    totalRevenue.value = 158960.50
    revenueGrowth.value = 8
    totalSales.value = 258
    salesGrowth.value = 15
    totalExpenses.value = 50000
    expensesGrowth.value = 10
    fixedExpenses.value = 20000
    variableExpenses.value = 30000

    // Dados para o gráfico de vendas
    const salesData = {
      labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'],
      values: [30, 45, 35, 50, 40, 60]
    }

    // Atualiza os gráficos
    updateCharts(salesData)
  } catch (error) {
    console.error('Erro ao carregar dados do dashboard:', error)
  }
}

// Função para atualizar os gráficos com dados reais
function updateCharts(salesData) {
  // Gráfico de vendas
  if (salesChart.value) {
    new Chart(salesChart.value, {
      type: 'line',
      data: {
        labels: salesData.labels,
        datasets: [{
          label: 'Vendas',
          data: salesData.values,
          borderColor: 'rgb(79, 70, 229)',
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
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: {
              display: true,
              color: 'rgba(0, 0, 0, 0.1)'
            }
          },
          x: {
            grid: {
              display: false
            }
          }
        }
      }
    })
  }
}

onMounted(() => {
  loadDashboardData()
})
</script>
