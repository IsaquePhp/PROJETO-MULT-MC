<template>
  <div>
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center mb-6">
          <h1 class="text-2xl font-semibold text-gray-900">Clientes</h1>
          <button @click="showAddModal = true" class="btn-primary">
            Adicionar Cliente
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
                placeholder="Nome, Email ou CPF/CNPJ"
              />
            </div>
            <div>
              <label class="form-label">Tipo</label>
              <select v-model="filters.type" class="input-field">
                <option value="">Todos</option>
                <option value="individual">Pessoa Física</option>
                <option value="company">Pessoa Jurídica</option>
              </select>
            </div>
            <div>
              <label class="form-label">Status</label>
              <select v-model="filters.status" class="input-field">
                <option value="">Todos</option>
                <option value="active">Ativo</option>
                <option value="inactive">Inativo</option>
              </select>
            </div>
            <div>
              <label class="form-label">Cidade</label>
              <input 
                type="text" 
                v-model="filters.city" 
                class="input-field" 
                placeholder="Cidade"
              />
            </div>
          </div>
        </div>

        <!-- Lista de Clientes -->
        <div class="card">
          <table class="min-w-full divide-y divide-gray-200">
            <thead>
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Nome
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Email
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  CPF/CNPJ
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Tipo
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Status
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Ações
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="customer in customers" :key="customer.id">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                  {{ customer.name }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ customer.email }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ customer.cpf_cnpj }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ customer.type === 'individual' ? 'Pessoa Física' : 'Pessoa Jurídica' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  <span :class="getStatusClass(customer.status)">
                    {{ customer.status === 'active' ? 'Ativo' : 'Inativo' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <button @click="editCustomer(customer)" class="text-indigo-600 hover:text-indigo-900">
                    Editar
                  </button>
                  <button @click="deleteCustomer(customer.id)" class="text-red-600 hover:text-red-900 ml-4">
                    Excluir
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </main>

    <!-- Modal de Adicionar/Editar Cliente -->
    <div v-if="showAddModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center">
      <div class="bg-white rounded-lg p-6 max-w-md w-full">
        <h2 class="text-xl font-semibold mb-4">
          {{ editingCustomer ? 'Editar Cliente' : 'Novo Cliente' }}
        </h2>
        <form @submit.prevent="saveCustomer" class="space-y-4">
          <div>
            <label class="form-label">Nome *</label>
            <input 
              type="text" 
              v-model="customerForm.name" 
              class="input-field" 
              required
            />
          </div>
          <div>
            <label class="form-label">Email</label>
            <input 
              type="email" 
              v-model="customerForm.email" 
              class="input-field"
            />
          </div>
          <div>
            <label class="form-label">CPF/CNPJ *</label>
            <input 
              type="text" 
              v-model="customerForm.cpf_cnpj" 
              class="input-field" 
              required
            />
          </div>
          <div>
            <label class="form-label">Tipo *</label>
            <select v-model="customerForm.type" class="input-field" required>
              <option value="individual">Pessoa Física</option>
              <option value="company">Pessoa Jurídica</option>
            </select>
          </div>
          <div>
            <label class="form-label">Status *</label>
            <select v-model="customerForm.status" class="input-field" required>
              <option value="active">Ativo</option>
              <option value="inactive">Inativo</option>
            </select>
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

const customers = ref([])
const showAddModal = ref(false)
const editingCustomer = ref(null)
const errors = ref({})
const isSaving = ref(false)
const { showToast, toastMessage, showSuccessToast } = useToast()

const filters = ref({
  search: '',
  type: '',
  status: '',
  city: ''
})

const customerForm = ref({
  name: '',
  email: '',
  cpf_cnpj: '',
  type: 'individual',
  status: 'active'
})

const isFormValid = computed(() => {
  return customerForm.value.name &&
    customerForm.value.cpf_cnpj &&
    customerForm.value.type &&
    customerForm.value.status &&
    Object.keys(errors.value).length === 0
})

onMounted(async () => {
  await loadCustomers()
})

async function loadCustomers() {
  try {
    const response = await axios.get('/customers', { params: filters.value })
    customers.value = response.data.data
  } catch (error) {
    showToast('Erro ao carregar clientes. Tente novamente.')
  }
}

async function saveCustomer() {
  errors.value = {}

  if (!isFormValid.value) {
    showToast('Por favor, preencha todos os campos obrigatórios.')
    return
  }

  isSaving.value = true
  try {
    const formData = { ...customerForm.value }

    if (editingCustomer.value) {
      await axios.put(`/customers/${editingCustomer.value.id}`, formData)
      showSuccessToast('Cliente atualizado com sucesso!')
    } else {
      await axios.post('/customers', formData)
      showSuccessToast('Cliente criado com sucesso!')
    }

    await loadCustomers()
    closeModal()
  } catch (error) {
    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors
    } else {
      showToast('Erro ao salvar cliente. Tente novamente.')
    }
  } finally {
    isSaving.value = false
  }
}

function editCustomer(customer) {
  editingCustomer.value = customer
  customerForm.value = { ...customer }
  showAddModal.value = true
}

function deleteCustomer(id) {
  if (confirm('Tem certeza que deseja excluir este cliente?')) {
    axios.delete(`/customers/${id}`)
      .then(() => {
        showSuccessToast('Cliente excluído com sucesso!')
        loadCustomers()
      })
      .catch(() => {
        showToast('Erro ao excluir cliente. Tente novamente.')
      })
  }
}

function closeModal() {
  showAddModal.value = false
  editingCustomer.value = null
  customerForm.value = {
    name: '',
    email: '',
    cpf_cnpj: '',
    type: 'individual',
    status: 'active'
  }
}

function getStatusClass(status) {
  return status === 'active' ? 'text-green-500' : 'text-red-500'
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
