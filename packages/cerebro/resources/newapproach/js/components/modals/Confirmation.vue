<template>
    <div v-show="isVisible" @click.self="cancel" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-10">
        <div class="relative top-20 mx-auto p-6 min-w-[400px] border w-96 shadow-lg rounded-md bg-white transition-transform transform-gpu scale-100 hover:scale-105">
            <div class="text-center">
                <QuestionIcon class="mx-auto mb-4 w-16 h-16"/>
                <h3 class="text-lg leading-6 font-medium text-gray-900">{{ title }}</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">{{ message }}</p>
                </div>
                <div class="items-center px-4 py-3">
                    <button @click="confirm" class="px-4 py-2 w-1/3 bg-orange-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-orange-700">{{ btnOkText }}</button>
                    <button @click="cancel" class="px-4 py-2  w-1/3 ml-3 bg-green-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-green-700">{{ btnCancelText }}</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { defineProps, ref, watch } from 'vue';
import QuestionIcon from './icons/Question.vue';

const props = defineProps({
    visible: {
        required: true,
        default: false
    },
    title: {
        required: false,
        type: String,
        default: 'Action confirmation'
    },
    message: {
        required: false,
        type: String,
        default: 'Are you sure you want to perform this action?'
    },
    btnOkText: {
        required: false,
        type: String,
        default: 'Confirm'
    },
    btnCancelText: {
        required: false,
        type: String,
        default: 'Cancel'
    }
});

const emit = defineEmits(['update:visible', 'confirm']);

const isVisible = ref(props.visible);

watch(() => props.visible, (newVal) => {
    isVisible.value = newVal;
});

const cancel = () => {
    isVisible.value = false;
    emit('update:visible', false); 
};

const confirm = () => {
    cancel();
    emit('confirm');
};
</script>
