<template>
  <div class="px-4 py-6 sm:px-0">
    <div class="sm:flex sm:items-center">
      <div class="sm:flex-auto">
        <h1 class="text-2xl font-semibold text-gray-900">Branches</h1>
        <p class="mt-2 text-sm text-gray-700">
          Manage your branch locations and information.
        </p>
      </div>
      <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
        <button @click="openModal()" class="btn-primary">
          Add Branch
        </button>
      </div>
    </div>

    <div class="mt-8 flow-root">
      <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
          <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
            <table class="min-w-full divide-y divide-gray-300">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Name
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Address
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Contact
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
                <tr v-for="branch in branches" :key="branch.id">
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    {{ branch.name }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ branch.address }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <div v-if="branch.phone">{{ branch.phone }}</div>
                    <div v-if="branch.email">{{ branch.email }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="[
                      'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                      branch.is_active
                        ? 'bg-green-100 text-green-800'
                        : 'bg-red-100 text-red-800'
                    ]">
                      {{ branch.is_active ? 'Active' : 'Inactive' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <button @click="openModal(branch)" class="text-indigo-600 hover:text-indigo-900 mr-3">
                      Edit
                    </button>
                    <button @click="deleteBranch(branch.id)" class="text-red-600 hover:text-red-900">
                      Delete
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Branch Modal -->
    <div v-if="isModalOpen" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
      @click="closeModal">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white" @click.stop>
        <h3 class="text-lg font-bold text-gray-900 mb-4">
          {{ editingBranch ? 'Edit Branch' : 'Add Branch' }}
        </h3>
        <form @submit.prevent="saveBranch">
          <div class="mb-4">
            <label class="form-label">Name *</label>
            <input v-model="form.name" type="text" :class="[
              'form-input',
              hasFieldError('name') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : ''
            ]" />
            <FormError :errors="getFieldErrors('name')" />
          </div>
          <div class="mb-4">
            <label class="form-label">Address *</label>
            <textarea v-model="form.address" :class="[
              'form-input',
              hasFieldError('address') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : ''
            ]" rows="3"></textarea>
            <FormError :errors="getFieldErrors('address')" />
          </div>
          <div class="mb-4">
            <label class="form-label">Phone</label>
            <input v-model="form.phone" type="text" :class="[
              'form-input',
              hasFieldError('phone') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : ''
            ]" />
            <FormError :errors="getFieldErrors('phone')" />
          </div>
          <div class="mb-4">
            <label class="form-label">Email</label>
            <input v-model="form.email" type="email" :class="[
              'form-input',
              hasFieldError('email') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : ''
            ]" />
            <FormError :errors="getFieldErrors('email')" />
          </div>
          <div class="mb-4">
            <label class="flex items-center">
              <input v-model="form.is_active" type="checkbox"
                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" />
              <span class="ml-2 text-sm text-gray-600">Active</span>
            </label>
          </div>
          <div class="flex justify-end space-x-2">
            <button type="button" @click="closeModal" class="btn-secondary">
              Cancel
            </button>
            <button type="submit" :disabled="isSubmitting" :class="[
              'btn-primary',
              isSubmitting ? 'opacity-50 cursor-not-allowed' : ''
            ]">
              {{ isSubmitting ? 'Saving...' : 'Save' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { branchesApi } from '@/api'
import FormError from '@/components/form/FormError.vue'
import { useToast } from '@/composables/useToast'
import { useValidation } from '@/composables/useValidation'
import type { Branch } from '@/types'
import { onMounted, reactive, ref } from 'vue'

const branches = ref<Branch[]>([])
const isModalOpen = ref(false)
const editingBranch = ref<Branch | null>(null)
const isSubmitting = ref(false)

const { validate, errors, clearErrors, getFieldErrors, hasFieldError } = useValidation()
const { success, error } = useToast()

const form = reactive({
  name: '',
  address: '',
  phone: '',
  email: '',
  is_active: true,
})

const validationRules = {
  name: {
    required: true,
    minLength: 2,
    maxLength: 100
  },
  address: {
    required: true,
    minLength: 10,
    maxLength: 500
  },
  phone: {
    pattern: /^[\+]?[0-9\-\(\)\s]+$/,
    minLength: 8,
    maxLength: 20
  },
  email: {
    email: true,
    maxLength: 100
  },
}

const fetchBranches = async () => {
  try {
    const response = await branchesApi.getAll()
    branches.value = response.data
  } catch (err) {
    console.error('Error fetching branches:', err)
    error('Error', 'Failed to load branches')
  }
}

const openModal = (branch?: Branch) => {
  clearErrors()
  if (branch) {
    editingBranch.value = branch
    Object.assign(form, {
      name: branch.name,
      address: branch.address,
      phone: branch.phone || '',
      email: branch.email || '',
      is_active: branch.is_active,
    })
  } else {
    editingBranch.value = null
    Object.assign(form, {
      name: '',
      address: '',
      phone: '',
      email: '',
      is_active: true,
    })
  }
  isModalOpen.value = true
}

const closeModal = () => {
  isModalOpen.value = false
  editingBranch.value = null
  clearErrors()
}

const saveBranch = async () => {
  if (!validate(form, validationRules)) {
    error('Invalid Form', 'Please fix the errors below')
    return
  }

  isSubmitting.value = true
  try {
    if (editingBranch.value) {
      await branchesApi.update(editingBranch.value.id, form)
      success('Success', 'Branch updated successfully')
    } else {
      await branchesApi.create(form)
      success('Success', 'Branch created successfully')
    }
    closeModal()
    fetchBranches()
  } catch (err) {
    console.error('Error saving branch:', err)
    error('Error', 'Failed to save branch')
  } finally {
    isSubmitting.value = false
  }
}

const deleteBranch = async (id: number) => {
  if (confirm('Are you sure you want to delete this branch?')) {
    try {
      await branchesApi.delete(id)
      success('Success', 'Branch deleted successfully')
      fetchBranches()
    } catch (err) {
      console.error('Error deleting branch:', err)
      error('Error', 'Failed to delete branch')
    }
  }
}

onMounted(fetchBranches)
</script>