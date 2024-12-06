<template>
  <div class="p-6">
    <header class="mb-8">
      <h1 class="text-2xl font-bold text-gray-900">Vendas por Loja</h1>
      <p class="mt-2 text-sm text-gray-700">Acompanhamento de vendas por unidade</p>
    </header>

    <!-- Resumo de Vendas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex items-center justify-between mb-2">
          <h3 class="font-medium text-gray-900">Total de Vendas</h3>
          <span class="text-sm text-green-600">+12.5%</span>
        </div>
        <p class="text-2xl font-semibold text-gray-900">R$ 158.250,00</p>
        <p class="text-sm text-gray-500 mt-1">Últimos 30 dias</p>
      </div>

      <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex items-center justify-between mb-2">
          <h3 class="font-medium text-gray-900">Ticket Médio</h3>
          <span class="text-sm text-green-600">+5.2%</span>
        </div>
        <p class="text-2xl font-semibold text-gray-900">R$ 285,00</p>
        <p class="text-sm text-gray-500 mt-1">Por venda</p>
      </div>

      <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex items-center justify-between mb-2">
          <h3 class="font-medium text-gray-900">Total de Pedidos</h3>
          <span class="text-sm text-green-600">+8.3%</span>
        </div>
        <p class="text-2xl font-semibold text-gray-900">555</p>
        <p class="text-sm text-gray-500 mt-1">Últimos 30 dias</p>
      </div>

      <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex items-center justify-between mb-2">
          <h3 class="font-medium text-gray-900">Meta Atingida</h3>
          <span class="text-sm text-yellow-600">85%</span>
        </div>
        <p class="text-2xl font-semibold text-gray-900">R$ 158.250/185.000</p>
        <p class="text-sm text-gray-500 mt-1">Meta mensal</p>
      </div>
    </div>

    <!-- Filtros e Ações -->
    <div class="bg-white rounded-lg shadow">
      <div class="p-4 border-b border-gray-200">
        <div class="flex flex-wrap gap-4">
          <div>
            <select class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
              <option value="">Todas as Lojas</option>
              <option v-for="store in stores" :key="store.id" :value="store.id">
                {{ store.name }}
              </option>
            </select>
          </div>
          <div>
            <select class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
              <option value="today">Hoje</option>
              <option value="yesterday">Ontem</option>
              <option value="week">Última Semana</option>
              <option value="month">Último Mês</option>
              <option value="custom">Período Personalizado</option>
            </select>
          </div>
          <button class="px-4 py-2 text-indigo-600 bg-indigo-50 rounded-lg hover:bg-indigo-100">
            Exportar Relatório
          </button>
        </div>
      </div>

      <!-- Tabela de Vendas por Loja -->
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Loja</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Vendas</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nº Pedidos</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ticket Médio</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Meta</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="store in stores" :key="store.id">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="h-10 w-10 flex-shrink-0">
                    <span class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-500">
                      {{ store.name.charAt(0) }}
                    </span>
                  </div>
                  <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900">{{ store.name }}</div>
                    <div class="text-sm text-gray-500">{{ store.location }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">{{ store.totalSales }}</div>
                <div class="text-sm text-green-600">{{ store.growth }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ store.orders }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ store.averageTicket }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <span class="text-sm text-gray-900 mr-2">{{ store.goalPercentage }}</span>
                  <div class="w-24 bg-gray-200 rounded-full h-2">
                    <div 
                      class="bg-indigo-600 h-2 rounded-full" 
                      :style="{ width: store.goalPercentage }"
                    ></div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <button class="text-indigo-600 hover:text-indigo-900">Detalhes</button>
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

const stores = ref([
  {
    id: 1,
    name: 'Loja Centro',
    location: 'Centro Comercial',
    totalSales: 'R$ 58.250,00',
    growth: '+15.2%',
    orders: '215',
    averageTicket: 'R$ 270,93',
    goalPercentage: '92%'
  },
  {
    id: 2,
    name: 'Loja Shopping',
    location: 'Shopping Plaza',
    totalSales: 'R$ 45.800,00',
    growth: '+8.5%',
    orders: '180',
    averageTicket: 'R$ 254,44',
    goalPercentage: '85%'
  },
  {
    id: 3,
    name: 'Loja Norte',
    location: 'Zona Norte',
    totalSales: 'R$ 54.200,00',
    growth: '+12.8%',
    orders: '160',
    averageTicket: 'R$ 338,75',
    goalPercentage: '78%'
  }
])
</script>
