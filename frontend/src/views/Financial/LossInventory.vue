<template>
  <div class="p-6">
    <header class="mb-8">
      <h1 class="text-2xl font-bold text-gray-900">Inventário de Perdas</h1>
      <p class="mt-2 text-sm text-gray-700">Controle e registro de perdas no estoque</p>
    </header>

    <!-- Resumo de Perdas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex items-center justify-between mb-2">
          <h3 class="font-medium text-gray-900">Total de Perdas</h3>
          <span class="text-sm text-red-600">+2.5%</span>
        </div>
        <p class="text-2xl font-semibold text-gray-900">R$ 12.850,00</p>
        <p class="text-sm text-gray-500 mt-1">Últimos 30 dias</p>
      </div>

      <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex items-center justify-between mb-2">
          <h3 class="font-medium text-gray-900">Itens Perdidos</h3>
          <span class="text-sm text-red-600">+1.8%</span>
        </div>
        <p class="text-2xl font-semibold text-gray-900">85</p>
        <p class="text-sm text-gray-500 mt-1">Últimos 30 dias</p>
      </div>

      <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex items-center justify-between mb-2">
          <h3 class="font-medium text-gray-900">Taxa de Perda</h3>
          <span class="text-sm text-green-600">-0.3%</span>
        </div>
        <p class="text-2xl font-semibold text-gray-900">1.2%</p>
        <p class="text-sm text-gray-500 mt-1">Do estoque total</p>
      </div>

      <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex items-center justify-between mb-2">
          <h3 class="font-medium text-gray-900">Custo Médio por Perda</h3>
          <span class="text-sm text-yellow-600">+0.5%</span>
        </div>
        <p class="text-2xl font-semibold text-gray-900">R$ 151,18</p>
        <p class="text-sm text-gray-500 mt-1">Por item</p>
      </div>
    </div>

    <!-- Registro de Nova Perda -->
    <div class="bg-white rounded-lg shadow mb-8">
      <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-6">Registrar Nova Perda</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Produto</label>
            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
              <option value="">Selecione um produto</option>
              <option v-for="product in products" :key="product.id" :value="product.id">
                {{ product.name }} ({{ product.sku }})
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Quantidade</label>
            <input
              type="number"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
              min="1"
            >
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Motivo</label>
            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
              <option value="">Selecione um motivo</option>
              <option value="damage">Dano/Avaria</option>
              <option value="expiration">Vencimento</option>
              <option value="theft">Furto</option>
              <option value="quality">Problema de Qualidade</option>
              <option value="other">Outro</option>
            </select>
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
              placeholder="Descreva detalhes sobre a perda..."
            ></textarea>
          </div>
          <div class="md:col-span-2">
            <button class="w-full px-4 py-2 text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
              Registrar Perda
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Histórico de Perdas -->
    <div class="bg-white rounded-lg shadow">
      <div class="p-4 border-b border-gray-200">
        <div class="flex flex-wrap gap-4">
          <div class="flex-1 min-w-[200px]">
            <input
              type="text"
              placeholder="Buscar registro..."
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
          </div>
          <div>
            <select class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
              <option value="">Motivo</option>
              <option value="damage">Dano/Avaria</option>
              <option value="expiration">Vencimento</option>
              <option value="theft">Furto</option>
              <option value="quality">Problema de Qualidade</option>
              <option value="other">Outro</option>
            </select>
          </div>
          <div>
            <input
              type="date"
              class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
          </div>
        </div>
      </div>

      <!-- Tabela de Perdas -->
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produto</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantidade</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Motivo</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Custo</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registrado por</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="loss in losses" :key="loss.id">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ loss.date }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="h-10 w-10 flex-shrink-0">
                    <img class="h-10 w-10 rounded-full" :src="loss.image" :alt="loss.product">
                  </div>
                  <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900">{{ loss.product }}</div>
                    <div class="text-sm text-gray-500">{{ loss.sku }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ loss.quantity }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="getReasonClass(loss.reason)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                  {{ loss.reason }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">{{ loss.cost }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ loss.registeredBy }}</td>
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

const products = ref([
  { id: 1, name: 'Smartphone XYZ', sku: 'SMT-001' },
  { id: 2, name: 'Notebook ABC', sku: 'NTB-002' },
  { id: 3, name: 'Fone de Ouvido', sku: 'FNE-003' }
])

const losses = ref([
  {
    id: 1,
    date: '2024-03-15',
    product: 'Smartphone XYZ',
    sku: 'SMT-001',
    quantity: '2',
    reason: 'Dano/Avaria',
    cost: 'R$ 1.700,00',
    registeredBy: 'João Silva',
    image: 'https://via.placeholder.com/40'
  },
  {
    id: 2,
    date: '2024-03-14',
    product: 'Fone de Ouvido',
    sku: 'FNE-003',
    quantity: '5',
    reason: 'Problema de Qualidade',
    cost: 'R$ 600,00',
    registeredBy: 'Maria Santos',
    image: 'https://via.placeholder.com/40'
  }
])

const getReasonClass = (reason) => {
  const classes = {
    'Dano/Avaria': 'bg-red-100 text-red-800',
    'Vencimento': 'bg-yellow-100 text-yellow-800',
    'Furto': 'bg-purple-100 text-purple-800',
    'Problema de Qualidade': 'bg-orange-100 text-orange-800'
  }
  return classes[reason] || 'bg-gray-100 text-gray-800'
}
</script>
