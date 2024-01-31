<template>
    <button type="submit" :disabled="isDisabled || isLoading" class="ml-3 px-4 py-2 bg-blue-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-75 flex items-center justify-center whitespace-nowrap">
        <span>{{buttonText}}</span>
        <span v-if="isLoading" class="ml-2">
            <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0116 0H4z"></path>
            </svg>
        </span>
    </button>
</template>
  
<script setup>
import { defineProps } from 'vue';

const props = defineProps({
    buttonText: {
        required: true
    },
    isLoading: {
        default: false
    },
    isDisabled: {
        default: false
    }
});
</script>
  