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
              v-model="searchQuery"
              placeholder="Buscar entrega..."
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
          </div>
          <div>
            <select 
              v-model="statusFilter"
              class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
              <option value="">Status</option>
              <option value="pending">Pendente</option>
              <option value="in_progress">Em Andamento</option>
              <option value="completed">Concluída</option>
            </select>
          </div>
          <button 
            @click="createDelivery"
            class="px-4 py-2 text-white bg-indigo-600 rounded-lg hover:bg-indigo-700"
          >
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
            <tr v-for="delivery in filteredDeliveries" :key="delivery.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">#{{ delivery.id }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ delivery.customer }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ delivery.address }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="getStatusClass(delivery.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                  {{ getStatusLabel(delivery.status) }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(delivery.date) }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <button
                  @click="viewDelivery(delivery)"
                  class="text-blue-600 hover:text-blue-900 mr-3"
                >
                  Ver
                </button>
                <button
                  @click="editDelivery(delivery)"
                  class="text-green-600 hover:text-green-900 mr-3"
                >
                  Editar
                </button>
                <button
                  @click="deleteDelivery(delivery)"
                  class="text-red-600 hover:text-red-900"
                >
                  Excluir
                </button>
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
import { ref, computed } from 'vue'

// Estado
const searchQuery = ref('')
const statusFilter = ref('')
const deliveries = ref([
  {
    id: 1,
    customer: 'João Silva',
    address: 'Rua A, 123 - Centro',
    status: 'pending',
    date: '2024-01-19T10:00:00'
  },
  {
    id: 2,
    customer: 'Maria Santos',
    address: 'Av B, 456 - Jardim',
    status: 'in_progress',
    date: '2024-01-19T14:00:00'
  },
  {
    id: 3,
    customer: 'Pedro Oliveira',
    address: 'Rua C, 789 - Vila',
    status: 'completed',
    date: '2024-01-18T16:00:00'
  }
])

// Computed Properties
const filteredDeliveries = computed(() => {
  return deliveries.value.filter(delivery => {
    const matchesSearch = 
      delivery.customer.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      delivery.address.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchesStatus = !statusFilter.value || delivery.status === statusFilter.value
    return matchesSearch && matchesStatus
  })
})

// Métodos
const getStatusClass = (status) => {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-800',
    in_progress: 'bg-blue-100 text-blue-800',
    completed: 'bg-green-100 text-green-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getStatusLabel = (status) => {
  const labels = {
    pending: 'Pendente',
    in_progress: 'Em Andamento',
    completed: 'Concluída'
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

const createDelivery = () => {
  // Implementar lógica para criar nova entrega
  console.log('Criar nova entrega')
}

const viewDelivery = (delivery) => {
  // Implementar lógica para visualizar entrega
  console.log('Visualizar entrega:', delivery)
}

const editDelivery = (delivery) => {
  // Implementar lógica para editar entrega
  console.log('Editar entrega:', delivery)
}

const deleteDelivery = (delivery) => {
  // Implementar lógica para excluir entrega
  console.log('Excluir entrega:', delivery)
}
</script>
