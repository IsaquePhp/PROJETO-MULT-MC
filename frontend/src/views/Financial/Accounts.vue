<template>
  <div class="p-6">
    <header class="mb-8">
      <h1 class="text-2xl font-bold text-gray-900">Contas</h1>
      <p class="mt-2 text-sm text-gray-700">Gerencie todas as contas financeiras</p>
    </header>

    <!-- Filtros e Ações -->
    <div class="mb-6 flex justify-between items-center">
      <div class="flex gap-4">
        <div class="relative">
          <input
            type="text"
            placeholder="Buscar conta..."
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
          <option value="ativa">Ativa</option>
          <option value="inativa">Inativa</option>
          <option value="bloqueada">Bloqueada</option>
        </select>
      </div>
      <button
        @click="openNewAccountModal"
        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
      >
        Nova Conta
      </button>
    </div>

    <!-- Lista de Contas -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Banco</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Agência</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Conta</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Saldo</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="account in filteredAccounts" :key="account.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <img :src="account.bankLogo" alt="Bank Logo" class="h-8 w-8 mr-3">
                <div class="text-sm font-medium text-gray-900">{{ account.bankName }}</div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ account.agency }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ account.number }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ account.type }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" :class="getBalanceClass(account.balance)">
              {{ formatCurrency(account.balance) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span :class="getStatusClass(account.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                {{ getStatusLabel(account.status) }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button
                @click="viewTransactions(account)"
                class="text-blue-600 hover:text-blue-900 mr-3"
              >
                Extrato
              </button>
              <button
                @click="editAccount(account)"
                class="text-green-600 hover:text-green-900 mr-3"
              >
                Editar
              </button>
              <button
                @click="deleteAccount(account)"
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
const accounts = ref([
  {
    id: 1,
    bankName: 'Banco do Brasil',
    bankLogo: '/images/bb-logo.png',
    agency: '1234-5',
    number: '12345-6',
    type: 'Conta Corrente',
    balance: 15000.50,
    status: 'ativa'
  },
  {
    id: 2,
    bankName: 'Itaú',
    bankLogo: '/images/itau-logo.png',
    agency: '4321-0',
    number: '54321-0',
    type: 'Conta Poupança',
    balance: 25000.75,
    status: 'ativa'
  }
])

// Computed Properties
const filteredAccounts = computed(() => {
  return accounts.value.filter(account => {
    const matchesSearch = account.bankName.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                         account.number.includes(searchQuery.value)
    const matchesStatus = !statusFilter.value || account.status === statusFilter.value
    return matchesSearch && matchesStatus
  })
})

// Métodos
const getStatusClass = (status) => {
  const classes = {
    ativa: 'bg-green-100 text-green-800',
    inativa: 'bg-gray-100 text-gray-800',
    bloqueada: 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getStatusLabel = (status) => {
  const labels = {
    ativa: 'Ativa',
    inativa: 'Inativa',
    bloqueada: 'Bloqueada'
  }
  return labels[status] || status
}

const getBalanceClass = (balance) => {
  return balance >= 0 ? 'text-green-600' : 'text-red-600'
}

const formatCurrency = (value) => {
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL'
  }).format(value)
}

const openNewAccountModal = () => {
  // Implementar lógica para abrir modal de nova conta
  console.log('Abrir modal de nova conta')
}

const viewTransactions = (account) => {
  // Implementar lógica para visualizar extrato
  console.log('Visualizar extrato:', account)
}

const editAccount = (account) => {
  // Implementar lógica para editar conta
  console.log('Editar conta:', account)
}

const deleteAccount = (account) => {
  // Implementar lógica para excluir conta
  console.log('Excluir conta:', account)
}
</script>
