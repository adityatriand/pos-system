<template>
  <div class="px-4 py-6 sm:px-0">
    <div class="sm:flex sm:items-center">
      <div class="sm:flex-auto">
        <h1 class="text-2xl font-semibold text-gray-900">Items</h1>
        <p class="mt-2 text-sm text-gray-700">Manage food and beverage items.</p>
      </div>
      <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
        <button @click="openModal()" class="btn-primary">Add Item</button>
      </div>
    </div>

    <div class="mt-8 flow-root">
      <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
          <table class="min-w-full divide-y divide-gray-300 bg-white shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="item in items" :key="item.id">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-medium text-gray-900">{{ item.name }}</div>
                  <div class="text-sm text-gray-500">{{ item.description }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="[
                    'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                    item.category === 'food' ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800'
                  ]">
                    {{ item.category }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  Rp {{ formatCurrency(item.price) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ item.stock_quantity }} {{ item.unit }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="[
                    'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                    item.is_available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                  ]">
                    {{ item.is_available ? 'Available' : 'Unavailable' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <button @click="openModal(item)" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                  <button @click="deleteItem(item.id)" class="text-red-600 hover:text-red-900">Delete</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Item Modal -->
    <div v-if="isModalOpen" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click="closeModal">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white" @click.stop>
        <h3 class="text-lg font-bold text-gray-900 mb-4">{{ editingItem ? 'Edit Item' : 'Add Item' }}</h3>
        <form @submit.prevent="saveItem">
          <div class="mb-4">
            <label class="form-label">Name</label>
            <input v-model="form.name" type="text" class="form-input" required />
          </div>
          <div class="mb-4">
            <label class="form-label">Description</label>
            <textarea v-model="form.description" class="form-input" rows="2"></textarea>
          </div>
          <div class="mb-4">
            <label class="form-label">Category</label>
            <select v-model="form.category" class="form-input" required>
              <option value="">Select Category</option>
              <option value="food">Food</option>
              <option value="beverage">Beverage</option>
            </select>
          </div>
          <div class="mb-4">
            <label class="form-label">Price</label>
            <input v-model="form.price" type="number" step="0.01" class="form-input" required />
          </div>
          <div class="mb-4">
            <label class="form-label">Stock Quantity</label>
            <input v-model="form.stock_quantity" type="number" class="form-input" required />
          </div>
          <div class="mb-4">
            <label class="form-label">Unit</label>
            <input v-model="form.unit" type="text" class="form-input" required />
          </div>
          <div class="mb-4">
            <label class="flex items-center">
              <input v-model="form.is_available" type="checkbox" class="rounded border-gray-300" />
              <span class="ml-2 text-sm text-gray-600">Available</span>
            </label>
          </div>
          <div class="flex justify-end space-x-2">
            <button type="button" @click="closeModal" class="btn-secondary">Cancel</button>
            <button type="submit" class="btn-primary">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { itemsApi } from '@/api'
import type { Item } from '@/types'

const items = ref<Item[]>([])
const isModalOpen = ref(false)
const editingItem = ref<Item | null>(null)

const form = reactive({
  name: '',
  description: '',
  category: '',
  price: '',
  stock_quantity: '',
  unit: '',
  is_available: true,
})

const formatCurrency = (amount: number | string): string => {
  const numAmount = Number(amount) || 0
  if (isNaN(numAmount)) {
    return '0'
  }
  return new Intl.NumberFormat('id-ID').format(numAmount)
}

const fetchItems = async () => {
  try {
    const response = await itemsApi.getAll()
    items.value = response.data
  } catch (error) {
    console.error('Error fetching items:', error)
  }
}

const openModal = (item?: Item) => {
  if (item) {
    editingItem.value = item
    Object.assign(form, {
      name: item.name,
      description: item.description || '',
      category: item.category,
      price: item.price,
      stock_quantity: item.stock_quantity,
      unit: item.unit,
      is_available: item.is_available,
    })
  } else {
    editingItem.value = null
    Object.assign(form, {
      name: '',
      description: '',
      category: '',
      price: '',
      stock_quantity: '',
      unit: '',
      is_available: true,
    })
  }
  isModalOpen.value = true
}

const closeModal = () => {
  isModalOpen.value = false
  editingItem.value = null
}

const saveItem = async () => {
  try {
    if (editingItem.value) {
      await itemsApi.update(editingItem.value.id, form)
    } else {
      await itemsApi.create(form)
    }
    closeModal()
    fetchItems()
  } catch (error) {
    console.error('Error saving item:', error)
  }
}

const deleteItem = async (id: number) => {
  if (confirm('Are you sure you want to delete this item?')) {
    try {
      await itemsApi.delete(id)
      fetchItems()
    } catch (error) {
      console.error('Error deleting item:', error)
    }
  }
}

onMounted(fetchItems)
</script>