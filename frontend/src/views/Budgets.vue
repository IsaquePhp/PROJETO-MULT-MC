<template>
  <div class="p-6">
    <header class="mb-8">
      <h1 class="text-2xl font-bold text-gray-900">Orçamentos</h1>
      <p class="mt-2 text-sm text-gray-700">Gerencie todos os orçamentos</p>
    </header>

    <div class="bg-white rounded-lg shadow">
      <!-- Filtros e Ações -->
      <div class="p-4 border-b border-gray-200">
        <div class="flex flex-wrap gap-4">
          <div class="flex-1 min-w-[200px]">
            <input
              type="text"
              placeholder="Buscar orçamento..."
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
          </div>
          <div>
            <select class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
              <option value="">Status</option>
              <option value="draft">Rascunho</option>
              <option value="sent">Enviado</option>
              <option value="approved">Aprovado</option>
              <option value="rejected">Rejeitado</option>
            </select>
          </div>
          <button class="px-4 py-2 text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
            Novo Orçamento
          </button>
        </div>
      </div>

      <!-- Lista de Orçamentos -->
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Número</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor Total</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Validade</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="budget in budgets" :key="budget.id">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">#{{ budget.id }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ budget.customer }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ budget.total }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="getStatusClass(budget.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                  {{ budget.status }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ budget.date }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ budget.validUntil }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <button class="text-indigo-600 hover:text-indigo-900 mr-2">Ver PDF</button>
                <button class="text-indigo-600 hover:text-indigo-900 mr-2">Editar</button>
                <button class="text-indigo-600 hover:text-indigo-900">Enviar</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Paginação -->
      <div class="px-6 py-4 border-t border-gray-200">
        <div class="flex items-center justify-between">
          <div class="text-sm text-gray-700">
            Mostrando <span class="font-medium">1</span> até <span class="font-medium">10</span> de <span class="font-medium">20</span> resultados
          </div>
          <div class="flex space-x-2">
            <button class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
              Anterior
            </button>
            <button class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
              Próxima
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const budgets = ref([
  {
    id: 1,
    customer: 'João Silva',
    total: 'R$ 1.500,00',
    status: 'Enviado',
    date: '2024-03-15',
    validUntil: '2024-04-15'
  },
  {
    id: 2,
    customer: 'Maria Santos',
    total: 'R$ 2.750,00',
    status: 'Aprovado',
    date: '2024-03-16',
    validUntil: '2024-04-16'
  }
])

const getStatusClass = (status) => {
  const classes = {
    'Rascunho': 'bg-gray-100 text-gray-800',
    'Enviado': 'bg-blue-100 text-blue-800',
    'Aprovado': 'bg-green-100 text-green-800',
    'Rejeitado': 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}
</script>
