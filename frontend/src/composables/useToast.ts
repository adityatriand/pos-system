import { ref, reactive } from 'vue'

export type ToastType = 'success' | 'error' | 'warning' | 'info'

export interface Toast {
  id: string
  type: ToastType
  title: string
  message?: string
  duration?: number
}

const toasts = ref<Toast[]>([])

export function useToast() {
  const show = (type: ToastType, title: string, message?: string, duration = 5000) => {
    const id = Math.random().toString(36).substr(2, 9)
    
    const toast: Toast = {
      id,
      type,
      title,
      message,
      duration,
    }

    toasts.value.push(toast)

    // Auto remove toast
    if (duration > 0) {
      setTimeout(() => {
        remove(id)
      }, duration)
    }

    return id
  }

  const remove = (id: string) => {
    const index = toasts.value.findIndex(toast => toast.id === id)
    if (index > -1) {
      toasts.value.splice(index, 1)
    }
  }

  const clear = () => {
    toasts.value = []
  }

  const success = (title: string, message?: string, duration?: number) => {
    return show('success', title, message, duration)
  }

  const error = (title: string, message?: string, duration?: number) => {
    return show('error', title, message, duration)
  }

  const warning = (title: string, message?: string, duration?: number) => {
    return show('warning', title, message, duration)
  }

  const info = (title: string, message?: string, duration?: number) => {
    return show('info', title, message, duration)
  }

  return {
    toasts,
    show,
    remove,
    clear,
    success,
    error,
    warning,
    info,
  }
}