<template>
    <div :class="avatarClasses" class="flex items-center justify-center rounded-full bg-gray-400 text-white">
        <img v-if="imageSrc" :src="imageSrc" :alt="altText" class="w-full h-full object-cover rounded-full" />
        <span v-else class="text-xl font-semibold">{{ initials }}</span>
    </div>
</template>

<script setup>
import { computed } from 'vue'
// define props
const props = defineProps({
    imageSrc: {
        type: String,
        default: '',
    },
    name: {
        type: String,
        required: true,
    },
    size: {
        type: String,
        default: 'w-10 h-10',
    },
})

// computed values
const initials = computed(() => {
    const nameParts = props.name.split(' ')
    return nameParts.length > 1
        ? nameParts[0].charAt(0) + nameParts[1].charAt(0)
        : nameParts[0].charAt(0)
})

const avatarClasses = computed(() => props.size)
</script>
