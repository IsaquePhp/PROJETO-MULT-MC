<template>
  <div class="min-h-screen w-full flex bg-white">
    <div class="container mx-auto flex">
      <!-- Container do conteúdo -->
      <div class="w-full flex flex-col md:flex-row items-center">
        <!-- Lado Esquerdo - Formulário -->
        <div class="w-full md:w-1/2 p-8 md:p-12 lg:p-16">
          <div class="max-w-md mx-auto">
            <!-- Logo -->
            <div class="mb-8">
              <h1 class="text-4xl font-bold text-indigo-600">MC</h1>
            </div>

            <!-- Título e Subtítulo -->
            <div class="text-center mb-8">
              <h2 class="text-3xl font-semibold text-gray-900 mb-2">Criar conta</h2>
              <p class="text-gray-600">
                Seja bem vindo ao MC<br />
                Crie sua conta para acessar a plataforma.
              </p>
            </div>

            <!-- Formulário -->
            <form @submit.prevent="handleRegister" class="space-y-6">
              <!-- Campo Nome -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1 text-left">Nome</label>
                <input
                  type="text"
                  v-model="name"
                  placeholder="Digite seu nome"
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                  required
                />
              </div>

              <!-- Campo Email -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1 text-left">Email</label>
                <input
                  type="email"
                  v-model="email"
                  placeholder="Digite seu email"
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                  required
                />
              </div>

              <!-- Campo Senha -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1 text-left">Senha</label>
                <input
                  type="password"
                  v-model="password"
                  placeholder="Digite sua senha"
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                  required
                />
              </div>

              <!-- Campo Confirmar Senha -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1 text-left">Confirmar Senha</label>
                <input
                  type="password"
                  v-model="password_confirmation"
                  placeholder="Confirme sua senha"
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                  required
                />
              </div>

              <!-- Botão de Registro -->
              <button
                type="submit"
                :disabled="loading"
                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200"
              >
                <span v-if="loading">Carregando...</span>
                <span v-else>Criar conta</span>
              </button>

              <!-- Link para Login -->
              <div class="text-sm text-center pt-4">
                <span class="text-gray-600">Já tem uma conta?</span>
                <router-link 
                  to="/login" 
                  class="font-medium text-indigo-600 hover:text-indigo-500 ml-1"
                >
                  Fazer login
                </router-link>
              </div>
            </form>
          </div>
        </div>

        <!-- Lado Direito - Decorativo -->
        <div class="hidden md:block md:w-1/2 bg-indigo-50 h-screen">
          <div class="h-full w-full p-12 relative">
            <!-- Grid de formas decorativas -->
            <div class="absolute inset-0 grid grid-cols-2 gap-8 p-12">
              <div class="space-y-8">
                <div class="bg-white rounded-2xl shadow-lg h-40 transform hover:scale-105 transition-transform duration-300"></div>
                <div class="bg-indigo-200 rounded-2xl h-56 transform translate-x-6 hover:scale-105 transition-transform duration-300"></div>
                <div class="bg-indigo-100 rounded-2xl h-40 transform -translate-y-8 hover:scale-105 transition-transform duration-300"></div>
              </div>
              <div class="space-y-8 pt-12">
                <div class="bg-indigo-300 rounded-2xl h-56 transform hover:scale-105 transition-transform duration-300"></div>
                <div class="bg-white rounded-2xl shadow-lg h-40 transform translate-y-6 hover:scale-105 transition-transform duration-300"></div>
                <div class="bg-indigo-100 rounded-2xl h-40 transform -translate-x-6 hover:scale-105 transition-transform duration-300"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const auth = useAuthStore()

const name = ref('')
const email = ref('')
const password = ref('')
const password_confirmation = ref('')
const loading = ref(false)

const handleRegister = async () => {
  try {
    loading.value = true
    const success = await auth.register(
      name.value,
      email.value,
      password.value,
      password_confirmation.value
    )
    
    if (success) {
      router.push('/dashboard')
    }
  } catch (error) {
    console.error('Registration error:', error)
  } finally {
    loading.value = false
  }
}
</script>
