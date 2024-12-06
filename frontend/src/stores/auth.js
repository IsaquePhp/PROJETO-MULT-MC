import { defineStore } from 'pinia'
import axios from '../plugins/axios'
import router from '../router'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token') || null,
    loading: false
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
    getUser: (state) => state.user
  },

  actions: {
    async login(email, password) {
      this.loading = true
      try {
        const response = await axios.post('/login', { email, password })
        const { token, user } = response.data

        this.token = token
        this.user = user
        localStorage.setItem('token', token)
        localStorage.setItem('user', JSON.stringify(user))

        // Configure axios headers
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`

        await router.push('/dashboard')
        return true
      } catch (error) {
        console.error('Login error:', error)
        throw error
      } finally {
        this.loading = false
      }
    },

    async logout() {
      try {
        await axios.post('/logout')
      } catch (error) {
        console.error('Logout error:', error)
      } finally {
        this.token = null
        this.user = null
        localStorage.removeItem('token')
        localStorage.removeItem('user')
        delete axios.defaults.headers.common['Authorization']
        await router.push('/login')
      }
    },

    async checkAuth() {
      if (!this.token) {
        await router.push('/login')
        return false
      }

      try {
        const response = await axios.get('/me')
        this.user = response.data
        return true
      } catch (error) {
        console.error('Auth check error:', error)
        await this.logout()
        return false
      }
    },

    initializeAuth() {
      const token = localStorage.getItem('token')
      const user = localStorage.getItem('user')

      if (token && user) {
        this.token = token
        this.user = JSON.parse(user)
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
      }
    }
  }
})
