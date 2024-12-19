import { createRouter, createWebHistory } from 'vue-router'
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
import Products from '../views/Products.vue'
import SalesList from '../views/SalesList.vue'
import Profile from '../views/Profile.vue'
import OrderKanban from '../views/OrderKanban.vue'
import DefaultLayout from '../layouts/DefaultLayout.vue'

// Logistics Views
import Deliveries from '../views/Logistics/Deliveries.vue'
import Routes from '../views/Logistics/Routes.vue'

// Inventory Views
import StockEntries from '../views/Inventory/StockEntries.vue'

// Financial Views
import Accounts from '../views/Financial/Accounts.vue'
import Bills from '../views/Financial/Bills.vue'

// Reports View
import Reports from '../views/Reports.vue'

// Budgets View
import Budgets from '../views/Budgets.vue'

const routes = [
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
    path: '/',
    component: DefaultLayout,
    children: [
      {
        path: '',
        redirect: '/dashboard'
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
        path: '/pdv',
        name: 'PDV',
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
        path: '/products',
        name: 'Products',
        component: Products,
        meta: { requiresAuth: true }
      },
      {
        path: '/profile',
        name: 'Profile',
        component: Profile,
        meta: { requiresAuth: true }
      },
      {
        path: '/logistics/kanban',
        name: 'OrderKanban',
        component: OrderKanban,
        meta: { requiresAuth: true }
      },
      {
        path: '/logistics/deliveries',
        name: 'Deliveries',
        component: Deliveries,
        meta: { requiresAuth: true }
      },
      {
        path: '/logistics/routes',
        name: 'Routes',
        component: Routes,
        meta: { requiresAuth: true }
      },
      {
        path: '/inventory/entries',
        name: 'StockEntries',
        component: StockEntries,
        meta: { requiresAuth: true }
      },
      {
        path: '/financial/accounts',
        name: 'Accounts',
        component: Accounts,
        meta: { requiresAuth: true }
      },
      {
        path: '/financial/bills',
        name: 'Bills',
        component: Bills,
        meta: { requiresAuth: true }
      },
      {
        path: '/reports',
        name: 'Reports',
        component: Reports,
        meta: { requiresAuth: true }
      },
      {
        path: '/budgets',
        name: 'Budgets',
        component: Budgets,
        meta: { requiresAuth: true }
      }
    ]
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Navigation guard
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()
  const isAuthenticated = authStore.isAuthenticated

  if (to.meta.requiresAuth && !isAuthenticated) {
    next('/login')
  } else if (to.meta.requiresGuest && isAuthenticated) {
    next('/')
  } else {
    next()
  }
})

export default router
