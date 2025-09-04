import type { Component } from "vue";

export interface Menus {
  label: string;
  link: string;
  icon: Component;
}

export interface Branch {
  id: number;
  name: string;
  address: string;
  phone?: string;
  email?: string;
  is_active: boolean;
  created_at: string;
  updated_at: string;
  employees?: Employee[];
}

export interface Employee {
  id: number;
  name: string;
  email: string;
  phone?: string;
  position: string;
  branch_id: number;
  salary?: number | string;
  hire_date: string;
  is_active: boolean;
  created_at: string;
  updated_at: string;
  branch?: Branch;
}

export interface Item {
  id: number;
  name: string;
  description?: string;
  category: "food" | "beverage";
  price: number | string;
  stock_quantity: number;
  unit: string;
  is_available: boolean;
  min_stock_level?: number;
  is_low_stock?: boolean;
  image_url?: string;
  created_at: string;
  updated_at: string;
}

export interface Transaction {
  id: number;
  transaction_number: string;
  branch_id: number;
  employee_id: number;
  subtotal: number | string;
  service_charge: number | string;
  total_amount: number | string;
  payment_method: "cash" | "card" | "digital_wallet";
  status: "pending" | "completed" | "cancelled";
  transaction_date: string;
  notes?: string;
  created_at: string;
  updated_at: string;
  branch?: Branch;
  employee?: Employee;
  transaction_items?: TransactionItem[];
}

export interface TransactionItem {
  id: number;
  transaction_id: number;
  item_id: number;
  quantity: number;
  unit_price: number | string;
  total_price: number | string;
  notes?: string;
  created_at: string;
  updated_at: string;
  item?: Item;
}

export interface DashboardSummary {
  period: {
    start_date: string;
    end_date: string;
  };
  total_revenue: number | string;
  total_service_charge: number | string;
  total_transactions: number | string;
}

export interface TopItem {
  id: number;
  name: string;
  category: string;
  total_sold: number | string;
  total_revenue: number | string;
}

export interface BranchRevenue {
  id: number;
  name: string;
  total_revenue: number | string;
  total_service_charge: number | string;
  total_transactions: number | string;
}

export interface CreateTransactionRequest {
  branch_id: number;
  employee_id: number;
  payment_method: "cash" | "card" | "digital_wallet";
  notes?: string;
  items: {
    item_id: number;
    quantity: number;
    unit_price: number;
    notes?: string;
  }[];
}
