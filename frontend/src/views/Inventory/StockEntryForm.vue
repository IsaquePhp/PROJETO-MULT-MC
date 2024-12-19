<template>
  <div class="container mx-auto p-4">
    <div class="bg-white rounded-lg shadow">
      <!-- Cabeçalho -->
      <div class="p-6 border-b">
        <div class="flex justify-between items-center">
          <h1 class="text-2xl font-semibold">{{ isEditing ? 'Editar' : 'Novo' }} Lançamento</h1>
          <router-link 
            to="/inventory/entries"
            class="px-4 py-2 text-gray-600 hover:text-gray-800"
          >
            <i class="fas fa-arrow-left mr-2"></i>
            Voltar
          </router-link>
        </div>
      </div>

      <!-- Formulário -->
      <form @submit.prevent="saveEntry" class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Tipo de Lançamento -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Tipo de Lançamento
            </label>
            <select 
              v-model="form.type"
              class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500"
              required
            >
              <option value="">Selecione...</option>
              <option value="donation">Doação</option>
              <option value="purchase">Compra</option>
              <option value="return">Devolução</option>
            </select>
          </div>

          <!-- Status -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Status
            </label>
            <select 
              v-model="form.status"
              class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500"
              required
            >
              <option value="">Selecione...</option>
              <option value="with_note">Com nota</option>
              <option value="without_note">Sem nota</option>
            </select>
          </div>

          <!-- Data de Emissão -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Data de Emissão
            </label>
            <input 
              type="date"
              v-model="form.issueDate"
              class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500"
              required
            >
          </div>

          <!-- Data de Recebimento -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Data de Recebimento
            </label>
            <input 
              type="date"
              v-model="form.receiveDate"
              class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500"
              required
            >
          </div>

          <!-- Instituição -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Instituição
            </label>
            <select 
              v-model="form.institutionId"
              class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500"
              required
            >
              <option value="">Selecione...</option>
              <option 
                v-for="institution in institutions" 
                :key="institution.id"
                :value="institution.id"
              >
                {{ institution.name }}
              </option>
            </select>
          </div>

          <!-- Fornecedor -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Fornecedor
            </label>
            <select 
              v-model="form.supplierId"
              class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500"
              required
            >
              <option value="">Selecione...</option>
              <option 
                v-for="supplier in suppliers" 
                :key="supplier.id"
                :value="supplier.id"
              >
                {{ supplier.name }}
              </option>
            </select>
          </div>
        </div>

        <!-- Produtos -->
        <div class="mt-8">
          <h2 class="text-lg font-medium mb-4">Produtos</h2>
          
          <div class="mb-4">
            <button 
              type="button"
              @click="addProduct"
              class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200"
            >
              <i class="fas fa-plus mr-2"></i>
              Adicionar Produto
            </button>
          </div>

          <div v-for="(product, index) in form.products" :key="index" class="mb-4 p-4 border rounded-lg">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
              <!-- Produto -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Produto
                </label>
                <select 
                  v-model="product.productId"
                  class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500"
                  required
                >
                  <option value="">Selecione...</option>
                  <option 
                    v-for="item in products" 
                    :key="item.id"
                    :value="item.id"
                  >
                    {{ item.name }}
                  </option>
                </select>
              </div>

              <!-- Quantidade -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Quantidade
                </label>
                <input 
                  type="number"
                  v-model="product.quantity"
                  min="1"
                  class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500"
                  required
                >
              </div>

              <!-- Valor Unitário -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Valor Unitário
                </label>
                <input 
                  type="number"
                  v-model="product.unitPrice"
                  min="0"
                  step="0.01"
                  class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500"
                  required
                >
              </div>

              <!-- Remover -->
              <div class="flex items-end">
                <button 
                  type="button"
                  @click="removeProduct(index)"
                  class="px-3 py-2 text-red-500 hover:text-red-700"
                >
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Botões -->
        <div class="mt-8 flex justify-end gap-4">
          <router-link 
            to="/inventory/entries"
            class="px-6 py-2 border text-gray-700 rounded-lg hover:bg-gray-50"
          >
            Cancelar
          </router-link>
          <button 
            type="submit"
            class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700"
            :disabled="loading"
          >
            {{ loading ? 'Salvando...' : 'Salvar' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from '../../plugins/axios'
import { useToast } from 'vue-toastification'

export default {
  name: 'StockEntryForm',
  setup() {
    const router = useRouter()
    const route = useRoute()
    const toast = useToast()
    const loading = ref(false)
    const isEditing = ref(false)

    // Dados para os selects
    const institutions = ref([])
    const suppliers = ref([])
    const products = ref([])

    // Formulário
    const form = ref({
      type: '',
      status: '',
      issueDate: '',
      receiveDate: '',
      institutionId: '',
      supplierId: '',
      products: []
    })

    // Carregar dados para os selects
    const loadFormData = async () => {
      try {
        const [institutionsRes, suppliersRes, productsRes] = await Promise.all([
          axios.get('/institutions'),
          axios.get('/suppliers'),
          axios.get('/products')
        ])

        institutions.value = institutionsRes.data.data
        suppliers.value = suppliersRes.data.data
        products.value = productsRes.data.data
      } catch (error) {
        console.error('Erro ao carregar dados do formulário:', error)
        toast.error('Erro ao carregar dados do formulário')
      }
    }

    // Carregar dados do lançamento se estiver editando
    const loadEntry = async (id) => {
      try {
        const response = await axios.get(`/inventory/entries/${id}`)
        const entry = response.data.data

        form.value = {
          type: entry.type,
          status: entry.status,
          issueDate: entry.issueDate,
          receiveDate: entry.receiveDate,
          institutionId: entry.institution?.id,
          supplierId: entry.supplier?.id,
          products: entry.products.map(p => ({
            productId: p.id,
            quantity: p.pivot.quantity,
            unitPrice: p.pivot.unit_price
          }))
        }
      } catch (error) {
        console.error('Erro ao carregar lançamento:', error)
        toast.error('Erro ao carregar o lançamento')
        router.push('/inventory/entries')
      }
    }

    // Adicionar produto ao formulário
    const addProduct = () => {
      form.value.products.push({
        productId: '',
        quantity: 1,
        unitPrice: 0
      })
    }

    // Remover produto do formulário
    const removeProduct = (index) => {
      form.value.products.splice(index, 1)
    }

    // Salvar lançamento
    const saveEntry = async () => {
      if (form.value.products.length === 0) {
        toast.error('Adicione pelo menos um produto')
        return
      }

      loading.value = true

      try {
        const endpoint = isEditing.value 
          ? `/inventory/entries/${route.params.id}`
          : '/inventory/entries'
        
        const method = isEditing.value ? 'put' : 'post'

        await axios[method](endpoint, form.value)

        toast.success(`Lançamento ${isEditing.value ? 'atualizado' : 'criado'} com sucesso!`)
        router.push('/inventory/entries')
      } catch (error) {
        console.error('Erro ao salvar lançamento:', error)
        toast.error('Erro ao salvar o lançamento')
      } finally {
        loading.value = false
      }
    }

    onMounted(async () => {
      await loadFormData()

      if (route.params.id) {
        isEditing.value = true
        await loadEntry(route.params.id)
      }
    })

    return {
      loading,
      isEditing,
      institutions,
      suppliers,
      products,
      form,
      addProduct,
      removeProduct,
      saveEntry
    }
  }
}
</script>
