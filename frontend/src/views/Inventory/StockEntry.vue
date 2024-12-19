<template>
  <div class="min-h-screen bg-gray-100 p-6">
    <div class="bg-white rounded-lg shadow-sm">
      <!-- Cabeçalho -->
      <div class="bg-[#6366F1] p-4 rounded-t-lg">
        <div class="flex justify-between items-center">
          <h1 class="text-xl font-semibold text-white">Novo lançamento</h1>
          <div class="text-white text-sm opacity-90">ID: #{{ entryId }}</div>
        </div>
      </div>

      <!-- Formulário -->
      <div class="p-6">
        <!-- Linha 1: Informações Principais -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
          <!-- Tipo de lançamento -->
          <div>
            <label class="block text-sm text-gray-600 mb-1">Tipo de lançamento</label>
            <select 
              v-model="entry.type"
              class="w-full p-2.5 border rounded focus:ring-1 focus:ring-[#6366F1] focus:border-[#6366F1] bg-white"
            >
              <option value="purchase">Compra</option>
              <option value="return">Devolução</option>
            </select>
          </div>

          <!-- Status -->
          <div>
            <label class="block text-sm text-gray-600 mb-1">Status</label>
            <select 
              v-model="entry.status"
              class="w-full p-2.5 border rounded focus:ring-1 focus:ring-[#6366F1] focus:border-[#6366F1] bg-white"
            >
              <option value="with_note">Com nota</option>
              <option value="without_note">Sem nota</option>
              <option value="pending">Pendente</option>
              <option value="cancelled">Cancelado</option>
            </select>
          </div>

          <!-- Data da Emissão -->
          <div>
            <label class="block text-sm text-gray-600 mb-1">Data da Emissão</label>
            <input 
              type="date" 
              v-model="entry.issueDate"
              class="w-full p-2.5 border rounded focus:ring-1 focus:ring-[#6366F1] focus:border-[#6366F1] bg-white"
            >
          </div>

          <!-- Data do Recebimento -->
          <div>
            <label class="block text-sm text-gray-600 mb-1">Data do recebimento</label>
            <input 
              type="date" 
              v-model="entry.receiveDate"
              class="w-full p-2.5 border rounded focus:ring-1 focus:ring-[#6366F1] focus:border-[#6366F1] bg-white"
            >
          </div>
        </div>

        <!-- Grid para Loja e Fornecedor -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
          <!-- Loja -->
          <div>
            <div class="flex justify-between items-center mb-2">
              <label class="block text-sm text-gray-600">Loja: Unidade</label>
              <button 
                @click="editInstitution"
                class="text-[#6366F1] hover:text-[#4F46E5] text-sm flex items-center gap-1"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                </svg>
                Editar
              </button>
            </div>
            <div class="p-4 border rounded bg-gray-50 min-h-[80px]">
              <p class="text-sm text-gray-600">Endereço</p>
            </div>
          </div>

          <!-- Fornecedor -->
          <div>
            <div class="flex justify-between items-center mb-2">
              <label class="block text-sm text-gray-600">Fornecedor</label>
              <button 
                @click="addSupplier"
                class="text-[#6366F1] hover:text-[#4F46E5] text-sm flex items-center gap-1"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Add Fornecedor
              </button>
            </div>
            <div class="p-4 border rounded bg-gray-50 min-h-[80px]">
              <p class="text-sm text-gray-600">Fornencedor</p>
            </div>
          </div>
        </div>

        <!-- Lista de itens -->
        <div class="mb-8">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-base font-medium text-gray-900">Lista de itens</h2>
            <button 
              @click="addItem"
              class="inline-flex items-center px-4 py-2 bg-[#6366F1] text-white rounded hover:bg-[#4F46E5] transition-colors duration-200"
            >
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
              </svg>
              Add novo item
            </button>
          </div>

          <!-- Tabela de Itens -->
          <div class="overflow-x-auto border rounded">
            <table class="w-full">
              <thead>
                <tr class="text-left text-xs text-gray-500 border-b bg-gray-50">
                  <th class="px-4 py-3">#</th>
                  <th class="px-4 py-3">NOME DO ITEM</th>
                  <th class="px-4 py-3">VALIDADE</th>
                  <th class="px-4 py-3">SETOR</th>
                  <th class="px-4 py-3">CATEGORIA</th>
                  <th class="px-4 py-3 text-center">QUANT.</th>
                  <th class="px-4 py-3">UNI. MEDIDA</th>
                  <th class="px-4 py-3">VALOR UNIT</th>
                  <th class="px-4 py-3">VALOR TOTAL</th>
                  <th class="px-4 py-3">AÇÃO</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item, index) in entry.items" :key="index" class="border-b">
                  <td class="px-4 py-3">{{ index + 1 }}</td>
                  <td class="px-4 py-3">
                    <input 
                      type="text" 
                      v-model="item.name"
                      class="w-full p-2 border rounded focus:ring-1 focus:ring-[#6366F1] focus:border-[#6366F1] bg-white"
                      placeholder="Nome do item"
                    >
                  </td>
                  <td class="px-4 py-3">
                    <input 
                      type="date" 
                      v-model="item.expiryDate"
                      class="w-full p-2 border rounded focus:ring-1 focus:ring-[#6366F1] focus:border-[#6366F1] bg-white"
                    >
                  </td>
                  <td class="px-4 py-3">
                    <select 
                      v-model="item.sector"
                      class="w-full p-2 border rounded focus:ring-1 focus:ring-[#6366F1] focus:border-[#6366F1] bg-white"
                    >
                      <option value="">Setor</option>
                      <option v-for="sector in sectors" :key="sector.id" :value="sector.id">
                        {{ sector.name }}
                      </option>
                    </select>
                  </td>
                  <td class="px-4 py-3">
                    <select 
                      v-model="item.category"
                      class="w-full p-2 border rounded focus:ring-1 focus:ring-[#6366F1] focus:border-[#6366F1] bg-white"
                    >
                      <option value="">Categoria</option>
                      <option v-for="category in categories" :key="category.id" :value="category.id">
                        {{ category.name }}
                      </option>
                    </select>
                  </td>
                  <td class="px-4 py-3">
                    <input 
                      type="number" 
                      v-model="item.quantity"
                      class="w-24 p-2 border rounded focus:ring-1 focus:ring-[#6366F1] focus:border-[#6366F1] bg-white text-center"
                      min="1"
                    >
                  </td>
                  <td class="px-4 py-3">
                    <select 
                      v-model="item.unit"
                      class="w-full p-2 border rounded focus:ring-1 focus:ring-[#6366F1] focus:border-[#6366F1] bg-white"
                    >
                      <option value="cx">Caixa</option>
                      <option value="un">Unidade</option>
                      <option value="kg">Kg</option>
                      <option value="lt">Litro</option>
                      <option value="pc">Pacote</option>
                    </select>
                  </td>
                  <td class="px-4 py-3">
                    <input 
                      type="text" 
                      v-model="item.unitPrice"
                      class="w-32 p-2 border rounded focus:ring-1 focus:ring-[#6366F1] focus:border-[#6366F1] bg-white"
                      placeholder="R$ 0,00"
                    >
                  </td>
                  <td class="px-4 py-3">
                    <input 
                      type="text" 
                      :value="formatCurrency(item.quantity * item.unitPrice)"
                      class="w-32 p-2 border rounded bg-gray-50"
                      readonly
                    >
                  </td>
                  <td class="px-4 py-3">
                    <button 
                      @click="removeItem(index)"
                      class="text-red-500 hover:text-red-700"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Total -->
          <div class="flex justify-end mt-4">
            <div class="text-right">
              <span class="text-gray-600">Total:</span>
              <span class="ml-2 text-xl font-semibold">{{ formatCurrency(totalAmount) }}</span>
            </div>
          </div>
        </div>

        <!-- Anotações -->
        <div class="mb-8">
          <label class="block text-sm text-gray-600 mb-2">Anotações</label>
          <textarea 
            v-model="entry.notes"
            rows="4"
            class="w-full p-3 border rounded focus:ring-1 focus:ring-[#6366F1] focus:border-[#6366F1] bg-white"
            placeholder="Adicione observações relevantes aqui..."
          ></textarea>
        </div>

        <!-- Botões -->
        <div class="flex justify-end gap-4">
          <button 
            @click="cancel"
            class="px-6 py-2 border rounded text-gray-700 hover:bg-gray-50"
          >
            Cancelar
          </button>
          <button 
            @click="save"
            class="px-6 py-2 bg-[#6366F1] text-white rounded hover:bg-[#4F46E5]"
          >
            Salvar
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import axios from '../../plugins/axios'
import { useToast } from 'vue-toastification'

export default {
  name: 'StockEntry',
  setup() {
    const router = useRouter()
    const toast = useToast()

    // Estado inicial
    const entry = ref({
      type: 'purchase',
      status: 'with_note',
      issueDate: new Date().toISOString().split('T')[0],
      receiveDate: new Date().toISOString().split('T')[0],
      institution: null,
      supplier: null,
      items: [],
      notes: ''
    })

    const entryId = ref('NOLZ1WQP')
    const sectors = ref([])
    const categories = ref([])

    // Computed
    const totalAmount = computed(() => {
      return entry.value.items.reduce((total, item) => {
        const quantity = Number(item.quantity) || 0
        const price = Number(item.unitPrice) || 0
        return total + (quantity * price)
      }, 0)
    })

    // Métodos
    const addItem = () => {
      entry.value.items.push({
        name: '',
        expiryDate: '',
        sector: '',
        category: '',
        quantity: 1,
        unit: 'cx',
        unitPrice: ''
      })
    }

    const removeItem = (index) => {
      entry.value.items.splice(index, 1)
    }

    const formatCurrency = (value) => {
      const number = Number(value) || 0
      return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
      }).format(number)
    }

    const editInstitution = () => {
      // Implementar modal de edição da instituição
    }

    const addSupplier = () => {
      // Implementar modal de adição de fornecedor
    }

    const cancel = () => {
      router.push('/inventory/entries')
    }

    const save = async () => {
      try {
        // Validações básicas
        if (entry.value.items.length === 0) {
          toast.error('Adicione pelo menos um item')
          return
        }

        // Enviar para o backend
        const response = await axios.post('/inventory/entries', entry.value)
        
        if (response.data.success) {
          toast.success('Lançamento salvo com sucesso!')
          router.push('/inventory/entries')
        }
      } catch (error) {
        console.error('Erro ao salvar:', error)
        toast.error('Erro ao salvar o lançamento')
      }
    }

    // Carregar dados iniciais
    const loadInitialData = async () => {
      try {
        const [sectorsResponse, categoriesResponse] = await Promise.all([
          axios.get('/sectors'),
          axios.get('/categories')
        ])

        sectors.value = sectorsResponse.data.data
        categories.value = categoriesResponse.data.data
      } catch (error) {
        console.error('Erro ao carregar dados:', error)
        toast.error('Erro ao carregar dados iniciais')
      }
    }

    loadInitialData()

    return {
      entry,
      entryId,
      sectors,
      categories,
      totalAmount,
      addItem,
      removeItem,
      formatCurrency,
      editInstitution,
      addSupplier,
      cancel,
      save
    }
  }
}
</script>
