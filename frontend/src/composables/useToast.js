import { ref } from 'vue'

export function useToast() {
  const showToast = ref(false)
  const toastMessage = ref('')
  const toastType = ref('success') // 'success' ou 'error'
  const toastDuration = 3000 // 3 segundos

  function showSuccessToast(message) {
    toastMessage.value = message
    toastType.value = 'success'
    showToast.value = true

    setTimeout(() => {
      showToast.value = false
      toastMessage.value = ''
    }, toastDuration)
  }

  function showErrorToast(message) {
    toastMessage.value = message
    toastType.value = 'error'
    showToast.value = true

    setTimeout(() => {
      showToast.value = false
      toastMessage.value = ''
    }, toastDuration)
  }

  return {
    showToast,
    toastMessage,
    toastType,
    showSuccessToast,
    showErrorToast
  }
}
