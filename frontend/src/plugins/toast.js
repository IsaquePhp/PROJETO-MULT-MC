import { createApp } from 'vue'
import Toast from 'vue-toastification'
import 'vue-toastification/dist/index.css'

const options = {
  position: 'top-right',
  timeout: 3000,
  closeOnClick: true,
  pauseOnFocusLoss: true,
  pauseOnHover: true,
  draggable: true,
  draggablePercent: 0.6,
  showCloseButtonOnHover: false,
  hideProgressBar: false,
  closeButton: 'button',
  icon: true,
  rtl: false
}

const app = createApp({})
app.use(Toast, options)

export const toast = {
  success: (message) => app.config.globalProperties.$toast.success(message),
  error: (message) => app.config.globalProperties.$toast.error(message),
  info: (message) => app.config.globalProperties.$toast.info(message),
  warning: (message) => app.config.globalProperties.$toast.warning(message)
}
