import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'
import './style.css'
import { clickOutside } from './directives/click-outside'
import { useAuthStore } from './stores/auth'

const app = createApp(App)
const pinia = createPinia()

app.use(pinia)
app.use(router)
app.directive('click-outside', clickOutside)

// Inicializar autenticação antes de montar o app
const authStore = useAuthStore(pinia)
await authStore.initializeAuth()

app.mount('#app')
