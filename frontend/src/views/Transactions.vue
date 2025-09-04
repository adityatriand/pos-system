<template>
  <div class="px-4 py-6 sm:px-0">
    <h1 class="text-2xl font-semibold text-gray-900 mb-6">New Transaction</h1>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Transaction Form -->
      <div class="card">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Transaction Details</h2>
        <form @submit.prevent="createTransaction">
          <div class="mb-4">
            <label class="form-label">Branch *</label>
            <select v-model="form.branch_id" :class="[
              'form-input',
              hasFieldError('branch_id') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : ''
            ]" @change="fetchEmployeesAndItems">
              <option value="">Select Branch</option>
              <option v-for="branch in branches" :key="branch.id" :value="branch.id">
                {{ branch.name }}
              </option>
            </select>
            <FormError :errors="getFieldErrors('branch_id')" />
          </div>

          <div class="mb-4">
            <label class="form-label">Employee *</label>
            <select v-model="form.employee_id" :class="[
              'form-input',
              hasFieldError('employee_id') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : ''
            ]">
              <option value="">Select Employee</option>
              <option v-for="employee in employees" :key="employee.id" :value="employee.id">
                {{ employee.name }} - {{ employee.position }}
              </option>
            </select>
            <FormError :errors="getFieldErrors('employee_id')" />
          </div>

          <div class="mb-4">
            <label class="form-label">Payment Method *</label>
            <select v-model="form.payment_method" :class="[
              'form-input',
              hasFieldError('payment_method') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : ''
            ]">
              <option value="">Select Payment Method</option>
              <option value="cash">Cash</option>
              <option value="card">Card</option>
              <option value="digital_wallet">Digital Wallet</option>
            </select>
            <FormError :errors="getFieldErrors('payment_method')" />
          </div>

          <div class="mb-4">
            <label class="form-label">Notes</label>
            <textarea v-model="form.notes" class="form-input" rows="3"></textarea>
          </div>
        </form>
      </div>

      <!-- Items Selection -->
      <div class="card">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Select Items</h2>
        <div class="mb-4">
          <input v-model="itemSearch" type="text" placeholder="Search items..." class="form-input" />
        </div>
        <div class="max-h-96 overflow-y-auto">
          <div v-for="item in filteredItems" :key="item.id"
            class="flex items-center justify-between p-3 border rounded-lg mb-2 hover:bg-gray-50">
            <div class="flex-1">
              <div class="font-medium text-gray-900">{{ item.name }}</div>
              <div class="text-sm text-gray-500">
                Rp {{ formatCurrency(Number(item.price)) }} - Stock: {{ item.stock_quantity }}
              </div>
            </div>
            <button @click="addItemToTransaction(item)" class="btn-primary text-sm" :disabled="!item.is_available">
              Add
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Transaction Items -->
    <div class="mt-6 card">
      <h2 class="text-lg font-medium text-gray-900 mb-4">Transaction Items</h2>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="(transactionItem, index) in transactionItems" :key="index">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                {{ transactionItem.name }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                Rp {{ formatCurrency(transactionItem.unit_price) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <input v-model="transactionItem.quantity" type="number" min="1" class="w-20 form-input text-sm"
                  @input="updateItemTotal(index)" />
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                Rp {{ formatCurrency(transactionItem.total_price) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <button @click="removeItem(index)" class="text-red-600 hover:text-red-900">
                  Remove
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Transaction Summary -->
      <div class="mt-6 border-t pt-6">
        <div class="flex justify-end">
          <div class="w-64">
            <div class="flex justify-between mb-2">
              <span class="text-sm text-gray-600">Subtotal:</span>
              <span class="text-sm font-medium">Rp {{ formatCurrency(subtotal) }}</span>
            </div>
            <div class="flex justify-between mb-2">
              <span class="text-sm text-gray-600">Service Charge (5%):</span>
              <span class="text-sm font-medium">Rp {{ formatCurrency(serviceCharge) }}</span>
            </div>
            <div class="flex justify-between text-lg font-bold border-t pt-2">
              <span>Total:</span>
              <span>Rp {{ formatCurrency(total) }}</span>
            </div>
          </div>
        </div>

        <div class="flex justify-end mt-6 space-x-3">
          <button @click="resetTransaction" class="btn-secondary">Reset</button>
          <button @click="createTransaction" :disabled="isSubmitting || transactionItems.length === 0" :class="[
            'btn-success',
            (isSubmitting || transactionItems.length === 0) ? 'opacity-50 cursor-not-allowed' : ''
          ]">
            {{ isSubmitting ? 'Processing...' : 'Complete Transaction' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { branchesApi, transactionsApi } from '@/api'
import FormError from '@/components/form/FormError.vue'
import { useToast } from '@/composables/useToast'
import { useValidation } from '@/composables/useValidation'
import type { Branch, CreateTransactionRequest, Employee, Item } from '@/types'
import { computed, onMounted, reactive, ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const { validate, errors, clearErrors, getFieldErrors, hasFieldError } = useValidation()
const { success, error, warning } = useToast()

const branches = ref<Branch[]>([])
const employees = ref<Employee[]>([])
const items = ref<Item[]>([])
const itemSearch = ref('')
const isSubmitting = ref(false)
const transactionItems = ref<Array<{
  item_id: number
  name: string
  unit_price: number
  quantity: number
  total_price: number
}>>([])

const form = reactive({
  branch_id: '',
  employee_id: '',
  payment_method: '',
  notes: '',
})

const validationRules = {
  branch_id: { required: true },
  employee_id: { required: true },
  payment_method: { required: true },
}

const filteredItems = computed(() => {
  return items.value.filter(item =>
    item.name.toLowerCase().includes(itemSearch.value.toLowerCase()) &&
    item.is_available
  )
})

const subtotal = computed(() => {
  const result = transactionItems.value.reduce((sum, item) => {
    const itemTotal = isNaN(item.total_price) ? 0 : item.total_price
    return sum + itemTotal
  }, 0)
  return isNaN(result) ? 0 : result
})

const serviceCharge = computed(() => {
  const result = Math.round(subtotal.value * 0.05 * 100) / 100
  return isNaN(result) ? 0 : result
})

const total = computed(() => {
  const result = subtotal.value + serviceCharge.value
  return isNaN(result) ? 0 : result
})

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

const fetchEmployeesAndItems = async () => {
  if (!form.branch_id) {
    employees.value = []
    items.value = []
    return
  }

  try {
    // Fetch employees for the selected branch
    const employeesResponse = await branchesApi.getEmployees(Number(form.branch_id))
    employees.value = employeesResponse.data.filter(employee => employee.is_active)

    // Fetch items available at the selected branch
    const itemsResponse = await branchesApi.getItems(Number(form.branch_id))
    items.value = itemsResponse.data.filter((item: Item) => item.is_available && item.stock_quantity > 0)
  } catch (err) {
    console.error('Error fetching employees and items:', err)
    error('Error', 'Failed to load employees and items')
  }
}


const addItemToTransaction = (item: Item) => {
  const existingIndex = transactionItems.value.findIndex(ti => ti.item_id === item.id)

  if (existingIndex >= 0) {
    transactionItems.value[existingIndex].quantity++
    updateItemTotal(existingIndex)
  } else {
    const unitPrice = Number(item.price) || 0
    transactionItems.value.push({
      item_id: item.id,
      name: item.name,
      unit_price: unitPrice,
      quantity: 1,
      total_price: unitPrice,
    })
  }
}

const updateItemTotal = (index: number) => {
  const item = transactionItems.value[index]
  const quantity = Number(item.quantity) || 0
  const unitPrice = Number(item.unit_price) || 0
  item.total_price = Math.round(quantity * unitPrice * 100) / 100
}

const removeItem = (index: number) => {
  transactionItems.value.splice(index, 1)
}

const resetTransaction = () => {
  Object.assign(form, {
    branch_id: '',
    employee_id: '',
    payment_method: '',
    notes: '',
  })
  transactionItems.value = []
  employees.value = []
  items.value = []
}

const createTransaction = async () => {
  // Validate form
  if (!validate(form, validationRules)) {
    error('Missing Info', 'Please fill required fields')
    return
  }

  // Check if transaction has items
  if (transactionItems.value.length === 0) {
    warning('No Items', 'Add items to continue')
    return
  }

  isSubmitting.value = true
  try {
    const transactionData: CreateTransactionRequest = {
      branch_id: Number(form.branch_id),
      employee_id: Number(form.employee_id),
      payment_method: form.payment_method as any,
      notes: form.notes,
      items: transactionItems.value.map(item => ({
        item_id: item.item_id,
        quantity: item.quantity,
        unit_price: item.unit_price,
      })),
    }

    await transactionsApi.create(transactionData)
    success('Success', 'Transaction completed successfully!')
    resetTransaction()
    router.push('/sales-history')
  } catch (err) {
    console.error('Error creating transaction:', err)
    error('Error', 'Failed to create transaction. Please try again.')
  } finally {
    isSubmitting.value = false
  }
}

onMounted(() => {
  fetchBranches()
})
</script>