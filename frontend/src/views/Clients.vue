<template>
  <div class="p-6">
    <header class="mb-8">
      <h1 class="text-2xl font-bold text-gray-900">CLIENTES</h1>
      <p class="mt-2 text-sm text-gray-700">Gerencie os clientes da loja</p>
    </header>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-4">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600 mx-auto"></div>
      <p class="mt-2 text-gray-600">Carregando clientes...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-red-50 p-4 rounded-md">
      <p class="text-red-700">{{ error }}</p>
    </div>

    <!-- Empty State -->
    <div v-else-if="clients.length === 0" class="text-center py-4">
      <p class="text-gray-600">Nenhum cliente encontrado</p>
    </div>

    <!-- Tabela de Clientes -->
    <div v-else class="bg-white shadow rounded-lg overflow-hidden">
      <!-- Cabeçalho da Tabela -->
      <div class="p-6 border-b border-gray-200">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
          <div class="flex-1 min-w-0">
            <h2 class="text-lg font-medium text-gray-900">Lista de Clientes</h2>
          </div>
          <div class="mt-4 md:mt-0">
            <button 
              @click="openNewClientModal"
              class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              Novo Cliente
            </button>
          </div>
        </div>
      </div>

      <!-- Tabela -->
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telefone</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cidade</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="client in clients" :key="client.id">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">#{{ client.id }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ client.name }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ client.email }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ client.phone }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ client.city }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span 
                  class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                  :class="{
                    'bg-green-100 text-green-800': client.active,
                    'bg-red-100 text-red-800': !client.active
                  }"
                >
                  {{ client.active ? 'Ativo' : 'Inativo' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <button 
                  @click="editClient(client)"
                  class="text-indigo-600 hover:text-indigo-900 mr-2"
                >
                  Editar
                </button>
                <button 
                  @click="toggleClientStatus(client)"
                  :class="{
                    'text-red-600 hover:text-red-900': client.active,
                    'text-green-600 hover:text-green-900': !client.active
                  }"
                >
                  {{ client.active ? 'Desativar' : 'Ativar' }}
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center">
      <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-medium text-gray-900">
            {{ selectedClient ? 'Editar Cliente' : 'Novo Cliente' }}
          </h3>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-500">
            <span class="sr-only">Fechar</span>
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <form @submit.prevent="submitClient" class="space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Nome</label>
              <input 
                v-model="clientForm.name" 
                type="text" 
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                required
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Email</label>
              <input 
                v-model="clientForm.email" 
                type="email" 
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                required
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Telefone</label>
              <input 
                v-model="clientForm.phone" 
                type="text" 
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                required
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">CPF</label>
              <input 
                v-model="clientForm.cpf" 
                type="text" 
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                required
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Endereço</label>
              <input 
                v-model="clientForm.address" 
                type="text" 
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                required
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Cidade</label>
              <input 
                v-model="clientForm.city" 
                type="text" 
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                required
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Estado</label>
              <input 
                v-model="clientForm.state" 
                type="text" 
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                required
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">CEP</label>
              <input 
                v-model="clientForm.postal_code" 
                type="text" 
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                required
              >
            </div>
          </div>
          <div class="flex justify-end space-x-3">
            <button 
              type="button"
              @click="closeModal"
              class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              Cancelar
            </button>
            <button 
              type="submit"
              class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              {{ selectedClient ? 'Salvar' : 'Criar' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from '../plugins/axios'

// State for modal and form
const showModal = ref(false)
const selectedClient = ref(null)
const clients = ref([])
const loading = ref(false)
const error = ref(null)

const clientForm = ref({
  name: '',
  email: '',
  phone: '',
  cpf: '',
  address: '',
  city: '',
  state: '',
  postal_code: ''
})

// Fetch clients from API
const fetchClients = async () => {
  loading.value = true
  error.value = null
  try {
    const response = await axios.get('/clients')
    clients.value = response.data
  } catch (err) {
    error.value = 'Erro ao carregar clientes'
    console.error('Error fetching clients:', err)
  } finally {
    loading.value = false
  }
}

// Open modal for new client
const openNewClientModal = () => {
  selectedClient.value = null
  clientForm.value = {
    name: '',
    email: '',
    phone: '',
    cpf: '',
    address: '',
    city: '',
    state: '',
    postal_code: ''
  }
  showModal.value = true
}

// Edit existing client
const editClient = (client) => {
  selectedClient.value = client
  clientForm.value = {
    name: client.name,
    email: client.email,
    phone: client.phone,
    cpf: client.cpf,
    address: client.address,
    city: client.city,
    state: client.state,
    postal_code: client.postal_code
  }
  showModal.value = true
}

// Toggle client status
const toggleClientStatus = async (client) => {
  try {
    await axios.put(`/clients/${client.id}/toggle-status`)
    await fetchClients() // Refresh the list
  } catch (error) {
    console.error('Error toggling client status:', error)
  }
}

// Close modal
const closeModal = () => {
  showModal.value = false
  selectedClient.value = null
  clientForm.value = {
    name: '',
    email: '',
    phone: '',
    cpf: '',
    address: '',
    city: '',
    state: '',
    postal_code: ''
  }
}

// Submit client form
const submitClient = async () => {
  try {
    if (selectedClient.value) {
      // Update existing client
      await axios.put(`/clients/${selectedClient.value.id}`, clientForm.value)
    } else {
      // Create new client
      await axios.post('/clients', clientForm.value)
    }
    
    await fetchClients() // Refresh the list
    closeModal()
  } catch (error) {
    console.error('Error submitting client:', error)
  }
}

// Load clients when component mounts
onMounted(() => {
  fetchClients()
})
</script>
