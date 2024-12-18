<template>
  <div class="min-h-screen flex bg-white">
    <!-- Lado esquerdo com formulário -->
    <div class="flex-1 flex flex-col p-8">
      <!-- Logo -->
      <div class="flex-shrink-0">
        <img 
          src="../assets/Logo-Azul.png" 
          alt="Lojas MC" 
          class="h-7 w-auto object-contain"
          style="image-rendering: -webkit-optimize-contrast; transform: translateZ(0);"
        />
      </div>

      <!-- Formulário centralizado -->
      <div class="flex-grow flex items-center justify-center">
        <div class="w-full max-w-md space-y-8">
          <div>
            <h2 class="text-3xl font-bold text-gray-900">
              Fazer login
            </h2>
            <p class="mt-2 text-base text-gray-600">
              Seja bem vindo ao sistema de gestão da Lojas MC.
            </p>
          </div>

          <form class="mt-8 space-y-6" @submit.prevent="handleLogin">
            <div class="space-y-5">
              <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input
                  id="email"
                  v-model="email"
                  name="email"
                  type="email"
                  required
                  class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-lg shadow-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  placeholder="Digite seu email"
                  :disabled="authStore.loading"
                />
              </div>
              <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Senha</label>
                <input
                  id="password"
                  v-model="password"
                  name="password"
                  type="password"
                  required
                  class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-lg shadow-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  placeholder="Digite sua senha"
                  :disabled="authStore.loading"
                />
              </div>
            </div>

            <div class="flex items-center justify-between">
              <div class="flex items-center">
                <input
                  id="remember-me"
                  name="remember-me"
                  type="checkbox"
                  class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                />
                <label for="remember-me" class="ml-2 block text-sm text-gray-900">
                  Lembrar senha
                </label>
              </div>
              <div class="text-sm">
                <a href="#" class="font-medium text-indigo-600 hover:text-indigo-500">
                  Esqueceu sua senha?
                </a>
              </div>
            </div>

            <div>
              <button
                type="submit"
                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                :disabled="authStore.loading"
              >
                <span v-if="authStore.loading">
                  <svg class="animate-spin h-5 w-5 mr-3" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Entrando...
                </span>
                <span v-else>Acessar conta</span>
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Footer -->
      <div class="flex-shrink-0 pt-8">
        <p class="text-sm text-gray-500">BY ATUALZ SOLUÇÕES | 2024</p>
      </div>
    </div>

    <!-- Lado direito com imagem -->
    <div class="hidden lg:block lg:w-1/2">
      <img 
        src="https://images.unsplash.com/photo-1567401893414-76b7b1e5a7a5?w=1200&q=80" 
        alt="Loja" 
        class="w-full h-full object-cover"
      />
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useAuthStore } from '../stores/auth'

const authStore = useAuthStore()
const email = ref('')
const password = ref('')

const handleLogin = async () => {
  try {
    await authStore.login(email.value, password.value)
  } catch (error) {
    console.error('Login failed:', error)
  }
}
</script>
