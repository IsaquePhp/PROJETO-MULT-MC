<template>
  <div class="p-6">
    <header class="mb-8">
      <h1 class="text-2xl font-bold text-gray-900">Fluxo de Caixa</h1>
      <p class="mt-2 text-sm text-gray-700">Controle de entradas e saídas financeiras</p>
    </header>

    <!-- Resumo do Fluxo de Caixa -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-600">Entradas</p>
            <p class="mt-1 text-2xl font-semibold text-green-600">R$ 25.650,00</p>
          </div>
          <div class="p-3 bg-green-100 rounded-full">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-600">Saídas</p>
            <p class="mt-1 text-2xl font-semibold text-red-600">R$ 18.420,00</p>
          </div>
          <div class="p-3 bg-red-100 rounded-full">
            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-600">Saldo</p>
            <p class="mt-1 text-2xl font-semibold text-blue-600">R$ 7.230,00</p>
          </div>
          <div class="p-3 bg-blue-100 rounded-full">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Filtros e Ações -->
    <div class="bg-white rounded-lg shadow mb-8">
      <div class="p-4 border-b border-gray-200">
        <div class="flex flex-wrap gap-4">
          <div class="flex-1 min-w-[200px]">
            <input
              type="text"
              placeholder="Buscar transação..."
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
          </div>
          <div>
            <select class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
              <option value="">Tipo</option>
              <option value="entrada">Entrada</option>
              <option value="saida">Saída</option>
            </select>
          </div>
          <div>
            <input
              type="date"
              class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
          </div>
          <button class="px-4 py-2 text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
            Nova Transação
          </button>
        </div>
      </div>

      <!-- Lista de Transações -->
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descrição</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoria</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="transaction in transactions" :key="transaction.id">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ transaction.date }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ transaction.description }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ transaction.category }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="getTypeClass(transaction.type)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                  {{ transaction.type }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm" :class="transaction.type === 'Entrada' ? 'text-green-600' : 'text-red-600'">
                {{ transaction.amount }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <button class="text-indigo-600 hover:text-indigo-900 mr-4">Editar</button>
                <button class="text-red-600 hover:text-red-900">Excluir</button>
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

const transactions = ref([
  {
    id: 1,
    date: '2024-03-15',
    description: 'Venda de Produtos',
    category: 'Vendas',
    type: 'Entrada',
    amount: 'R$ 1.500,00'
  },
  {
    id: 2,
    date: '2024-03-15',
    description: 'Pagamento Fornecedor',
    category: 'Fornecedores',
    type: 'Saída',
    amount: 'R$ 800,00'
  }
])

const getTypeClass = (type) => {
  return type === 'Entrada' 
    ? 'bg-green-100 text-green-800'
    : 'bg-red-100 text-red-800'
}
</script>
