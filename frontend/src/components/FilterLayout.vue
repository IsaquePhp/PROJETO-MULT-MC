&lt;template>
  <div class="mb-6">
    <!-- Título e Botão -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">{{ title }}</h1>
      <button
        v-if="showAddButton"
        @click="$emit('add')"
        class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
      >
        {{ addButtonText }}
      </button>
    </div>

    <!-- Filtros -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
      <slot></slot>
    </div>

    <!-- Limpar Filtros -->
    <div class="flex justify-end">
      <button
        @click="$emit('reset')"
        class="text-gray-600 hover:text-gray-800 text-sm"
      >
        Limpar Filtros
      </button>
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: {
    type: String,
    required: true
  },
  showAddButton: {
    type: Boolean,
    default: true
  },
  addButtonText: {
    type: String,
    default: 'Adicionar'
  }
})

defineEmits(['add', 'reset'])
</script>

<style scoped>
/* Herda os estilos globais de input */
</style>
