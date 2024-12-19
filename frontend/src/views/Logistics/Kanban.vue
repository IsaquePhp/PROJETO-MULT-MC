<template>
  <div class="p-6">
    <!-- Título e Subtítulo -->
    <div class="mb-6">
      <h2 class="text-2xl font-bold text-gray-900">Kanban de Logística</h2>
      <p class="text-sm text-gray-600">Gerencie o fluxo de entregas e rotas</p>
    </div>

    <!-- Kanban Board -->
    <div class="grid grid-cols-4 gap-6">
      <!-- Aguardando -->
      <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center justify-between mb-4">
          <h3 class="font-medium text-gray-900">Aguardando</h3>
          <span class="px-2.5 py-0.5 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
            {{ pendingDeliveries.length }}
          </span>
        </div>
        <div class="space-y-3">
          <div v-for="delivery in pendingDeliveries" :key="delivery.id"
            class="bg-gray-50 p-3 rounded-lg border border-gray-200 cursor-pointer hover:shadow transition-shadow"
            @click="openDeliveryDetails(delivery)"
          >
            <div class="flex items-center justify-between mb-2">
              <span class="text-sm font-medium text-gray-900">#{{ delivery.code }}</span>
              <span class="px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                Aguardando
              </span>
            </div>
            <p class="text-sm text-gray-600 mb-2">{{ delivery.customer_name }}</p>
            <div class="flex items-center text-xs text-gray-500">
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              {{ formatDate(delivery.created_at) }}
            </div>
          </div>
        </div>
      </div>

      <!-- Em Rota -->
      <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center justify-between mb-4">
          <h3 class="font-medium text-gray-900">Em Rota</h3>
          <span class="px-2.5 py-0.5 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
            {{ inRouteDeliveries.length }}
          </span>
        </div>
        <div class="space-y-3">
          <div v-for="delivery in inRouteDeliveries" :key="delivery.id"
            class="bg-gray-50 p-3 rounded-lg border border-gray-200 cursor-pointer hover:shadow transition-shadow"
            @click="openDeliveryDetails(delivery)"
          >
            <div class="flex items-center justify-between mb-2">
              <span class="text-sm font-medium text-gray-900">#{{ delivery.code }}</span>
              <span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                Em Rota
              </span>
            </div>
            <p class="text-sm text-gray-600 mb-2">{{ delivery.customer_name }}</p>
            <div class="flex items-center justify-between text-xs">
              <div class="flex items-center text-gray-500">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                {{ delivery.route_name }}
              </div>
              <div class="flex items-center text-gray-500">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ delivery.estimated_time }}
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Entregue -->
      <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center justify-between mb-4">
          <h3 class="font-medium text-gray-900">Entregue</h3>
          <span class="px-2.5 py-0.5 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
            {{ deliveredDeliveries.length }}
          </span>
        </div>
        <div class="space-y-3">
          <div v-for="delivery in deliveredDeliveries" :key="delivery.id"
            class="bg-gray-50 p-3 rounded-lg border border-gray-200 cursor-pointer hover:shadow transition-shadow"
            @click="openDeliveryDetails(delivery)"
          >
            <div class="flex items-center justify-between mb-2">
              <span class="text-sm font-medium text-gray-900">#{{ delivery.code }}</span>
              <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                Entregue
              </span>
            </div>
            <p class="text-sm text-gray-600 mb-2">{{ delivery.customer_name }}</p>
            <div class="flex items-center text-xs text-gray-500">
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
              {{ formatDate(delivery.delivered_at) }}
            </div>
          </div>
        </div>
      </div>

      <!-- Problemas -->
      <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center justify-between mb-4">
          <h3 class="font-medium text-gray-900">Problemas</h3>
          <span class="px-2.5 py-0.5 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
            {{ problemDeliveries.length }}
          </span>
        </div>
        <div class="space-y-3">
          <div v-for="delivery in problemDeliveries" :key="delivery.id"
            class="bg-gray-50 p-3 rounded-lg border border-gray-200 cursor-pointer hover:shadow transition-shadow"
            @click="openDeliveryDetails(delivery)"
          >
            <div class="flex items-center justify-between mb-2">
              <span class="text-sm font-medium text-gray-900">#{{ delivery.code }}</span>
              <span class="px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                Problema
              </span>
            </div>
            <p class="text-sm text-gray-600 mb-2">{{ delivery.customer_name }}</p>
            <p class="text-xs text-red-600">{{ delivery.problem_description }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de Detalhes -->
    <div v-if="showDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 w-full max-w-lg">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-medium text-gray-900">
            Detalhes da Entrega #{{ selectedDelivery?.code }}
          </h3>
          <button @click="showDetailsModal = false" class="text-gray-400 hover:text-gray-500">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">Cliente</label>
            <p class="mt-1 text-sm text-gray-900">{{ selectedDelivery?.customer_name }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Endereço</label>
            <p class="mt-1 text-sm text-gray-900">{{ selectedDelivery?.address }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Status</label>
            <span class="mt-1 inline-flex px-2 py-1 text-sm rounded-full"
              :class="{
                'bg-yellow-100 text-yellow-800': selectedDelivery?.status === 'pending',
                'bg-blue-100 text-blue-800': selectedDelivery?.status === 'in_route',
                'bg-green-100 text-green-800': selectedDelivery?.status === 'delivered',
                'bg-red-100 text-red-800': selectedDelivery?.status === 'problem'
              }"
            >
              {{ formatStatus(selectedDelivery?.status) }}
            </span>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Rota</label>
            <p class="mt-1 text-sm text-gray-900">{{ selectedDelivery?.route_name || '-' }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Observações</label>
            <p class="mt-1 text-sm text-gray-900">{{ selectedDelivery?.notes || '-' }}</p>
          </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
          <button
            v-if="selectedDelivery?.status === 'pending'"
            @click="startDelivery"
            class="h-[45px] px-4 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"
          >
            Iniciar Entrega
          </button>
          <button
            v-if="selectedDelivery?.status === 'in_route'"
            @click="completeDelivery"
            class="h-[45px] px-4 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
          >
            Finalizar Entrega
          </button>
          <button
            v-if="['pending', 'in_route'].includes(selectedDelivery?.status)"
            @click="reportProblem"
            class="h-[45px] px-4 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
          >
            Reportar Problema
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from '../../plugins/axios'
import { useToast } from '../../composables/useToast'

// Estado
const deliveries = ref([])
const selectedDelivery = ref(null)
const showDetailsModal = ref(false)
const toast = useToast()

// Computed
const pendingDeliveries = computed(() => 
  deliveries.value.filter(d => d.status === 'pending')
)

const inRouteDeliveries = computed(() => 
  deliveries.value.filter(d => d.status === 'in_route')
)

const deliveredDeliveries = computed(() => 
  deliveries.value.filter(d => d.status === 'delivered')
)

const problemDeliveries = computed(() => 
  deliveries.value.filter(d => d.status === 'problem')
)

// Funções
function formatDate(date) {
  return new Date(date).toLocaleString('pt-BR')
}

function formatStatus(status) {
  const statusMap = {
    pending: 'Aguardando',
    in_route: 'Em Rota',
    delivered: 'Entregue',
    problem: 'Problema'
  }
  return statusMap[status] || status
}

function openDeliveryDetails(delivery) {
  selectedDelivery.value = delivery
  showDetailsModal.value = true
}

async function loadDeliveries() {
  try {
    const response = await axios.get('/deliveries')
    if (response.data.success) {
      deliveries.value = response.data.data
    }
  } catch (err) {
    console.error('Erro ao carregar entregas:', err)
    toast.error('Erro ao carregar entregas')
  }
}

async function startDelivery() {
  try {
    const response = await axios.post(`/deliveries/${selectedDelivery.value.id}/start`)
    if (response.data.success) {
      await loadDeliveries()
      showDetailsModal.value = false
      toast.success('Entrega iniciada com sucesso')
    }
  } catch (err) {
    console.error('Erro ao iniciar entrega:', err)
    toast.error('Erro ao iniciar entrega')
  }
}

async function completeDelivery() {
  try {
    const response = await axios.post(`/deliveries/${selectedDelivery.value.id}/complete`)
    if (response.data.success) {
      await loadDeliveries()
      showDetailsModal.value = false
      toast.success('Entrega finalizada com sucesso')
    }
  } catch (err) {
    console.error('Erro ao finalizar entrega:', err)
    toast.error('Erro ao finalizar entrega')
  }
}

async function reportProblem() {
  // Aqui você pode abrir um modal para coletar mais informações sobre o problema
  try {
    const response = await axios.post(`/deliveries/${selectedDelivery.value.id}/problem`, {
      problem_description: 'Problema na entrega' // Você pode coletar isso de um formulário
    })
    if (response.data.success) {
      await loadDeliveries()
      showDetailsModal.value = false
      toast.success('Problema reportado com sucesso')
    }
  } catch (err) {
    console.error('Erro ao reportar problema:', err)
    toast.error('Erro ao reportar problema')
  }
}

// Lifecycle hooks
onMounted(() => {
  loadDeliveries()
})
</script>
