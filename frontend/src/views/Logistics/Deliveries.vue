<template>
  <div class="p-6">
    <header class="mb-8">
      <h1 class="text-2xl font-bold text-gray-900">Entregas</h1>
      <p class="mt-2 text-sm text-gray-700">Gerencie todas as entregas</p>
    </header>

    <div class="bg-white rounded-lg shadow">
      <!-- Filtros -->
      <div class="p-4 border-b border-gray-200">
        <div class="flex flex-wrap gap-4">
          <div class="flex-1 min-w-[200px]">
            <input
              type="text"
              placeholder="Buscar entrega..."
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
          </div>
          <div>
            <select class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
              <option value="">Status</option>
              <option value="pending">Pendente</option>
              <option value="in_progress">Em Andamento</option>
              <option value="completed">Concluída</option>
            </select>
          </div>
          <button class="px-4 py-2 text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
            Nova Entrega
          </button>
        </div>
      </div>

      <!-- Lista de Entregas -->
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Endereço</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="delivery in deliveries" :key="delivery.id">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">#{{ delivery.id }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ delivery.customer }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ delivery.address }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="getStatusClass(delivery.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                  {{ delivery.status }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ delivery.date }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <button class="text-indigo-600 hover:text-indigo-900">Editar</button>
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

const deliveries = ref([
  {
    id: 1,
    customer: 'João Silva',
    address: 'Rua A, 123',
    status: 'Pendente',
    date: '2024-03-15'
  },
  {
    id: 2,
    customer: 'Maria Santos',
    address: 'Av B, 456',
    status: 'Em Andamento',
    date: '2024-03-16'
  }
])

const getStatusClass = (status) => {
  const classes = {
    'Pendente': 'bg-yellow-100 text-yellow-800',
    'Em Andamento': 'bg-blue-100 text-blue-800',
    'Concluída': 'bg-green-100 text-green-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}
</script>
