<template>
    <div class="flex h-screen overflow-hidden">
        <Sidebar :isCollapsed="isSidebarCollapsed" :isFullyCollapsed="isSidebarFullyCollapsed" @toggle="toggleSidebar" />
        <div class="flex flex-col flex-1">
            <Header @toggle-sidebar-fully="toggleSidebarFully" />
            <!-- Content area -->
            <div class="flex flex-1 overflow-auto bg-gray-200">
                <main class="flex-1 transition-margin duration-300 m-4">
                    <slot></slot>
                </main>
            </div>
            <Footer />
        </div>
    </div>
    <Toast />
</template>

<script setup>

import { ref, onMounted, defineEmits } from 'vue';
import { useCsrfStore } from './stores/csrfStore';
import Header from './components/Header.vue';
import Sidebar from './components/Sidebar.vue';
import Footer from './components/Footer.vue';
import Toast from './components/Toast.vue';

const isSidebarCollapsed = ref(false);
const isSidebarFullyCollapsed = ref(false);
const csrfStore = useCsrfStore();

const props = defineProps({
    initialPage: Object,
    initialComponent: Object,
    resolveComponent: Function,
    titleCallback: Function
});

const emit = defineEmits(['headUpdate']);

const toggleSidebar = () => {
    isSidebarCollapsed.value = !isSidebarCollapsed.value;
};

const toggleSidebarFully = () => {
    isSidebarCollapsed.value = !isSidebarCollapsed.value;
    isSidebarFullyCollapsed.value = !isSidebarFullyCollapsed.value;
};

onMounted(() => {
    const metaTag = document.querySelector('meta[name="csrf"]');
    csrfStore.setToken(metaTag ? metaTag.getAttribute('content') : '');
})

</script>
