<template>
    <div :class="`flex flex-${direction} gap-4 p-6`">
        <label v-if="withLabel" for="dateRange" class="text-gray-700 font-semibold">Select Date Range</label>
        <input type="text" id="dateRange" v-model="selectedRange"
            class="p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
            placeholder="Select Date Range" ref="dateInput" autocomplete="off" />
        <button @click="resetDateRange"
            class="bg-indigo-500 text-white px-4 py-2 rounded-md hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            Reset
        </button>
    </div>
</template>

<script setup>
import { format } from 'date-fns'
import flatpickr from 'flatpickr'
import 'flatpickr/dist/flatpickr.css'
import { onMounted, ref } from 'vue'

// define props
const props = defineProps({
    withLabel: {
        type: Boolean,
        default: false,
    },
    direction: {
        type: String,
        default: 'col',
        validator: (value) => ['row', 'col'].includes(value),
    },
})

// emit definition
const emit = defineEmits(['update-date-range'])

// state
const selectedRange = ref('')
const dateInput = ref(null)

// format display string
function formatDisplay(start, end) {
    const sameMonth = format(start, 'MMMM yyyy') === format(end, 'MMMM yyyy')
    const sameYear = format(start, 'yyyy') === format(end, 'yyyy')

    if (sameMonth) {
        // Example: 1 - 5 August 2025
        return `${format(start, 'd')} - ${format(end, 'd MMMM yyyy')}`
    } else if (sameYear) {
        // Example: 1 August - 1 September 2025
        return `${format(start, 'd MMMM')} - ${format(end, 'd MMMM yyyy')}`
    } else {
        // Example: 1 September 2024 - 1 September 2025
        return `${format(start, 'd MMMM yyyy')} - ${format(end, 'd MMMM yyyy')}`
    }
}

// reset method
function resetDateRange() {
    selectedRange.value = ''
    dateInput.value._flatpickr.clear()
    emit('update-date-range', {
        start_date: '',
        end_date: '',
    })
}

// lifecycle
onMounted(() => {
    flatpickr(dateInput.value, {
        mode: 'range',
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'j F Y', // base format, but we’ll override
        onChange: (selectedDates, _, instance) => {
            if (selectedDates.length === 2) {
                const startDate = format(selectedDates[0], 'yyyy-MM-dd')
                const endDate = format(selectedDates[1], 'yyyy-MM-dd')

                // custom compressed format
                const display = formatDisplay(selectedDates[0], selectedDates[1])

                // overwrite the alt input field
                instance.altInput.value = display

                emit('update-date-range', {
                    start_date: startDate,
                    end_date: endDate,
                })
            }
        },
    })
})
</script>
