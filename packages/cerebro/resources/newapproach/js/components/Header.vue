<template>
    <header class="bg-white h-14 py-3 dark:bg-grey-300 border-b-2 flex justify-between items-center px-7">
        <button @click="emitToggleFully" class="focus:outline-none">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-16 6h16" />
            </svg>
        </button>
        <div class="relative" v-click-outside="hideDropdown">
            <button @click="toggleDropdown" class="flex items-center bg-gray-100 hover:bg-gray-200 rounded-full px-4 py-2 focus:outline-none">
                <i class="fas fa-user-circle mr-2"></i>
                <span>{{ user }}</span>
                <i class="fas fa-caret-down ml-2"></i>
            </button>
            <div v-show="isDropdownVisible" class="absolute right-0 mt-2 py-2 w-48 bg-white rounded-md shadow-xl z-20">
                <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-200" @click.prevent="logout">
                    <i class="fas fa-sign-out-alt mr-2"></i> Logout </a>
            </div>
        </div>
    </header>
</template>

<script setup>
import { ref, onMounted, defineEmits } from 'vue';
import { usePage } from '@inertiajs/inertia-vue3';
import { useCsrfStore } from '../stores/csrfStore';

const user = ref('Guest');
const isDropdownVisible = ref(false);
const page = usePage();
const csrfStore = useCsrfStore();

const emit = defineEmits(['toggle-sidebar-fully']);


onMounted(() => {
    user.value = page.props.value.user ? page.props.value.user.name : 'Guest';
});

const emitToggleFully = () => {
    emit('toggle-sidebar-fully');
};

const toggleDropdown = () => {
    isDropdownVisible.value = !isDropdownVisible.value;
};

const hideDropdown = () => {
    isDropdownVisible.value = false;
};

const logout = () => {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/logout';
    const hiddenField = document.createElement('input');
    hiddenField.type = 'hidden';
    hiddenField.name = '_token';
    hiddenField.value = csrfStore.token;
    form.appendChild(hiddenField);
    document.body.appendChild(form);
    form.submit();
};
</script>

<script>
export default {
    directives: {
        clickOutside: {
            beforeMount(el, binding) {
                el.clickOutsideEvent = (event) => {
                    if (!(el === event.target || el.contains(event.target))) {
                        binding.value(event);
                    }
                };
                document.addEventListener('click', el.clickOutsideEvent);
            },
            unmounted(el) {
                document.removeEventListener('click', el.clickOutsideEvent);
            },
        },
    },
};
</script>
