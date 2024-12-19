<template>
  <div class="p-6">
    <header class="mb-8">
      <h1 class="text-2xl font-bold text-gray-900">Faturas</h1>
      <p class="mt-2 text-sm text-gray-700">Gerencie todas as faturas do sistema</p>
    </header>

    <!-- Filtros e Ações -->
    <div class="mb-6 flex justify-between items-center">
      <div class="flex gap-4">
        <div class="relative">
          <input
            type="text"
            placeholder="Buscar fatura..."
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
          <option value="pendente">Pendente</option>
          <option value="paga">Paga</option>
          <option value="vencida">Vencida</option>
          <option value="cancelada">Cancelada</option>
        </select>
        <select
          v-model="monthFilter"
          class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        >
          <option value="">Todos os Meses</option>
          <option v-for="month in months" :key="month.value" :value="month.value">
            {{ month.label }}
          </option>
        </select>
      </div>
      <button
        @click="openNewBillModal"
        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
      >
        Nova Fatura
      </button>
    </div>

    <!-- Lista de Faturas -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Número</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vencimento</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="bill in filteredBills" :key="bill.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#{{ bill.id }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ bill.customer }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ formatCurrency(bill.amount) }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(bill.dueDate) }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span :class="getStatusClass(bill.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                {{ getStatusLabel(bill.status) }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button
                v-if="bill.status === 'pendente'"
                @click="markAsPaid(bill)"
                class="text-green-600 hover:text-green-900 mr-3"
              >
                Marcar como Paga
              </button>
              <button
                @click="viewBill(bill)"
                class="text-blue-600 hover:text-blue-900 mr-3"
              >
                Ver
              </button>
              <button
                @click="editBill(bill)"
                class="text-blue-600 hover:text-blue-900 mr-3"
              >
                Editar
              </button>
              <button
                @click="deleteBill(bill)"
                class="text-red-600 hover:text-red-900"
              >
                Excluir
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Resumo -->
    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-medium text-gray-900 mb-2">Total Pendente</h3>
        <p class="text-2xl font-bold text-red-600">{{ formatCurrency(totalPending) }}</p>
      </div>
      <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-medium text-gray-900 mb-2">Total Pago</h3>
        <p class="text-2xl font-bold text-green-600">{{ formatCurrency(totalPaid) }}</p>
      </div>
      <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-medium text-gray-900 mb-2">Total Vencido</h3>
        <p class="text-2xl font-bold text-orange-600">{{ formatCurrency(totalOverdue) }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

// Estado
const searchQuery = ref('')
const statusFilter = ref('')
const monthFilter = ref('')

const months = [
  { value: '01', label: 'Janeiro' },
  { value: '02', label: 'Fevereiro' },
  { value: '03', label: 'Março' },
  { value: '04', label: 'Abril' },
  { value: '05', label: 'Maio' },
  { value: '06', label: 'Junho' },
  { value: '07', label: 'Julho' },
  { value: '08', label: 'Agosto' },
  { value: '09', label: 'Setembro' },
  { value: '10', label: 'Outubro' },
  { value: '11', label: 'Novembro' },
  { value: '12', label: 'Dezembro' }
]

const bills = ref([
  {
    id: 1,
    customer: 'João Silva',
    amount: 1500.00,
    dueDate: '2024-01-25',
    status: 'pendente'
  },
  {
    id: 2,
    customer: 'Maria Santos',
    amount: 2500.00,
    dueDate: '2024-01-15',
    status: 'paga'
  },
  {
    id: 3,
    customer: 'Pedro Oliveira',
    amount: 1800.00,
    dueDate: '2024-01-10',
    status: 'vencida'
  }
])

// Computed Properties
const filteredBills = computed(() => {
  return bills.value.filter(bill => {
    const matchesSearch = bill.customer.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                         bill.id.toString().includes(searchQuery.value)
    const matchesStatus = !statusFilter.value || bill.status === statusFilter.value
    const matchesMonth = !monthFilter.value || bill.dueDate.substring(5, 7) === monthFilter.value
    return matchesSearch && matchesStatus && matchesMonth
  })
})

const totalPending = computed(() => {
  return bills.value
    .filter(bill => bill.status === 'pendente')
    .reduce((total, bill) => total + bill.amount, 0)
})

const totalPaid = computed(() => {
  return bills.value
    .filter(bill => bill.status === 'paga')
    .reduce((total, bill) => total + bill.amount, 0)
})

const totalOverdue = computed(() => {
  return bills.value
    .filter(bill => bill.status === 'vencida')
    .reduce((total, bill) => total + bill.amount, 0)
})

// Métodos
const getStatusClass = (status) => {
  const classes = {
    pendente: 'bg-yellow-100 text-yellow-800',
    paga: 'bg-green-100 text-green-800',
    vencida: 'bg-red-100 text-red-800',
    cancelada: 'bg-gray-100 text-gray-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getStatusLabel = (status) => {
  const labels = {
    pendente: 'Pendente',
    paga: 'Paga',
    vencida: 'Vencida',
    cancelada: 'Cancelada'
  }
  return labels[status] || status
}

const formatCurrency = (value) => {
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL'
  }).format(value)
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('pt-BR')
}

const openNewBillModal = () => {
  // Implementar lógica para abrir modal de nova fatura
  console.log('Abrir modal de nova fatura')
}

const markAsPaid = (bill) => {
  // Implementar lógica para marcar como paga
  console.log('Marcar como paga:', bill)
}

const viewBill = (bill) => {
  // Implementar lógica para visualizar fatura
  console.log('Visualizar fatura:', bill)
}

const editBill = (bill) => {
  // Implementar lógica para editar fatura
  console.log('Editar fatura:', bill)
}

const deleteBill = (bill) => {
  // Implementar lógica para excluir fatura
  console.log('Excluir fatura:', bill)
}
</script>
