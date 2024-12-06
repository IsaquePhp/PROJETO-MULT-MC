import { ref } from 'vue'

export function useToast() {
  const showToast = ref(false)
  const toastMessage = ref('')
  const toastDuration = 3000 // 3 segundos

  function showSuccessToast(message) {
    toastMessage.value = message
    showToast.value = true

    setTimeout(() => {
      showToast.value = false
      toastMessage.value = ''
    }, toastDuration)
  }

  return {
    showToast,
    toastMessage,
    showSuccessToast
  }
}
