<template>
    <div>
        <label :for="id" class="block text-gray-700 text-sm font-bold mb-1">
            {{ label }}
        </label>
        <div class="inline-flex rounded-md shadow-sm" role="group" :id="id">
            <button v-for="(item, index) in buttons" :key="index" type="button" :style="buttonStyle(item.value, index)" @click="setPosition(item.value)">
                {{ item.text }}
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, defineProps } from 'vue';

const props = defineProps({
    label: String,
    id: String,
    buttons: Array,
    activeColor: {
        type: String,
        default: 'blue'
    }
});

const emit = defineEmits(['update:selected']);

const selectedPosition = ref(props.buttons.find((b) => b.value === 'all')?.value || props.buttons[0]?.value);


const setPosition = (position) => {
    selectedPosition.value = position;
    emit('update:selected', position);
};

const buttonStyle = (position, index) => {
    const isActive = selectedPosition.value === position;
    const isFirstChild = index === 0;
    const isLastChild = index === props.buttons.length - 1;
    return {
        padding: '0.5rem 1rem',
        fontSize: '0.875rem',
        fontWeight: '500',
        textAlign: 'center',
        cursor: 'pointer',
        border: '1px solid #e2e8f0',
        borderRadius: isFirstChild ? '0.275rem 0 0 0.275rem' : isLastChild ? '0 0.275rem 0.275rem 0' : '0',
        backgroundColor: isActive ? props.activeColor : '#f7fafc',
        color: isActive ? 'white' : '#4a5568',
        outline: 'none',
        margin: '0',
        transition: 'background-color 0.3s, color 0.3s',
    };
};

</script>
