import { createRouter, createWebHistory } from 'vue-router'
import Dashboard from '@/views/Dashboard.vue'
import Branches from '@/views/Branches.vue'
import Employees from '@/views/Employees.vue'
import Items from '@/views/Items.vue'
import Transactions from '@/views/Transactions.vue'
import SalesHistory from '@/views/SalesHistory.vue'
import BranchInventory from '@/views/BranchInventory.vue'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      name: 'Dashboard',
      component: Dashboard,
    },
    {
      path: '/branches',
      name: 'Branches',
      component: Branches,
    },
    {
      path: '/employees',
      name: 'Employees',
      component: Employees,
    },
    {
      path: '/items',
      name: 'Items',
      component: Items,
    },
    {
      path: '/transactions',
      name: 'Transactions',
      component: Transactions,
    },
    {
      path: '/sales-history',
      name: 'SalesHistory',
      component: SalesHistory,
    },
    {
      path: '/branch-inventory',
      name: 'BranchInventory',
      component: BranchInventory,
    },
  ],
})

export default router