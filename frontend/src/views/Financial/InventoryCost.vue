<template>
  <div class="p-6">
    <header class="mb-8">
      <h1 class="text-2xl font-bold text-gray-900">Controle de Custo do Estoque</h1>
      <p class="mt-2 text-sm text-gray-700">Gerenciamento de custos e valorização do estoque</p>
    </header>

    <!-- Resumo de Custos -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex items-center justify-between mb-2">
          <h3 class="font-medium text-gray-900">Valor Total em Estoque</h3>
          <span class="text-sm text-green-600">+5.2%</span>
        </div>
        <p class="text-2xl font-semibold text-gray-900">R$ 285.750,00</p>
        <p class="text-sm text-gray-500 mt-1">Custo atual</p>
      </div>

      <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex items-center justify-between mb-2">
          <h3 class="font-medium text-gray-900">Custo Médio</h3>
          <span class="text-sm text-red-600">-2.1%</span>
        </div>
        <p class="text-2xl font-semibold text-gray-900">R$ 42,85</p>
        <p class="text-sm text-gray-500 mt-1">Por item</p>
      </div>

      <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex items-center justify-between mb-2">
          <h3 class="font-medium text-gray-900">Giro de Estoque</h3>
          <span class="text-sm text-green-600">+8.3%</span>
        </div>
        <p class="text-2xl font-semibold text-gray-900">4.2x</p>
        <p class="text-sm text-gray-500 mt-1">Últimos 30 dias</p>
      </div>

      <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex items-center justify-between mb-2">
          <h3 class="font-medium text-gray-900">Itens em Estoque</h3>
          <span class="text-sm text-yellow-600">+1.5%</span>
        </div>
        <p class="text-2xl font-semibold text-gray-900">6.658</p>
        <p class="text-sm text-gray-500 mt-1">Total de itens</p>
      </div>
    </div>

    <!-- Filtros e Ações -->
    <div class="bg-white rounded-lg shadow">
      <div class="p-4 border-b border-gray-200">
        <div class="flex flex-wrap gap-4">
          <div class="flex-1 min-w-[200px]">
            <input
              type="text"
              placeholder="Buscar produto..."
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
          </div>
          <div>
            <select class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
              <option value="">Categoria</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }}
              </option>
            </select>
          </div>
          <div>
            <select class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
              <option value="all">Todos os Status</option>
              <option value="low">Estoque Baixo</option>
              <option value="optimal">Estoque Ideal</option>
              <option value="excess">Estoque Excedente</option>
            </select>
          </div>
          <button class="px-4 py-2 text-indigo-600 bg-indigo-50 rounded-lg hover:bg-indigo-100">
            Exportar Relatório
          </button>
        </div>
      </div>

      <!-- Lista de Produtos -->
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produto</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantidade</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Custo Unitário</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Custo Total</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="product in products" :key="product.id">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="h-10 w-10 flex-shrink-0">
                    <img class="h-10 w-10 rounded-full" :src="product.image" :alt="product.name">
                  </div>
                  <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900">{{ product.name }}</div>
                    <div class="text-sm text-gray-500">{{ product.category }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ product.sku }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ product.quantity }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ product.unitCost }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ product.totalCost }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="getStatusClass(product.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                  {{ product.status }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <button class="text-indigo-600 hover:text-indigo-900 mr-4">Histórico</button>
                <button class="text-indigo-600 hover:text-indigo-900">Ajustar</button>
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

const categories = ref([
  { id: 1, name: 'Eletrônicos' },
  { id: 2, name: 'Vestuário' },
  { id: 3, name: 'Acessórios' }
])

const products = ref([
  {
    id: 1,
    name: 'Smartphone XYZ',
    category: 'Eletrônicos',
    sku: 'SMT-001',
    quantity: '85',
    unitCost: 'R$ 850,00',
    totalCost: 'R$ 72.250,00',
    status: 'Estoque Ideal',
    image: 'https://via.placeholder.com/40'
  },
  {
    id: 2,
    name: 'Notebook ABC',
    category: 'Eletrônicos',
    sku: 'NTB-002',
    quantity: '12',
    unitCost: 'R$ 2.850,00',
    totalCost: 'R$ 34.200,00',
    status: 'Estoque Baixo',
    image: 'https://via.placeholder.com/40'
  },
  {
    id: 3,
    name: 'Fone de Ouvido',
    category: 'Acessórios',
    sku: 'FNE-003',
    quantity: '250',
    unitCost: 'R$ 120,00',
    totalCost: 'R$ 30.000,00',
    status: 'Estoque Excedente',
    image: 'https://via.placeholder.com/40'
  }
])

const getStatusClass = (status) => {
  const classes = {
    'Estoque Ideal': 'bg-green-100 text-green-800',
    'Estoque Baixo': 'bg-red-100 text-red-800',
    'Estoque Excedente': 'bg-yellow-100 text-yellow-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}
</script>
