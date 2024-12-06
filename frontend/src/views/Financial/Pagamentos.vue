<template>
  <div class="p-6">
    <header class="mb-8">
      <h1 class="text-2xl font-bold text-gray-900">Pagamentos</h1>
      <p class="mt-2 text-sm text-gray-700">Gerencie todos os pagamentos do sistema</p>
    </header>

    <!-- Tabela de Pagamentos -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
      <!-- Cabeçalho da Tabela -->
      <div class="p-6 border-b border-gray-200">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
          <div class="flex-1 min-w-0">
            <h2 class="text-lg font-medium text-gray-900">Lista de Pagamentos</h2>
          </div>
          <div class="mt-4 md:mt-0">
            <button 
              @click="openNewPagamentoModal"
              class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              Novo Pagamento
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
            <tr v-for="pagamento in pagamentos" :key="pagamento.id">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">#{{ pagamento.id }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(pagamento.date) }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ pagamento.description }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ formatCurrency(pagamento.value) }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span 
                  class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                  :class="{
                    'bg-green-100 text-green-800': pagamento.type === 'recebido',
                    'bg-red-100 text-red-800': pagamento.type === 'pago'
                  }"
                >
                  {{ pagamento.type === 'recebido' ? 'Recebido' : 'Pago' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span 
                  class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                  :class="{
                    'bg-green-100 text-green-800': pagamento.status === 'confirmado',
                    'bg-yellow-100 text-yellow-800': pagamento.status === 'pendente',
                    'bg-red-100 text-red-800': pagamento.status === 'cancelado'
                  }"
                >
                  {{ pagamento.status }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <button 
                  @click="viewPagamento(pagamento)"
                  class="text-indigo-600 hover:text-indigo-900 mr-2"
                >
                  Ver Detalhes
                </button>
                <div class="inline-flex" v-if="pagamento.status === 'pendente'">
                  <button 
                    @click="updatePagamentoStatus(pagamento.id, 'confirmado')"
                    class="text-green-600 hover:text-green-900 mr-2"
                  >
                    Confirmar
                  </button>
                  <button 
                    @click="updatePagamentoStatus(pagamento.id, 'cancelado')"
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
            {{ selectedPagamento ? 'Detalhes do Pagamento' : 'Novo Pagamento' }}
          </h3>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-500">
            <span class="sr-only">Fechar</span>
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <div class="space-y-4">
          <!-- View Pagamento Details -->
          <template v-if="selectedPagamento">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">ID</label>
                <p class="mt-1 text-sm text-gray-900">#{{ selectedPagamento.id }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Data</label>
                <p class="mt-1 text-sm text-gray-900">{{ formatDate(selectedPagamento.date) }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Valor</label>
                <p class="mt-1 text-sm text-gray-900">{{ formatCurrency(selectedPagamento.value) }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Tipo</label>
                <p class="mt-1">
                  <span 
                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                    :class="{
                      'bg-green-100 text-green-800': selectedPagamento.type === 'recebido',
                      'bg-red-100 text-red-800': selectedPagamento.type === 'pago'
                    }"
                  >
                    {{ selectedPagamento.type === 'recebido' ? 'Recebido' : 'Pago' }}
                  </span>
                </p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <p class="mt-1">
                  <span 
                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                    :class="{
                      'bg-green-100 text-green-800': selectedPagamento.status === 'confirmado',
                      'bg-yellow-100 text-yellow-800': selectedPagamento.status === 'pendente',
                      'bg-red-100 text-red-800': selectedPagamento.status === 'cancelado'
                    }"
                  >
                    {{ selectedPagamento.status }}
                  </span>
                </p>
              </div>
              <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700">Descrição</label>
                <p class="mt-1 text-sm text-gray-900">{{ selectedPagamento.description }}</p>
              </div>
            </div>
          </template>

          <!-- New Pagamento Form -->
          <template v-else>
            <form @submit.prevent="submitPagamento" class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">Descrição</label>
                <input 
                  v-model="newPagamento.description" 
                  type="text" 
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                  required
                >
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Valor</label>
                <input 
                  v-model="newPagamento.value" 
                  type="number" 
                  step="0.01" 
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                  required
                >
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Tipo</label>
                <select 
                  v-model="newPagamento.type"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                  required
                >
                  <option value="recebido">Recebido</option>
                  <option value="pago">Pago</option>
                </select>
              </div>
              <div class="flex justify-end space-x-3">
                <button 
                  type="button"
                  @click="closeModal"
                  class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                  Cancelar
                </button>
                <button 
                  type="submit"
                  class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
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

const pagamentos = ref([
  {
    id: 1,
    date: '2024-03-15',
    description: 'Pagamento de fornecedor',
    value: '3.500,00',
    type: 'pago',
    status: 'confirmado'
  },
  {
    id: 2,
    date: '2024-03-15',
    description: 'Recebimento de cliente',
    value: '5.000,00',
    type: 'recebido',
    status: 'pendente'
  }
])

const showModal = ref(false)
const selectedPagamento = ref(null)
const newPagamento = ref({
  description: '',
  value: '',
  type: 'pago'
})

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('pt-BR')
}

const formatCurrency = (value) => {
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL'
  }).format(value)
}

const viewPagamento = (pagamento) => {
  selectedPagamento.value = pagamento
  showModal.value = true
}

const openNewPagamentoModal = () => {
  selectedPagamento.value = null
  newPagamento.value = {
    description: '',
    value: '',
    type: 'pago'
  }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  selectedPagamento.value = null
  newPagamento.value = {
    description: '',
    value: '',
    type: 'pago'
  }
}

const submitPagamento = async () => {
  try {
    // Aqui você faria a chamada para a API
    // const response = await axios.post('/api/pagamentos', newPagamento.value)
    
    // Simulando adição do novo pagamento
    pagamentos.value.push({
      id: pagamentos.value.length + 1,
      date: new Date().toISOString(),
      description: newPagamento.value.description,
      value: newPagamento.value.value,
      type: newPagamento.value.type,
      status: 'pendente'
    })
    
    closeModal()
  } catch (error) {
    console.error('Erro ao criar pagamento:', error)
  }
}

const updatePagamentoStatus = async (pagamentoId, newStatus) => {
  try {
    // Aqui você faria a chamada para a API
    // const response = await axios.patch(`/api/pagamentos/${pagamentoId}`, { status: newStatus })
    
    // Simulando atualização do status
    const pagamento = pagamentos.value.find(p => p.id === pagamentoId)
    if (pagamento) {
      pagamento.status = newStatus
    }
  } catch (error) {
    console.error('Erro ao atualizar status:', error)
  }
}
</script>
