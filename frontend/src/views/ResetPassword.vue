<template>
  <div class="min-h-screen w-full flex bg-white">
    <div class="container mx-auto flex">
      <!-- Container do conteúdo -->
      <div class="w-full flex items-center justify-center">
        <!-- Formulário -->
        <div class="w-full max-w-md p-8 md:p-12 lg:p-16">
          <!-- Logo -->
          <div class="mb-8">
            <h1 class="text-4xl font-bold text-indigo-600">MC</h1>
          </div>

          <!-- Título e Subtítulo -->
          <div class="text-center mb-8">
            <h2 class="text-3xl font-semibold text-gray-900 mb-2">Resetar senha</h2>
            <p class="text-gray-600">
              Digite seu email para receber um link<br />
              de redefinição de senha.
            </p>
          </div>

          <!-- Formulário -->
          <form @submit.prevent="handleResetPassword" class="space-y-6">
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

            <!-- Botão de Reset -->
            <button
              type="submit"
              :disabled="loading"
              class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200"
            >
              <span v-if="loading">Enviando...</span>
              <span v-else>Enviar link de redefinição</span>
            </button>

            <!-- Link para Login -->
            <div class="text-sm text-center pt-4">
              <span class="text-gray-600">Lembrou sua senha?</span>
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
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const auth = useAuthStore()

const email = ref('')
const loading = ref(false)

const handleResetPassword = async () => {
  try {
    loading.value = true
    const success = await auth.resetPassword(email.value)
    
    if (success) {
      // Redirecionar para uma página de confirmação ou mostrar uma mensagem
      alert('Um link de redefinição de senha foi enviado para seu email.')
      router.push('/login')
    }
  } catch (error) {
    console.error('Reset password error:', error)
  } finally {
    loading.value = false
  }
}
</script>
