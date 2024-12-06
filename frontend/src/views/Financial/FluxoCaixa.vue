<template>
  <div class="p-6">
    <header class="mb-8">
      <h1 class="text-2xl font-bold text-gray-900">Fluxo de Caixa</h1>
      <p class="mt-2 text-sm text-gray-700">Gerencie o fluxo de caixa da empresa</p>
    </header>

    <div class="bg-white rounded-lg shadow">
      <!-- Filtros -->
      <div class="p-4 border-b border-gray-200">
        <div class="flex flex-wrap gap-4">
          <div class="flex-1 min-w-[200px]">
            <input
              type="text"
              placeholder="Buscar..."
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
          </div>
          <div>
            <select class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
              <option value="">Período</option>
              <option value="day">Hoje</option>
              <option value="week">Esta Semana</option>
              <option value="month">Este Mês</option>
              <option value="year">Este Ano</option>
            </select>
          </div>
          <button class="px-4 py-2 text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
            Novo Registro
          </button>
        </div>
      </div>

      <!-- Resumo -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4">
        <div class="bg-green-50 p-4 rounded-lg">
          <h3 class="text-sm font-medium text-green-800">Entradas</h3>
          <p class="mt-2 text-2xl font-semibold text-green-900">R$ 25.000,00</p>
          <p class="text-sm text-green-700">+15% em relação ao mês anterior</p>
        </div>
        <div class="bg-red-50 p-4 rounded-lg">
          <h3 class="text-sm font-medium text-red-800">Saídas</h3>
          <p class="mt-2 text-2xl font-semibold text-red-900">R$ 18.000,00</p>
          <p class="text-sm text-red-700">-5% em relação ao mês anterior</p>
        </div>
        <div class="bg-blue-50 p-4 rounded-lg">
          <h3 class="text-sm font-medium text-blue-800">Saldo</h3>
          <p class="mt-2 text-2xl font-semibold text-blue-900">R$ 7.000,00</p>
          <p class="text-sm text-blue-700">Saldo atual em conta</p>
        </div>
      </div>

      <!-- Tabela -->
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descrição</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoria</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="item in items" :key="item.id">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ item.date }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ item.description }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="getTypeClass(item.type)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                  {{ item.type }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm" :class="item.type === 'Entrada' ? 'text-green-600' : 'text-red-600'">
                R$ {{ item.value }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ item.category }}</td>
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

const items = ref([
  {
    id: 1,
    date: '2024-03-15',
    description: 'Venda de produtos',
    type: 'Entrada',
    value: '5.000,00',
    category: 'Vendas'
  },
  {
    id: 2,
    date: '2024-03-15',
    description: 'Pagamento de fornecedor',
    type: 'Saída',
    value: '3.500,00',
    category: 'Fornecedores'
  }
])

const getTypeClass = (type) => {
  return type === 'Entrada'
    ? 'bg-green-100 text-green-800'
    : 'bg-red-100 text-red-800'
}
</script>
