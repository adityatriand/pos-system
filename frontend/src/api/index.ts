import api from './client'
import type {
  Branch,
  Employee,
  Item,
  Transaction,
  CreateTransactionRequest,
  DashboardSummary,
  TopItem,
  BranchRevenue,
} from '@/types'

// Branches API
export const branchesApi = {
  getAll: () => api.get<Branch[]>('/branches'),
  getById: (id: number) => api.get<Branch>(`/branches/${id}`),
  create: (data: Partial<Branch>) => api.post<Branch>('/branches', data),
  update: (id: number, data: Partial<Branch>) => api.put<Branch>(`/branches/${id}`, data),
  delete: (id: number) => api.delete(`/branches/${id}`),
  getEmployees: (id: number) => api.get<Employee[]>(`/branches/${id}/employees`),
  getItems: (id: number) => api.get<Item[]>(`/branches/${id}/items`),
}

// Employees API
export const employeesApi = {
  getAll: () => api.get<Employee[]>('/employees'),
  getById: (id: number) => api.get<Employee>(`/employees/${id}`),
  create: (data: Partial<Employee>) => api.post<Employee>('/employees', data),
  update: (id: number, data: Partial<Employee>) => api.put<Employee>(`/employees/${id}`, data),
  delete: (id: number) => api.delete(`/employees/${id}`),
}

// Items API
export const itemsApi = {
  getAll: () => api.get<Item[]>('/items'),
  getById: (id: number) => api.get<Item>(`/items/${id}`),
  create: (data: Partial<Item>) => api.post<Item>('/items', data),
  update: (id: number, data: Partial<Item>) => api.put<Item>(`/items/${id}`, data),
  delete: (id: number) => api.delete(`/items/${id}`),
  getByCategory: (category: string) => api.get<Item[]>(`/items/category/${category}`),
}

// Transactions API
export const transactionsApi = {
  getAll: () => api.get<Transaction[]>('/transactions'),
  getById: (id: number) => api.get<Transaction>(`/transactions/${id}`),
  create: (data: CreateTransactionRequest) => api.post<Transaction>('/transactions', data),
  filter: (params: {
    branch_id?: number
    start_date?: string
    end_date?: string
  }) => api.get<Transaction[]>('/transactions/history/filter', { params }),
  exportPdf: (params: {
    branch_id?: number
    start_date?: string
    end_date?: string
  }) => api.get('/transactions/history/export-pdf', { 
    params, 
    responseType: 'blob' 
  }),
}

// Dashboard API
export const dashboardApi = {
  getSummary: (params: {
    start_date?: string
    end_date?: string
  }) => api.get<DashboardSummary>('/dashboard/summary', { params }),
  getTopItems: (params: {
    start_date?: string
    end_date?: string
    limit?: number
  }) => api.get<TopItem[]>('/dashboard/top-items', { params }),
  getBranchRevenue: (params: {
    start_date?: string
    end_date?: string
  }) => api.get<BranchRevenue[]>('/dashboard/branch-revenue', { params }),
  getInventoryStatus: () => api.get('/dashboard/inventory-status'),
}