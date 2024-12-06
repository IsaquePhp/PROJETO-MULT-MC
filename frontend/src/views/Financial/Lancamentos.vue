<template>
  <div class="p-6">
    <header class="mb-8">
      <h1 class="text-2xl font-bold text-gray-900">LANÇAMENTOS</h1>
      <p class="mt-2 text-sm text-gray-700">Gerencie todos os lançamentos do sistema</p>
    </header>

    <!-- Tabela de Lançamentos -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
      <!-- Cabeçalho da Tabela -->
      <div class="p-6 border-b border-gray-200">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
          <div class="flex-1 min-w-0">
            <h2 class="text-lg font-medium text-gray-900">Lista de Lançamentos</h2>
          </div>
          <div class="mt-4 md:mt-0">
            <button 
              @click="openNewLancamentoModal"
              class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              Novo Lançamento
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
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descrição</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="lancamento in lancamentos" :key="lancamento.id">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">#{{ lancamento.id }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(lancamento.date) }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ lancamento.description }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ formatCurrency(lancamento.value) }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span 
                  class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                  :class="{
                    'bg-green-100 text-green-800': lancamento.type === 'entrada',
                    'bg-red-100 text-red-800': lancamento.type === 'saida'
                  }"
                >
                  {{ lancamento.type === 'entrada' ? 'Entrada' : 'Saída' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span 
                  class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                  :class="{
                    'bg-green-100 text-green-800': lancamento.status === 'confirmado',
                    'bg-yellow-100 text-yellow-800': lancamento.status === 'pendente',
                    'bg-red-100 text-red-800': lancamento.status === 'cancelado'
                  }"
                >
                  {{ lancamento.status }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <button 
                  @click="viewLancamento(lancamento)"
                  class="text-indigo-600 hover:text-indigo-900 mr-2"
                >
                  Ver Detalhes
                </button>
                <div class="inline-flex" v-if="lancamento.status === 'pendente'">
                  <button 
                    @click="updateLancamentoStatus(lancamento.id, 'confirmado')"
                    class="text-green-600 hover:text-green-900 mr-2"
                  >
                    Confirmar
                  </button>
                  <button 
                    @click="updateLancamentoStatus(lancamento.id, 'cancelado')"
                    class="text-red-600 hover:text-red-900"
                  >
                    Cancelar
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
            {{ selectedLancamento ? 'Detalhes do Lançamento' : 'Novo Lançamento' }}
          </h3>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-500">
            <span class="sr-only">Fechar</span>
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <div class="space-y-4">
          <!-- View Lancamento Details -->
          <template v-if="selectedLancamento">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">ID</label>
                <p class="mt-1 text-sm text-gray-900">#{{ selectedLancamento.id }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Data</label>
                <p class="mt-1 text-sm text-gray-900">{{ formatDate(selectedLancamento.date) }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Valor</label>
                <p class="mt-1 text-sm text-gray-900">{{ formatCurrency(selectedLancamento.value) }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Tipo</label>
                <p class="mt-1">
                  <span 
                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                    :class="{
                      'bg-green-100 text-green-800': selectedLancamento.type === 'entrada',
                      'bg-red-100 text-red-800': selectedLancamento.type === 'saida'
                    }"
                  >
                    {{ selectedLancamento.type === 'entrada' ? 'Entrada' : 'Saída' }}
                  </span>
                </p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <p class="mt-1">
                  <span 
                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                    :class="{
                      'bg-green-100 text-green-800': selectedLancamento.status === 'confirmado',
                      'bg-yellow-100 text-yellow-800': selectedLancamento.status === 'pendente',
                      'bg-red-100 text-red-800': selectedLancamento.status === 'cancelado'
                    }"
                  >
                    {{ selectedLancamento.status }}
                  </span>
                </p>
              </div>
              <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700">Descrição</label>
                <p class="mt-1 text-sm text-gray-900">{{ selectedLancamento.description }}</p>
              </div>
            </div>
          </template>

          <!-- New Lancamento Form -->
          <template v-else>
            <form @submit.prevent="submitLancamento" class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">Descrição</label>
                <input 
                  v-model="newLancamento.description" 
                  type="text" 
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                  required
                >
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Valor</label>
                <input 
                  v-model="newLancamento.value" 
                  type="number" 
                  step="0.01" 
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                  required
                >
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Tipo</label>
                <select 
                  v-model="newLancamento.type"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                  required
                >
                  <option value="entrada">Entrada</option>
                  <option value="saida">Saída</option>
                </select>
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
const selectedLancamento = ref(null)

// State for new lancamento form
const newLancamento = ref({
  description: '',
  value: '',
  type: 'entrada'
})

// Mock data for lancamentos
const lancamentos = ref([
  {
    id: 1,
    date: new Date(),
    description: 'Venda de produtos',
    value: 1500.00,
    type: 'entrada',
    status: 'confirmado'
  },
  {
    id: 2,
    date: new Date(Date.now() - 86400000), // Yesterday
    description: 'Pagamento de fornecedor',
    value: 800.00,
    type: 'saida',
    status: 'pendente'
  },
  {
    id: 3,
    date: new Date(Date.now() - 172800000), // 2 days ago
    description: 'Venda de serviços',
    value: 2000.00,
    type: 'entrada',
    status: 'pendente'
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

// Format currency function
const formatCurrency = (value) => {
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL'
  }).format(value)
}

// View lancamento details
const viewLancamento = (lancamento) => {
  selectedLancamento.value = lancamento
  showModal.value = true
}

// Open new lancamento modal
const openNewLancamentoModal = () => {
  selectedLancamento.value = null
  showModal.value = true
}

// Close modal
const closeModal = () => {
  showModal.value = false
  selectedLancamento.value = null
  newLancamento.value = {
    description: '',
    value: '',
    type: 'entrada'
  }
}

// Submit new lancamento
const submitLancamento = async () => {
  try {
    // TODO: Implement API call
    // const response = await axios.post('/api/lancamentos', newLancamento.value)
    
    // For now, add locally
    lancamentos.value.push({
      id: lancamentos.value.length + 1,
      date: new Date(),
      description: newLancamento.value.description,
      value: parseFloat(newLancamento.value.value),
      type: newLancamento.value.type,
      status: 'pendente'
    })
    
    closeModal()
  } catch (error) {
    console.error('Error creating lancamento:', error)
  }
}

// Update lancamento status
const updateLancamentoStatus = async (lancamentoId, newStatus) => {
  try {
    // TODO: Implement API call
    // await axios.patch(`/api/lancamentos/${lancamentoId}`, { status: newStatus })
    
    // For now, update locally
    const lancamentoToUpdate = lancamentos.value.find(l => l.id === lancamentoId)
    if (lancamentoToUpdate) {
      lancamentoToUpdate.status = newStatus
    }
  } catch (error) {
    console.error('Error updating lancamento status:', error)
  }
}
</script>
