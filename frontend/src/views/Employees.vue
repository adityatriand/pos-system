<template>
  <div class="px-4 py-6 sm:px-0">
    <div class="sm:flex sm:items-center">
      <div class="sm:flex-auto">
        <h1 class="text-2xl font-semibold text-gray-900">Employees</h1>
        <p class="mt-2 text-sm text-gray-700">Manage employee information.</p>
      </div>
      <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
        <button @click="openModal()" class="btn-primary">Add Employee</button>
      </div>
    </div>

    <div class="mt-8 flow-root">
      <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
          <table class="min-w-full divide-y divide-gray-300 bg-white shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Position</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Branch</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="employee in employees" :key="employee.id">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                  {{ employee.name }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ employee.position }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ employee.branch?.name }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  <div>{{ employee.email }}</div>
                  <div v-if="employee.phone">{{ employee.phone }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="[
                    'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                    employee.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                  ]">
                    {{ employee.is_active ? 'Active' : 'Inactive' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <button @click="openModal(employee)" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                  <button @click="deleteEmployee(employee.id)" class="text-red-600 hover:text-red-900">Delete</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Employee Modal -->
    <div v-if="isModalOpen" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click="closeModal">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white" @click.stop>
        <h3 class="text-lg font-bold text-gray-900 mb-4">{{ editingEmployee ? 'Edit Employee' : 'Add Employee' }}</h3>
        <form @submit.prevent="saveEmployee">
          <div class="mb-4">
            <label class="form-label">Name</label>
            <input v-model="form.name" type="text" class="form-input" required />
          </div>
          <div class="mb-4">
            <label class="form-label">Email</label>
            <input v-model="form.email" type="email" class="form-input" required />
          </div>
          <div class="mb-4">
            <label class="form-label">Phone</label>
            <input v-model="form.phone" type="text" class="form-input" />
          </div>
          <div class="mb-4">
            <label class="form-label">Position</label>
            <input v-model="form.position" type="text" class="form-input" required />
          </div>
          <div class="mb-4">
            <label class="form-label">Branch</label>
            <select v-model.number="form.branch_id" class="form-input" required>
              <option value="0">Select Branch</option>
              <option v-for="branch in branches" :key="branch.id" :value="branch.id">
                {{ branch.name }}
              </option>
            </select>
          </div>
          <div class="mb-4">
            <label class="form-label">Salary</label>
            <input v-model="form.salary" type="number" class="form-input" />
          </div>
          <div class="mb-4">
            <label class="form-label">Hire Date</label>
            <input v-model="form.hire_date" type="date" class="form-input" required />
          </div>
          <div class="mb-4">
            <label class="flex items-center">
              <input v-model="form.is_active" type="checkbox" class="rounded border-gray-300" />
              <span class="ml-2 text-sm text-gray-600">Active</span>
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
import { employeesApi, branchesApi } from '@/api'
import type { Employee, Branch } from '@/types'

const employees = ref<Employee[]>([])
const branches = ref<Branch[]>([])
const isModalOpen = ref(false)
const editingEmployee = ref<Employee | null>(null)

const form = reactive({
  name: '',
  email: '',
  phone: '',
  position: '',
  branch_id: 0,
  salary: '',
  hire_date: '',
  is_active: true,
})

const fetchEmployees = async () => {
  try {
    const response = await employeesApi.getAll()
    employees.value = response.data
  } catch (error) {
    console.error('Error fetching employees:', error)
  }
}

const fetchBranches = async () => {
  try {
    const response = await branchesApi.getAll()
    branches.value = response.data
  } catch (error) {
    console.error('Error fetching branches:', error)
  }
}

const openModal = (employee?: Employee) => {
  if (employee) {
    editingEmployee.value = employee
    Object.assign(form, {
      name: employee.name,
      email: employee.email,
      phone: employee.phone || '',
      position: employee.position,
      branch_id: employee.branch_id,
      salary: employee.salary || '',
      hire_date: employee.hire_date,
      is_active: employee.is_active,
    })
  } else {
    editingEmployee.value = null
    Object.assign(form, {
      name: '',
      email: '',
      phone: '',
      position: '',
      branch_id: 0,
      salary: '',
      hire_date: '',
      is_active: true,
    })
  }
  isModalOpen.value = true
}

const closeModal = () => {
  isModalOpen.value = false
  editingEmployee.value = null
}

const saveEmployee = async () => {
  try {
    if (editingEmployee.value) {
      await employeesApi.update(editingEmployee.value.id, form)
    } else {
      await employeesApi.create(form)
    }
    closeModal()
    fetchEmployees()
  } catch (error) {
    console.error('Error saving employee:', error)
  }
}

const deleteEmployee = async (id: number) => {
  if (confirm('Are you sure you want to delete this employee?')) {
    try {
      await employeesApi.delete(id)
      fetchEmployees()
    } catch (error) {
      console.error('Error deleting employee:', error)
    }
  }
}

onMounted(() => {
  fetchEmployees()
  fetchBranches()
})
</script>