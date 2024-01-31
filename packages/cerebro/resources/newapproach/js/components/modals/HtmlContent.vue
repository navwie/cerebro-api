<template>
    <div v-show="isVisible" @click.self="closeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-start justify-center z-10">
        <div class="bg-white rounded-lg shadow-xl p-8 min-w-[600px] max-w-2xl mx-auto mt-10 md:mt-20 transition-transform transform-gpu scale-100 hover:scale-105">
            <div class="text-center">
                <h2 class="mb-3 text-xl font-semibold text-gray-900" v-html="title"></h2>
                <div class="text-gray-600 break-words" v-html="message"></div>
                <button @click="closeModal" class="mt-4 w-1/3 text-white rounded px-4 py-2 hover:bg-blue-600 focus:outline-none focus:ring bg-blue-500 focus:ring-blue-300 transition-all">OK</button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { defineProps, ref, watch, defineEmits } from 'vue';

const props = defineProps({
    visible: Boolean,
    title: String,
    message: String
});

const isVisible = ref(props.visible);

const emit = defineEmits(['update:visible']);

watch(() => props.visible, (newVal) => {
    isVisible.value = newVal;
});

const closeModal = () => {
    isVisible.value = false;
    emit('update:visible', false);
};
</script>
