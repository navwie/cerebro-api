<template>
        <label :for="id" class="block text-gray-700 text-sm font-bold mb-1">
            {{ label }}
        </label>
        <select :id="id" :name="name" v-model="selectedValue" class="block appearance-none w-full bg-gray-100 border border-gray-200 text-gray-700 py-2 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
            <option v-for="option in options" :key="option.value" :value="option.value">
                {{ option.text }}
            </option>
        </select>
        <p v-if="error" class="text-red-500 text-xs italic">{{ error }}</p>
</template>
  
<script setup>
import { ref, watch, defineProps } from 'vue';

const props = defineProps({
    label: String,
    modelValue: [String, Number],
    options: Array,
    error: String,
    id: String,
    name: String
});

const selectedValue = ref(props.modelValue);
const emit = defineEmits(['update:modelValue']);

watch(() => props.modelValue, (newValue) => {
    if (newValue !== selectedValue.value) {
        selectedValue.value = newValue;
    }
});

watch(selectedValue, (newValue) => {
    emit('update:modelValue', newValue);
});
</script>
