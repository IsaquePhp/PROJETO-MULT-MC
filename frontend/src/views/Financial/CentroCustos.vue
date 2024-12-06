<template>
  <div class="p-6">
    <header class="mb-8">
      <h1 class="text-2xl font-bold text-gray-900">Centro de Custos</h1>
      <p class="mt-2 text-sm text-gray-700">Gerencie os centros de custos da empresa</p>
    </header>

    <div class="bg-white rounded-lg shadow">
      <!-- Filtros -->
      <div class="p-4 border-b border-gray-200">
        <div class="flex flex-wrap gap-4">
          <div class="flex-1 min-w-[200px]">
            <input
              type="text"
              placeholder="Buscar centro de custo..."
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
          </div>
          <div>
            <select class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
              <option value="">Status</option>
              <option value="active">Ativo</option>
              <option value="inactive">Inativo</option>
            </select>
          </div>
          <button class="px-4 py-2 text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
            Novo Centro de Custo
          </button>
        </div>
      </div>

      <!-- Resumo -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4">
        <div class="bg-blue-50 p-4 rounded-lg">
          <h3 class="text-sm font-medium text-blue-800">Total de Centros</h3>
          <p class="mt-2 text-2xl font-semibold text-blue-900">12</p>
          <p class="text-sm text-blue-700">Centros de custos ativos</p>
        </div>
        <div class="bg-green-50 p-4 rounded-lg">
          <h3 class="text-sm font-medium text-green-800">Orçamento Total</h3>
          <p class="mt-2 text-2xl font-semibold text-green-900">R$ 150.000,00</p>
          <p class="text-sm text-green-700">Orçamento planejado</p>
        </div>
        <div class="bg-yellow-50 p-4 rounded-lg">
          <h3 class="text-sm font-medium text-yellow-800">Realizado</h3>
          <p class="mt-2 text-2xl font-semibold text-yellow-900">R$ 125.000,00</p>
          <p class="text-sm text-yellow-700">83% do planejado</p>
        </div>
      </div>

      <!-- Tabela -->
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Responsável</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Orçamento</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Realizado</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="centro in centrosCusto" :key="centro.id">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ centro.codigo }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ centro.nome }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ centro.responsavel }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">R$ {{ centro.orcamento }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">R$ {{ centro.realizado }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="getStatusClass(centro.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                  {{ centro.status }}
                </span>
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

const centrosCusto = ref([
  {
    id: 1,
    codigo: 'CC001',
    nome: 'Administrativo',
    responsavel: 'João Silva',
    orcamento: '50.000,00',
    realizado: '45.000,00',
    status: 'Ativo'
  },
  {
    id: 2,
    codigo: 'CC002',
    nome: 'Comercial',
    responsavel: 'Maria Santos',
    orcamento: '30.000,00',
    realizado: '28.000,00',
    status: 'Ativo'
  }
])

const getStatusClass = (status) => {
  return status === 'Ativo'
    ? 'bg-green-100 text-green-800'
    : 'bg-red-100 text-red-800'
}
</script>
