<template>
  <div class="p-6">
    <header class="mb-8">
      <h1 class="text-2xl font-bold text-gray-900">Movimentação entre Lojas</h1>
      <p class="mt-2 text-sm text-gray-700">Controle de transferências de produtos entre unidades</p>
    </header>

    <!-- Nova Transferência -->
    <div class="bg-white rounded-lg shadow mb-8">
      <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-6">Nova Transferência</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Loja de Origem</label>
            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
              <option value="">Selecione a loja de origem</option>
              <option v-for="store in stores" :key="store.id" :value="store.id">
                {{ store.name }}
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Loja de Destino</label>
            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
              <option value="">Selecione a loja de destino</option>
              <option v-for="store in stores" :key="store.id" :value="store.id">
                {{ store.name }}
              </option>
            </select>
          </div>

          <!-- Seleção de Produtos -->
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">Produtos</label>
            <div class="border rounded-lg divide-y">
              <div v-for="(item, index) in selectedItems" :key="index" class="p-4 flex items-center gap-4">
                <select class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                  <option value="">Selecione um produto</option>
                  <option v-for="product in products" :key="product.id" :value="product.id">
                    {{ product.name }} ({{ product.sku }})
                  </option>
                </select>
                <input
                  type="number"
                  placeholder="Quantidade"
                  class="w-32 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                  min="1"
                >
                <button 
                  @click="removeItem(index)"
                  class="p-2 text-red-600 hover:text-red-900"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                </button>
              </div>
            </div>
            <button 
              @click="addItem"
              class="mt-2 px-4 py-2 text-sm text-indigo-600 hover:text-indigo-900"
            >
              + Adicionar Produto
            </button>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Data Prevista</label>
            <input
              type="date"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Responsável</label>
            <input
              type="text"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
              placeholder="Nome do responsável"
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
              Iniciar Transferência
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Lista de Transferências -->
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
            <select class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
              <option value="">Status</option>
              <option value="pending">Pendente</option>
              <option value="in_transit">Em Trânsito</option>
              <option value="completed">Concluída</option>
              <option value="cancelled">Cancelada</option>
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

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Origem</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Destino</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Itens</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="transfer in transfers" :key="transfer.id">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">#{{ transfer.id }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ transfer.origin }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ transfer.destination }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ transfer.items }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ transfer.date }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="getStatusClass(transfer.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                  {{ transfer.status }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <button class="text-indigo-600 hover:text-indigo-900 mr-4">Detalhes</button>
                <button 
                  v-if="transfer.status === 'Pendente'"
                  class="text-red-600 hover:text-red-900"
                >
                  Cancelar
                </button>
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

const stores = ref([
  { id: 1, name: 'Loja Centro' },
  { id: 2, name: 'Loja Shopping' },
  { id: 3, name: 'Loja Norte' }
])

const products = ref([
  { id: 1, name: 'Smartphone XYZ', sku: 'SMT-001' },
  { id: 2, name: 'Notebook ABC', sku: 'NTB-002' },
  { id: 3, name: 'Fone de Ouvido', sku: 'FNE-003' }
])

const selectedItems = ref([{}])

const transfers = ref([
  {
    id: 1,
    origin: 'Loja Centro',
    destination: 'Loja Shopping',
    items: '5 itens',
    date: '2024-03-15',
    status: 'Em Trânsito'
  },
  {
    id: 2,
    origin: 'Loja Shopping',
    destination: 'Loja Norte',
    items: '3 itens',
    date: '2024-03-14',
    status: 'Pendente'
  }
])

const addItem = () => {
  selectedItems.value.push({})
}

const removeItem = (index) => {
  selectedItems.value.splice(index, 1)
  if (selectedItems.value.length === 0) {
    selectedItems.value.push({})
  }
}

const getStatusClass = (status) => {
  const classes = {
    'Pendente': 'bg-yellow-100 text-yellow-800',
    'Em Trânsito': 'bg-blue-100 text-blue-800',
    'Concluída': 'bg-green-100 text-green-800',
    'Cancelada': 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}
</script>
