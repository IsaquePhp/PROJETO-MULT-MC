<template>
  <div class="p-6">
    <header class="mb-8">
      <h1 class="text-2xl font-bold text-gray-900">Centro de Custos</h1>
      <p class="mt-2 text-sm text-gray-700">Gerenciamento de centros de custos e despesas</p>
    </header>

    <!-- Resumo por Centro de Custo -->
    <div class="bg-white rounded-lg shadow mb-8">
      <div class="p-4">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Despesas por Centro de Custo</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <div v-for="center in costCenters" :key="center.id" class="p-4 border rounded-lg">
            <div class="flex items-center justify-between mb-2">
              <h3 class="font-medium text-gray-900">{{ center.name }}</h3>
              <span :class="getBudgetStatusClass(center.status)" class="px-2 py-1 text-xs rounded-full">
                {{ center.status }}
              </span>
            </div>
            <p class="text-2xl font-semibold text-gray-900">{{ center.total }}</p>
            <div class="mt-2">
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-indigo-600 h-2 rounded-full" :style="{ width: center.percentage }"></div>
              </div>
              <p class="text-sm text-gray-600 mt-1">{{ center.percentage }} do orçamento</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Filtros e Ações -->
    <div class="bg-white rounded-lg shadow">
      <div class="p-4 border-b border-gray-200">
        <div class="flex flex-wrap gap-4">
          <div class="flex-1 min-w-[200px]">
            <input
              type="text"
              placeholder="Buscar centro de custo..."
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
          </div>
          <div>
            <select class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
              <option value="">Status</option>
              <option value="dentro">Dentro do Orçamento</option>
              <option value="proximo">Próximo do Limite</option>
              <option value="excedido">Orçamento Excedido</option>
            </select>
          </div>
          <button class="px-4 py-2 text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
            Novo Centro de Custo
          </button>
        </div>
      </div>

      <!-- Lista de Lançamentos -->
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Centro de Custo</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descrição</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Responsável</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="entry in entries" :key="entry.id">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ entry.date }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ entry.costCenter }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ entry.description }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">{{ entry.amount }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ entry.responsible }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <button class="text-indigo-600 hover:text-indigo-900 mr-4">Editar</button>
                <button class="text-red-600 hover:text-red-900">Excluir</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const costCenters = ref([
  {
    id: 1,
    name: 'Marketing',
    total: 'R$ 12.500,00',
    percentage: '75%',
    status: 'Dentro do Orçamento'
  },
  {
    id: 2,
    name: 'Operacional',
    total: 'R$ 28.900,00',
    percentage: '92%',
    status: 'Próximo do Limite'
  },
  {
    id: 3,
    name: 'Administrativo',
    total: 'R$ 15.800,00',
    percentage: '65%',
    status: 'Dentro do Orçamento'
  },
  {
    id: 4,
    name: 'Vendas',
    total: 'R$ 32.100,00',
    percentage: '105%',
    status: 'Orçamento Excedido'
  }
])

const entries = ref([
  {
    id: 1,
    date: '2024-03-15',
    costCenter: 'Marketing',
    description: 'Campanha Digital',
    amount: 'R$ 2.500,00',
    responsible: 'João Silva'
  },
  {
    id: 2,
    date: '2024-03-15',
    costCenter: 'Operacional',
    description: 'Manutenção Equipamentos',
    amount: 'R$ 1.800,00',
    responsible: 'Maria Santos'
  }
])

const getBudgetStatusClass = (status) => {
  const classes = {
    'Dentro do Orçamento': 'bg-green-100 text-green-800',
    'Próximo do Limite': 'bg-yellow-100 text-yellow-800',
    'Orçamento Excedido': 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}
</script>
