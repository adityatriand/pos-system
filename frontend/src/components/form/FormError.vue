<template>
  <div v-if="errors && errors.length > 0" class="mt-1">
    <p
      v-for="error in errors"
      :key="error"
      class="text-sm text-red-600"
    >
      {{ error }}
    </p>
  </div>
</template>

<script setup lang="ts">
defineProps<{
  errors?: string[]
}>()
</script>