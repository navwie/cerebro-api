<template>
    <div class="mb-3">
        <label v-if="labelPosition === 'top'" :for="id" class="block text-gray-700 text-sm font-bold mb-1">
            {{ label }}
        </label>
        <input type="number" :id="id" :name="name" v-model.number="innerValue" :autocomplete="autocomplete" :min="min" :class="['appearance-none block w-full bg-gray-100 text-gray-700 border', { 'border-red-500': hasError }, 'appearance-none block w-full bg-gray-100 text-gray-700 border border-gray-200 rounded py-2 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500']">
        <label v-if="labelPosition === 'bottom'" :for="id" class="block text-gray-700 text-sm font-bold mt-1">
            {{ label }}
        </label>
        <p class="text-red-500 text-xs italic" v-if="hasError">{{ errorMessage }}</p>
    </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue';

const props = defineProps({
    modelValue: null,
    label: null,
    id: null,
    name: null,
    errorMessage: null,
    min: null,
    labelPosition: {
        default: 'top'
    },
    autocomplete: {
        default: 'off'
    }
});

const innerValue = ref(props.modelValue);

const emit = defineEmits(['update:modelValue']);

watch(() => props.modelValue, (newValue) => {
    if (newValue !== innerValue.value) {
        innerValue.value = newValue;
    }
});

watch(innerValue, (newValue) => {
    emit('update:modelValue', newValue);
});

const hasError = computed(() => props.errorMessage && props.errorMessage.length > 0);
</script>

  