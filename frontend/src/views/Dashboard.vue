<template>
  <div class="dashboard p-6 bg-gray-50">
    <!-- Cabeçalho -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-semibold text-gray-900">Dashboard</h1>
      
      <div class="flex items-center gap-4">
        <select v-model="period" class="bg-white border border-gray-300 rounded-lg px-4 py-2 text-sm">
          <option value="today">Hoje</option>
          <option value="week">Última Semana</option>
          <option value="month">Último Mês</option>
          <option value="year">Último Ano</option>
        </select>
      </div>
    </div>

    <!-- Cards de Métricas -->
    <div class="grid grid-cols-1 gap-6 mb-6 sm:grid-cols-2 lg:grid-cols-4">
      <!-- Total de Vendas -->
      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-sm font-medium text-gray-500">Total de Vendas</h3>
          <div class="p-2 bg-blue-50 rounded-lg">
            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
            </svg>
          </div>
        </div>
        <div class="flex items-baseline">
          <p class="text-2xl font-bold text-gray-900">2.457</p>
          <p class="ml-2 text-sm font-medium text-green-600">
            <span>+12.5%</span>
          </p>
        </div>
      </div>

      <!-- Receita Total -->
      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-sm font-medium text-gray-500">Receita Total</h3>
          <div class="p-2 bg-green-50 rounded-lg">
            <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </div>
        <div class="flex items-baseline">
          <p class="text-2xl font-bold text-gray-900">R$ 156.789,00</p>
          <p class="ml-2 text-sm font-medium text-green-600">
            <span>+8.2%</span>
          </p>
        </div>
      </div>

      <!-- Ticket Médio -->
      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-sm font-medium text-gray-500">Ticket Médio</h3>
          <div class="p-2 bg-purple-50 rounded-lg">
            <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v4m-6-4v4m6-11v-4m-6 4v-4" />
            </svg>
          </div>
        </div>
        <div class="flex items-baseline">
          <p class="text-2xl font-bold text-gray-900">R$ 63,81</p>
          <p class="ml-2 text-sm font-medium text-red-600">
            <span>-2.4%</span>
          </p>
        </div>
      </div>

      <!-- Produtos Vendidos -->
      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-sm font-medium text-gray-500">Produtos Vendidos</h3>
          <div class="p-2 bg-orange-50 rounded-lg">
            <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
          </div>
        </div>
        <div class="flex items-baseline">
          <p class="text-2xl font-bold text-gray-900">3.849</p>
          <p class="ml-2 text-sm font-medium text-green-600">
            <span>+5.7%</span>
          </p>
        </div>
      </div>
    </div>

    <!-- Gráficos e Tabelas -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
      <!-- Gráfico de Vendas -->
      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Vendas por Período</h3>
        <div class="h-80 bg-gray-50 rounded-lg flex items-center justify-center">
          <!-- Placeholder para o gráfico -->
          <div class="w-full h-full p-4">
            <div class="relative h-full">
              <!-- Barras do gráfico (simuladas) -->
              <div class="absolute bottom-0 left-0 w-full h-full flex items-end justify-between px-2">
                <div class="w-8 bg-blue-500 rounded-t-lg" style="height: 60%"></div>
                <div class="w-8 bg-blue-500 rounded-t-lg" style="height: 75%"></div>
                <div class="w-8 bg-blue-500 rounded-t-lg" style="height: 45%"></div>
                <div class="w-8 bg-blue-500 rounded-t-lg" style="height: 90%"></div>
                <div class="w-8 bg-blue-500 rounded-t-lg" style="height: 65%"></div>
                <div class="w-8 bg-blue-500 rounded-t-lg" style="height: 80%"></div>
                <div class="w-8 bg-blue-500 rounded-t-lg" style="height: 70%"></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Top Produtos -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <h3 class="text-lg font-medium text-gray-900 p-6 border-b">Produtos Mais Vendidos</h3>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produto</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendas</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Receita</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="h-10 w-10 flex-shrink-0">
                      <div class="h-10 w-10 rounded-lg bg-gray-100"></div>
                    </div>
                    <div class="ml-4">
                      <div class="text-sm font-medium text-gray-900">iPhone 13 Pro</div>
                      <div class="text-sm text-gray-500">Smartphones</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900">324</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900">R$ 45.890,00</div>
                </td>
              </tr>
              <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="h-10 w-10 flex-shrink-0">
                      <div class="h-10 w-10 rounded-lg bg-gray-100"></div>
                    </div>
                    <div class="ml-4">
                      <div class="text-sm font-medium text-gray-900">MacBook Pro</div>
                      <div class="text-sm text-gray-500">Notebooks</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900">256</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900">R$ 38.750,00</div>
                </td>
              </tr>
              <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="h-10 w-10 flex-shrink-0">
                      <div class="h-10 w-10 rounded-lg bg-gray-100"></div>
                    </div>
                    <div class="ml-4">
                      <div class="text-sm font-medium text-gray-900">AirPods Pro</div>
                      <div class="text-sm text-gray-500">Acessórios</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900">198</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900">R$ 23.460,00</div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Últimas Vendas -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
      <h3 class="text-lg font-medium text-gray-900 p-6 border-b">Últimas Vendas</h3>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produtos</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center text-sm font-medium text-gray-600">JD</div>
                  <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900">João da Silva</div>
                    <div class="text-sm text-gray-500">joao@email.com</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">iPhone 13 Pro, AirPods Pro</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">R$ 8.990,00</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                  Concluído
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                19/12/2023 14:30
              </td>
            </tr>
            <tr>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center text-sm font-medium text-gray-600">MS</div>
                  <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900">Maria Santos</div>
                    <div class="text-sm text-gray-500">maria@email.com</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">MacBook Pro</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">R$ 12.490,00</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                  Em Processamento
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                19/12/2023 13:45
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue'

export default {
  setup() {
    const period = ref('month')

    return {
      period
    }
  }
}
</script>

<style scoped>
.dashboard {
  min-height: calc(100vh - 64px);
}
</style>
