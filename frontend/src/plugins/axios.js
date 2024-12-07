import axios from 'axios'
import { useAuthStore } from '../stores/auth'

// Configuração base do axios
const instance = axios.create({
  baseURL: 'http://localhost/VERSAO%20DIEGO/api-loja-mc/public/api',
  withCredentials: true,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
})

// Interceptor de requisição
instance.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('token')
    if (token) {
      config.headers['Authorization'] = `Bearer ${token}`
    }
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

// Interceptor de resposta
instance.interceptors.response.use(
  (response) => response,
  async (error) => {
    const authStore = useAuthStore()
    
    if (error.response) {
      // Erro de autenticação
      if (error.response.status === 401) {
        await authStore.logout()
      }
      
      // Erro de validação
      if (error.response.status === 422) {
        return Promise.reject(error.response.data)
      }

      // Log de erro para debug
      console.error('API Error:', {
        status: error.response.status,
        data: error.response.data,
        url: error.config.url
      })
    }
    
    return Promise.reject(error)
  }
)

export default instance
