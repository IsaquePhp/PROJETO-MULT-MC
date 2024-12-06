<template>
  <div class="p-6">
    <header class="mb-8">
      <h1 class="text-2xl font-bold text-gray-900">DEVOLUÇÃO</h1>
      <p class="mt-2 text-sm text-gray-700">Gerencie todas as devoluções de produtos</p>
    </header>

    <!-- Tabela de Devoluções -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
      <!-- Cabeçalho da Tabela -->
      <div class="p-6 border-b border-gray-200">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
          <div class="flex-1 min-w-0">
            <h2 class="text-lg font-medium text-gray-900">Lista de Devoluções</h2>
          </div>
          <div class="mt-4 md:mt-0">
            <button 
              @click="openNewReturnModal"
              class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              Nova Devolução
            </button>
          </div>
        </div>
      </div>

      <!-- Tabela -->
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produtos</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Motivo</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="return_ in returns" :key="return_.id">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">#{{ return_.id }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(return_.date) }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ return_.customer }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ return_.products }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ return_.reason }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span 
                  class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                  :class="{
                    'bg-green-100 text-green-800': return_.status === 'approved',
                    'bg-yellow-100 text-yellow-800': return_.status === 'pending',
                    'bg-red-100 text-red-800': return_.status === 'rejected'
                  }"
                >
                  {{ return_.status }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <button 
                  @click="viewReturn(return_)"
                  class="text-indigo-600 hover:text-indigo-900 mr-2"
                >
                  Ver Detalhes
                </button>
                <div class="inline-flex" v-if="return_.status === 'pending'">
                  <button 
                    @click="updateReturnStatus(return_.id, 'approved')"
                    class="text-green-600 hover:text-green-900 mr-2"
                  >
                    Aprovar
                  </button>
                  <button 
                    @click="updateReturnStatus(return_.id, 'rejected')"
                    class="text-red-600 hover:text-red-900"
                  >
                    Rejeitar
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center">
      <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-medium text-gray-900">
            {{ selectedReturn ? 'Detalhes da Devolução' : 'Nova Devolução' }}
          </h3>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-500">
            <span class="sr-only">Fechar</span>
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <div class="space-y-4">
          <!-- View Return Details -->
          <template v-if="selectedReturn">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">ID</label>
                <p class="mt-1 text-sm text-gray-900">#{{ selectedReturn.id }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Data</label>
                <p class="mt-1 text-sm text-gray-900">{{ formatDate(selectedReturn.date) }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Cliente</label>
                <p class="mt-1 text-sm text-gray-900">{{ selectedReturn.customer }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <p class="mt-1">
                  <span 
                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                    :class="{
                      'bg-green-100 text-green-800': selectedReturn.status === 'approved',
                      'bg-yellow-100 text-yellow-800': selectedReturn.status === 'pending',
                      'bg-red-100 text-red-800': selectedReturn.status === 'rejected'
                    }"
                  >
                    {{ selectedReturn.status }}
                  </span>
                </p>
              </div>
              <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700">Produtos</label>
                <p class="mt-1 text-sm text-gray-900">{{ selectedReturn.products }}</p>
              </div>
              <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700">Motivo</label>
                <p class="mt-1 text-sm text-gray-900">{{ selectedReturn.reason }}</p>
              </div>
            </div>
          </template>

          <!-- New Return Form -->
          <template v-else>
            <form @submit.prevent class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">Cliente</label>
                <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Produtos</label>
                <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Motivo</label>
                <textarea class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" rows="3"></textarea>
              </div>
              <div class="flex justify-end space-x-3">
                <button 
                  type="button"
                  @click="closeModal"
                  class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                  Cancelar
                </button>
                <button 
                  type="submit"
                  class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                  Salvar
                </button>
              </div>
            </form>
          </template>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'

// State for modal
const showModal = ref(false)
const selectedReturn = ref(null)

// Mock data for returns
const returns = ref([
  {
    id: 1,
    date: new Date(),
    customer: 'João Silva',
    products: 'Camiseta Azul (2x)',
    reason: 'Tamanho incorreto',
    status: 'pending'
  },
  {
    id: 2,
    date: new Date(Date.now() - 86400000), // Yesterday
    customer: 'Maria Santos',
    products: 'Calça Jeans (1x)',
    reason: 'Produto com defeito',
    status: 'approved'
  },
  {
    id: 3,
    date: new Date(Date.now() - 172800000), // 2 days ago
    customer: 'Pedro Oliveira',
    products: 'Tênis Esportivo (1x)',
    reason: 'Cor diferente da foto',
    status: 'rejected'
  }
])

// Format date function
const formatDate = (date) => {
  return new Intl.DateTimeFormat('pt-BR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  }).format(date)
}

// View return details
const viewReturn = (return_) => {
  selectedReturn.value = return_
  showModal.value = true
}

// Open new return modal
const openNewReturnModal = () => {
  selectedReturn.value = null
  showModal.value = true
}

// Close modal
const closeModal = () => {
  showModal.value = false
  selectedReturn.value = null
}

// Update return status
const updateReturnStatus = async (returnId, newStatus) => {
  try {
    // TODO: Implement API call
    // await axios.patch(`/api/returns/${returnId}`, { status: newStatus })
    
    // For now, update locally
    const returnToUpdate = returns.value.find(r => r.id === returnId)
    if (returnToUpdate) {
      returnToUpdate.status = newStatus
    }
  } catch (error) {
    console.error('Error updating return status:', error)
  }
}
</script>
