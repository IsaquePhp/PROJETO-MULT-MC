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

        <!-- Grid de Produtos -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div v-for="product in filteredProducts" :key="product.id" class="border rounded-lg p-4">
            <div class="text-sm text-green-600 mb-2">Estoque: {{ product.stock }}</div>
            <div class="h-32 bg-gray-100 rounded-lg mb-2 flex items-center justify-center">
              <span class="text-gray-400">Sem imagem</span>
            </div>
            <h3 class="font-medium mb-2">{{ product.name }}</h3>
            <div class="text-lg font-bold text-indigo-600 mb-2">R$ {{ formatPrice(product.price) }}</div>
            <button 
              @click="addToCart(product)"
              class="w-full bg-indigo-600 text-white py-2 px-4 rounded hover:bg-indigo-700"
            >
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
          <p class="text-gray-500">Seu carrinho está vazio</p>
        </div>

        <!-- Cart Items -->
        <div v-else class="space-y-4">
          <!-- Cart Items List -->
          <div v-for="(item, index) in cart" :key="item.id" class="flex items-center gap-4 p-3 bg-gray-50 rounded-lg">
            <div class="flex-1">
              <h3 class="font-medium">{{ item.name }}</h3>
              <div class="text-indigo-600">R$ {{ formatPrice(item.price) }}</div>
            </div>
            <div class="flex items-center gap-2">
              <button @click="updateQuantity(index, -1)" class="p-1 rounded hover:bg-gray-200">-</button>
              <span class="w-8 text-center">{{ item.quantity }}</span>
              <button @click="updateQuantity(index, 1)" class="p-1 rounded hover:bg-gray-200">+</button>
              <button @click="removeFromCart(index)" class="text-red-500 hover:text-red-700">×</button>
            </div>
          </div>

          <!-- Busca de Cliente -->
          <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Cliente</label>
            <input
              type="text"
              v-model="customerSearch"
              @input="searchCustomers"
              placeholder="Digite o nome do cliente"
              class="w-full p-3 border rounded-lg mb-2"
            />
            <div class="text-sm text-gray-500 mb-2">Digite pelo menos 2 caracteres para buscar</div>
            
            <!-- Lista de Clientes -->
            <div v-if="filteredCustomers.length > 0" class="mb-4 max-h-40 overflow-y-auto">
              <div
                v-for="customer in filteredCustomers"
                :key="customer.id"
                @click="selectCustomer(customer)"
                class="p-2 hover:bg-gray-50 cursor-pointer rounded"
              >
                {{ customer.name }} - {{ customer.phone }}
              </div>
            </div>

            <!-- Cliente Selecionado -->
            <div v-if="selectedCustomer" class="p-2 bg-green-50 rounded mb-4">
              <div class="font-medium">{{ selectedCustomer.name }}</div>
              <div class="text-sm text-gray-600">{{ selectedCustomer.phone }}</div>
            </div>
          </div>

          <!-- Forma de Pagamento -->
          <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Método de Pagamento</label>
            <select 
              v-model="paymentMethod"
              class="w-full p-2 border rounded"
            >
              <option value="">Selecione o método</option>
              <option value="money">Dinheiro</option>
              <option value="credit_card">Cartão de Crédito</option>
              <option value="debit_card">Cartão de Débito</option>
              <option value="pix">PIX</option>
            </select>
          </div>

          <!-- Totais -->
          <div class="border-t pt-4 mt-4">
            <div class="flex justify-between mb-2">
              <span>Subtotal</span>
              <span>R$ {{ formatPrice(cartSubtotal) }}</span>
            </div>
            <div class="flex justify-between mb-2">
              <span>Desconto (%)</span>
              <input
                type="number"
                v-model="discount"
                class="w-20 text-right border rounded"
                min="0"
                max="100"
              />
            </div>
            <div class="flex justify-between font-bold text-lg">
              <span>Total</span>
              <span>R$ {{ formatPrice(cartTotal) }}</span>
            </div>
          </div>

          <!-- Finalizar Venda -->
          <button
            @click="finalizeSale"
            :disabled="!canFinalize"
            class="w-full bg-emerald-500 text-white py-3 px-4 rounded-lg hover:bg-emerald-600 disabled:opacity-50 disabled:cursor-not-allowed mt-4"
          >
            Finalizar Venda
          </button>
        </div>
      </div>
    </div>

    <!-- Modal de Detalhes da Venda -->
    <div v-if="showSaleDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg w-full max-w-2xl mx-4">
        <div class="p-6">
          <h2 class="text-xl font-bold mb-4">Detalhes da Venda</h2>
          <!-- Detalhes da venda aqui -->
          <button @click="showSaleDetailsModal = false" class="mt-4 px-4 py-2 bg-gray-200 rounded">
            Fechar
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from '../plugins/axios'
import { useToast } from 'vue-toastification'
import html2pdf from 'html2pdf.js'

const toast = useToast()

// Refs
const cart = ref([])
const productSearch = ref('')
const customerSearch = ref('')
const filteredProducts = ref([])
const filteredCustomers = ref([])
const selectedCustomer = ref(null)
const paymentMethod = ref('')
const loading = ref(false)
const error = ref(null)
const discount = ref(0)
const showSaleDetailsModal = ref(false)
const saleDetails = ref(null)

// Computed
const cartSubtotal = computed(() => {
  return cart.value.reduce((total, item) => total + (item.price * item.quantity), 0)
})

const discountAmount = computed(() => {
  return (cartSubtotal.value * discount.value) / 100
})

const cartTotal = computed(() => {
  return cartSubtotal.value - discountAmount.value
})

const canFinalize = computed(() => {
  return cart.value.length > 0 && selectedCustomer.value && paymentMethod.value
})

// Funções
const searchProducts = async () => {
  console.log('Buscando produtos...')
  if (productSearch.value.length < 2) {
    filteredProducts.value = []
    return
  }

  try {
    loading.value = true
    error.value = null
    const response = await axios.get('/products/search', {
      params: { search: productSearch.value }
    })
    console.log('Resposta da busca:', response.data)
    
    // Verifica se a resposta tem a propriedade data e se é um array
    if (response.data && Array.isArray(response.data.data)) {
      filteredProducts.value = response.data.data.map(product => ({
        ...product,
        price: Number(product.price) || 0,
        stock: Number(product.stock) || 0
      }))
    } else {
      console.error('Formato de resposta inválido na busca:', response.data)
      filteredProducts.value = []
    }
  } catch (err) {
    console.error('Erro detalhado ao buscar produtos:', err)
    error.value = err.response?.data?.message || 'Erro ao buscar produtos'
    toast.error(error.value)
    filteredProducts.value = []
  } finally {
    loading.value = false
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

async function searchCustomers() {
  if (!customerSearch.value || customerSearch.value.length < 2) {
    filteredCustomers.value = []
    return
  }

  try {
    loading.value = true
    const response = await axios.get('/customers/search', {
      params: { search: customerSearch.value }
    })
    console.log('Resposta da busca de clientes:', response.data)
    
    // Verifica se a resposta é um array ou se tem a propriedade data
    if (Array.isArray(response.data)) {
      filteredCustomers.value = response.data
    } else if (response.data && Array.isArray(response.data.data)) {
      filteredCustomers.value = response.data.data
    } else {
      console.error('Formato de resposta inválido na busca de clientes:', response.data)
      filteredCustomers.value = []
    }
  } catch (err) {
    console.error('Erro ao buscar clientes:', err)
    toast.error('Erro ao buscar clientes')
    filteredCustomers.value = []
  } finally {
    loading.value = false
  }
}

const selectCustomer = (customer) => {
  selectedCustomer.value = customer
  customerSearch.value = customer.name
  filteredCustomers.value = []
}

const finalizeSale = async () => {
  if (!selectedCustomer.value?.id) {
    toast.error('Por favor, selecione um cliente')
    return
  }

  if (!paymentMethod.value) {
    toast.error('Por favor, selecione um método de pagamento')
    return
  }

  if (cart.value.length === 0) {
    toast.error('O carrinho está vazio')
    return
  }

  try {
    loading.value = true
    
    // Formatar os itens do carrinho
    const items = cart.value.map(item => ({
      product_id: parseInt(item.id),
      quantity: parseInt(item.quantity),
      unit_price: parseFloat(item.price),
      total: parseFloat((item.quantity * item.price).toFixed(2))
    }))

    // Calcular o total
    const subtotal = items.reduce((sum, item) => sum + item.total, 0)
    const discount_value = (parseFloat(discount.value) / 100) * subtotal
    const total = subtotal - discount_value

    // Mapear método de pagamento
    const paymentMethodMap = {
      'dinheiro': 'money',
      'cartao_credito': 'credit_card',
      'cartao_debito': 'debit_card',
      'pix': 'pix'
    }

    const normalizedPaymentMethod = paymentMethod.value.toLowerCase()
    const payment_method = paymentMethodMap[normalizedPaymentMethod] || normalizedPaymentMethod

    // Validar método de pagamento
    if (!['money', 'credit_card', 'debit_card', 'pix'].includes(payment_method)) {
      toast.error('Método de pagamento inválido. Use: Dinheiro, Cartão de Crédito, Cartão de Débito ou PIX')
      return
    }

    const saleData = {
      customer_id: parseInt(selectedCustomer.value.id),
      payment_method: payment_method,
      items: items,
      subtotal: parseFloat(subtotal.toFixed(2)),
      discount_percentage: parseFloat(discount.value) || 0,
      discount_value: parseFloat(discount_value.toFixed(2)),
      total: parseFloat(total.toFixed(2)),
      company_id: 1,
      store_id: 1, // Usando ID fixo da loja
      installments: payment_method === 'credit_card' ? 1 : null // Parcelas apenas para cartão de crédito
    }

    console.log('Dados da venda a serem enviados:', JSON.stringify(saleData, null, 2))

    const response = await axios.post('/sales', saleData)
    console.log('Resposta da venda:', response.data)
    
    if (response.data) {
      saleDetails.value = response.data.data || response.data
      showSaleDetailsModal.value = true
      
      // Limpa o carrinho após a venda
      cart.value = []
      selectedCustomer.value = null
      customerSearch.value = ''
      paymentMethod.value = ''
      discount.value = 0
      
      toast.success('Venda realizada com sucesso!')
    }
  } catch (err) {
    console.error('Erro ao finalizar venda:', err)
    console.error('Detalhes do erro:', err.response?.data)
    console.error('Campos com erro:', err.response?.data?.errors)
    
    if (err.response?.data?.errors) {
      const errors = err.response.data.errors
      Object.entries(errors).forEach(([field, messages]) => {
        console.log(`Campo ${field}:`, messages)
        if (Array.isArray(messages)) {
          messages.forEach(message => {
            toast.error(`${field}: ${message}`)
          })
        } else {
          toast.error(`${field}: ${messages}`)
        }
      })
    } else if (err.response?.data?.message) {
      toast.error(err.response.data.message)
    } else {
      toast.error('Erro ao finalizar venda. Verifique os dados e tente novamente.')
    }
  } finally {
    loading.value = false
  }
}

const formatPrice = (value) => {
  if (value === undefined || value === null) return '0.00'
  return Number(value).toFixed(2)
}

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('pt-BR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const formatPaymentMethod = (method) => {
  const methods = {
    dinheiro: 'Dinheiro',
    cartao_credito: 'Cartão de Crédito',
    cartao_debito: 'Cartão de Débito',
    pix: 'PIX'
  }
  return methods[method] || method
}

const generatePDF = () => {
  const element = document.querySelector('.modal-print-area')
  const opt = {
    margin: 1,
    filename: `venda-${saleDetails.value?.code || 'comprovante'}.pdf`,
    image: { type: 'jpeg', quality: 0.98 },
    html2canvas: { scale: 2 },
    jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
  }

  html2pdf().set(opt).from(element).save()
}

// Lifecycle
onMounted(async () => {
  console.log('PDV montado, carregando produtos iniciais...')
  try {
    loading.value = true
    error.value = null
    const response = await axios.get('/products')
    console.log('Resposta completa:', response)
    
    // Verifica se a resposta tem a propriedade data e se é um array
    if (response.data && Array.isArray(response.data.data)) {
      filteredProducts.value = response.data.data.map(product => ({
        ...product,
        price: Number(product.price) || 0,
        stock: Number(product.stock) || 0
      }))
    } else {
      console.error('Formato de resposta inválido:', response.data)
      filteredProducts.value = []
    }
    console.log('Produtos processados:', filteredProducts.value)
  } catch (err) {
    console.error('Erro detalhado ao carregar produtos:', err)
    error.value = err.response?.data?.message || 'Erro ao carregar produtos iniciais'
    toast.error(error.value)
    filteredProducts.value = []
  } finally {
    loading.value = false
  }
})
</script>