<template>
    <div v-show="isVisible" @click.self="closeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-start justify-center z-10">
        <div :class="`bg-white rounded-lg shadow-xl p-8 min-w-[400px] max-w-sm mx-auto mt-10 md:mt-20 transition-transform transform-gpu scale-100 hover:scale-105 ${modalClass}`">
            <div class="text-center">
                <component :is="iconComponent" :class="`mx-auto mb-4 w-16 h-16 ${iconColor}`"></component>
                <h3 class="mb-2 text-lg font-semibold text-gray-900">{{ title }}</h3>
                <p class="text-gray-600 break-words">{{ message }}</p>
                <button @click="closeModal" :class="`mt-4 w-1/2 text-white rounded px-4 py-2 hover:${buttonHoverColor} focus:outline-none focus:ring ${buttonColor} focus:ring-blue-300 transition-all`">OK</button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { defineProps, ref, watch, defineEmits, computed } from 'vue';
import ErrorIcon from './icons/ErrorIcon.vue';
import SuccessIcon from './icons/SuccessIcon.vue';
import InfoIcon from './icons/InfoIcon.vue';
import WarningIcon from './icons/WarningIcon.vue';

const props = defineProps({
    visible: {
        type: Boolean,
        default: false
    },
    type: {
        type: String,
        default: 'info' // 'error', 'success', 'info', 'warning'
    },
    title: String,
    message: String
});

const isVisible = ref(props.visible);
const modalType = computed(() => props.type || 'info');

const emit = defineEmits(['update:visible']);

watch(() => props.visible, (newVal) => {
    isVisible.value = newVal;
});

const closeModal = () => {
    isVisible.value = false;
    emit('update:visible', false);
};

const typeMappings = {
    error: {
        icon: ErrorIcon,
        buttonColor: 'bg-red-500',
        buttonHoverColor: 'bg-red-600',
        iconColor: 'text-red-600',
        modalClass: ''
    },
    success: {
        icon: SuccessIcon,
        buttonColor: 'bg-green-500',
        buttonHoverColor: 'bg-green-600',
        iconColor: 'text-green-600',
        modalClass: ''
    },
    info: {
        icon: InfoIcon,
        buttonColor: 'bg-blue-500',
        buttonHoverColor: 'bg-blue-600',
        iconColor: 'text-blue-600',
        modalClass: ''
    },
    warning: {
        icon: WarningIcon,
        buttonColor: 'bg-yellow-500',
        buttonHoverColor: 'bg-yellow-600',
        iconColor: 'text-yellow-600',
        modalClass: ''
    }
};

const typeData = computed(() => typeMappings[modalType.value] || typeMappings.info);
const iconComponent = computed(() => typeData.value.icon);
const buttonColor = computed(() => typeData.value.buttonColor);
const buttonHoverColor = computed(() => typeData.value.buttonHoverColor);
const iconColor = computed(() => typeData.value.iconColor);
const modalClass = computed(() => typeData.value.modalClass);

watch(() => props.visible, (newVal) => {
    isVisible.value = newVal;
});

</script>
