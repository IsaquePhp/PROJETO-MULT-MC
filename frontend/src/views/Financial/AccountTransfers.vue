<template>
  <div class="p-6">
    <header class="mb-8">
      <h1 class="text-2xl font-bold text-gray-900">Movimentação entre Contas</h1>
      <p class="mt-2 text-sm text-gray-700">Gerenciamento de transferências entre contas</p>
    </header>

    <!-- Resumo das Contas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <div v-for="account in accounts" :key="account.id" class="bg-white p-6 rounded-lg shadow">
        <div class="flex items-center justify-between mb-2">
          <h3 class="font-medium text-gray-900">{{ account.name }}</h3>
          <span class="text-sm text-gray-500">{{ account.bank }}</span>
        </div>
        <p class="text-2xl font-semibold text-gray-900">{{ account.balance }}</p>
        <p class="text-sm text-gray-500 mt-1">Última movimentação: {{ account.lastTransaction }}</p>
      </div>
    </div>

    <!-- Nova Transferência -->
    <div class="bg-white rounded-lg shadow mb-8">
      <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-6">Nova Transferência</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Conta de Origem</label>
            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
              <option value="">Selecione uma conta</option>
              <option v-for="account in accounts" :key="account.id" :value="account.id">
                {{ account.name }} - {{ account.balance }}
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Conta de Destino</label>
            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
              <option value="">Selecione uma conta</option>
              <option v-for="account in accounts" :key="account.id" :value="account.id">
                {{ account.name }} - {{ account.balance }}
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Valor</label>
            <input
              type="text"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
              placeholder="R$ 0,00"
            >
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Data</label>
            <input
              type="date"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
          </div>
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">Observação</label>
            <textarea
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
              rows="3"
              placeholder="Adicione uma observação..."
            ></textarea>
          </div>
          <div class="md:col-span-2">
            <button class="w-full px-4 py-2 text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
              Realizar Transferência
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Histórico de Transferências -->
    <div class="bg-white rounded-lg shadow">
      <div class="p-4 border-b border-gray-200">
        <div class="flex flex-wrap gap-4">
          <div class="flex-1 min-w-[200px]">
            <input
              type="text"
              placeholder="Buscar transferência..."
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
          </div>
          <div>
            <input
              type="date"
              class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
          </div>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Origem</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Destino</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="transfer in transfers" :key="transfer.id">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ transfer.date }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ transfer.origin }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ transfer.destination }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ transfer.amount }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="getStatusClass(transfer.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                  {{ transfer.status }}
                </span>
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

const accounts = ref([
  {
    id: 1,
    name: 'Conta Principal',
    bank: 'Banco do Brasil',
    balance: 'R$ 45.850,00',
    lastTransaction: '15/03/2024'
  },
  {
    id: 2,
    name: 'Conta Secundária',
    bank: 'Itaú',
    balance: 'R$ 28.320,00',
    lastTransaction: '14/03/2024'
  },
  {
    id: 3,
    name: 'Conta Reserva',
    bank: 'Santander',
    balance: 'R$ 15.600,00',
    lastTransaction: '13/03/2024'
  }
])

const transfers = ref([
  {
    id: 1,
    date: '2024-03-15',
    origin: 'Conta Principal',
    destination: 'Conta Secundária',
    amount: 'R$ 5.000,00',
    status: 'Concluída'
  },
  {
    id: 2,
    date: '2024-03-14',
    origin: 'Conta Secundária',
    destination: 'Conta Reserva',
    amount: 'R$ 2.500,00',
    status: 'Concluída'
  }
])

const getStatusClass = (status) => {
  const classes = {
    'Concluída': 'bg-green-100 text-green-800',
    'Pendente': 'bg-yellow-100 text-yellow-800',
    'Falha': 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}
</script>
