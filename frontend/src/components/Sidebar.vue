<template>
  <aside class="h-screen w-64 flex-shrink-0 sidebar-bg flex flex-col">
    <!-- Logo -->
    <div class="p-4 border-b border-white/10 sidebar-bg">
      <div class="flex items-center justify-center">
        <h1 class="text-xl font-semibold text-white">LojaMC</h1>
      </div>
    </div>

    <!-- Menu Items -->
    <nav class="flex-1 overflow-y-auto px-3 py-2 sidebar-bg">
      <div class="space-y-1 sidebar-bg">
        <!-- Dashboard -->
        <router-link to="/dashboard" class="flex items-center gap-2 px-4 py-2.5 text-white sidebar-bg hover:bg-white/10 rounded-lg">
          <HomeIcon class="w-5 h-5" />
          <span>Dashboard</span>
        </router-link>

        <!-- Clientes -->
        <router-link to="/customers" class="flex items-center gap-2 px-4 py-2.5 text-white sidebar-bg hover:bg-white/10 rounded-lg">
          <UserGroupIcon class="w-5 h-5" />
          <span>Clientes</span>
        </router-link>

        <!-- Produtos -->
        <router-link to="/products" class="flex items-center gap-2 px-4 py-2.5 text-white sidebar-bg hover:bg-white/10 rounded-lg">
          <CubeIcon class="w-5 h-5" />
          <span>Produtos</span>
        </router-link>

        <!-- Categorias -->
        <router-link to="/categories" class="flex items-center gap-2 px-4 py-2.5 text-white sidebar-bg hover:bg-white/10 rounded-lg">
          <TagIcon class="w-5 h-5" />
          <span>Categorias</span>
        </router-link>

        <!-- PDV -->
        <router-link to="/pdv" class="flex items-center gap-2 px-4 py-2.5 text-white sidebar-bg hover:bg-white/10 rounded-lg">
          <ShoppingCartIcon class="w-5 h-5" />
          <span>PDV</span>
        </router-link>

        <!-- Histórico de Vendas -->
        <router-link to="/sales-history" class="flex items-center gap-2 px-4 py-2.5 text-white sidebar-bg hover:bg-white/10 rounded-lg">
          <DocumentTextIcon class="w-5 h-5" />
          <span>Histórico de Vendas</span>
        </router-link>

        <!-- Estoque -->
        <div class="sidebar-bg">
          <button @click="toggleEstoque" class="w-full flex items-center justify-between px-4 py-2.5 text-white sidebar-bg hover:bg-white/10 rounded-lg">
            <div class="flex items-center gap-2">
              <ArchiveBoxIcon class="w-5 h-5" />
              <span>Estoque</span>
            </div>
            <ChevronDownIcon class="w-4 h-4 transition-transform duration-200 text-white"
              :class="{ 'rotate-180': estoqueOpen }" />
          </button>
          <div v-show="estoqueOpen" class="submenu pl-4 mt-1">
            <router-link to="/inventory/entries" class="flex items-center gap-2 px-4 py-2.5 text-white submenu-item hover:bg-white/10 rounded-lg">
              <DocumentPlusIcon class="w-5 h-5" />
              <span>Lançamentos</span>
            </router-link>
          </div>
        </div>

        <!-- Logística -->
        <div class="sidebar-bg">
          <button @click="toggleLogistica" class="w-full flex items-center justify-between px-4 py-2.5 text-white sidebar-bg hover:bg-white/10 rounded-lg">
            <div class="flex items-center gap-2">
              <TruckIcon class="w-5 h-5" />
              <span>Logística</span>
            </div>
            <ChevronDownIcon class="w-4 h-4 transition-transform duration-200 text-white"
              :class="{ 'rotate-180': logisticaOpen }" />
          </button>
          <div v-show="logisticaOpen" class="submenu pl-4 mt-1">
            <router-link to="/logistics/deliveries" class="flex items-center gap-2 px-4 py-2.5 text-white submenu-item hover:bg-white/10 rounded-lg">
              <ClipboardDocumentListIcon class="w-5 h-5" />
              <span>Entregas</span>
            </router-link>
            <router-link to="/logistics/kanban" class="flex items-center gap-2 px-4 py-2.5 text-white submenu-item hover:bg-white/10 rounded-lg">
              <ViewColumnsIcon class="w-5 h-5" />
              <span>Kanban de Entregas</span>
            </router-link>
            <router-link to="/logistics/routes" class="flex items-center gap-2 px-4 py-2.5 text-white submenu-item hover:bg-white/10 rounded-lg">
              <MapIcon class="w-5 h-5" />
              <span>Rotas</span>
            </router-link>
          </div>
        </div>

        <!-- Financeiro -->
        <div class="sidebar-bg">
          <button @click="toggleFinanceiro" class="w-full flex items-center justify-between px-4 py-2.5 text-white sidebar-bg hover:bg-white/10 rounded-lg">
            <div class="flex items-center gap-2">
              <BanknotesIcon class="w-5 h-5" />
              <span>Financeiro</span>
            </div>
            <ChevronDownIcon class="w-4 h-4 transition-transform duration-200 text-white"
              :class="{ 'rotate-180': financeiroOpen }" />
          </button>
          <div v-show="financeiroOpen" class="submenu pl-4 mt-1">
            <router-link to="/financial/accounts" class="flex items-center gap-2 px-4 py-2.5 text-white submenu-item hover:bg-white/10 rounded-lg">
              <CreditCardIcon class="w-5 h-5" />
              <span>Contas</span>
            </router-link>
            <router-link to="/financial/bills" class="flex items-center gap-2 px-4 py-2.5 text-white submenu-item hover:bg-white/10 rounded-lg">
              <ReceiptPercentIcon class="w-5 h-5" />
              <span>Faturas</span>
            </router-link>
          </div>
        </div>

        <!-- Orçamentos -->
        <router-link to="/budgets" class="flex items-center gap-2 px-4 py-2.5 text-white sidebar-bg hover:bg-white/10 rounded-lg">
          <CalculatorIcon class="w-5 h-5" />
          <span>Orçamentos</span>
        </router-link>

        <!-- Relatórios -->
        <router-link to="/reports" class="flex items-center gap-2 px-4 py-2.5 text-white sidebar-bg hover:bg-white/10 rounded-lg">
          <ChartBarIcon class="w-5 h-5" />
          <span>Relatórios</span>
        </router-link>
      </div>
    </nav>

    <!-- User Profile -->
    <div class="p-4 border-t border-white/10 sidebar-bg">
      <router-link to="/profile" class="flex items-center space-x-3 text-white hover:bg-white/10 rounded-lg p-2">
        <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-white">
          AD
        </div>
        <div class="flex-1">
          <p class="text-sm font-medium">Administrador</p>
          <p class="text-xs text-white/60">Ver perfil</p>
        </div>
      </router-link>
    </div>
  </aside>
</template>

<script setup>
import { ref } from 'vue'
import {
  HomeIcon,
  UserGroupIcon,
  CubeIcon,
  TagIcon,
  DocumentTextIcon,
  ArchiveBoxIcon,
  TruckIcon,
  BanknotesIcon,
  CalculatorIcon,
  ChartBarIcon,
  ChevronDownIcon,
  DocumentPlusIcon,
  ShoppingCartIcon,
  ViewColumnsIcon,
  ClipboardDocumentListIcon,
  MapIcon,
  CreditCardIcon,
  ReceiptPercentIcon
} from '@heroicons/vue/24/outline'

const estoqueOpen = ref(false)
const logisticaOpen = ref(false)
const financeiroOpen = ref(false)

const toggleEstoque = () => {
  estoqueOpen.value = !estoqueOpen.value
}

const toggleLogistica = () => {
  logisticaOpen.value = !logisticaOpen.value
}

const toggleFinanceiro = () => {
  financeiroOpen.value = !financeiroOpen.value
}
</script>

<style scoped>
.sidebar-bg {
  background-color: #4338CA !important;
}
.submenu {
  background-color: #4338CA !important;
}
.submenu-item {
  background-color: #4338CA !important;
}

/* Active link styles */
.router-link-active {
  background-color: rgba(255, 255, 255, 0.1);
}
</style>
