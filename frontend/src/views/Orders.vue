<template>
  <div class="container mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Vendas</h1>
      <div class="flex gap-2">
        <input 
          type="text" 
          v-model="searchTerm" 
          placeholder="Buscar venda..." 
          class="px-4 py-2 border rounded-lg"
        >
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-8">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto"></div>
      <p class="mt-4 text-gray-600">Carregando vendas...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
      <strong class="font-bold">Erro!</strong>
      <span class="block sm:inline">{{ error }}</span>
    </div>

    <!-- Data Table -->
    <div v-else>
      <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data do Pedido</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="sale in filteredSales" :key="sale.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                #{{ String(sale.id).padStart(5, '0') }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ sale.customer_name || 'N/A' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ formatDate(sale.created_at) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="getStatusClass(sale.status)">
                  {{ getStatusText(sale.status) }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                {{ formatCurrency(sale.total) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-indigo-600 hover:text-indigo-900">
                <button @click="showSaleDetails(sale)">Detalhes</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="pagination" class="flex justify-between items-center mt-4 bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
        <div class="flex items-center">
          <select 
            v-model="perPage" 
            class="form-select rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
            @change="handlePerPageChange"
          >
            <option value="15">15 por página</option>
            <option value="30">30 por página</option>
            <option value="50">50 por página</option>
            <option value="100">100 por página</option>
          </select>
        </div>
        
        <div class="flex justify-end space-x-2">
          <button 
            @click="changePage(pagination.current_page - 1)"
            :disabled="!pagination.prev_page_url"
            class="px-3 py-1 rounded border" 
            :class="pagination.prev_page_url ? 'hover:bg-gray-100' : 'opacity-50 cursor-not-allowed'"
          >
            Anterior
          </button>
          
          <span class="px-3 py-1">
            Página {{ pagination.current_page }} de {{ pagination.last_page }}
          </span>
          
          <button 
            @click="changePage(pagination.current_page + 1)"
            :disabled="!pagination.next_page_url"
            class="px-3 py-1 rounded border"
            :class="pagination.next_page_url ? 'hover:bg-gray-100' : 'opacity-50 cursor-not-allowed'"
          >
            Próxima
          </button>
        </div>
      </div>
    </div>

    <!-- Modal de Detalhes -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-lg max-w-4xl w-full p-8">
        <!-- Cabeçalho -->
        <div class="flex justify-between items-center mb-8 border-b pb-4">
          <h2 class="text-2xl font-bold text-gray-800">Detalhes do Pedidos</h2>
          <div class="flex items-center gap-4">
            <button 
              @click="handlePrint" 
              class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700 transition-colors"
            >
              Imprimir
            </button>
            <button 
              @click="closeModal" 
              class="text-gray-500 hover:text-gray-700 text-2xl font-light"
            >×</button>
          </div>
        </div>

        <!-- Informações -->
        <div class="grid grid-cols-3 gap-8 mb-8">
          <!-- Coluna 1: Informações do Pedido -->
          <div class="bg-gray-50 p-4 rounded-lg space-y-3">
            <p class="text-lg font-medium text-gray-800">N° {{ String(selectedSale?.id).padStart(4, '0') }}</p>
            <p class="text-gray-600">Pagamento: <span class="text-gray-800">{{ selectedSale?.payment_method || 'Cartão de Crédito' }}</span></p>
            <p class="text-gray-600">Status: 
              <span :class="{
                'text-yellow-600': selectedSale?.status === 'pending',
                'text-green-600': selectedSale?.status === 'completed',
                'text-red-600': selectedSale?.status === 'cancelled'
              }">
                {{ selectedSale?.status || 'Pendente' }}
              </span>
            </p>
          </div>
          
          <!-- Coluna 2: Informações do Cliente -->
          <div class="bg-gray-50 p-4 rounded-lg space-y-3">
            <p class="text-lg font-medium text-gray-800">{{ selectedSale?.customer?.name || 'Nome' }}</p>
            <p class="text-gray-600">{{ selectedSale?.customer?.address || 'Rua do cliente' }}</p>
            <p class="text-gray-600">{{ selectedSale?.customer?.phone || '(77) 98811-3043' }}</p>
          </div>

          <!-- Coluna 3: Informações da Loja -->
          <div class="bg-gray-50 p-4 rounded-lg space-y-3">
            <p class="text-lg font-medium text-gray-800">Loja MC</p>
            <p class="text-gray-600">Unidade: {{ selectedSale?.store?.name || 'Nome da Unidade' }}</p>
          </div>
        </div>

        <!-- Lista de Itens -->
        <div class="mb-8">
          <h3 class="font-medium text-lg mb-4 text-gray-800">Lista de itens</h3>
          <div class="bg-gray-50 rounded-lg overflow-hidden">
            <table class="w-full">
              <thead class="bg-gray-100">
                <tr>
                  <th class="text-left py-3 px-4 text-xs font-medium text-gray-600 uppercase">#</th>
                  <th class="text-left py-3 px-4 text-xs font-medium text-gray-600 uppercase">Nome do Item</th>
                  <th class="text-left py-3 px-4 text-xs font-medium text-gray-600 uppercase">Categoria</th>
                  <th class="text-center py-3 px-4 text-xs font-medium text-gray-600 uppercase">Quantidade</th>
                  <th class="text-right py-3 px-4 text-xs font-medium text-gray-600 uppercase">Valor Total</th>
                </tr>
              </thead>
              <tbody class="bg-white">
                <tr v-for="(item, index) in selectedSale?.items" :key="index" class="border-t border-gray-100">
                  <td class="py-3 px-4 text-gray-600">{{ index + 1 }}</td>
                  <td class="py-3 px-4 text-gray-800">{{ item.name }}</td>
                  <td class="py-3 px-4 text-gray-600">{{ item.category || 'N/A' }}</td>
                  <td class="py-3 px-4 text-center text-gray-600">{{ item.quantity }}</td>
                  <td class="py-3 px-4 text-right font-medium text-gray-800">R$ {{ Number(item.total).toFixed(2) }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Total -->
        <div class="flex justify-end">
          <div class="bg-gray-50 px-6 py-4 rounded-lg">
            <p class="text-sm text-gray-600 mb-1">Total</p>
            <p class="text-3xl font-bold text-gray-800">R$ {{ Number(selectedSale?.total || 0).toFixed(2) }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from '../services/axios'
import jsPDF from 'jspdf'
import 'jspdf-autotable'
import Logo from '../assets/Logo.png'
import { generateOrderQRCode } from '../utils/qrcode'

// Estado
const sales = ref([])
const loading = ref(false)
const error = ref(null)
const showModal = ref(false)
const selectedSale = ref(null)
const searchTerm = ref('')
const currentPage = ref(1)
const perPage = ref(10)
const totalItems = ref(0)

// Computed
const filteredSales = computed(() => {
  if (!searchTerm.value) return sales.value
  
  return sales.value.filter(sale => {
    return sale.id.toString().includes(searchTerm.value) ||
           (sale.customer_name || '').toLowerCase().includes(searchTerm.value.toLowerCase())
  })
})

// Métodos
const fetchSales = async (page = 1) => {
  try {
    loading.value = true
    error.value = null
    
    const response = await axios.get('/api/sales', {
      params: {
        page,
        per_page: perPage.value,
        with: ['items.product', 'customer']
      }
    })

    if (!response?.data?.success) {
      throw new Error('Resposta inválida do servidor')
    }

    const responseData = response.data.data
    sales.value = responseData.data.map(sale => ({
      id: sale.id,
      customer_name: sale.customer?.name || 'Cliente não informado',
      created_at: sale.created_at,
      total: Number(sale.total || 0),
      status: sale.status || 'pending',
      items: (sale.items || []).map(item => ({
        id: item.id,
        name: item.product?.name || item.name || 'Produto não informado',
        quantity: Number(item.quantity || 0),
        price: Number(item.product?.price || item.price || 0),
        total: Number(item.quantity || 0) * Number(item.product?.price || item.price || 0)
      }))
    }))

    // Atualiza a paginação
    totalItems.value = responseData.total
    currentPage.value = responseData.current_page

    console.log('Vendas carregadas:', sales.value)
  } catch (err) {
    console.error('Erro ao buscar vendas:', err)
    error.value = 'Erro ao carregar vendas: ' + (err.message || 'Erro desconhecido')
    sales.value = []
  } finally {
    loading.value = false
  }
}

const showSaleDetails = async (sale) => {
  try {
    const response = await axios.get(`/api/sales/${sale.id}`)
    if (response.data.success) {
      const saleData = response.data.data
      selectedSale.value = {
        id: saleData.id,
        customer_name: saleData.customer?.name || 'Cliente não informado',
        created_at: saleData.created_at,
        total: Number(saleData.total || 0),
        status: saleData.status || 'pending',
        items: (saleData.items || []).map(item => ({
          id: item.id,
          name: item.product?.name || item.name || 'Produto não informado',
          quantity: Number(item.quantity || 0),
          price: Number(item.product?.price || item.price || 0),
          total: Number(item.quantity || 0) * Number(item.product?.price || item.price || 0)
        }))
      }
      console.log('Detalhes da venda:', selectedSale.value)
    }
  } catch (error) {
    console.error('Erro ao buscar detalhes da venda:', error)
  }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false;
  selectedSale.value = null;
}

const handlePerPageChange = () => {
  currentPage.value = 1
  fetchSales(1)
}

const changePage = (page) => {
  if (page < 1 || page > 6) return
  currentPage.value = page
  fetchSales(page)
}

const formatDate = (date) => {
  if (!date) return '';
  const d = new Date(date);
  return d.toLocaleString('pt-BR');
}

const formatCurrency = (value) => {
  return `R$ ${Number(value || 0).toFixed(2)}`;
}

const getStatusText = (status) => {
  const statusMap = {
    'pending': 'Pendente',
    'completed': 'Concluído',
    'cancelled': 'Cancelado',
    'processing': 'Em Processamento',
    'delivered': 'Entregue'
  }
  return statusMap[status?.toLowerCase()] || 'Pendente'
}

const getStatusClass = (status) => {
  const classes = {
    'pending': 'bg-yellow-100 text-yellow-800',
    'completed': 'bg-green-100 text-green-800',
    'cancelled': 'bg-red-100 text-red-800',
    'processing': 'bg-blue-100 text-blue-800',
    'delivered': 'bg-purple-100 text-purple-800'
  }
  
  return `px-2 py-1 rounded-full text-xs font-medium ${classes[status?.toLowerCase()] || classes.pending}`
}

const handlePrint = async () => {
  if (selectedSale.value) {
    try {
      const doc = new jsPDF()
      const sale = selectedSale.value
      
      console.log('Dados para impressão:', sale)
      console.log('Items da venda:', sale.items)

      // Fundo azul do cabeçalho
      doc.setFillColor(47, 47, 180)
      doc.rect(0, 0, 210, 35, 'F')
      
      // Logo com tamanho reduzido em 20%
      const logoWidth = 36
      const logoHeight = 20
      doc.addImage(Logo, 'PNG', 15, 8, logoWidth, logoHeight)
      
      // Linha vertical branca
      doc.setDrawColor(255, 255, 255)
      doc.setLineWidth(0.5)
      doc.line(65, 5, 65, 30)
      
      // Informações em branco diretamente no fundo azul
      doc.setTextColor(255, 255, 255)
      doc.setFontSize(10)
      doc.setFont('helvetica', 'normal')
      
      // CNPJ
      doc.text('CNPJ: 12.345.678/0001-90', 75, 10)
      
      // Telefone e WhatsApp
      doc.text('Telefone: (31) 3434-3434 | WhatsApp: (31) 99999-9999', 75, 16)
      
      // Endereço
      doc.text('R. dos Marmelos, 97 - Vila Cloris, Belo Horizonte - MG, 31744-093', 75, 22)
      
      // Email e Site
      doc.text('Email: contato@lojasmc.com.br | Site: www.lojasmc.com.br', 75, 28)

      // Informações do pedido
      doc.setTextColor(0, 0, 0)
      doc.setFontSize(12)
      doc.text(`Pedido #${sale.id}`, 15, 45)
      doc.setFontSize(10)
      doc.text(`Data: ${new Date(sale.created_at).toLocaleString()}`, 15, 52)
      doc.text(`Cliente: ${sale.customer_name || 'Cliente não informado'}`, 15, 59)

      // Tabela de itens
      const tableColumn = ["Descrição", "Qtde", "Unitário", "Subtotal"]
      const tableRows = sale.items.map(item => {
        console.log('Item processando:', item)
        
        const price = Number(item.price || 0)
        const quantity = Number(item.quantity || 0)
        const subtotal = price * quantity
        
        return [
          item.name || 'Produto não informado',
          quantity,
          `R$ ${price.toFixed(2)}`,
          `R$ ${subtotal.toFixed(2)}`
        ]
      })

      doc.autoTable({
        startY: 65,
        head: [tableColumn],
        body: tableRows,
        theme: 'grid',
        headStyles: {
          fillColor: [47, 47, 180],
          textColor: [255, 255, 255],
          fontSize: 10,
          halign: 'center'
        },
        columnStyles: {
          0: { halign: 'left' },
          1: { halign: 'center' },
          2: { halign: 'right' },
          3: { halign: 'right' }
        },
        styles: {
          fontSize: 10,
          cellPadding: 3,
        },
      })

      const finalY = doc.lastAutoTable.finalY + 10

      // Calculando o total
      const total = sale.items.reduce((acc, item) => {
        const price = Number(item.price || 0)
        const quantity = Number(item.quantity || 0)
        return acc + (price * quantity)
      }, 0)

      // Totais
      doc.setDrawColor(200, 200, 200)
      doc.setLineWidth(0.5)
      doc.line(15, finalY - 5, 195, finalY - 5)

      doc.text('Subtotal:', 150, finalY + 5)
      doc.text(`R$ ${total.toFixed(2)}`, 190, finalY + 5, { align: 'right' })

      doc.text('Entrega:', 150, finalY + 12)
      doc.text('R$ 0,00', 190, finalY + 12, { align: 'right' })

      doc.setTextColor(47, 47, 180)
      doc.setFontSize(12)
      doc.text('Total:', 150, finalY + 20)
      doc.text(`R$ ${total.toFixed(2)}`, 190, finalY + 20, { align: 'right' })

      // Linha separadora do rodapé
      doc.setDrawColor(200, 200, 200)
      doc.setLineWidth(0.5)
      doc.line(0, 270, 210, 270)

      try {
        // Gerar QR Code
        const qrCodeDataUrl = await generateOrderQRCode(sale)
        
        if (qrCodeDataUrl) {
          // Reduzindo o tamanho do QR Code de 25x25 para 15x15
          doc.addImage(qrCodeDataUrl, 'PNG', 15, doc.internal.pageSize.height - 30, 15, 15)
          
          // Ajustando a posição do texto para alinhar com o QR Code menor
          doc.setFontSize(8)
          doc.setTextColor(0, 0, 0)
          doc.text('Para verificar a autenticidade deste comprovante,', 35, doc.internal.pageSize.height - 25)
          doc.text('escaneie o QR Code ao lado ou acesse:', 35, doc.internal.pageSize.height - 20)
          doc.setTextColor(0, 0, 255)
          doc.text('www.lojasmc.com.br/verify', 35, doc.internal.pageSize.height - 15)
        }
      } catch (error) {
        console.error('Erro ao gerar QR Code:', error)
      }

      // Slogan e página
      doc.setTextColor(47, 47, 180)
      doc.setFontSize(8)
      doc.text('Sua loja, seu estilo!', 15, doc.internal.pageSize.height - 5)
      doc.text('Página 1 de 1', 195, doc.internal.pageSize.height - 5, { align: 'right' })

      // Salvar o PDF
      doc.save(`pedido-${sale.id}.pdf`)
    } catch (error) {
      console.error('Erro ao gerar PDF:', error)
    }
  }
}

// Lifecycle
onMounted(() => {
  fetchSales()
})
</script>

<style scoped>
.btn-primary {
  @apply bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors;
}

.btn-secondary {
  @apply bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors;
}
</style>
