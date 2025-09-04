import {
  ChartBarIcon,
  CogIcon,
  CreditCardIcon,
  FolderIcon,
  MapPinIcon,
  ShoppingBagIcon,
  UserGroupIcon,
} from "@heroicons/vue/24/outline";
import type { Menus } from "../../types";

export const MENUS: Menus[] = [
  { label: "Dashboard", link: "/", icon: CogIcon }, // Changed icon
  { label: "Branches", link: "/branches", icon: MapPinIcon }, // Changed icon
  { label: "Employees", link: "/employees", icon: UserGroupIcon }, // Changed icon
  { label: "Items", link: "/items", icon: ShoppingBagIcon }, // Changed icon
  { label: "Branch Inventory", link: "/branch-inventory", icon: FolderIcon }, // Changed icon
  { label: "New Transaction", link: "/transactions", icon: CreditCardIcon }, // Changed icon
  { label: "Sales History", link: "/sales-history", icon: ChartBarIcon }, // Changed icon
];
