<template>
    <div class="bg-white shadow-md rounded">
        <div class="border-b border-gray-200 flex">
            <h2 class="bg-gray-100 text-black text-xl px-4 py-4 w-full font-bold rounded-t-md flex justify-center whitespace-nowrap">{{ settings.title }}</h2>
        </div>
        <div class="flex divide-x min-h-[80px] divide-gray-200">
            <div v-for="(item, index) in settings.values" :key="index" class="flex-1 flex flex-col items-center justify-center p-4">
                <div class="text-xl font-bold">{{ item.value }}</div>
                <div class="description text-sm uppercase whitespace-nowrap">{{ item.description }}</div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { defineProps } from 'vue'

const props = defineProps({
    settings: {
        type: Object,
        default: () => ({
            title: '',
            values: []
        })
    }
})
</script>
