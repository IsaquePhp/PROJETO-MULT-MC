<template>
  <div>
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center mb-6">
          <h1 class="text-2xl font-semibold text-gray-900">Pagamentos</h1>
          <button @click="showAddModal = true" class="btn-primary">
            Adicionar Pagamento
          </button>
        </div>

        <!-- Filtros -->
        <div class="card mb-6">
          <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
            <div>
              <label class="form-label">Buscar</label>
              <input 
                type="text" 
                v-model="filters.search" 
                class="input-field" 
                placeholder="Nome do Cliente"
              />
            </div>
            <div>
              <label class="form-label">Status</label>
              <select v-model="filters.status" class="input-field">
                <option value="">Todos</option>
                <option value="pending">Pendente</option>
                <option value="paid">Pago</option>
                <option value="partial">Parcial</option>
                <option value="late">Atrasado</option>
                <option value="cancelled">Cancelado</option>
              </select>
            </div>
            <div>
              <label class="form-label">Método de Pagamento</label>
              <select v-model="filters.payment_method" class="input-field">
                <option value="">Todos</option>
                <option value="money">Dinheiro</option>
                <option value="credit_card">Cartão de Crédito</option>
                <option value="debit_card">Cartão de Débito</option>
                <option value="pix">PIX</option>
                <option value="bank_slip">Boleto</option>
              </select>
            </div>
            <div>
              <label class="form-label">Data de Vencimento</label>
              <input 
                type="date" 
                v-model="filters.due_date" 
                class="input-field"
              />
            </div>
          </div>
        </div>

        <!-- Lista de Pagamentos -->
        <div class="card">
          <table class="min-w-full divide-y divide-gray-200">
            <thead>
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Cliente
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Valor
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Status
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Método
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Vencimento
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Ações
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="payment in payments" :key="payment.id">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                  {{ payment.customer.name }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ formatCurrency(payment.amount) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  <span :class="getStatusClass(payment.status)">
                    {{ getStatusText(payment.status) }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ getPaymentMethodText(payment.payment_method) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ formatDate(payment.due_date) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <button @click="editPayment(payment)" class="text-indigo-600 hover:text-indigo-900">
                    Editar
                  </button>
                  <button @click="deletePayment(payment.id)" class="text-red-600 hover:text-red-900 ml-4">
                    Excluir
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </main>

    <!-- Modal de Adicionar/Editar Pagamento -->
    <div v-if="showAddModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center">
      <div class="bg-white rounded-lg p-6 max-w-md w-full">
        <h2 class="text-xl font-semibold mb-4">
          {{ editingPayment ? 'Editar Pagamento' : 'Novo Pagamento' }}
        </h2>
        <form @submit.prevent="savePayment" class="space-y-4">
          <div>
            <label class="form-label">Cliente *</label>
            <select v-model="paymentForm.customer_id" class="input-field" required>
              <option v-for="customer in customers" :key="customer.id" :value="customer.id">
                {{ customer.name }}
              </option>
            </select>
          </div>
          <div>
            <label class="form-label">Valor *</label>
            <input 
              type="number" 
              v-model="paymentForm.amount" 
              class="input-field" 
              required
            />
          </div>
          <div>
            <label class="form-label">Método de Pagamento *</label>
            <select v-model="paymentForm.payment_method" class="input-field" required>
              <option value="money">Dinheiro</option>
              <option value="credit_card">Cartão de Crédito</option>
              <option value="debit_card">Cartão de Débito</option>
              <option value="pix">PIX</option>
              <option value="bank_slip">Boleto</option>
            </select>
          </div>
          <div>
            <label class="form-label">Data de Vencimento *</label>
            <input 
              type="date" 
              v-model="paymentForm.due_date" 
              class="input-field" 
              required
            />
          </div>
          <div class="flex justify-end space-x-4">
            <button 
              type="button" 
              @click="closeModal" 
              class="btn-secondary"
            >
              Cancelar
            </button>
            <button 
              type="submit" 
              class="btn-primary"
            >
              Salvar
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Toast de Sucesso -->
    <div 
      v-if="showToast" 
      class="fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded shadow-lg"
    >
      {{ toastMessage }}
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from '../plugins/axios'
import { useToast } from '../composables/useToast'

const payments = ref([])
const customers = ref([])
const showAddModal = ref(false)
const editingPayment = ref(null)
const errors = ref({})
const isSaving = ref(false)
const { showToast, toastMessage, showSuccessToast } = useToast()

const filters = ref({
  search: '',
  status: '',
  payment_method: '',
  due_date: ''
})

const paymentForm = ref({
  customer_id: '',
  amount: '',
  payment_method: 'money',
  due_date: ''
})

const isFormValid = computed(() => {
  return paymentForm.value.customer_id &&
    paymentForm.value.amount &&
    paymentForm.value.payment_method &&
    paymentForm.value.due_date &&
    Object.keys(errors.value).length === 0
})

onMounted(async () => {
  await loadPayments()
  await loadCustomers()
})

async function loadPayments() {
  try {
    const response = await axios.get('/payments', { params: filters.value })
    payments.value = response.data.data
  } catch (error) {
    showToast('Erro ao carregar pagamentos. Tente novamente.')
  }
}

async function loadCustomers() {
  try {
    const response = await axios.get('/customers')
    customers.value = response.data.data
  } catch (error) {
    showToast('Erro ao carregar clientes. Tente novamente.')
  }
}

async function savePayment() {
  errors.value = {}

  if (!isFormValid.value) {
    showToast('Por favor, preencha todos os campos obrigatórios.')
    return
  }

  isSaving.value = true
  try {
    const formData = { ...paymentForm.value }

    if (editingPayment.value) {
      await axios.put(`/payments/${editingPayment.value.id}`, formData)
      showSuccessToast('Pagamento atualizado com sucesso!')
    } else {
      await axios.post('/payments', formData)
      showSuccessToast('Pagamento criado com sucesso!')
    }

    await loadPayments()
    closeModal()
  } catch (error) {
    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors
    } else {
      showToast('Erro ao salvar pagamento. Tente novamente.')
    }
  } finally {
    isSaving.value = false
  }
}

function editPayment(payment) {
  editingPayment.value = payment
  paymentForm.value = { ...payment }
  showAddModal.value = true
}

function deletePayment(id) {
  if (confirm('Tem certeza que deseja excluir este pagamento?')) {
    axios.delete(`/payments/${id}`)
      .then(() => {
        showSuccessToast('Pagamento excluído com sucesso!')
        loadPayments()
      })
      .catch(() => {
        showToast('Erro ao excluir pagamento. Tente novamente.')
      })
  }
}

function closeModal() {
  showAddModal.value = false
  editingPayment.value = null
  paymentForm.value = {
    customer_id: '',
    amount: '',
    payment_method: 'money',
    due_date: ''
  }
}

function getStatusClass(status) {
  switch (status) {
    case 'paid':
      return 'text-green-500'
    case 'partial':
      return 'text-yellow-500'
    case 'late':
      return 'text-red-500'
    default:
      return 'text-gray-500'
  }
}

function getStatusText(status) {
  switch (status) {
    case 'paid':
      return 'Pago'
    case 'partial':
      return 'Parcial'
    case 'late':
      return 'Atrasado'
    case 'cancelled':
      return 'Cancelado'
    default:
      return 'Pendente'
  }
}

function getPaymentMethodText(method) {
  switch (method) {
    case 'credit_card':
      return 'Cartão de Crédito'
    case 'debit_card':
      return 'Cartão de Débito'
    case 'pix':
      return 'PIX'
    case 'bank_slip':
      return 'Boleto'
    default:
      return 'Dinheiro'
  }
}

function formatCurrency(value) {
  return value.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })
}

function formatDate(date) {
  return new Date(date).toLocaleDateString('pt-BR')
}
</script>

<style scoped>
.input-field {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  transition: border-color 0.3s ease;
}
.input-field:focus {
  outline: none;
  border-color: #2563eb;
}
.btn-primary {
  background-color: #2563eb;
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 0.375rem;
  transition: background-color 0.3s ease;
}
.btn-primary:hover {
  background-color: #1d4ed8;
}
.btn-secondary {
  background-color: #e5e7eb;
  color: #374151;
  padding: 0.5rem 1rem;
  border-radius: 0.375rem;
  transition: background-color 0.3s ease;
}
.btn-secondary:hover {
  background-color: #d1d5db;
}
.card {
  background-color: white;
  border-radius: 0.375rem;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
  padding: 1rem;
}
</style>
