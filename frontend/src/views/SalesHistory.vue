<template>
  <div class="px-4 py-6 sm:px-0">
    <div class="sm:flex sm:items-center">
      <div class="sm:flex-auto">
        <h1 class="text-2xl font-semibold text-gray-900">Sales History</h1>
        <p class="mt-2 text-sm text-gray-700">View and filter transaction history.</p>
      </div>
      <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
        <button @click="exportPDF" class="btn-primary" :disabled="transactions.length === 0">
          Export PDF
        </button>
      </div>
    </div>

    <!-- Filters -->
    <div class="mt-6 bg-white rounded-lg p-4 shadow">
      <h2 class="text-lg font-medium text-gray-900 mb-3">Filters</h2>
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="form-label">Branch</label>
          <select v-model="filters.branch_id" class="form-input" @change="applyFilters">
            <option value="">All Branches</option>
            <option v-for="branch in branches" :key="branch.id" :value="branch.id">
              {{ branch.name }}
            </option>
          </select>
        </div>
        <div>
          <label class="form-label">Start Date</label>
          <input
            v-model="filters.start_date"
            type="date"
            class="form-input"
            @change="applyFilters"
          />
        </div>
        <div>
          <label class="form-label">End Date</label>
          <input
            v-model="filters.end_date"
            type="date"
            class="form-input"
            @change="applyFilters"
          />
        </div>
        <div class="flex items-end">
          <button @click="resetFilters" class="btn-secondary w-full">
            Reset Filters
          </button>
        </div>
      </div>
    </div>

    <!-- Transactions Table -->
    <div class="mt-8 flow-root">
      <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
          <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
            <table class="min-w-full divide-y divide-gray-300">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    Transaction #
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    Date
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    Branch
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    Employee
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    Items
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    Total
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    Payment
                  </th>
                  <th class="relative px-6 py-3">
                    <span class="sr-only">Actions</span>
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="transaction in transactions" :key="transaction.id">
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    {{ transaction.transaction_number }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ formatDate(transaction.transaction_date) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ transaction.branch?.name }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ transaction.employee?.name }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ transaction.transaction_items?.length || 0 }} items
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    <div>Rp {{ formatCurrency(transaction.total_amount) }}</div>
                    <div class="text-xs text-gray-500">
                      Service: Rp {{ formatCurrency(transaction.service_charge) }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                      {{ formatPaymentMethod(transaction.payment_method) }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <button
                      @click="viewTransactionDetails(transaction)"
                      class="text-indigo-600 hover:text-indigo-900"
                    >
                      View Details
                    </button>
                  </td>
                </tr>
                <tr v-if="transactions.length === 0">
                  <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                    No transactions found
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Transaction Details Modal -->
    <div
      v-if="selectedTransaction"
      class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
      @click="selectedTransaction = null"
    >
      <div
        class="relative top-20 mx-auto p-5 border w-4/5 max-w-4xl shadow-lg rounded-md bg-white"
        @click.stop
      >
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-bold text-gray-900">
            Transaction Details - {{ selectedTransaction.transaction_number }}
          </h3>
          <button
            @click="selectedTransaction = null"
            class="text-gray-400 hover:text-gray-600"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
          <div>
            <h4 class="font-medium text-gray-900 mb-2">Transaction Info</h4>
            <div class="text-sm text-gray-600 space-y-1">
              <div><strong>Date:</strong> {{ formatDate(selectedTransaction.transaction_date) }}</div>
              <div><strong>Branch:</strong> {{ selectedTransaction.branch?.name }}</div>
              <div><strong>Employee:</strong> {{ selectedTransaction.employee?.name }}</div>
              <div><strong>Payment:</strong> {{ formatPaymentMethod(selectedTransaction.payment_method) }}</div>
            </div>
          </div>
          <div>
            <h4 class="font-medium text-gray-900 mb-2">Summary</h4>
            <div class="text-sm text-gray-600 space-y-1">
              <div><strong>Subtotal:</strong> Rp {{ formatCurrency(selectedTransaction.subtotal) }}</div>
              <div><strong>Service Charge:</strong> Rp {{ formatCurrency(selectedTransaction.service_charge) }}</div>
              <div><strong>Total:</strong> Rp {{ formatCurrency(selectedTransaction.total_amount) }}</div>
            </div>
          </div>
        </div>

        <div>
          <h4 class="font-medium text-gray-900 mb-2">Items</h4>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                  <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                  <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                  <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="item in selectedTransaction.transaction_items" :key="item.id">
                  <td class="px-4 py-2 text-sm text-gray-900">{{ item.item?.name }}</td>
                  <td class="px-4 py-2 text-sm text-gray-500">Rp {{ formatCurrency(item.unit_price) }}</td>
                  <td class="px-4 py-2 text-sm text-gray-500">{{ item.quantity }}</td>
                  <td class="px-4 py-2 text-sm text-gray-900">Rp {{ formatCurrency(item.total_price) }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { transactionsApi, branchesApi } from '@/api'
import type { Transaction, Branch } from '@/types'

const transactions = ref<Transaction[]>([])
const branches = ref<Branch[]>([])
const selectedTransaction = ref<Transaction | null>(null)

const filters = reactive({
  branch_id: '',
  start_date: '',
  end_date: '',
})

const formatCurrency = (amount: number | string): string => {
  const numAmount = Number(amount) || 0
  if (isNaN(numAmount)) {
    return '0'
  }
  return new Intl.NumberFormat('id-ID').format(numAmount)
}

const formatDate = (dateString: string): string => {
  return new Date(dateString).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

const formatPaymentMethod = (method: string): string => {
  return method.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())
}

const fetchTransactions = async () => {
  try {
    const response = await transactionsApi.getAll()
    transactions.value = response.data
  } catch (error) {
    console.error('Error fetching transactions:', error)
  }
}

const fetchBranches = async () => {
  try {
    const response = await branchesApi.getAll()
    branches.value = response.data.filter(branch => branch.is_active)
  } catch (error) {
    console.error('Error fetching branches:', error)
  }
}

const applyFilters = async () => {
  try {
    const filterParams = {
      branch_id: filters.branch_id ? Number(filters.branch_id) : undefined,
      start_date: filters.start_date || undefined,
      end_date: filters.end_date || undefined,
    }
    
    const response = await transactionsApi.filter(filterParams)
    transactions.value = response.data
  } catch (error) {
    console.error('Error applying filters:', error)
  }
}

const resetFilters = () => {
  Object.assign(filters, {
    branch_id: '',
    start_date: '',
    end_date: '',
  })
  fetchTransactions()
}

const viewTransactionDetails = (transaction: Transaction) => {
  selectedTransaction.value = transaction
}

const exportPDF = async () => {
  try {
    const filterParams = {
      branch_id: filters.branch_id ? Number(filters.branch_id) : undefined,
      start_date: filters.start_date || undefined,
      end_date: filters.end_date || undefined,
    }
    
    const response = await transactionsApi.exportPdf(filterParams)
    
    // Create blob link to download
    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `sales-history-${new Date().toISOString().split('T')[0]}.pdf`)
    document.body.appendChild(link)
    link.click()
    link.remove()
    window.URL.revokeObjectURL(url)
  } catch (error) {
    console.error('Error exporting PDF:', error)
    alert('Error exporting PDF. Please try again.')
  }
}

onMounted(() => {
  fetchTransactions()
  fetchBranches()
})
</script>