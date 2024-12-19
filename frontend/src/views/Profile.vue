<template>
  <div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
      <!-- Cabeçalho do Perfil -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex items-center space-x-6">
          <div class="h-24 w-24 bg-blue-600 rounded-full flex items-center justify-center text-white text-2xl font-bold">
            AD
          </div>
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Administrador</h1>
            <p class="text-gray-500">admin@sistema.com</p>
            <div class="mt-2 flex items-center space-x-2">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                Administrador
              </span>
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                Ativo
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Informações do Perfil -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Informações Pessoais -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">Informações Pessoais</h2>
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Nome Completo</label>
              <input 
                type="text" 
                v-model="profile.name"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
              <input 
                type="email" 
                v-model="profile.email"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
              <input 
                type="tel" 
                v-model="profile.phone"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
              >
            </div>
          </div>
        </div>

        <!-- Segurança -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">Segurança</h2>
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Senha Atual</label>
              <input 
                type="password" 
                v-model="security.currentPassword"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Nova Senha</label>
              <input 
                type="password" 
                v-model="security.newPassword"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar Nova Senha</label>
              <input 
                type="password" 
                v-model="security.confirmPassword"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
              >
            </div>
          </div>
        </div>

        <!-- Preferências -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">Preferências</h2>
          <div class="space-y-4">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-sm font-medium text-gray-700">Notificações por E-mail</h3>
                <p class="text-sm text-gray-500">Receber atualizações e alertas por e-mail</p>
              </div>
              <button 
                @click="preferences.emailNotifications = !preferences.emailNotifications"
                :class="[
                  'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2',
                  preferences.emailNotifications ? 'bg-blue-600' : 'bg-gray-200'
                ]"
              >
                <span 
                  :class="[
                    'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                    preferences.emailNotifications ? 'translate-x-5' : 'translate-x-0'
                  ]"
                />
              </button>
            </div>
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-sm font-medium text-gray-700">Tema Escuro</h3>
                <p class="text-sm text-gray-500">Alternar entre tema claro e escuro</p>
              </div>
              <button 
                @click="preferences.darkMode = !preferences.darkMode"
                :class="[
                  'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2',
                  preferences.darkMode ? 'bg-blue-600' : 'bg-gray-200'
                ]"
              >
                <span 
                  :class="[
                    'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                    preferences.darkMode ? 'translate-x-5' : 'translate-x-0'
                  ]"
                />
              </button>
            </div>
          </div>
        </div>

        <!-- Atividade -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">Atividade Recente</h2>
          <div class="space-y-4">
            <div v-for="(activity, index) in recentActivity" :key="index" class="flex items-start space-x-3">
              <div class="flex-shrink-0">
                <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                  <component :is="activity.icon" class="h-4 w-4 text-blue-600" />
                </div>
              </div>
              <div>
                <p class="text-sm font-medium text-gray-900">{{ activity.description }}</p>
                <p class="text-xs text-gray-500">{{ activity.time }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Botões de Ação -->
      <div class="flex justify-end space-x-4 mt-6">
        <button 
          @click="resetForm" 
          class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
          Cancelar
        </button>
        <button 
          @click="saveChanges" 
          class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
          Salvar Alterações
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { 
  UserIcon, 
  KeyIcon, 
  BellIcon, 
  CogIcon,
  DocumentTextIcon,
  ShoppingCartIcon
} from '@heroicons/vue/24/outline'

const profile = ref({
  name: 'Administrador',
  email: 'admin@sistema.com',
  phone: '(11) 99999-9999'
})

const security = ref({
  currentPassword: '',
  newPassword: '',
  confirmPassword: ''
})

const preferences = ref({
  emailNotifications: true,
  darkMode: false
})

const recentActivity = ref([
  {
    icon: DocumentTextIcon,
    description: 'Relatório mensal gerado',
    time: 'Há 2 horas'
  },
  {
    icon: ShoppingCartIcon,
    description: 'Nova venda registrada #1234',
    time: 'Há 5 horas'
  },
  {
    icon: CogIcon,
    description: 'Configurações do sistema atualizadas',
    time: 'Há 1 dia'
  }
])

const saveChanges = () => {
  // Implementar lógica de salvamento
  console.log('Salvando alterações...')
}

const resetForm = () => {
  // Implementar lógica de reset
  console.log('Resetando formulário...')
}
</script>
