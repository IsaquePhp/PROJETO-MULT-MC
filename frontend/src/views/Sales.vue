<template>
  <div class="container mx-auto p-4">
    <!-- Grid de Produtos -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <!-- Coluna 1 e 2: Lista de Produtos -->
      <div class="md:col-span-2 bg-white rounded-lg shadow p-4">
        <h2 class="text-xl font-bold mb-4">Produtos</h2>

        <!-- Busca -->
        <div class="mb-4">
          <input
            type="text"
            v-model="searchTerm"
            @input="searchProducts"
            placeholder="Buscar produtos..."
            class="w-full p-2 border rounded-lg"
          />
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="text-center py-8">
          <p class="text-gray-600">Carregando produtos...</p>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="text-center py-8">
          <p class="text-red-600">{{ error }}</p>
        </div>

        <!-- Empty State -->
        <div v-else-if="filteredProducts.length === 0" class="text-center py-8">
          <p class="text-gray-600">Nenhum produto encontrado</p>
        </div>

        <!-- Product Grid -->
        <div v-else class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div
            v-for="product in filteredProducts"
            :key="product.id"
            class="border rounded-lg p-4 relative hover:shadow-lg transition-shadow"
          >
            <!-- Badge de Estoque -->
            <div class="absolute top-2 right-2 bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">
              Estoque: {{ product.stock }}
            </div>

            <!-- Imagem -->
            <div class="w-full h-32 bg-gray-100 rounded-lg mb-2 flex items-center justify-center hover:bg-gray-200">
              <img
                v-if="product.image"
                :src="product.image"
                :alt="product.name"
                class="w-full h-full object-cover rounded-lg"
              />
              <div v-else class="text-gray-400">
                Sem imagem
              </div>
            </div>

            <!-- Informações do Produto -->
            <h3 class="font-medium mb-1">{{ product.name }}</h3>
            <p class="text-green-600 font-medium mb-2">R$ {{ formatPrice(product.price) }}</p>

            <!-- Botão Adicionar -->
            <button
              @click="addToCart(product)"
              class="w-full bg-indigo-600 text-white py-2 px-4 rounded-lg hover:bg-indigo-700 flex items-center justify-center gap-2"
            >
              <i class="fas fa-cart-plus"></i>
              Adicionar
            </button>
          </div>
        </div>
      </div>

      <!-- Coluna 3: Carrinho -->
      <div class="bg-white rounded-lg shadow p-4">
        <h2 class="text-xl font-bold mb-4">Carrinho</h2>

        <!-- Empty Cart -->
        <div v-if="cart.length === 0" class="text-center py-8">
          <p class="text-gray-600">Carrinho vazio</p>
        </div>

        <!-- Cart Items -->
        <div v-else class="space-y-4">
          <table class="min-w-full">
            <thead>
              <tr>
                <th class="py-2">#</th>
                <th class="py-2">Produto</th>
                <th class="py-2 text-center">Qtd</th>
                <th class="py-2 text-right">Preço</th>
                <th class="py-2 text-right">Total</th>
                <th class="py-2"></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in cart" :key="item.id">
                <td class="py-2">{{ index + 1 }}</td>
                <td class="py-2">{{ item.name }}</td>
                <td class="py-2 text-center">
                  <div class="flex items-center justify-center gap-2">
                    <button 
                      @click="decreaseQuantity(item)"
                      class="px-2 py-1 bg-gray-200 rounded hover:bg-gray-300"
                    >
                      <i class="fas fa-minus"></i>
                    </button>
                    {{ item.quantity }}
                    <button 
                      @click="increaseQuantity(item)"
                      class="px-2 py-1 bg-gray-200 rounded hover:bg-gray-300"
                    >
                      <i class="fas fa-plus"></i>
                    </button>
                  </div>
                </td>
                <td class="py-2 text-right">{{ formatCurrency(item.price) }}</td>
                <td class="py-2 text-right">{{ formatCurrency(item.price * item.quantity) }}</td>
                <td class="py-2 text-right">
                  <button 
                    @click="removeFromCart(item)" 
                    class="text-red-600 hover:text-red-800 p-1 rounded-full hover:bg-red-100"
                    title="Remover item"
                  >
                    <i class="fas fa-trash"></i>
                  </button>
                </td>
              </tr>
            </tbody>
            <tfoot>
              <tr>
                <td colspan="4" class="py-2 text-right font-bold">Total:</td>
                <td class="py-2 text-right font-bold">{{ formatCurrency(cartTotal) }}</td>
                <td></td>
              </tr>
            </tfoot>
          </table>

          <!-- Finish Sale Button -->
          <button
            @click="openFinalizationModal"
            :disabled="cart.length === 0"
            class="w-full bg-indigo-600 text-white py-3 px-4 rounded-lg hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed mt-4"
          >
            Finalizar Venda
          </button>
        </div>
      </div>
    </div>

    <!-- Modal de Finalização -->
    <div v-show="showFinalizationModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg w-full max-w-md mx-4">
        <div class="p-6">
          <h2 class="text-xl font-bold mb-4">Finalizar Venda</h2>

          <!-- Busca de Cliente -->
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Cliente
            </label>
            <input
              type="text"
              v-model="customerSearch"
              @input="searchCustomers"
              placeholder="Digite o nome do cliente..."
              class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
            />
          </div>

          <!-- Lista de Clientes -->
          <div v-if="filteredCustomers.length > 0" class="mb-4 max-h-48 overflow-y-auto border rounded-lg divide-y">
            <div
              v-for="customer in filteredCustomers"
              :key="customer.id"
              @click="selectCustomer(customer)"
              class="p-3 hover:bg-indigo-50 cursor-pointer transition-colors"
            >
              <div class="font-medium">{{ customer.name }}</div>
              <div class="text-sm text-gray-500">{{ customer.phone || 'Sem telefone' }}</div>
            </div>
          </div>

          <!-- Cliente Selecionado -->
          <div v-if="selectedCustomer" class="mb-4 p-3 bg-green-50 rounded-lg border border-green-200">
            <div class="font-medium">Cliente Selecionado:</div>
            <div>{{ selectedCustomer.name }}</div>
            <div class="text-sm text-gray-600">{{ selectedCustomer.phone || 'Sem telefone' }}</div>
          </div>

          <!-- Forma de Pagamento -->
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Forma de Pagamento
            </label>
            <select 
              v-model="selectedPaymentMethod"
              class="w-full p-3 border rounded-lg"
            >
              <option value="cash">Dinheiro</option>
              <option value="credit_card">Cartão de Crédito</option>
              <option value="debit_card">Cartão de Débito</option>
              <option value="pix">PIX</option>
            </select>
          </div>

          <!-- Botões -->
          <div class="flex justify-end gap-3 mt-6">
            <button
              @click="closeFinalizationModal"
              class="px-4 py-2 text-gray-600 hover:text-gray-800"
            >
              Cancelar
            </button>
            <button
              @click="handleConfirmSale"
              :disabled="!selectedCustomer || !selectedPaymentMethod"
              class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Confirmar Venda
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de Detalhes do Pedido -->
    <div v-if="showSaleDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg w-full max-w-2xl mx-4 modal-print-area">
        <div class="p-6">
          <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold">Detalhes do Pedido #{{ saleDetails.code }}</h2>
            <div class="flex gap-2">
              <button 
                @click="printSale" 
                class="bg-indigo-600 text-white py-2 px-4 rounded-lg hover:bg-indigo-700"
              >
                Imprimir
              </button>
              <button 
                @click="showSaleDetailsModal = false" 
                class="bg-gray-200 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-300"
              >
                Fechar
              </button>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-x-12 mb-6">
            <div>
              <p class="mb-2"><strong>Data:</strong> {{ formatDate(saleDetails.created_at) }}</p>
              <p><strong>Status:</strong> {{ saleDetails.sale_status }}</p>
            </div>
            <div>
              <p class="mb-2"><strong>Cliente:</strong> {{ saleDetails.customer_name }}</p>
              <p><strong>Método:</strong> {{ saleDetails.payment_method }}</p>
            </div>
          </div>

          <table class="w-full mb-6">
            <thead>
              <tr class="border-b">
                <th class="text-left py-2">#</th>
                <th class="text-left py-2">PRODUTO</th>
                <th class="text-center py-2">QUANTIDADE</th>
                <th class="text-right py-2">PREÇO UNIT.</th>
                <th class="text-right py-2">TOTAL</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in saleDetails.items" :key="item.id" class="border-b">
                <td class="py-3">{{ index + 1 }}</td>
                <td class="py-3">{{ item.product.name }}</td>
                <td class="text-center py-3">{{ item.quantity }}</td>
                <td class="text-right py-3">R$ {{ formatPrice(item.unit_price) }}</td>
                <td class="text-right py-3">R$ {{ formatPrice(item.total) }}</td>
              </tr>
            </tbody>
          </table>

          <div class="text-right">
            <p class="text-lg font-bold">Total: R$ {{ formatPrice(saleDetails.total) }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import axios from '../plugins/axios'
import { format } from 'date-fns'
import { ptBR } from 'date-fns/locale'
import { useToast } from 'vue-toastification'

export default {
  setup() {
    const toast = useToast()
    const loading = ref(false)
    const error = ref(null)
    const products = ref([])
    const filteredProducts = ref([])
    const cart = ref([])
    const searchTerm = ref('')
    const customerSearch = ref('')
    const filteredCustomers = ref([])
    const selectedCustomer = ref(null)
    const selectedPaymentMethod = ref('cash')
    const installments = ref(1)
    const notes = ref('')
    const store_id = ref(1)
    const showSaleDetailsModal = ref(false)
    const saleDetails = ref({})
    const showFinalizationModal = ref(false)

    // Computed para calcular o total do carrinho
    const cartTotal = computed(() => {
      return cart.value.reduce((total, item) => total + (item.price * item.quantity), 0)
    })

    // Buscar produtos ao montar o componente
    onMounted(async () => {
      try {
        loading.value = true
        const response = await axios.get('http://127.0.0.1:8000/api/products')
        if (response.data && response.data.data) {
          products.value = response.data.data
          filteredProducts.value = response.data.data
        } else if (Array.isArray(response.data)) {
          products.value = response.data
          filteredProducts.value = response.data
        } else {
          throw new Error('Formato de dados inválido')
        }
      } catch (err) {
        console.error('Erro ao carregar produtos:', err)
        error.value = 'Erro ao carregar produtos'
        toast.error('Erro ao carregar produtos. Por favor, tente novamente.')
      } finally {
        loading.value = false
      }
    })

    // Computed Properties
    const filteredProductsComputed = computed(() => {
      if (!searchTerm.value) return products.value
      const search = searchTerm.value.toLowerCase()
      return products.value.filter(product => 
        product.name.toLowerCase().includes(search)
      )
    })

    // Methods
    const searchProducts = () => {
      try {
        if (!searchTerm.value.trim()) {
          filteredProducts.value = products.value
          return
        }
        const search = searchTerm.value.toLowerCase().trim()
        filteredProducts.value = products.value.filter(product => 
          product.name.toLowerCase().includes(search)
        )
      } catch (err) {
        console.error('Erro ao filtrar produtos:', err)
        toast.error('Erro ao filtrar produtos')
      }
    }

    const addToCart = (product) => {
      const existingItem = cart.value.find(item => item.id === product.id)
      if (existingItem) {
        existingItem.quantity++
      } else {
        cart.value.push({
          id: product.id,
          name: product.name,
          price: product.price,
          quantity: 1
        })
      }
    }

    const removeFromCart = (item) => {
      const index = cart.value.findIndex(cartItem => cartItem.id === item.id)
      cart.value.splice(index, 1)
    }

    const openFinalizationModal = () => {
      if (cart.value.length === 0) {
        toast.error('Adicione produtos ao carrinho primeiro')
        return
      }
      showFinalizationModal.value = true
      customerSearch.value = ''
      filteredCustomers.value = []
      selectedCustomer.value = null
      selectedPaymentMethod.value = 'cash'
    }

    const closeFinalizationModal = () => {
      showFinalizationModal.value = false
    }

    const searchCustomers = async () => {
      try {
        if (customerSearch.value.length < 2) {
          filteredCustomers.value = []
          return
        }

        loading.value = true
        const response = await axios.get('http://127.0.0.1:8000/api/customers/search', {
          params: { search: customerSearch.value }
        })

        if (response.data && response.data.data) {
          filteredCustomers.value = response.data.data
        } else {
          filteredCustomers.value = []
        }
      } catch (error) {
        console.error('Erro ao buscar clientes:', error)
        toast.error('Erro ao buscar clientes')
        filteredCustomers.value = []
      } finally {
        loading.value = false
      }
    }

    const selectCustomer = (customer) => {
      selectedCustomer.value = customer
      customerSearch.value = ''
      filteredCustomers.value = []
    }

    const formatPrice = (value) => {
      return Number(value).toFixed(2)
    }

    const formatCurrency = (value) => {
      return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
      }).format(value)
    }

    const formatDate = (date) => {
      if (!date) return ''
      return format(new Date(date), "dd/MM/yyyy, HH:mm", { locale: ptBR })
    }

    const showSaleDetails = (sale) => {
      saleDetails.value = {
        ...sale,
        items: sale.items.map(item => ({
          ...item,
          total: item.quantity * item.unit_price
        }))
      }
      showSaleDetailsModal.value = true
    }

    const closeSaleDetailsModal = () => {
      showSaleDetailsModal.value = false
    }

    const printSale = () => {
      window.print()
    }

    const handleConfirmSale = async () => {
      if (!selectedCustomer.value) {
        toast.error('Por favor, selecione um cliente')
        return
      }

      if (!selectedPaymentMethod.value) {
        toast.error('Por favor, selecione uma forma de pagamento')
        return
      }

      try {
        loading.value = true
        const saleData = {
          customer_id: selectedCustomer.value.id,
          payment_method: selectedPaymentMethod.value,
          store_id: store_id.value,
          installments: installments.value,
          items: cart.value.map(item => ({
            product_id: item.id,
            quantity: item.quantity,
            unit_price: item.price,
            total: item.price * item.quantity
          }))
        }

        const response = await axios.post('http://127.0.0.1:8000/api/sales', saleData)

        if (response.data.success) {
          toast.success('Venda realizada com sucesso!')
          cart.value = []
          closeFinalizationModal()
          if (response.data.data) {
            showSaleDetails(response.data.data)
          }
        } else {
          throw new Error(response.data.message || 'Erro ao realizar venda')
        }
      } catch (error) {
        console.error('Erro ao confirmar venda:', error)
        toast.error(error.response?.data?.message || 'Erro ao processar venda')
      } finally {
        loading.value = false
      }
    }

    const decreaseQuantity = (item) => {
      if (item.quantity > 1) {
        item.quantity--
      }
    }

    const increaseQuantity = (item) => {
      item.quantity++
    }

    return {
      loading,
      error,
      products,
      filteredProducts,
      cart,
      cartTotal,
      searchTerm,
      customerSearch,
      filteredCustomers,
      selectedCustomer,
      selectedPaymentMethod,
      installments,
      notes,
      store_id,
      showSaleDetailsModal,
      saleDetails,
      showFinalizationModal,
      searchProducts,
      addToCart,
      removeFromCart,
      formatPrice,
      formatCurrency,
      formatDate,
      openFinalizationModal,
      closeFinalizationModal,
      searchCustomers,
      selectCustomer,
      handleConfirmSale,
      decreaseQuantity,
      increaseQuantity
    }
  }
}
</script>

<style>
@media print {
  body * {
    visibility: hidden;
  }
  .modal-print-area, .modal-print-area * {
    visibility: visible;
  }
  .modal-print-area {
    position: absolute;
    left: 0;
    top: 0;
  }
  .no-print {
    display: none !important;
  }
}
</style>