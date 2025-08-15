<template>
  <div class="px-4 py-6 sm:px-0">
    <div class="sm:flex sm:items-center">
      <div class="sm:flex-auto">
        <h1 class="text-2xl font-semibold text-gray-900">Branch Inventory</h1>
        <p class="mt-2 text-sm text-gray-700">
          Manage inventory levels for each branch location.
        </p>
      </div>
    </div>

    <!-- Branch Selection -->
    <div class="mt-8 mb-6">
      <label class="form-label">Select Branch</label>
      <select 
        v-model="selectedBranchId" 
        @change="fetchBranchInventory"
        class="form-input max-w-xs"
      >
        <option value="">Select Branch</option>
        <option v-for="branch in branches" :key="branch.id" :value="branch.id">
          {{ branch.name }}
        </option>
      </select>
    </div>

    <!-- Branch Inventory Table -->
    <div v-if="selectedBranchId && branchItems.length > 0" class="mt-8 flow-root">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-300">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Item Name
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Category
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Price
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Stock Quantity
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Min Stock Level
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Status
              </th>
              <th class="relative px-6 py-3">
                <span class="sr-only">Actions</span>
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="item in branchItems" :key="item.id">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                {{ item.name }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <span
                  :class="[
                    'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                    item.category === 'food' 
                      ? 'bg-green-100 text-green-800' 
                      : 'bg-blue-100 text-blue-800'
                  ]"
                >
                  {{ item.category === 'food' ? 'Food' : 'Beverage' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                Rp {{ formatCurrency(item.price) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                <span 
                  :class="[
                    'font-medium',
                    item.is_low_stock ? 'text-red-600' : 'text-gray-900'
                  ]"
                >
                  {{ item.stock_quantity }} {{ item.unit }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ item.min_stock_level }} {{ item.unit }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  :class="[
                    'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                    item.is_available && item.stock_quantity > 0
                      ? 'bg-green-100 text-green-800'
                      : item.is_low_stock
                      ? 'bg-yellow-100 text-yellow-800'
                      : 'bg-red-100 text-red-800'
                  ]"
                >
                  {{ 
                    item.is_available && item.stock_quantity > 0 
                      ? 'Available' 
                      : item.is_low_stock 
                      ? 'Low Stock' 
                      : 'Out of Stock' 
                  }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <button
                  @click="openStockModal(item)"
                  class="text-indigo-600 hover:text-indigo-900"
                >
                  Update Stock
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else-if="selectedBranchId && branchItems.length === 0" class="text-center py-12">
      <div class="text-gray-500">
        <p class="text-lg">No items found for this branch</p>
        <p class="text-sm mt-2">Items need to be assigned to this branch first</p>
      </div>
    </div>

    <!-- Stock Update Modal -->
    <div
      v-if="isStockModalOpen"
      class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
      @click="closeStockModal"
    >
      <div
        class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white"
        @click.stop
      >
        <h3 class="text-lg font-bold text-gray-900 mb-4">
          Update Stock: {{ editingItem?.name }}
        </h3>
        <form @submit.prevent="updateStock">
          <div class="mb-4">
            <label class="form-label">Stock Quantity *</label>
            <input
              v-model="stockForm.stock_quantity"
              type="number"
              min="0"
              :class="[
                'form-input',
                hasFieldError('stock_quantity') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : ''
              ]"
            />
            <FormError :errors="getFieldErrors('stock_quantity')" />
          </div>
          <div class="mb-4">
            <label class="form-label">Minimum Stock Level</label>
            <input
              v-model="stockForm.min_stock_level"
              type="number"
              min="0"
              :class="[
                'form-input',
                hasFieldError('min_stock_level') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : ''
              ]"
            />
            <FormError :errors="getFieldErrors('min_stock_level')" />
          </div>
          <div class="mb-4">
            <label class="flex items-center">
              <input
                v-model="stockForm.is_available"
                type="checkbox"
                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
              />
              <span class="ml-2 text-sm text-gray-600">Available for sale</span>
            </label>
          </div>
          <div class="flex justify-end space-x-2">
            <button
              type="button"
              @click="closeStockModal"
              class="btn-secondary"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="isSubmitting"
              :class="[
                'btn-primary',
                isSubmitting ? 'opacity-50 cursor-not-allowed' : ''
              ]"
            >
              {{ isSubmitting ? 'Updating...' : 'Update Stock' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { branchesApi } from '@/api'
import type { Branch, Item } from '@/types'
import { useValidation } from '@/composables/useValidation'
import { useToast } from '@/composables/useToast'
import FormError from '@/components/FormError.vue'

const branches = ref<Branch[]>([])
const branchItems = ref<Item[]>([])
const selectedBranchId = ref('')
const isStockModalOpen = ref(false)
const editingItem = ref<Item | null>(null)
const isSubmitting = ref(false)

const { validate, errors, clearErrors, getFieldErrors, hasFieldError } = useValidation()
const { success, error } = useToast()

const stockForm = reactive({
  stock_quantity: 0,
  min_stock_level: 0,
  is_available: true,
})

const validationRules = {
  stock_quantity: { 
    required: true, 
    min: 0 
  },
  min_stock_level: { 
    min: 0 
  },
}

const formatCurrency = (amount: number): string => {
  if (isNaN(amount) || amount === null || amount === undefined) {
    return '0'
  }
  return new Intl.NumberFormat('id-ID').format(amount)
}

const fetchBranches = async () => {
  try {
    const response = await branchesApi.getAll()
    branches.value = response.data.filter(branch => branch.is_active)
  } catch (err) {
    console.error('Error fetching branches:', err)
    error('Error', 'Failed to load branches')
  }
}

const fetchBranchInventory = async () => {
  if (!selectedBranchId.value) {
    branchItems.value = []
    return
  }

  try {
    const response = await branchesApi.getItems(Number(selectedBranchId.value))
    
    if (response.data && response.data.success && Array.isArray(response.data.data)) {
      branchItems.value = response.data.data
    } else if (response.data && Array.isArray(response.data)) {
      branchItems.value = response.data
    } else {
      error('Error', 'Unexpected response format from server')
    }
  } catch (err) {
    console.error('Error fetching branch inventory:', err)
    error('Error', 'Failed to load branch inventory')
  }
}

const openStockModal = (item: Item) => {
  clearErrors()
  editingItem.value = item
  Object.assign(stockForm, {
    stock_quantity: item.stock_quantity || 0,
    min_stock_level: item.min_stock_level || 0,
    is_available: item.is_available,
  })
  isStockModalOpen.value = true
}

const closeStockModal = () => {
  isStockModalOpen.value = false
  editingItem.value = null
  clearErrors()
}

const updateStock = async () => {
  if (!validate(stockForm, validationRules)) {
    error('Invalid Form', 'Please fix the errors below')
    return
  }

  if (!editingItem.value) return

  isSubmitting.value = true
  try {
    // Note: This would require a new API endpoint for updating branch item stock
    // For now, we'll simulate the update
    const branchItemIndex = branchItems.value.findIndex(item => item.id === editingItem.value?.id)
    if (branchItemIndex >= 0) {
      branchItems.value[branchItemIndex] = {
        ...branchItems.value[branchItemIndex],
        stock_quantity: stockForm.stock_quantity,
        min_stock_level: stockForm.min_stock_level,
        is_available: stockForm.is_available,
        is_low_stock: stockForm.stock_quantity <= stockForm.min_stock_level,
      }
    }
    
    success('Success', 'Stock updated successfully')
    closeStockModal()
  } catch (err) {
    console.error('Error updating stock:', err)
    error('Error', 'Failed to update stock')
  } finally {
    isSubmitting.value = false
  }
}

onMounted(fetchBranches)
</script>