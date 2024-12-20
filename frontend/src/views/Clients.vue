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
          <thead>
            <tr>
              <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Avatar
              </th>
              <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Nome
              </th>
              <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Email
              </th>
              <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                CPF/CNPJ
              </th>
              <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Tipo
              </th>
              <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Status
              </th>
              <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Ações
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="client in clients" :key="client.id" class="hover:bg-gray-50">
              <td class="px-4 py-2 whitespace-nowrap">
                <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center overflow-hidden">
                  <img 
                    v-if="client.image" 
                    :src="client.image" 
                    :alt="client.name"
                    class="w-full h-full object-cover"
                  />
                  <div v-else class="text-xl text-gray-400">👤</div>
                </div>
              </td>
              <td class="px-4 py-2 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">{{ client.name }}</div>
              </td>
              <td class="px-4 py-2 whitespace-nowrap">
                <div class="text-sm text-gray-500">{{ client.email }}</div>
              </td>
              <td class="px-4 py-2 whitespace-nowrap">
                <div class="text-sm text-gray-500">{{ client.cpf_cnpj }}</div>
              </td>
              <td class="px-4 py-2 whitespace-nowrap">
                <div class="text-sm text-gray-500">{{ client.type }}</div>
              </td>
              <td class="px-4 py-2 whitespace-nowrap">
                <span 
                  :class="[
                    'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                    client.active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                  ]"
                >
                  {{ client.active ? 'Ativo' : 'Inativo' }}
                </span>
              </td>
              <td class="px-4 py-2 whitespace-nowrap text-sm font-medium space-x-2">
                <button 
                  @click="editClient(client)"
                  class="text-blue-600 hover:text-blue-900"
                >
                  Editar
                </button>
                <button 
                  @click="deleteClient(client.id)"
                  class="text-red-600 hover:text-red-900"
                >
                  Excluir
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
          <!-- Upload de Imagem -->
          <div class="flex items-center space-x-6">
            <div class="w-24 h-24 rounded-full bg-gray-100 flex items-center justify-center overflow-hidden">
              <img 
                v-if="previewImage"
                :src="previewImage"
                class="w-full h-full object-cover"
              />
              <div v-else class="text-4xl text-gray-400">👤</div>
            </div>
            <div class="flex-1">
              <label class="form-label">Foto do Cliente</label>
              <input
                type="file"
                @change="handleImageUpload"
                accept="image/*"
                class="block w-full text-sm text-gray-500
                  file:mr-4 file:py-2 file:px-4
                  file:rounded-full file:border-0
                  file:text-sm file:font-semibold
                  file:bg-blue-50 file:text-blue-700
                  hover:file:bg-blue-100"
              />
              <p class="mt-1 text-sm text-gray-500">
                PNG, JPG ou GIF até 2MB
              </p>
            </div>
          </div>
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

    <!-- Modal de Confirmação de Exclusão -->
    <div v-if="showDeleteConfirmModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 max-w-sm w-full mx-4">
        <div class="text-center mb-6">
          <svg class="mx-auto h-12 w-12 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
          <h3 class="mt-4 text-lg font-medium text-gray-900">
            Confirmar Exclusão
          </h3>
          <p class="mt-2 text-sm text-gray-500">
            {{ deleteConfirmMessage }}
          </p>
        </div>
        <div class="flex justify-center space-x-4">
          <button
            @click="confirmDelete"
            class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
          >
            Sim
          </button>
          <button
            @click="cancelDelete"
            class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
          >
            Não
          </button>
        </div>
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

// Estado para imagem
const previewImage = ref('')
const imageFile = ref(null)

// Estado para o modal de confirmação
const showDeleteConfirmModal = ref(false)
const deleteConfirmMessage = ref('')
const clientToDelete = ref(null)

// Método para manipular upload de imagem
const handleImageUpload = (event) => {
  const file = event.target.files[0]
  if (file) {
    if (file.size > 2 * 1024 * 1024) {
      console.error('A imagem deve ter no máximo 2MB')
      event.target.value = ''
      return
    }

    imageFile.value = file
    const reader = new FileReader()
    reader.onload = (e) => {
      previewImage.value = e.target.result
    }
    reader.readAsDataURL(file)
  }
}

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
  previewImage.value = ''
  imageFile.value = null
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
  previewImage.value = client.image || ''
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

// Excluir cliente
function deleteClient(client) {
  clientToDelete.value = client
  deleteConfirmMessage.value = 'Tem certeza que deseja excluir este cliente?'
  showDeleteConfirmModal.value = true
}

// Confirmar exclusão
async function confirmDelete() {
  try {
    await axios.delete(`/api/clients/${clientToDelete.value.id}`)
    await fetchClients() // Refresh the list
    showDeleteConfirmModal.value = false
    clientToDelete.value = null
  } catch (err) {
    error.value = err.response?.data?.message || 'Erro ao excluir cliente'
  }
}

// Cancelar exclusão
function cancelDelete() {
  showDeleteConfirmModal.value = false
  clientToDelete.value = null
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
  previewImage.value = ''
  imageFile.value = null
}

// Submit client form
const submitClient = async () => {
  try {
    const formData = new FormData()
    
    // Dados básicos do cliente
    const clientData = {
      name: clientForm.value.name,
      email: clientForm.value.email,
      phone: clientForm.value.phone,
      cpf: clientForm.value.cpf,
      address: clientForm.value.address,
      city: clientForm.value.city,
      state: clientForm.value.state,
      postal_code: clientForm.value.postal_code
    }

    // Adicionar dados ao FormData
    formData.append('data', JSON.stringify(clientData))
    
    // Adicionar imagem se houver
    if (imageFile.value) {
      formData.append('image', imageFile.value)
    }

    if (selectedClient.value) {
      // Update existing client
      await axios.put(`/clients/${selectedClient.value.id}`, formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })
    } else {
      // Create new client
      await axios.post('/clients', formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })
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
