<template>
  <div class="min-h-screen bg-backgroundGray flex">
    <ToastNotification />
    <NavbarVertical />
  </div>
</template>

<script setup lang="ts">
import NavbarVertical from '@/components/navbar/NavbarVertical.vue';
import ToastNotification from '@/components/notification/ToastNotification.vue';
</script>