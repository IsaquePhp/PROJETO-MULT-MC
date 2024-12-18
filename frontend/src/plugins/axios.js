import axios from 'axios'
import { useAuthStore } from '../stores/auth'
import router from '../router'

// Configuração base do axios
const instance = axios.create({
  baseURL: 'http://127.0.0.1:8000/api',
  withCredentials: true,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
})

// Interceptor de requisição
instance.interceptors.request.use(
  async (config) => {
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
  (response) => {
    return response
  },
  async (error) => {
    if (error.response && error.response.status === 401) {
      const currentPath = router.currentRoute.value.path
      
      // Apenas faz logout se não estiver na página de login
      if (currentPath !== '/login') {
        const authStore = useAuthStore()
        await authStore.logout()
        router.push('/login')
      }
    }
    return Promise.reject(error)
  }
)

export default instance
