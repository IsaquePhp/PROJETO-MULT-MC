<template>
  <div class="p-6">
    <!-- Título e Subtítulo -->
    <div class="mb-6">
      <h2 class="text-2xl font-bold text-gray-900">PDV</h2>
      <p class="text-sm text-gray-600">Registre vendas e gerencie o caixa</p>
    </div>

    <div class="grid grid-cols-12 gap-6">
      <!-- Coluna Esquerda - Produtos -->
      <div class="col-span-8">
        <div class="bg-white rounded-lg shadow p-6">
          <!-- Busca de Produtos -->
          <div class="mb-6">
            <div class="relative">
              <input
                v-model="productSearch"
                type="text"
                placeholder="Buscar produtos..."
                class="w-full h-[45px] pl-10 pr-4 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                @input="searchProducts"
              />
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 absolute left-3 top-[10px]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>

            <!-- Resultados da Busca -->
            <div v-if="showProductResults && filteredProducts.length > 0" class="absolute z-10 mt-1 w-full bg-white rounded-lg shadow-lg border border-gray-200 max-h-64 overflow-y-auto">
              <div v-for="product in filteredProducts" :key="product.id" @click="addToCart(product)" class="p-3 hover:bg-gray-50 cursor-pointer flex items-center space-x-3">
                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                  <img v-if="product.image" :src="product.image" class="w-full h-full object-cover rounded-lg" />
                  <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                </div>
                <div>
                  <div class="font-medium text-gray-900">{{ product.name }}</div>
                  <div class="text-sm text-gray-500">R$ {{ formatPrice(product.price) }}</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Lista de Produtos no Carrinho -->
          <div class="space-y-4">
            <div v-if="cart.length === 0" class="text-center py-8 text-gray-500">
              Nenhum produto adicionado
            </div>
            <div v-else v-for="(item, index) in cart" :key="index" class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
              <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center">
                  <img v-if="item.image" :src="item.image" class="w-full h-full object-cover rounded-lg" />
                  <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                </div>
                <div>
                  <div class="font-medium text-gray-900">{{ item.name }}</div>
                  <div class="text-sm text-gray-500">R$ {{ formatPrice(item.price) }}</div>
                </div>
              </div>
              <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-2">
                  <button @click="updateQuantity(index, -1)" class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center hover:bg-gray-300 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                    </svg>
                  </button>
                  <span class="w-8 text-center">{{ item.quantity }}</span>
                  <button @click="updateQuantity(index, 1)" class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center hover:bg-gray-300 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                  </button>
                </div>
                <button @click="removeFromCart(index)" class="text-red-500 hover:text-red-700 transition-colors">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Coluna Direita - Resumo e Finalização -->
      <div class="col-span-4">
        <div class="bg-white rounded-lg shadow p-6">
          <!-- Cliente -->
          <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Cliente</label>
            <select
              v-model="selectedCustomer"
              class="w-full h-[45px] px-4 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
            >
              <option value="">Selecione um cliente</option>
              <option v-for="customer in customers" :key="customer?.id" :value="customer">
                {{ customer?.name }}
              </option>
            </select>
          </div>

          <!-- Resumo -->
          <div class="space-y-4">
            <div class="flex justify-between text-sm">
              <span class="text-gray-600">Subtotal</span>
              <span class="font-medium">R$ {{ formatPrice(cartSubtotal) }}</span>
            </div>
            <div class="flex justify-between text-sm">
              <span class="text-gray-600">Desconto</span>
              <div class="flex items-center space-x-2">
                <input
                  v-model="discount"
                  type="number"
                  min="0"
                  class="w-20 h-8 px-2 text-right rounded border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                />
                <span class="text-gray-500">%</span>
              </div>
            </div>
            <div class="pt-4 border-t">
              <div class="flex justify-between items-center">
                <span class="text-lg font-medium text-gray-900">Total</span>
                <span class="text-2xl font-bold text-indigo-600">R$ {{ formatPrice(cartTotal) }}</span>
              </div>
            </div>
          </div>

          <!-- Método de Pagamento -->
          <div class="mt-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Método de Pagamento</label>
            <select
              v-model="paymentMethod"
              class="w-full h-[45px] px-4 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
            >
              <option value="">Selecione o método</option>
              <option value="cash">Dinheiro</option>
              <option value="credit">Cartão de Crédito</option>
              <option value="debit">Cartão de Débito</option>
              <option value="pix">PIX</option>
            </select>
          </div>

          <!-- Botão Finalizar -->
          <button
            @click="finalizeSale"
            :disabled="!canFinalize"
            class="w-full h-[45px] mt-6 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Finalizar Venda
          </button>
        </div>
      </div>
    </div>

    <!-- Modal de Conclusão -->
    <div v-if="showCompletionModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-8 w-full max-w-md">
        <div class="text-center">
          <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
            <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
          </div>
          <h3 class="text-lg font-medium text-gray-900 mb-2">Venda Finalizada</h3>
          <p class="text-sm text-gray-500 mb-6">
            Venda realizada com sucesso! O que deseja fazer agora?
          </p>
          <div class="flex justify-center space-x-4">
            <button
              @click="printReceipt"
              class="h-[45px] px-6 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"
            >
              Imprimir Comprovante
            </button>
            <button
              @click="closeCompletionModal"
              class="h-[45px] px-6 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors"
            >
              Nova Venda
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from '../plugins/axios'
import { useToast } from 'vue-toastification'

const toast = useToast()

// Estado
const productSearch = ref('')
const showProductResults = ref(false)
const filteredProducts = ref([])
const cart = ref([])
const customers = ref([])
const selectedCustomer = ref(null)
const discount = ref(0)
const paymentMethod = ref('')
const showCompletionModal = ref(false)
const loading = ref(false)
const searchTimeout = ref(null)

// Computed
const cartSubtotal = computed(() => {
  return cart.value.reduce((total, item) => total + (item.price * item.quantity), 0)
})

const cartTotal = computed(() => {
  const subtotal = cartSubtotal.value
  const discountAmount = subtotal * (discount.value / 100)
  return subtotal - discountAmount
})

const canFinalize = computed(() => {
  return cart.value.length > 0 && selectedCustomer.value && paymentMethod.value
})

// Funções
const formatPrice = (value) => {
  return Number(value).toFixed(2)
}

const searchProducts = async () => {
  if (searchTimeout.value) {
    clearTimeout(searchTimeout.value)
  }

  if (productSearch.value.length < 2) {
    showProductResults.value = false
    filteredProducts.value = []
    return
  }

  searchTimeout.value = setTimeout(async () => {
    try {
      console.log('Buscando produtos com:', productSearch.value)
      const response = await axios.get(`/products`, {
        params: {
          search: productSearch.value
        }
      })
      console.log('Resposta da API:', response.data)
      
      if (response.data.success && Array.isArray(response.data.data)) {
        filteredProducts.value = response.data.data
        console.log('Produtos encontrados:', filteredProducts.value)
      } else {
        console.log('Nenhum produto encontrado')
        filteredProducts.value = []
      }
      showProductResults.value = true
    } catch (error) {
      console.error('Erro ao buscar produtos:', error)
      toast.error('Erro ao buscar produtos')
      filteredProducts.value = []
    }
  }, 300)
}

const addToCart = (product) => {
  const existingItem = cart.value.find(item => item.id === product.id)
  
  if (existingItem) {
    existingItem.quantity++
  } else {
    cart.value.push({
      ...product,
      quantity: 1
    })
  }
  
  showProductResults.value = false
  productSearch.value = ''
  toast.success('Produto adicionado ao carrinho')
}

const updateQuantity = (index, change) => {
  const item = cart.value[index]
  const newQuantity = item.quantity + change
  
  if (newQuantity > 0) {
    item.quantity = newQuantity
  } else {
    removeFromCart(index)
  }
}

const removeFromCart = (index) => {
  cart.value.splice(index, 1)
  toast.success('Produto removido do carrinho')
}

const finalizeSale = async () => {
  if (!canFinalize.value) return

  try {
    loading.value = true
    const sale = {
      customer_id: selectedCustomer.value.id,
      items: cart.value.map(item => ({
        product_id: item.id,
        quantity: item.quantity,
        price: item.price
      })),
      discount: discount.value,
      payment_method: paymentMethod.value,
      total: cartTotal.value
    }

    await axios.post('/sales', sale)
    showCompletionModal.value = true
    toast.success('Venda finalizada com sucesso!')
  } catch (error) {
    console.error('Erro ao finalizar venda:', error)
    toast.error('Erro ao finalizar venda')
  } finally {
    loading.value = false
  }
}

const printReceipt = () => {
  // Implementar impressão do comprovante
  toast.info('Impressão do comprovante em desenvolvimento')
}

const closeCompletionModal = () => {
  showCompletionModal.value = false
  cart.value = []
  selectedCustomer.value = null
  discount.value = 0
  paymentMethod.value = ''
}

const loadCustomers = async () => {
  try {
    const response = await axios.get('/customers')
    if (Array.isArray(response.data)) {
      customers.value = response.data.filter(customer => customer && customer.id)
    } else {
      customers.value = []
    }
  } catch (error) {
    console.error('Erro ao carregar clientes:', error)
    toast.error('Erro ao carregar clientes')
    customers.value = []
  }
}

// Lifecycle hooks
onMounted(async () => {
  try {
    await loadCustomers()
    // Carregar todos os produtos ao montar
    const response = await axios.get('/products')
    if (response.data.success && Array.isArray(response.data.data)) {
      filteredProducts.value = response.data.data
      showProductResults.value = true
    }
  } catch (error) {
    console.error('Erro ao inicializar PDV:', error)
  }
})
</script>
