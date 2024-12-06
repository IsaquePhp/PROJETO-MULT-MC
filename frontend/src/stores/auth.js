import { defineStore } from 'pinia'
import axios from '../plugins/axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('user')) || null,
    token: localStorage.getItem('token') || null,
    loading: false,
    error: null
  }),
  
  getters: {
    isAuthenticated: (state) => !!state.token,
    getUser: (state) => state.user,
    getError: (state) => state.error
  },
  
  actions: {
    async register(name, email, password, password_confirmation) {
      this.loading = true
      this.error = null
      
      try {
        console.log('Iniciando registro...')
        
        // Primeiro, obter o CSRF token
        const csrfResponse = await axios.get('http://localhost/VERSAO%20DIEGO/api-loja-mc/public/sanctum/csrf-cookie')
        console.log('CSRF Response:', csrfResponse)
        
        // Tentar fazer registro
        console.log('Enviando credenciais...')
        const response = await axios.post('/register', {
          name,
          email,
          password,
          password_confirmation
        })
        console.log('Registro Response:', response.data)
        
        if (response.data.access_token) {
          this.token = response.data.access_token
          this.user = response.data.user
          localStorage.setItem('token', this.token)
          localStorage.setItem('user', JSON.stringify(this.user))
          return true
        } else {
          throw new Error('Token não recebido')
        }
      } catch (error) {
        console.error('Erro detalhado:', {
          message: error.message,
          response: error.response?.data,
          status: error.response?.status
        })
        this.error = error.response?.data?.message || 'Erro ao registrar'
        throw error
      } finally {
        this.loading = false
      }
    },
    
    async login(email, password) {
      this.loading = true
      this.error = null
      
      try {
        console.log('Iniciando login...')
        
        // Primeiro, obter o CSRF token
        const csrfResponse = await axios.get('http://localhost/VERSAO%20DIEGO/api-loja-mc/public/sanctum/csrf-cookie')
        console.log('CSRF Response:', csrfResponse)
        
        // Tentar fazer login
        console.log('Enviando credenciais...')
        const response = await axios.post('/login', {
          email,
          password
        })
        console.log('Login Response:', response.data)
        
        if (response.data.access_token) {
          this.token = response.data.access_token
          this.user = response.data.user
          localStorage.setItem('token', this.token)
          localStorage.setItem('user', JSON.stringify(this.user))
          return true
        } else {
          throw new Error('Token não recebido')
        }
      } catch (error) {
        console.error('Erro detalhado:', {
          message: error.message,
          response: error.response?.data,
          status: error.response?.status
        })
        this.error = error.response?.data?.message || 'Erro ao fazer login'
        throw error
      } finally {
        this.loading = false
      }
    },
    
    async logout() {
      try {
        console.log('Iniciando logout...')
        const response = await axios.post('/logout')
        console.log('Logout Response:', response.data)
      } catch (error) {
        console.error('Logout error:', error)
      } finally {
        this.token = null
        this.user = null
        localStorage.removeItem('token')
        localStorage.removeItem('user')
      }
    },
    
    checkAuth() {
      const token = localStorage.getItem('token')
      const user = localStorage.getItem('user')
      if (token && user) {
        this.token = token
        this.user = JSON.parse(user)
        return true
      }
      return false
    }
  }
})
