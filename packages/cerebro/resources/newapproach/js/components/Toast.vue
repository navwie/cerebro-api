<template>
    <div class="fixed top-10 right-0 m-4 space-y-2 z-20">
        <div v-for="toast in toasts" :key="toast.id" @click="removeToast(toast.id)" :class="toastClasses(toast.type)" class="cursor-pointer p-4 rounded-lg shadow-md">
            {{ toast.message }}
        </div>
    </div>
</template>

<script setup>
import { useToast } from './../services/toastService.js';

const { toasts, removeToast } = useToast();

const toastClasses = (type) => {
    switch (type) {
        case 'success': return 'bg-green-500 text-white';
        case 'info': return 'bg-blue-500 text-white';
        case 'error': return 'bg-red-500 text-white';
        case 'warning': return 'bg-yellow-500 text-white';
        default: return 'bg-gray-500 text-white';
    }
};
</script>
