<template>
  <div class="p-6">
    <header class="mb-8">
      <h1 class="text-2xl font-bold text-gray-900">Relatórios</h1>
      <p class="mt-2 text-sm text-gray-700">Gerencie todos os relatórios do sistema</p>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <!-- Cards de Relatórios -->
      <div v-for="report in reports" :key="report.id" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
        <div class="flex items-center mb-4">
          <div class="p-2 bg-indigo-100 rounded-lg">
            <component :is="report.icon" class="w-6 h-6 text-indigo-600" />
          </div>
          <h3 class="ml-3 text-lg font-medium text-gray-900">{{ report.title }}</h3>
        </div>
        <p class="text-sm text-gray-600 mb-4">{{ report.description }}</p>
        <button 
          @click="generateReport(report.id)"
          class="w-full px-4 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 rounded-lg hover:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          Gerar Relatório
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const reports = ref([
  {
    id: 1,
    title: 'Vendas por Período',
    description: 'Relatório detalhado de vendas por período com gráficos e análises',
    icon: 'ChartBarIcon'
  },
  {
    id: 2,
    title: 'Desempenho de Produtos',
    description: 'Análise de desempenho dos produtos mais vendidos',
    icon: 'ShoppingBagIcon'
  },
  {
    id: 3,
    title: 'Relatório Financeiro',
    description: 'Resumo financeiro com receitas, despesas e lucros',
    icon: 'CurrencyDollarIcon'
  }
])

const generateReport = (reportId) => {
  // Implementar geração de relatório
  console.log(`Gerando relatório ${reportId}`)
}
</script>
