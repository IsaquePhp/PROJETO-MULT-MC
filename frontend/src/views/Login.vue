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
              <h2 class="text-3xl font-semibold text-gray-900 mb-2">Fazer login</h2>
              <p class="text-gray-600">
                Seja bem vindo ao MC<br />
                a plataforma criada para sua loja.
              </p>
            </div>

            <!-- Formulário -->
            <form @submit.prevent="handleLogin" class="space-y-6">
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

              <!-- Lembrar senha e Esqueceu senha -->
              <div class="flex items-center justify-between">
                <div class="flex items-center">
                  <input
                    type="checkbox"
                    v-model="rememberMe"
                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                  />
                  <label class="ml-2 block text-sm text-gray-900">Lembrar senha</label>
                </div>
                <div class="text-sm">
                  <router-link 
                    to="/reset-password" 
                    class="font-medium text-indigo-600 hover:text-indigo-500"
                  >
                    Esqueceu senha?
                  </router-link>
                </div>
              </div>

              <!-- Botão de Login -->
              <button
                type="submit"
                :disabled="loading"
                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200"
              >
                <span v-if="loading">Carregando...</span>
                <span v-else>Acessar conta</span>
              </button>

              <!-- Link para Criar conta -->
              <div class="text-sm text-center pt-4">
                <span class="text-gray-600">Ainda não tem conta?</span>
                <router-link 
                  to="/register" 
                  class="font-medium text-indigo-600 hover:text-indigo-500 ml-1"
                >
                  Criar conta
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
const authStore = useAuthStore()

const email = ref('')
const password = ref('')
const rememberMe = ref(false)
const loading = ref(false)
const error = ref('')

const handleLogin = async () => {
  try {
    loading.value = true
    error.value = ''
    
    await authStore.login(
      email.value,
      password.value
    )
    
    router.push('/dashboard')
  } catch (err) {
    error.value = 'Credenciais inválidas'
  } finally {
    loading.value = false
  }
}
</script>

<style>
/* Reset básico */
*, *::before, *::after {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

/* Garante que o html e body ocupem toda a altura */
html, body, #app {
  height: 100%;
  min-height: 100vh;
  width: 100%;
  background-color: white;
}

/* Garante que inputs em iOS mantenham o estilo */
input {
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
}

/* Animações suaves */
.transition-transform {
  transition-property: transform;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 300ms;
}

/* Melhora a visualização em telas muito pequenas */
@media (max-width: 360px) {
  .text-3xl {
    font-size: 1.5rem;
  }
  .text-2xl {
    font-size: 1.25rem;
  }
}
</style>
