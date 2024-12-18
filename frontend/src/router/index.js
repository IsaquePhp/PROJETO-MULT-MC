import { createRouter, createWebHashHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'

// Views
import Login from '../views/Login.vue'
import Register from '../views/Register.vue'
import ResetPassword from '../views/ResetPassword.vue'
import Dashboard from '../views/Dashboard.vue'
import Customers from '../views/Customers.vue'
import Categories from '../views/Categories.vue'
import Clients from '../views/Clients.vue'
import Sales from '../views/Sales.vue'
import Inventory from '../views/Inventory.vue'
import Deliveries from '../views/Logistics/Deliveries.vue'
import Returns from '../views/Logistics/Returns.vue'
import Pagamentos from '../views/Financial/Pagamentos.vue'
import FluxoCaixa from '../views/Financial/FluxoCaixa.vue'
import CentroCustos from '../views/Financial/CentroCustos.vue'
import Lancamentos from '../views/Financial/Lancamentos.vue'
import Budgets from '../views/Budgets.vue'
import Reports from '../views/Reports.vue'
import Products from '../views/Products.vue'
import SalesList from '../views/SalesList.vue'

const routes = [
  {
    path: '/',
    redirect: '/dashboard'
  },
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: { requiresGuest: true }
  },
  {
    path: '/register',
    name: 'Register',
    component: Register,
    meta: { requiresGuest: true }
  },
  {
    path: '/reset-password',
    name: 'ResetPassword',
    component: ResetPassword,
    meta: { requiresGuest: true }
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: Dashboard,
    meta: { requiresAuth: true }
  },
  {
    path: '/customers',
    name: 'Customers',
    component: Customers,
    meta: { requiresAuth: true }
  },
  {
    path: '/categories',
    name: 'Categories',
    component: Categories,
    meta: { requiresAuth: true }
  },
  {
    path: '/clients',
    name: 'Clients',
    component: Clients,
    meta: { requiresAuth: true }
  },
  {
    path: '/sales',
    name: 'Sales',
    component: Sales,
    meta: { requiresAuth: true }
  },
  {
    path: '/sales-history',
    name: 'SalesHistory',
    component: SalesList,
    meta: { requiresAuth: true }
  },
  {
    path: '/inventory',
    name: 'Inventory',
    component: Inventory,
    meta: { requiresAuth: true }
  },
  {
    path: '/lancamentos',
    name: 'Lancamentos',
    component: Lancamentos,
    meta: { requiresAuth: true }
  },
  {
    path: '/financial',
    name: 'Financial',
    meta: { requiresAuth: true },
    children: [
      {
        path: 'pagamentos',
        name: 'Pagamentos',
        component: Pagamentos
      },
      {
        path: 'fluxo-caixa',
        name: 'FluxoCaixa',
        component: FluxoCaixa
      },
      {
        path: 'centro-custos',
        name: 'CentroCustos',
        component: CentroCustos
      }
    ]
  },
  {
    path: '/logistics',
    name: 'Logistics',
    meta: { requiresAuth: true },
    children: [
      {
        path: 'entregas',
        name: 'deliveries',
        component: () => import('../views/Logistics/Deliveries.vue')
      },
      {
        path: 'devolucoes',
        name: 'returns',
        component: () => import('../views/Logistics/Returns.vue')
      }
    ]
  },
  {
    path: '/budgets',
    name: 'Budgets',
    component: Budgets,
    meta: { requiresAuth: true }
  },
  {
    path: '/reports',
    name: 'Reports',
    component: Reports,
    meta: { requiresAuth: true }
  },
  {
    path: '/products',
    name: 'Products',
    component: () => import('../views/Products.vue'),
    meta: { requiresAuth: true }
  }
]

const router = createRouter({
  history: createWebHashHistory(),
  routes
})

router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore()
  const requiresAuth = to.matched.some(record => record.meta.requiresAuth)
  const requiresGuest = to.matched.some(record => record.meta.requiresGuest)

  // Se a rota não requer autenticação nem é para visitantes, permite
  if (!requiresAuth && !requiresGuest) {
    return next()
  }

  // Verifica autenticação
  const isAuthenticated = await authStore.checkAuth()

  // Redireciona com base nas regras de autenticação
  if (requiresAuth && !isAuthenticated) {
    return next('/login')
  } else if (requiresGuest && isAuthenticated) {
    return next('/dashboard')
  }

  next()
})

export default router
