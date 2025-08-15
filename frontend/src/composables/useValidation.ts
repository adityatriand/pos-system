import { ref, reactive } from 'vue'

export interface ValidationRule {
  required?: boolean
  email?: boolean
  minLength?: number
  maxLength?: number
  min?: number
  max?: number
  pattern?: RegExp
  custom?: (value: any) => string | null
}

export interface ValidationErrors {
  [key: string]: string[]
}

export function useValidation() {
  const errors = ref<ValidationErrors>({})
  const isValidating = ref(false)

  const validateField = (fieldName: string, value: any, rules: ValidationRule): string[] => {
    const fieldErrors: string[] = []

    // Required validation
    if (rules.required && (!value || (typeof value === 'string' && value.trim() === ''))) {
      fieldErrors.push(`${fieldName} is required`)
    }

    // Skip other validations if field is empty and not required
    if (!value && !rules.required) {
      return fieldErrors
    }

    // Email validation
    if (rules.email && value) {
      const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
      if (!emailPattern.test(value)) {
        fieldErrors.push(`${fieldName} must be a valid email`)
      }
    }

    // Min length validation
    if (rules.minLength && value && value.length < rules.minLength) {
      fieldErrors.push(`${fieldName} must be at least ${rules.minLength} characters`)
    }

    // Max length validation
    if (rules.maxLength && value && value.length > rules.maxLength) {
      fieldErrors.push(`${fieldName} must not exceed ${rules.maxLength} characters`)
    }

    // Min value validation
    if (rules.min !== undefined && value && Number(value) < rules.min) {
      fieldErrors.push(`${fieldName} must be at least ${rules.min}`)
    }

    // Max value validation
    if (rules.max !== undefined && value && Number(value) > rules.max) {
      fieldErrors.push(`${fieldName} must not exceed ${rules.max}`)
    }

    // Pattern validation
    if (rules.pattern && value && !rules.pattern.test(value)) {
      fieldErrors.push(`${fieldName} has invalid format`)
    }

    // Custom validation
    if (rules.custom && value) {
      const customError = rules.custom(value)
      if (customError) {
        fieldErrors.push(customError)
      }
    }

    return fieldErrors
  }

  const validate = (data: Record<string, any>, rules: Record<string, ValidationRule>): boolean => {
    isValidating.value = true
    errors.value = {}

    let isValid = true

    Object.keys(rules).forEach(fieldName => {
      const fieldErrors = validateField(fieldName, data[fieldName], rules[fieldName])
      if (fieldErrors.length > 0) {
        errors.value[fieldName] = fieldErrors
        isValid = false
      }
    })

    isValidating.value = false
    return isValid
  }

  const clearErrors = () => {
    errors.value = {}
  }

  const clearFieldError = (fieldName: string) => {
    if (errors.value[fieldName]) {
      delete errors.value[fieldName]
    }
  }

  const hasErrors = () => {
    return Object.keys(errors.value).length > 0
  }

  const getFieldErrors = (fieldName: string): string[] => {
    return errors.value[fieldName] || []
  }

  const hasFieldError = (fieldName: string): boolean => {
    return !!(errors.value[fieldName] && errors.value[fieldName].length > 0)
  }

  return {
    errors,
    isValidating,
    validate,
    validateField,
    clearErrors,
    clearFieldError,
    hasErrors,
    getFieldErrors,
    hasFieldError,
  }
}