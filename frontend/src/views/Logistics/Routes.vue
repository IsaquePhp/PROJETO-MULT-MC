<template>
  <div class="p-6">
    <header class="mb-8">
      <h1 class="text-2xl font-bold text-gray-900">Rotas de Entrega</h1>
      <p class="mt-2 text-sm text-gray-700">Gerencie as rotas de entrega dos pedidos</p>
    </header>

    <!-- Filtros e Ações -->
    <div class="mb-6 flex justify-between items-center">
      <div class="flex gap-4">
        <div class="relative">
          <input
            type="text"
            placeholder="Buscar rota..."
            class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            v-model="searchQuery"
          />
          <span class="absolute left-3 top-2.5 text-gray-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </span>
        </div>
        <select
          v-model="statusFilter"
          class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        >
          <option value="">Todos os Status</option>
          <option value="planejada">Planejada</option>
          <option value="em_andamento">Em Andamento</option>
          <option value="concluida">Concluída</option>
          <option value="cancelada">Cancelada</option>
        </select>
      </div>
      <button
        @click="openNewRouteModal"
        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
      >
        Nova Rota
      </button>
    </div>

    <!-- Lista de Rotas -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Motorista</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Veículo</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Entregas</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="route in filteredRoutes" :key="route.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ route.id }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ route.driver }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ route.vehicle }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span :class="getStatusClass(route.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                {{ getStatusLabel(route.status) }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ route.deliveries }} entregas</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(route.date) }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button
                @click="viewRoute(route)"
                class="text-blue-600 hover:text-blue-900 mr-3"
              >
                Ver
              </button>
              <button
                @click="editRoute(route)"
                class="text-green-600 hover:text-green-900 mr-3"
              >
                Editar
              </button>
              <button
                @click="deleteRoute(route)"
                class="text-red-600 hover:text-red-900"
              >
                Excluir
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

// Estado
const searchQuery = ref('')
const statusFilter = ref('')
const routes = ref([
  {
    id: 1,
    driver: 'João Silva',
    vehicle: 'Fiorino - ABC-1234',
    status: 'em_andamento',
    deliveries: 8,
    date: '2024-01-19T10:00:00'
  },
  {
    id: 2,
    driver: 'Maria Santos',
    vehicle: 'Van - DEF-5678',
    status: 'planejada',
    deliveries: 12,
    date: '2024-01-19T14:00:00'
  },
  // Adicione mais rotas conforme necessário
])

// Computed Properties
const filteredRoutes = computed(() => {
  return routes.value.filter(route => {
    const matchesSearch = route.driver.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                         route.vehicle.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchesStatus = !statusFilter.value || route.status === statusFilter.value
    return matchesSearch && matchesStatus
  })
})

// Métodos
const getStatusClass = (status) => {
  const classes = {
    planejada: 'bg-yellow-100 text-yellow-800',
    em_andamento: 'bg-blue-100 text-blue-800',
    concluida: 'bg-green-100 text-green-800',
    cancelada: 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getStatusLabel = (status) => {
  const labels = {
    planejada: 'Planejada',
    em_andamento: 'Em Andamento',
    concluida: 'Concluída',
    cancelada: 'Cancelada'
  }
  return labels[status] || status
}

const formatDate = (date) => {
  return new Date(date).toLocaleString('pt-BR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const openNewRouteModal = () => {
  // Implementar lógica para abrir modal de nova rota
  console.log('Abrir modal de nova rota')
}

const viewRoute = (route) => {
  // Implementar lógica para visualizar rota
  console.log('Visualizar rota:', route)
}

const editRoute = (route) => {
  // Implementar lógica para editar rota
  console.log('Editar rota:', route)
}

const deleteRoute = (route) => {
  // Implementar lógica para excluir rota
  console.log('Excluir rota:', route)
}
</script>
