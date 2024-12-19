<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-semibold text-gray-900">Kanban de Entregas</h1>
      <div class="flex space-x-4">
        <button 
          @click="refreshOrders"
          class="btn-secondary flex items-center"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
          Atualizar
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center items-center h-64">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
    </div>

    <!-- Kanban Board -->
    <div v-else class="flex space-x-4 overflow-x-auto pb-4">
      <!-- Aguardando Processamento -->
      <div class="flex-shrink-0 w-80">
        <div class="bg-gray-100 rounded-lg p-4">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">Aguardando</h3>
            <span class="px-2 py-1 text-sm font-semibold bg-gray-200 text-gray-700 rounded-full">
              {{ pendingOrders.length }}
            </span>
          </div>
          <div class="space-y-3">
            <div 
              v-for="order in pendingOrders" 
              :key="order.id"
              class="bg-white p-4 rounded-lg shadow cursor-pointer hover:shadow-md"
              @click="openOrderDetails(order)"
            >
              <div class="flex justify-between items-start mb-2">
                <span class="font-medium text-gray-900">#{{ order.id }}</span>
                <span class="text-sm text-gray-500">{{ formatDate(order.created_at) }}</span>
              </div>
              <div class="text-sm text-gray-600 mb-2">{{ order.client_name }}</div>
              <div class="flex justify-between items-center">
                <span class="text-sm font-medium text-blue-600">R$ {{ order.total.toFixed(2) }}</span>
                <button 
                  @click.stop="moveToInProgress(order)"
                  class="text-sm text-blue-600 hover:text-blue-800"
                >
                  Iniciar
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Em Preparação -->
      <div class="flex-shrink-0 w-80">
        <div class="bg-gray-100 rounded-lg p-4">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">Em Preparação</h3>
            <span class="px-2 py-1 text-sm font-semibold bg-blue-200 text-blue-700 rounded-full">
              {{ inProgressOrders.length }}
            </span>
          </div>
          <div class="space-y-3">
            <div 
              v-for="order in inProgressOrders" 
              :key="order.id"
              class="bg-white p-4 rounded-lg shadow cursor-pointer hover:shadow-md"
              @click="openOrderDetails(order)"
            >
              <div class="flex justify-between items-start mb-2">
                <span class="font-medium text-gray-900">#{{ order.id }}</span>
                <span class="text-sm text-gray-500">{{ formatDate(order.processing_start) }}</span>
              </div>
              <div class="text-sm text-gray-600 mb-2">{{ order.client_name }}</div>
              <div class="flex justify-between items-center">
                <span class="text-sm font-medium text-blue-600">R$ {{ order.total.toFixed(2) }}</span>
                <button 
                  @click.stop="moveToReadyForDelivery(order)"
                  class="text-sm text-green-600 hover:text-green-800"
                >
                  Pronto
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Pronto para Entrega -->
      <div class="flex-shrink-0 w-80">
        <div class="bg-gray-100 rounded-lg p-4">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">Pronto</h3>
            <span class="px-2 py-1 text-sm font-semibold bg-yellow-200 text-yellow-700 rounded-full">
              {{ readyOrders.length }}
            </span>
          </div>
          <div class="space-y-3">
            <div 
              v-for="order in readyOrders" 
              :key="order.id"
              class="bg-white p-4 rounded-lg shadow cursor-pointer hover:shadow-md"
              @click="openOrderDetails(order)"
            >
              <div class="flex justify-between items-start mb-2">
                <span class="font-medium text-gray-900">#{{ order.id }}</span>
                <span class="text-sm text-gray-500">{{ formatDate(order.ready_at) }}</span>
              </div>
              <div class="text-sm text-gray-600 mb-2">{{ order.client_name }}</div>
              <div class="flex justify-between items-center">
                <span class="text-sm font-medium text-blue-600">R$ {{ order.total.toFixed(2) }}</span>
                <button 
                  @click.stop="moveToInDelivery(order)"
                  class="text-sm text-yellow-600 hover:text-yellow-800"
                >
                  Entregar
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Em Entrega -->
      <div class="flex-shrink-0 w-80">
        <div class="bg-gray-100 rounded-lg p-4">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">Em Entrega</h3>
            <span class="px-2 py-1 text-sm font-semibold bg-purple-200 text-purple-700 rounded-full">
              {{ inDeliveryOrders.length }}
            </span>
          </div>
          <div class="space-y-3">
            <div 
              v-for="order in inDeliveryOrders" 
              :key="order.id"
              class="bg-white p-4 rounded-lg shadow cursor-pointer hover:shadow-md"
              @click="openOrderDetails(order)"
            >
              <div class="flex justify-between items-start mb-2">
                <span class="font-medium text-gray-900">#{{ order.id }}</span>
                <span class="text-sm text-gray-500">{{ formatDate(order.delivery_start) }}</span>
              </div>
              <div class="text-sm text-gray-600 mb-2">{{ order.client_name }}</div>
              <div class="flex justify-between items-center">
                <span class="text-sm font-medium text-blue-600">R$ {{ order.total.toFixed(2) }}</span>
                <button 
                  @click.stop="moveToDelivered(order)"
                  class="text-sm text-purple-600 hover:text-purple-800"
                >
                  Finalizar
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Entregue -->
      <div class="flex-shrink-0 w-80">
        <div class="bg-gray-100 rounded-lg p-4">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">Entregue</h3>
            <span class="px-2 py-1 text-sm font-semibold bg-green-200 text-green-700 rounded-full">
              {{ deliveredOrders.length }}
            </span>
          </div>
          <div class="space-y-3">
            <div 
              v-for="order in deliveredOrders" 
              :key="order.id"
              class="bg-white p-4 rounded-lg shadow cursor-pointer hover:shadow-md"
              @click="openOrderDetails(order)"
            >
              <div class="flex justify-between items-start mb-2">
                <span class="font-medium text-gray-900">#{{ order.id }}</span>
                <span class="text-sm text-gray-500">{{ formatDate(order.delivered_at) }}</span>
              </div>
              <div class="text-sm text-gray-600 mb-2">{{ order.client_name }}</div>
              <div class="flex justify-between items-center">
                <span class="text-sm font-medium text-blue-600">R$ {{ order.total.toFixed(2) }}</span>
                <span class="text-sm text-green-600">✓ Entregue</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de Detalhes do Pedido -->
    <TransitionRoot appear :show="showOrderDetails" as="template">
      <Dialog as="div" @close="closeOrderDetails" class="relative z-10">
        <TransitionChild
          as="template"
          enter="duration-300 ease-out"
          enter-from="opacity-0"
          enter-to="opacity-100"
          leave="duration-200 ease-in"
          leave-from="opacity-100"
          leave-to="opacity-0"
        >
          <div class="fixed inset-0 bg-black bg-opacity-25" />
        </TransitionChild>

        <div class="fixed inset-0 overflow-y-auto">
          <div class="flex min-h-full items-center justify-center p-4 text-center">
            <TransitionChild
              as="template"
              enter="duration-300 ease-out"
              enter-from="opacity-0 scale-95"
              enter-to="opacity-100 scale-100"
              leave="duration-200 ease-in"
              leave-from="opacity-100 scale-100"
              leave-to="opacity-0 scale-95"
            >
              <DialogPanel class="w-full max-w-2xl transform overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all">
                <DialogTitle as="h3" class="text-lg font-medium leading-6 text-gray-900 mb-4">
                  Detalhes do Pedido #{{ selectedOrder?.id }}
                </DialogTitle>

                <div v-if="selectedOrder" class="space-y-4">
                  <!-- Informações do Cliente -->
                  <div>
                    <h4 class="font-medium text-gray-700 mb-2">Cliente</h4>
                    <div class="bg-gray-50 p-3 rounded">
                      <p class="text-sm text-gray-600">{{ selectedOrder.client_name }}</p>
                      <p class="text-sm text-gray-500">{{ selectedOrder.client_phone }}</p>
                      <p class="text-sm text-gray-500">{{ selectedOrder.delivery_address }}</p>
                    </div>
                  </div>

                  <!-- Produtos -->
                  <div>
                    <h4 class="font-medium text-gray-700 mb-2">Produtos</h4>
                    <div class="bg-gray-50 p-3 rounded space-y-2">
                      <div 
                        v-for="item in selectedOrder.items" 
                        :key="item.id"
                        class="flex justify-between items-center"
                      >
                        <div>
                          <p class="text-sm text-gray-600">{{ item.quantity }}x {{ item.product_name }}</p>
                          <p class="text-xs text-gray-500">{{ item.notes }}</p>
                        </div>
                        <span class="text-sm font-medium text-gray-600">
                          R$ {{ (item.price * item.quantity).toFixed(2) }}
                        </span>
                      </div>
                    </div>
                  </div>

                  <!-- Timeline -->
                  <div>
                    <h4 class="font-medium text-gray-700 mb-2">Timeline</h4>
                    <div class="bg-gray-50 p-3 rounded space-y-3">
                      <div class="flex items-center space-x-3">
                        <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                        <p class="text-sm text-gray-600">
                          Pedido realizado em {{ formatDate(selectedOrder.created_at) }}
                        </p>
                      </div>
                      <div v-if="selectedOrder.processing_start" class="flex items-center space-x-3">
                        <div class="w-2 h-2 bg-yellow-500 rounded-full"></div>
                        <p class="text-sm text-gray-600">
                          Preparação iniciada em {{ formatDate(selectedOrder.processing_start) }}
                        </p>
                      </div>
                      <div v-if="selectedOrder.ready_at" class="flex items-center space-x-3">
                        <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                        <p class="text-sm text-gray-600">
                          Pronto para entrega em {{ formatDate(selectedOrder.ready_at) }}
                        </p>
                      </div>
                      <div v-if="selectedOrder.delivery_start" class="flex items-center space-x-3">
                        <div class="w-2 h-2 bg-purple-500 rounded-full"></div>
                        <p class="text-sm text-gray-600">
                          Saiu para entrega em {{ formatDate(selectedOrder.delivery_start) }}
                        </p>
                      </div>
                      <div v-if="selectedOrder.delivered_at" class="flex items-center space-x-3">
                        <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                        <p class="text-sm text-gray-600">
                          Entregue em {{ formatDate(selectedOrder.delivered_at) }}
                        </p>
                      </div>
                    </div>
                  </div>

                  <!-- Total -->
                  <div class="flex justify-between items-center pt-4 border-t">
                    <span class="font-medium text-gray-700">Total do Pedido</span>
                    <span class="text-lg font-medium text-blue-600">
                      R$ {{ selectedOrder.total.toFixed(2) }}
                    </span>
                  </div>
                </div>

                <div class="mt-6 flex justify-end">
                  <button
                    type="button"
                    class="btn-secondary"
                    @click="closeOrderDetails"
                  >
                    Fechar
                  </button>
                </div>
              </DialogPanel>
            </TransitionChild>
          </div>
        </div>
      </Dialog>
    </TransitionRoot>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import axios from '../plugins/axios'
import { useToast } from 'vue-toastification'

const toast = useToast()

// Estado
const loading = ref(false)
const orders = ref([])
const showOrderDetails = ref(false)
const selectedOrder = ref(null)

// Computed properties para filtrar pedidos por status
const pendingOrders = computed(() => 
  orders.value.filter(order => order.status === 'pending')
)

const inProgressOrders = computed(() => 
  orders.value.filter(order => order.status === 'processing')
)

const readyOrders = computed(() => 
  orders.value.filter(order => order.status === 'ready')
)

const inDeliveryOrders = computed(() => 
  orders.value.filter(order => order.status === 'delivering')
)

const deliveredOrders = computed(() => 
  orders.value.filter(order => order.status === 'delivered')
)

// Métodos para mover pedidos entre status
const moveToInProgress = async (order) => {
  try {
    await axios.put(`/orders/${order.id}/status`, { 
      status: 'processing',
      processing_start: new Date().toISOString()
    })
    await refreshOrders()
    toast.success('Pedido em preparação')
  } catch (error) {
    toast.error('Erro ao atualizar status do pedido')
  }
}

const moveToReadyForDelivery = async (order) => {
  try {
    await axios.put(`/orders/${order.id}/status`, { 
      status: 'ready',
      ready_at: new Date().toISOString()
    })
    await refreshOrders()
    toast.success('Pedido pronto para entrega')
  } catch (error) {
    toast.error('Erro ao atualizar status do pedido')
  }
}

const moveToInDelivery = async (order) => {
  try {
    await axios.put(`/orders/${order.id}/status`, { 
      status: 'delivering',
      delivery_start: new Date().toISOString()
    })
    await refreshOrders()
    toast.success('Pedido em entrega')
  } catch (error) {
    toast.error('Erro ao atualizar status do pedido')
  }
}

const moveToDelivered = async (order) => {
  try {
    await axios.put(`/orders/${order.id}/status`, { 
      status: 'delivered',
      delivered_at: new Date().toISOString()
    })
    await refreshOrders()
    toast.success('Pedido entregue com sucesso')
  } catch (error) {
    toast.error('Erro ao atualizar status do pedido')
  }
}

// Métodos para gerenciar modal de detalhes
const openOrderDetails = (order) => {
  selectedOrder.value = order
  showOrderDetails.value = true
}

const closeOrderDetails = () => {
  selectedOrder.value = null
  showOrderDetails.value = false
}

// Método para formatar data
const formatDate = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleString('pt-BR', {
    day: '2-digit',
    month: '2-digit',
    year: '2-digit',
    hour: '2-digit',
    minute: '2-digit'
  })
}

// Método para buscar pedidos
const refreshOrders = async () => {
  loading.value = true
  try {
    const response = await axios.get('/orders')
    orders.value = response.data
  } catch (error) {
    toast.error('Erro ao carregar pedidos')
  } finally {
    loading.value = false
  }
}

// Carregar pedidos quando o componente é montado
onMounted(() => {
  refreshOrders()
})
</script>
