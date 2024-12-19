<template>
  <div class="min-h-screen bg-gray-100">
    <!-- Header -->
    <header class="bg-white shadow">
      <div class="px-4 py-3">
        <h2 class="text-xl font-semibold text-gray-800">Profile</h2>
      </div>
    </header>

    <!-- Main Content -->
    <main class="p-6">
      <!-- Profile Info -->
      <div class="flex items-center mb-8">
        <div class="w-20 h-20 bg-blue-500 rounded-full flex items-center justify-center text-white text-2xl font-semibold">
          AD
        </div>
        <div class="ml-6">
          <h1 class="text-2xl font-semibold">{{ user.name }}</h1>
          <p class="text-gray-600">{{ user.email }}</p>
          <div class="flex gap-2 mt-2">
            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">Administrador</span>
            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">Ativo</span>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Informações Pessoais -->
        <div class="bg-white rounded-lg shadow p-6">
          <h2 class="text-lg font-semibold mb-4">Informações Pessoais</h2>
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Nome Completo</label>
              <input
                type="text"
                v-model="form.name"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Digite seu nome completo"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
              <input
                type="email"
                v-model="form.email"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Digite seu e-mail"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
              <input
                type="tel"
                v-model="form.phone"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="(00) 00000-0000"
              />
            </div>
          </div>
        </div>

        <!-- Segurança -->
        <div class="bg-white rounded-lg shadow p-6">
          <h2 class="text-lg font-semibold mb-4">Segurança</h2>
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Senha Atual</label>
              <input
                type="password"
                v-model="form.currentPassword"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Digite sua senha atual"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Nova Senha</label>
              <input
                type="password"
                v-model="form.newPassword"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Digite sua nova senha"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar Nova Senha</label>
              <input
                type="password"
                v-model="form.confirmPassword"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Confirme sua nova senha"
              />
            </div>
          </div>
        </div>

        <!-- Preferências -->
        <div class="bg-white rounded-lg shadow p-6">
          <h2 class="text-lg font-semibold mb-4">Preferências</h2>
          <div class="space-y-4">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="font-medium">Notificações por E-mail</h3>
                <p class="text-sm text-gray-500">Receber atualizações e alertas por e-mail</p>
              </div>
              <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" v-model="form.emailNotifications" class="sr-only peer">
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
              </label>
            </div>
            <div class="flex items-center justify-between">
              <div>
                <h3 class="font-medium">Tema Escuro</h3>
                <p class="text-sm text-gray-500">Alternar entre tema claro e escuro</p>
              </div>
              <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" v-model="form.darkTheme" class="sr-only peer">
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
              </label>
            </div>
          </div>
        </div>

        <!-- Atividade Recente -->
        <div class="bg-white rounded-lg shadow p-6">
          <h2 class="text-lg font-semibold mb-4">Atividade Recente</h2>
          <div class="space-y-4">
            <div class="flex items-center">
              <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                <DocumentTextIcon class="w-4 h-4 text-blue-600" />
              </div>
              <div>
                <p class="font-medium">Relatório mensal gerado</p>
                <p class="text-sm text-gray-500">Há 2 horas</p>
              </div>
            </div>
            <div class="flex items-center">
              <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                <ShoppingCartIcon class="w-4 h-4 text-blue-600" />
              </div>
              <div>
                <p class="font-medium">Nova venda registrada #1234</p>
                <p class="text-sm text-gray-500">Há 5 horas</p>
              </div>
            </div>
            <div class="flex items-center">
              <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                <CogIcon class="w-4 h-4 text-blue-600" />
              </div>
              <div>
                <p class="font-medium">Configurações do sistema atualizadas</p>
                <p class="text-sm text-gray-500">Há 1 dia</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Buttons -->
      <div class="flex justify-end gap-4 mt-6">
        <button
          @click="resetForm"
          class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
          Cancelar
        </button>
        <button
          @click="saveChanges"
          class="px-4 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
          Salvar Alterações
        </button>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { DocumentTextIcon, ShoppingCartIcon, CogIcon } from '@heroicons/vue/24/outline'
import { useAuthStore } from '../stores/auth'

const authStore = useAuthStore()
const user = ref({
  name: 'Administrador',
  email: 'admin@sistema.com',
  role: 'Administrador',
  status: 'Ativo'
})

const form = ref({
  name: user.value.name,
  email: user.value.email,
  phone: '(11) 99999-9999',
  currentPassword: '',
  newPassword: '',
  confirmPassword: '',
  emailNotifications: true,
  darkTheme: false
})

const resetForm = () => {
  form.value = {
    name: user.value.name,
    email: user.value.email,
    phone: '(11) 99999-9999',
    currentPassword: '',
    newPassword: '',
    confirmPassword: '',
    emailNotifications: true,
    darkTheme: false
  }
}

const saveChanges = async () => {
  // Implement save changes logic here
  console.log('Saving changes:', form.value)
}
</script>
