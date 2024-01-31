<template>
    <div v-if="isVisible" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex justify-center items-center">
        <div class="bg-white rounded-lg shadow-2xl w-2/4 min-w-[950px]">
            <div class="flex justify-between items-center bg-slate-700 text-white rounded-t-lg p-4">
                <span class="text-lg font-bold">General Settings</span>
                <button @click="closeModal" class="hover:text-gray-200">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-4 grid grid-cols-2 gap-4">
                <!-- Favicon Input -->
                <div v-if="'favicon' in generalStyles" class="flex items-center space-x-2">
                    <h3 for="favicon" class="text-lg font-semibold mb-2 block">Favicon</h3>
                    <input type="file" id="favicon" @change="event => handleFileInputChange(event, 'favicon')" class="mb-2 border border-gray-200 rounded shadow">
                    <div v-if="faviconPreview" class="mb-2">
                        <img :src="faviconPreview" class="w-8 h-8" alt="Favicon preview" />
                    </div>
                </div>
                <!-- Other General Style Inputs -->
                <div v-for="(styleObj, index) in generalStyleObjects" :key="index" class="flex justify-between items-center mb-2">
                    <h3 class="text-lg font-semibold">{{ styleObj.label }}</h3>
                    <div v-if="styleObj.variable.includes('color')" class="flex items-center space-x-2">
                        <input :id="`input-color-${index}`" v-model="styleObj.value" maxlength="9" class="p-1 border border-gray-200 rounded shadow">
                        <ColorInput :id="`color-picker-${index}`" v-model="styleObj.value" format="hex8" class="w-1/2" />
                    </div>
                    <input v-else :id="styleObj.variable" v-model="styleObj.value" type="text" maxlength="9" class="p-1 border border-gray-200 rounded shadow">
                </div>
            </div>
            <div class="flex justify-end bg-gray-100 p-4 rounded-b-lg">
                <button @click="applyStyles" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded transition duration-300">Apply</button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { defineProps, ref, computed, watch } from 'vue';
import ColorInput from 'vue-color-input'
const props = defineProps({
    isVisible: Boolean,
    generalStyles: Object
});
const emit = defineEmits(['update:isVisible', 'apply-general-styles']);

const imageFiles = ref({});

const generalStyleObjects = computed(() => {
    return Object.values(props.generalStyles).filter(styleObj => typeof styleObj === 'object' && styleObj.variable !== 'favicon');
});

const closeModal = () => {
    emit('update:isVisible', false);
};

const applyStyles = () => {
    emit('apply-general-styles', { styles: props.generalStyles, imageFiles: imageFiles.value });
    cleanState();
    emit('update:isVisible', false);
};

const cleanState = () => {
    imageFiles.value = {};
};

const handleFileInputChange = (event, property) => {
    const file = event.target.files[0];
    if (file) {
        imageFiles.value[property] = file;
    }
};

const faviconPreview = ref('');

watch(() => imageFiles.value.favicon, (newFile) => {
    if (newFile) {
        const reader = new FileReader();
        reader.onload = (e) => faviconPreview.value = e.target.result;
        reader.readAsDataURL(newFile);
    }
});



</script>
