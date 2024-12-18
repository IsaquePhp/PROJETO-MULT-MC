import { defineStore } from 'pinia'
import axios from '../plugins/axios'
import router from '../router'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('user')) || null,
    token: localStorage.getItem('token') || null,
    loading: false,
    error: null
  }),

  getters: {
    isAuthenticated: (state) => !!state.token && !!state.user,
    getUser: (state) => state.user,
    getError: (state) => state.error
  },

  actions: {
    async checkAuth() {
      if (!this.token) {
        return false
      }

      // Verificar se o token ainda é válido
      try {
        const response = await axios.get('/me')
        this.user = response.data
        return true
      } catch (error) {
        // Se o token for inválido, limpar o estado
        if (error.response && error.response.status === 401) {
          this.token = null
          this.user = null
          localStorage.removeItem('token')
          localStorage.removeItem('user')
        }
        return false
      }
    },

    async login(email, password) {
      this.loading = true
      this.error = null
      try {
        const response = await axios.post('/login', { email, password })
        const { access_token, user } = response.data

        this.token = access_token
        this.user = user
        localStorage.setItem('token', access_token)
        localStorage.setItem('user', JSON.stringify(user))

        await router.push('/dashboard')
        return true
      } catch (error) {
        if (error.response) {
          this.error = error.response.data.message
        } else {
          this.error = 'Erro ao fazer login'
        }
        throw error
      } finally {
        this.loading = false
      }
    },

    async logout() {
      try {
        await axios.post('/logout')
      } catch (error) {
        console.error('Erro ao fazer logout:', error)
      } finally {
        this.token = null
        this.user = null
        localStorage.removeItem('token')
        localStorage.removeItem('user')
        router.push('/login')
      }
    },

    async initializeAuth() {
      const token = localStorage.getItem('token')
      const user = localStorage.getItem('user')
      
      if (token && user) {
        this.token = token
        this.user = JSON.parse(user)
        return this.checkAuth()
      }
      
      return false
    },

    clearError() {
      this.error = null
    }
  }
})
