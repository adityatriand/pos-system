<template>
  <div class="flex flex-row justify-between w-[100%] items-center">
    <h1 class="text-3xl font-bold text-gray-900 mb-4">Dashboard</h1>
    <DateRangePicker :initialStartDate="dateRange.start_date" :initialEndDate="dateRange.end_date"
      @update-date-range="updateDateRange" direction="row" />
  </div>

  <!-- Summary Cards -->
  <div class="grid grid-cols-1 gap-5 sm:grid-cols-3 mb-8">
    <div class="card">
      <dt class="text-sm font-medium text-gray-500 truncate">
        Total Revenue
      </dt>
      <dd class="mt-1 text-3xl font-semibold text-gray-900">
        Rp {{ formatCurrency(summary?.total_revenue || 0) }}
      </dd>
    </div>
    <div class="card">
      <dt class="text-sm font-medium text-gray-500 truncate">
        Service Charge
      </dt>
      <dd class="mt-1 text-3xl font-semibold text-gray-900">
        Rp {{ formatCurrency(summary?.total_service_charge || 0) }}
      </dd>
    </div>
    <div class="card">
      <dt class="text-sm font-medium text-gray-500 truncate">
        Total Transactions
      </dt>
      <dd class="mt-1 text-3xl font-semibold text-gray-900">
        {{ summary?.total_transactions || 0 }}
      </dd>
    </div>
  </div>

  <!-- Low Stock Alerts -->
  <div v-if="inventoryStatus?.low_stock_items?.length > 0" class="mb-8">
    <div class="card border-l-4 border-red-500">
      <div class="flex items-center mb-4">
        <div class="flex-shrink-0">
          <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd"
              d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
              clip-rule="evenodd" />
          </svg>
        </div>
        <h3 class="ml-2 text-lg font-medium text-red-900">Low Stock Alerts</h3>
      </div>
      <div class="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-3">
        <div v-for="item in inventoryStatus.low_stock_items" :key="`${item.branch_name}-${item.item_name}`"
          class="bg-red-50 p-3 rounded-md">
          <p class="text-sm font-medium text-red-900">{{ item.item_name }}</p>
          <p class="text-xs text-red-700">{{ item.branch_name }}</p>
          <p class="text-xs text-red-600">Stock: {{ item.stock_quantity }} (Min: {{ item.min_stock_level }})</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Branch Inventory Summary -->
  <div class="mb-8">
    <div class="card">
      <h3 class="text-lg font-medium text-gray-900 mb-4">Branch Inventory Summary</h3>
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div v-for="branch in inventoryStatus?.branch_inventory_summary" :key="branch.id"
          class="bg-gray-50 p-4 rounded-md">
          <h4 class="font-medium text-gray-900">{{ branch.name }}</h4>
          <div class="mt-2 space-y-1">
            <p class="text-sm text-gray-600">Items: {{ branch.total_items }}</p>
            <p class="text-sm text-gray-600">Total Stock: {{ branch.total_stock }}</p>
            <p :class="[
              'text-sm font-medium',
              branch.low_stock_items > 0 ? 'text-red-600' : 'text-green-600'
            ]">
              Low Stock: {{ branch.low_stock_items }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Top Items and Branch Revenue -->
  <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
    <!-- Top 5 Items -->
    <div class="card">
      <h3 class="text-lg font-medium text-gray-900 mb-4">Top 5 Items</h3>
      <div class="overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Item
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Sold
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Revenue
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="item in topItems" :key="item.id">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">{{ item.name }}</div>
                <div class="text-sm text-gray-500">{{ item.category }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ item.total_sold }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                Rp {{ formatCurrency(item.total_revenue) }}
              </td>
            </tr>
            <tr v-if="topItems.length === 0">
              <td colspan="3" class="px-6 py-4 text-center text-gray-500">
                No data available
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Branch Revenue -->
    <div class="card">
      <h3 class="text-lg font-medium text-gray-900 mb-4">Branch Revenue</h3>
      <div class="overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Branch
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Revenue
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="branch in branchRevenue" :key="branch.id">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">{{ branch.name }}</div>
                <div class="text-sm text-gray-500">{{ branch.total_transactions }} transactions</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                Rp {{ formatCurrency(branch.total_revenue) }}
              </td>
            </tr>
            <tr v-if="branchRevenue.length === 0">
              <td colspan="2" class="px-6 py-4 text-center text-gray-500">
                No data available
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { dashboardApi } from '@/api'
import DateRangePicker from '@/components/date/DateRangePicker.vue'
import type { BranchRevenue, DashboardSummary, TopItem } from '@/types'
import { onMounted, reactive, ref } from 'vue'

const summary = ref<DashboardSummary>()
const topItems = ref<TopItem[]>([])
const branchRevenue = ref<BranchRevenue[]>([])
const inventoryStatus = ref<any>()

const dateRange = reactive({
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

const updateDateRange = (newRange: { start_date: string; end_date: string }) => {
  dateRange.start_date = newRange.start_date
  dateRange.end_date = newRange.end_date
  fetchData()
}

const resetDateRange = () => {
  const today = new Date().toISOString().split('T')[0]
  dateRange.start_date = today
  dateRange.end_date = today
  fetchData()
}

const fetchData = async () => {
  try {
    const params = {
      start_date: dateRange.start_date || undefined,
      end_date: dateRange.end_date || undefined,
    }

    const [summaryRes, topItemsRes, branchRevenueRes, inventoryRes] = await Promise.all([
      dashboardApi.getSummary(params),
      dashboardApi.getTopItems({ ...params, limit: 5 }),
      dashboardApi.getBranchRevenue(params),
      dashboardApi.getInventoryStatus(),
    ])

    summary.value = summaryRes.data
    topItems.value = topItemsRes.data
    branchRevenue.value = branchRevenueRes.data
    inventoryStatus.value = inventoryRes.data
  } catch (error) {
    console.error('Error fetching dashboard data:', error)
  }
}

onMounted(() => {
  resetDateRange()
})
</script>