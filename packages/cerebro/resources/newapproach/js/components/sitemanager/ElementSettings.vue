<template>
    <div v-if="isVisible" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex justify-center items-center">
        <div class="bg-white rounded-lg shadow-2xl w-2/4 min-w-[950px]">
            <div class="flex justify-between items-center bg-slate-700 text-white rounded-t-lg p-4">
                <span class="text-lg font-bold">Element Settings. Block {{ currentEdittableBlockIndex }}</span>
                <button @click="closeModal" class="hover:text-gray-200">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-4 grid grid-cols-2 gap-4">

                <template v-for="(styleValue, styleProperty) in editableElementStyles" :key="styleProperty">
                    <div v-if="!isPseudoClass(styleProperty)" class="border p-3">
                        <div v-if="!styleProperty.startsWith('CE_')" class="flex justify-between items-center">
                            <h3 class="text-lg font-semibold">{{ styleProperty }}</h3>
                            <div v-if="styleProperty.includes('color')" class="flex items-center space-x-2">
                                <input :id="`input-color-${styleProperty}`" v-model="editableElementStyles[styleProperty]" maxlength="9" class="p-1 border border-gray-200 rounded shadow">
                                <ColorInput :id="styleProperty" v-model="editableElementStyles[styleProperty]" format="hex8" class="" />
                            </div>
                            <input v-else-if="styleProperty === 'background-image' || styleProperty === 'src'" type="file"
                                @change="event => handleFileInputChange(event, styleProperty)" class="p-1">
                            <input v-else :id="styleProperty" v-model="editableElementStyles[styleProperty]" maxlength="9" type="text" class="p-1 border border-gray-200 rounded shadow">
                        </div>
                        <div v-else >
                            <h3 class="text-lg font-semibold mb-2">{{ styleValue.label }}</h3>
                            <div v-for="(cssValue, cssProperty) in styleValue" :key="`${styleProperty}-${cssProperty}`">
                                <div v-if="cssProperty !== 'label'"  class="flex justify-between items-center">
                                    <h3 class="text-lg font-semibold mb-2">{{ cssProperty }}</h3>
                                    <div v-if="cssProperty.includes('color') || cssProperty === 'fill'" class="flex items-center space-x-2">
                                        <input :id="`${styleProperty}-${cssProperty}`" v-model="styleValue[cssProperty]" maxlength="9" type="text" class="mb-2 p-1 border border-gray-200 rounded shadow">
                                        <ColorInput :id="`${styleProperty}-${cssProperty}`" v-model="styleValue[cssProperty]" format="hex8" class="mb-2" />
                                    </div>
                                    <div v-else-if="cssProperty === 'src' || cssProperty === 'background-image'" class="flex items-center space-x-2">
                                        <input type="file" @change="event => handleFileInputChange(event, styleProperty)" class="p-1">
                                    </div>
                                    <input v-else :id="`${styleProperty}-${cssProperty}`" v-model="styleValue[cssProperty]" maxlength="9" type="text" class="mb-2 p-1 border border-gray-200 rounded shadow">
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <template v-for="(styleValue, styleProperty) in editableElementStyles" :key="styleProperty">
                    <div v-if="isPseudoClass(styleProperty)" class="border p-3 rounded">
                        <h3 class="block text-lg font-semibold">{{ styleValue.label }}</h3>
                        <div v-for="(innerValue, innerProperty) in styleValue" :key="`${styleProperty}-${innerProperty}`" class="mb-2">
                            <template v-if="innerProperty !== 'label'">
                                <div class="flex justify-between items-center">
                                    <h3 :for="`${styleProperty}-${innerProperty}`" class="text-lg font-semibold">{{ innerProperty }}</h3>
                                    <div v-if="innerProperty.includes('color')" class="flex items-center space-x-2">
                                        <input :id="`${styleProperty}-${innerProperty}`" v-model="styleValue[innerProperty].value" type="text" class="p-1 border border-gray-200 rounded shadow">
                                        <ColorInput :id="`${styleProperty}-${innerProperty}`" v-model="styleValue[innerProperty].value" format="hex8" class="w-1/3" />
                                    </div>
                                    <input v-else :id="`${styleProperty}-${innerProperty}`" v-model="styleValue[innerProperty].value" type="text" class="p-1 border border-gray-200 rounded shadow">
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
            <div class="flex justify-end bg-gray-100 p-4 rounded-b-lg">
                <button @click="closeModal" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 mr-2 rounded transition duration-300">Close</button>
                <button @click="applyStyles" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition duration-300">Apply</button>
            </div>
        </div>
    </div>
</template>


<script setup>
import { defineProps, ref } from 'vue';
import ColorInput from 'vue-color-input'
const imageFiles = ref({});

const props = defineProps({
    isVisible: Boolean,
    editableElementStyles: Object,
    currentEditingElementId: String,
    currentEdittableBlockIndex: Number
});

const emit = defineEmits(['update:isVisible', 'apply-styles']);

const closeModal = () => {
    emit('update:isVisible', false);
};

const applyStyles = () => {
    const fileIdMappingData = {};
    for (const property in imageFiles.value) {
        if (imageFiles.value.hasOwnProperty(property)) {
            const file = imageFiles.value[property];

            fileIdMappingData[property] = `${props.currentEditingElementId}-${file.name}`;
        }
    }

    emit('apply-styles', {
        styles: props.editableElementStyles,
        elementId: props.currentEditingElementId,
        imageFiles: imageFiles.value,
        fileIdMappingData
    });
    // cleanState();
    emit('update:isVisible', false);
};

const handleFileInputChange = (event, property) => {
    const file = event.target.files[0];
    if (file) {
        if (imageFiles.value[property]) {
            delete imageFiles.value[property];
        }
        imageFiles.value[property] = file;
    }
};


const isPseudoClass = (property) => {
    return property.startsWith(':');
};

const isFileInput = (property) => {
    return property === 'background-image' || property === 'src';
};
</script>
