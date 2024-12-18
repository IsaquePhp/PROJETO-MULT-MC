<template>
  <div class="store-selector">
    <select 
      v-model="selectedStore" 
      class="w-full p-2 border rounded-lg"
      @change="$emit('update:modelValue', selectedStore)"
    >
      <option value="">Selecione uma loja</option>
      <option v-for="store in stores" :key="store.id" :value="store.id">
        {{ store.name }}
      </option>
    </select>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import axios from '../plugins/axios'

export default {
  name: 'StoreSelector',
  props: {
    modelValue: {
      type: [Number, String],
      default: ''
    }
  },
  emits: ['update:modelValue'],
  setup(props) {
    const stores = ref([])
    const selectedStore = ref(props.modelValue)

    const loadStores = async () => {
      try {
        const response = await axios.get('http://127.0.0.1:8000/api/stores')
        stores.value = response.data
      } catch (error) {
        console.error('Erro ao carregar lojas:', error)
      }
    }

    onMounted(() => {
      loadStores()
    })

    return {
      stores,
      selectedStore
    }
  }
}
</script>
