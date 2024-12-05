<template>
  <div class="p-6">
    <div class="mb-6 flex justify-between items-center">
      <h1 class="text-2xl font-semibold text-gray-900">Dashboard</h1>
      
      <div class="flex items-center space-x-4">
        <div class="flex items-center space-x-2">
          <button class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
            <span>Mensal</span>
            <svg class="w-5 h-5 ml-1 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
          </button>
          <button class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
            Filtrar
          </button>
          <button class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
            Exportar
          </button>
        </div>
      </div>
    </div>

    <!-- Cards de Métricas -->
    <div class="grid grid-cols-1 gap-6 mb-6 sm:grid-cols-2 lg:grid-cols-3">
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
          <p class="text-2xl font-semibold text-gray-900">R$ {{ formatPrice(totalRevenue) }}</p>
          <p class="ml-2 text-sm font-medium text-green-600">
            <span>+{{ revenueGrowth }}%</span>
          </p>
        </div>
      </div>

      <!-- Taxa de Rejeição -->
      <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-sm font-medium text-gray-500">Taxa de Rejeição</h3>
          <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <div class="flex items-baseline">
          <p class="text-2xl font-semibold text-gray-900">{{ bounceRate }}%</p>
          <p class="ml-2 text-sm font-medium text-red-600">
            <span>{{ bounceRateChange }}%</span>
          </p>
        </div>
      </div>
    </div>

    <!-- Gráfico de Vendas -->
    <div class="bg-white p-6 rounded-lg shadow mb-6">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-medium text-gray-900">Visão Geral de Vendas</h3>
        <div class="flex items-center space-x-2">
          <button class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
            Filtrar
          </button>
          <button class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
            Ordenar
          </button>
        </div>
      </div>
      <div class="relative" style="height: 300px;">
        <!-- Aqui irá o componente do gráfico -->
        <canvas ref="salesChart"></canvas>
      </div>
    </div>

    <!-- Grid Inferior -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
      <!-- Distribuição de Vendas -->
      <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-medium text-gray-900">Distribuição de Vendas</h3>
          <button class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
            Mensal
          </button>
        </div>
        <div class="relative" style="height: 200px;">
          <!-- Aqui irá o componente do gráfico de pizza -->
          <canvas ref="distributionChart"></canvas>
        </div>
      </div>

      <!-- Lista de Integrações -->
      <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-medium text-gray-900">Lista de Integrações</h3>
          <a href="#" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">Ver Todos</a>
        </div>
        <div class="space-y-4">
          <div v-for="integration in integrations" :key="integration.id" class="flex items-center justify-between">
            <div class="flex items-center">
              <img :src="integration.icon" :alt="integration.name" class="w-8 h-8 rounded-lg">
              <div class="ml-3">
                <p class="text-sm font-medium text-gray-900">{{ integration.name }}</p>
                <p class="text-sm text-gray-500">{{ integration.type }}</p>
              </div>
            </div>
            <div class="flex items-center space-x-4">
              <div class="w-24 bg-gray-200 rounded-full h-2">
                <div class="bg-indigo-600 h-2 rounded-full" :style="{ width: integration.rate + '%' }"></div>
              </div>
              <span class="text-sm font-medium text-gray-900">R$ {{ formatPrice(integration.profit) }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Chart from 'chart.js/auto'
import axios from 'axios'

// Importando os ícones
import stripeIcon from '../assets/icons/stripe.svg'
import zapierIcon from '../assets/icons/zapier.svg'
import shopifyIcon from '../assets/icons/shopify.svg'

// Dados do dashboard
const pageViews = ref(0)
const pageViewsGrowth = ref(0)
const totalRevenue = ref(0)
const revenueGrowth = ref(0)
const bounceRate = ref(0)
const bounceRateChange = ref(0)

const integrations = ref([])
const salesChart = ref(null)
const distributionChart = ref(null)

// Função para formatar preços
function formatPrice(value) {
  return Number(value).toFixed(2)
}

// Função para carregar dados do dashboard
async function loadDashboardData() {
  try {
    const token = localStorage.getItem('token')
    const response = await axios.get('http://localhost:8000/api/dashboard', {
      headers: { Authorization: `Bearer ${token}` }
    })

    const data = response.data

    // Atualiza as métricas
    pageViews.value = data.metrics.pageViews
    pageViewsGrowth.value = data.metrics.pageViewsGrowth
    totalRevenue.value = data.metrics.totalRevenue
    revenueGrowth.value = data.metrics.revenueGrowth
    bounceRate.value = data.metrics.bounceRate
    bounceRateChange.value = data.metrics.bounceRateChange

    // Atualiza as integrações com os ícones corretos
    integrations.value = data.integrations.map(integration => ({
      ...integration,
      icon: getIntegrationIcon(integration.name)
    }))

    // Atualiza os gráficos
    updateCharts(data.salesData, data.salesDistribution)
  } catch (error) {
    console.error('Erro ao carregar dados do dashboard:', error)
  }
}

// Função para obter o ícone correto para cada integração
function getIntegrationIcon(name) {
  const icons = {
    'Stripe': stripeIcon,
    'Zapier': zapierIcon,
    'Shopify': shopifyIcon
  }
  return icons[name] || ''
}

// Função para atualizar os gráficos com dados reais
function updateCharts(salesData, salesDistribution) {
  // Configuração do gráfico de vendas
  const salesCtx = salesChart.value.getContext('2d')
  const months = Object.keys(salesData).sort()
  const datasets = ['BR', 'US', 'CN'].map(country => ({
    label: country,
    data: months.map(month => {
      const monthData = salesData[month] || []
      const countryData = monthData.find(d => d.country === country)
      return countryData ? countryData.total : 0
    }),
    borderColor: getCountryColor(country),
    tension: 0.4
  }))

  new Chart(salesCtx, {
    type: 'line',
    data: {
      labels: months.map(formatMonth),
      datasets
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

  // Configuração do gráfico de distribuição
  const distributionCtx = distributionChart.value.getContext('2d')
  new Chart(distributionCtx, {
    type: 'doughnut',
    data: {
      labels: ['Website', 'Mobile App', 'Outros'],
      datasets: [{
        data: [
          salesDistribution.website,
          salesDistribution.mobile_app,
          salesDistribution.others
        ],
        backgroundColor: ['#6366F1', '#10B981', '#6B7280']
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

// Função auxiliar para formatar o mês
function formatMonth(monthStr) {
  const [year, month] = monthStr.split('-')
  const date = new Date(year, month - 1)
  return date.toLocaleString('pt-BR', { month: 'short' })
}

// Função para obter a cor do país
function getCountryColor(country) {
  const colors = {
    'BR': '#6366F1', // Indigo
    'US': '#10B981', // Verde
    'CN': '#6B7280'  // Cinza
  }
  return colors[country] || '#6B7280'
}

onMounted(() => {
  loadDashboardData()
})
</script>
