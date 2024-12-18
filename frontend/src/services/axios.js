import axios from 'axios'

// Configuração base do axios
const axiosInstance = axios.create({
  baseURL: 'http://localhost/API%20LOJA%20MC%20-%20Copy/public/api',  // URL com encoding correto
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'X-Requested-With': 'XMLHttpRequest'
  },
  withCredentials: true,
  timeout: 30000 // Aumentando o timeout para 30 segundos
})

// Interceptor para tratar erros
axiosInstance.interceptors.response.use(
  response => response,
  error => {
    console.error('Erro na requisição:', error.config)
    return Promise.reject({
      message: error.response?.data?.message || 'Erro ao processar a requisição',
      response: error.response
    })
  }
);

// Interceptor para requisições
axiosInstance.interceptors.request.use(
  config => {
    // Log da requisição
    console.log('Requisição sendo enviada:', {
      url: config.url,
      method: config.method,
      data: config.data,
      headers: config.headers
    });
    return config;
  },
  error => {
    return Promise.reject(error);
  }
);

export default axiosInstance
