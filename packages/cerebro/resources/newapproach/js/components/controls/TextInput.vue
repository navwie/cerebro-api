<template>
    <div class="mb-3">
        <label :for="id" class="block text-gray-700 text-sm font-bold mb-1">{{ label }}</label>
        <input :type="type" :id="id" :name="name" v-model="innerValue" :autocomplete="autocomplete" :class="['appearance-none block w-full bg-gray-100 text-gray-700 border', { 'border-red-500': hasError }, 'appearance-none block w-full bg-gray-100 text-gray-700 border border-gray-200 rounded py-2 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500']">
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
    type: {
        default: 'text'
    },
    errorMessage: null,
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

  