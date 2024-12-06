<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
          Criar nova conta
        </h2>
      </div>
      <form class="mt-8 space-y-6" @submit.prevent="handleRegister">
        <div class="rounded-md shadow-sm -space-y-px">
          <div>
            <label for="name" class="sr-only">Nome</label>
            <input
              id="name"
              v-model="name"
              name="name"
              type="text"
              required
              class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
              placeholder="Nome completo"
            />
          </div>
          <div>
            <label for="email" class="sr-only">Email</label>
            <input
              id="email"
              v-model="email"
              name="email"
              type="email"
              required
              class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
              placeholder="Email"
            />
          </div>
          <div>
            <label for="password" class="sr-only">Senha</label>
            <input
              id="password"
              v-model="password"
              name="password"
              type="password"
              required
              class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
              placeholder="Senha"
            />
          </div>
          <div>
            <label for="password_confirmation" class="sr-only">Confirmar Senha</label>
            <input
              id="password_confirmation"
              v-model="passwordConfirmation"
              name="password_confirmation"
              type="password"
              required
              class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
              placeholder="Confirmar senha"
            />
          </div>
        </div>

        <div v-if="error" class="text-red-500 text-sm text-center">
          {{ error }}
        </div>

        <div>
          <button
            type="submit"
            :disabled="loading"
            class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
          >
            <span v-if="loading">
              <svg class="animate-spin h-5 w-5 mr-3" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Registrando...
            </span>
            <span v-else>Registrar</span>
          </button>
        </div>

        <div class="text-sm text-center">
          <router-link to="/login" class="font-medium text-indigo-600 hover:text-indigo-500">
            Já tem uma conta? Faça login
          </router-link>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue'
import { useAuthStore } from '../stores/auth'
import axios from '../plugins/axios'

export default {
  name: 'Register',
  setup() {
    const authStore = useAuthStore()
    const name = ref('')
    const email = ref('')
    const password = ref('')
    const passwordConfirmation = ref('')
    const error = ref('')
    const loading = ref(false)

    const handleRegister = async () => {
      error.value = ''
      loading.value = true

      if (password.value !== passwordConfirmation.value) {
        error.value = 'As senhas não conferem'
        loading.value = false
        return
      }

      try {
        // Registrar usuário
        const response = await axios.post('/register', {
          name: name.value,
          email: email.value,
          password: password.value,
          password_confirmation: passwordConfirmation.value
        })

        // Login automático após registro
        if (response.data.access_token) {
          await authStore.login(email.value, password.value)
        }
      } catch (err) {
        error.value = err.response?.data?.message || 'Erro ao registrar. Tente novamente.'
      } finally {
        loading.value = false
      }
    }

    return {
      name,
      email,
      password,
      passwordConfirmation,
      error,
      loading,
      handleRegister
    }
  }
}
</script>
