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
            v-model="productSearch"
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
          <div
            v-for="(item, index) in cart"
            :key="item.id"
            class="border-b pb-4 last:border-b-0"
          >
            <div class="flex justify-between items-start mb-2">
              <div>
                <div class="font-medium">{{ item.name }}</div>
                <div class="text-sm text-gray-600">R$ {{ formatPrice(item.price) }}</div>
              </div>
              <div class="flex items-center space-x-2">
                <button @click="updateQuantity(index, -1)" 
                  class="text-gray-500 hover:text-gray-700">
                  -
                </button>
                <span class="text-gray-700">{{ item.quantity }}</span>
                <button @click="updateQuantity(index, 1)"
                  class="text-gray-500 hover:text-gray-700">
                  +
                </button>
                <button @click="removeFromCart(index)"
                  class="text-red-500 hover:text-red-700 ml-2">
                  Remover
                </button>
              </div>
            </div>
            <div class="text-right text-sm text-gray-600">
              Subtotal: R$ {{ formatPrice(item.price * item.quantity) }}
            </div>
          </div>

          <!-- Total -->
          <div class="border-t pt-4">
            <div class="flex justify-between items-center font-bold">
              <span>Total:</span>
              <span>R$ {{ formatPrice(cartTotal) }}</span>
            </div>
          </div>

          <!-- Finish Sale Button -->
          <button
            @click="openFinalizationModal"
            :disabled="cart.length === 0"
            class="w-full bg-indigo-600 text-white py-3 px-4 rounded-lg hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Finalizar Venda
          </button>
        </div>
      </div>
    </div>

    <!-- Modal de Finalização -->
    <div v-if="showFinalizationModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg w-full max-w-md mx-4">
        <div class="p-6">
          <h2 class="text-xl font-bold mb-4">Selecionar Cliente</h2>
          
          <!-- Busca de Cliente -->
          <div class="mb-4">
            <input
              type="text"
              v-model="customerSearch"
              @input="searchCustomers"
              placeholder="Digite o nome do cliente..."
              class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
            />
            <p class="text-sm text-gray-500 mt-1">Digite pelo menos 2 caracteres para buscar</p>
          </div>

          <!-- Loading -->
          <div v-if="loading" class="text-center py-4">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600 mx-auto"></div>
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
              <div class="text-sm text-gray-500">
                {{ customer.phone || customer.document || 'Sem contato' }}
              </div>
            </div>
          </div>

          <!-- Nenhum Resultado -->
          <div v-if="customerSearch.length >= 2 && filteredCustomers.length === 0 && !loading" class="text-center py-4 text-gray-500">
            Nenhum cliente encontrado
          </div>

          <!-- Debug Info -->
          <div v-if="customerSearch.length >= 2" class="text-xs text-gray-500 mt-2">
            Status da busca: {{ loading ? 'Buscando...' : 'Concluído' }}
            <br>
            Resultados encontrados: {{ filteredCustomers.length }}
          </div>

          <!-- Cliente Selecionado -->
          <div v-if="selectedCustomer" class="mb-6 bg-green-50 p-4 rounded-lg border border-green-200">
            <div class="font-medium text-green-800">Cliente Selecionado</div>
            <div class="text-green-700">{{ selectedCustomer.name }}</div>
            <div class="text-sm text-green-600">
              {{ selectedCustomer.phone || selectedCustomer.document || 'Sem contato' }}
            </div>
          </div>

          <!-- Forma de Pagamento -->
          <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Forma de Pagamento
            </label>
            <select 
              v-model="paymentMethod" 
              class="w-full p-3 border rounded-lg bg-white"
              :class="{'border-red-300': !paymentMethod && selectedCustomer}"
            >
              <option value="">Selecione uma forma de pagamento</option>
              <option value="cash">Dinheiro</option>
              <option value="credit_card">Cartão de Crédito</option>
              <option value="debit_card">Cartão de Débito</option>
              <option value="pix">PIX</option>
            </select>
          </div>

          <!-- Botões -->
          <div class="flex justify-end gap-3">
            <button
              @click="closeFinalizationModal"
              class="px-4 py-2 text-gray-600 hover:text-gray-800"
            >
              Cancelar
            </button>
            <button
              @click="confirmSale"
              :disabled="!selectedCustomer || !paymentMethod || loading"
              class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {{ loading ? 'Processando...' : 'Confirmar Venda' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import axios from '../plugins/axios'

export default {
  name: 'Sales',
  
  setup() {
    // Estado
    const sales = ref([])
    const loading = ref(false)
    const error = ref(null)
    const searchQuery = ref('')
    const pagination = ref({
      current_page: 1,
      total: 0,
      per_page: 10
    })

    const productSearch = ref('')
    const filteredProducts = ref([])
    const cart = ref([])

    const customers = ref([])
    const customerSearch = ref('')
    const filteredCustomers = ref([])
    const selectedCustomer = ref(null)
    const paymentMethod = ref('')
    const showFinalizationModal = ref(false)
    const showCustomerResults = ref(false)
    
    // Novo estado para o resumo do pedido
    const showOrderSummary = ref(false)
    const currentOrder = ref(null)

    // Computed
    const filteredSales = computed(() => {
      if (!searchQuery.value) return sales.value
      
      const search = searchQuery.value.toLowerCase()
      return sales.value.filter(sale => 
        sale.code.toLowerCase().includes(search) ||
        sale.customer?.name.toLowerCase().includes(search)
      )
    })

    const cartTotal = computed(() => {
      return cart.value.reduce((total, item) => {
        return total + (item.price * item.quantity)
      }, 0)
    })

    // Métodos
    function formatDateTime(dateString) {
      if (!dateString) return ''
      return new Date(dateString).toLocaleString('pt-BR')
    }

    function formatPrice(value) {
      return Number(value).toLocaleString('pt-BR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      })
    }

    async function loadSales(page = 1) {
      loading.value = true
      error.value = null
      
      try {
        const response = await axios.get('/sales', {
          params: {
            page,
            per_page: pagination.value.per_page
          }
        })

        if (response.data.success) {
          const paginatedData = response.data.data
          sales.value = paginatedData.data || []
          pagination.value = {
            current_page: paginatedData.current_page || 1,
            total: paginatedData.total || 0,
            per_page: paginatedData.per_page || 10
          }
        } else {
          sales.value = []
          error.value = 'Erro ao carregar vendas'
        }
      } catch (err) {
        error.value = err.message || 'Erro ao carregar vendas'
        sales.value = []
      } finally {
        loading.value = false
      }
    }

    async function loadCustomers() {
      try {
        const response = await axios.get('/customers')
        if (response.data.success) {
          customers.value = response.data.data
        }
      } catch (err) {
        console.error('Erro ao carregar clientes:', err)
      }
    }

    async function searchProducts() {
      try {
        loading.value = true
        
        console.log('Buscando produtos...')
        
        const response = await axios.get('/products', {
          params: {
            search: productSearch.value,
            stock: 'in'
          }
        })
        
        console.log('Resposta da API:', response.data)
        
        if (response.data && response.data.data) {
          // Mapear os produtos da resposta da API
          filteredProducts.value = response.data.data.map(product => ({
            id: product.id,
            name: product.name,
            price: parseFloat(product.price || 0),
            stock: parseInt(product.stock || 0),
            image: product.image_url
          }))
          
          console.log('Produtos filtrados:', filteredProducts.value)
        } else {
          filteredProducts.value = []
          console.error('Formato de resposta inválido:', response.data)
        }
      } catch (error) {
        console.error('Erro ao buscar produtos:', error)
        filteredProducts.value = []
      } finally {
        loading.value = false
      }
    }

    function addToCart(product) {
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

    function updateQuantity(index, change) {
      const item = cart.value[index]
      const newQuantity = item.quantity + change

      if (newQuantity > 0) {
        item.quantity = newQuantity
      } else {
        removeFromCart(index)
      }
    }

    function removeFromCart(index) {
      cart.value.splice(index, 1)
    }

    async function finalizeSale() {
      if (cart.value.length === 0) return

      try {
        // Implementar lógica de finalização da venda
        console.log('Finalizando venda:', {
          items: cart.value,
          total: cartTotal.value
        })

        // Limpar carrinho após venda
        cart.value = []
      } catch (error) {
        console.error('Erro ao finalizar venda:', error)
      }
    }

    function openFinalizationModal() {
      showFinalizationModal.value = true
    }

    function closeFinalizationModal() {
      showFinalizationModal.value = false
      selectedCustomer.value = null
      paymentMethod.value = ''
    }

    async function searchCustomers() {
      console.log('Iniciando busca de clientes:', customerSearch.value)
      
      if (customerSearch.value.length < 2) {
        showCustomerResults.value = false
        filteredCustomers.value = []
        return
      }

      try {
        loading.value = true
        showCustomerResults.value = true
        
        console.log('Fazendo requisição para /customers com busca:', customerSearch.value)
        
        const response = await axios.get('/customers', {
          params: {
            name: customerSearch.value // Alterado de 'search' para 'name' para corresponder à API
          }
        })

        console.log('Resposta completa da API:', response)

        if (response.data && response.data.data) {
          console.log('Clientes encontrados:', response.data.data)
          filteredCustomers.value = response.data.data
        } else {
          console.log('Nenhum cliente encontrado na resposta')
          filteredCustomers.value = []
        }
      } catch (error) {
        console.error('Erro detalhado ao buscar clientes:', error.response || error)
        filteredCustomers.value = []
      } finally {
        loading.value = false
      }
    }

    function selectCustomer(customer) {
      selectedCustomer.value = customer
      customerSearch.value = customer.name
      showCustomerResults.value = false
      console.log('Cliente selecionado:', customer)
    }

    async function confirmSale() {
      if (!selectedCustomer.value || !paymentMethod.value) {
        return
      }

      try {
        loading.value = true
        
        // Log para debug
        console.log('Método de pagamento selecionado:', paymentMethod.value)
        
        // Formatar os dados no formato que a API espera
        const formattedItems = cart.value.map(item => {
          const unit_price = parseFloat(item.price).toFixed(2)
          const total = parseFloat(item.price * item.quantity).toFixed(2)
          
          return {
            product_id: item.id,
            quantity: item.quantity,
            unit_price: unit_price,
            price: unit_price,
            total: total,
            subtotal: total
          }
        })

        const saleData = {
          customer_id: selectedCustomer.value.id,
          payment_method: paymentMethod.value,
          total_amount: parseFloat(cartTotal.value).toFixed(2),
          items: formattedItems
        }

        console.log('Dados completos da venda:', saleData)

        const response = await axios.post('/sales', saleData)

        if (response.data.success) {
          // Armazenar dados do pedido para exibição do resumo
          currentOrder.value = {
            id: response.data.data.id,
            customer: selectedCustomer.value,
            payment_method: paymentMethod.value,
            items: cart.value,
            total: cartTotal.value,
            date: new Date().toLocaleString()
          }
          
          // Esconder modal de finalização e mostrar resumo
          showFinalizationModal.value = false
          showOrderSummary.value = true
          
          // Limpar carrinho
          cart.value = []
          
          // Recarregar produtos para atualizar estoque
          await searchProducts()
        } else {
          throw new Error(response.data.message || 'Erro ao finalizar venda')
        }
      } catch (error) {
        console.error('Erro detalhado ao finalizar venda:', error.response?.data || error)
        const errorMessage = error.response?.data?.message || 'Erro ao finalizar venda. Verifique os dados e tente novamente.'
        const validationErrors = error.response?.data?.errors
        
        if (validationErrors) {
          const errorMessages = Object.entries(validationErrors)
            .map(([field, messages]) => `${field}: ${messages.join(', ')}`)
            .join('\n')
          alert(`Erros de validação:\n${errorMessages}`)
        } else {
          alert(errorMessage)
        }
      } finally {
        loading.value = false
      }
    }

    // Lifecycle
    onMounted(() => {
      console.log('Componente montado, buscando produtos...')
      searchProducts()
    })

    return {
      // Data
      sales,
      loading,
      error,
      searchQuery,
      pagination,
      filteredSales,
      productSearch,
      filteredProducts,
      cart,
      customers,
      customerSearch,
      filteredCustomers,
      selectedCustomer,
      paymentMethod,
      showFinalizationModal,
      showCustomerResults,

      // Computed
      cartTotal,

      // Funções
      formatDateTime,
      formatPrice,
      loadSales,
      searchProducts,
      addToCart,
      updateQuantity,
      removeFromCart,
      finalizeSale,
      openFinalizationModal,
      closeFinalizationModal,
      searchCustomers,
      selectCustomer,
      confirmSale
    }
  }
}
</script>