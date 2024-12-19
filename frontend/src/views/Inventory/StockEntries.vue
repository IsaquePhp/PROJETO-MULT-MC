<template>
  <div class="container mx-auto p-4">
    <div class="bg-white rounded-lg shadow">
      <!-- Cabeçalho com título e botão de novo lançamento -->
      <div class="p-6 border-b flex justify-between items-center">
        <h1 class="text-2xl font-semibold">Lançamentos</h1>
        <router-link 
          to="/inventory/entries/new"
          class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center gap-2"
        >
          <i class="fas fa-plus"></i>
          Novo lançamento
        </router-link>
      </div>

      <!-- Lista de lançamentos -->
      <div class="p-6">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="text-left text-xs text-gray-500 border-b">
                <th class="pb-2">ID</th>
                <th class="pb-2">TIPO</th>
                <th class="pb-2">STATUS</th>
                <th class="pb-2">DATA EMISSÃO</th>
                <th class="pb-2">DATA RECEBIMENTO</th>
                <th class="pb-2">INSTITUIÇÃO</th>
                <th class="pb-2">FORNECEDOR</th>
                <th class="pb-2">VALOR TOTAL</th>
                <th class="pb-2">AÇÕES</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="entry in entries" :key="entry.id" class="border-b">
                <td class="py-3">{{ entry.id }}</td>
                <td class="py-3">{{ formatType(entry.type) }}</td>
                <td class="py-3">{{ formatStatus(entry.status) }}</td>
                <td class="py-3">{{ formatDate(entry.issueDate) }}</td>
                <td class="py-3">{{ formatDate(entry.receiveDate) }}</td>
                <td class="py-3">{{ entry.institution?.name }}</td>
                <td class="py-3">{{ entry.supplier?.name }}</td>
                <td class="py-3">{{ formatCurrency(entry.totalAmount) }}</td>
                <td class="py-3">
                  <button 
                    @click="viewEntry(entry.id)"
                    class="text-indigo-600 hover:text-indigo-800 mr-2"
                    title="Ver detalhes"
                  >
                    <i class="fas fa-eye"></i>
                  </button>
                  <button 
                    @click="deleteEntry(entry.id)"
                    class="text-red-500 hover:text-red-700"
                    title="Excluir"
                  >
                    <i class="fas fa-trash"></i>
                  </button>
                </td>
              </tr>
              <tr v-if="entries.length === 0">
                <td colspan="9" class="py-4 text-center text-gray-500">
                  Nenhum lançamento encontrado
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from '../../plugins/axios'
import { useToast } from 'vue-toastification'

export default {
  name: 'StockEntries',
  setup() {
    const router = useRouter()
    const toast = useToast()
    const entries = ref([])

    const formatType = (type) => {
      const types = {
        donation: 'Doação',
        purchase: 'Compra',
        return: 'Devolução'
      }
      return types[type] || type
    }

    const formatStatus = (status) => {
      const statuses = {
        with_note: 'Com nota',
        without_note: 'Sem nota'
      }
      return statuses[status] || status
    }

    const formatDate = (date) => {
      if (!date) return ''
      return new Date(date).toLocaleDateString('pt-BR')
    }

    const formatCurrency = (value) => {
      const number = Number(value) || 0
      return number.toLocaleString('pt-BR', {
        style: 'currency',
        currency: 'BRL'
      })
    }

    const loadEntries = async () => {
      try {
        const response = await axios.get('/inventory/entries')
        entries.value = response.data.data
      } catch (error) {
        console.error('Erro ao carregar lançamentos:', error)
        toast.error('Erro ao carregar os lançamentos')
      }
    }

    const viewEntry = (id) => {
      router.push(`/inventory/entries/${id}`)
    }

    const deleteEntry = async (id) => {
      if (!confirm('Tem certeza que deseja excluir este lançamento?')) return

      try {
        await axios.delete(`/inventory/entries/${id}`)
        toast.success('Lançamento excluído com sucesso!')
        loadEntries()
      } catch (error) {
        console.error('Erro ao excluir lançamento:', error)
        toast.error('Erro ao excluir o lançamento')
      }
    }

    onMounted(loadEntries)

    return {
      entries,
      formatType,
      formatStatus,
      formatDate,
      formatCurrency,
      viewEntry,
      deleteEntry
    }
  }
}
</script>
