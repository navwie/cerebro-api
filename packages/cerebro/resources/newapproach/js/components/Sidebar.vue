<template>
    <aside :class="[
        isFullyCollapsed ? 'w-0 transition-all duration-500' : isCollapsed ? 'w-[56px]' : 'w-64',
        'flex flex-col justify-between h-full bg-slate-700 text-white transition-all duration-300'
    ]">
        <div class="bg-slate-800 flex justify-center items-center">
            <img :src="'/assets/brand/cerebroLogo.svg'" alt="" class="h-[80px] w-[80px] mt-[15px] mb-[15px]">
        </div>
        <nav :class="[isFullyCollapsed ? 'hidden' : 'flex-1 relative']">
            <ul class="flex flex-col">
                <li :class="{ 'bg-slate-600': isActive('/home') }" class="group">
                    <a href="/home" class="flex items-center p-3 h-[51px] relative hover:bg-blue-800 hover:text-white">
                        <i class="fas fa-tachometer ml-2 w-6"></i>
                        <span v-if="!isCollapsed" class="ml-3 whitespace-nowrap">Loan dashboard</span>
                        <!-- Tooltip -->
                        <div v-if="isCollapsed" class="absolute left-0 hidden group-hover:flex z-50 items-center h-full">
                            <div class="whitespace-nowrap bg-blue-800 text-white shadow-md rounded-l py-1 h-full w-64 flex items-center">
                                <i class="fas fa-tachometer ml-5 w-6"></i>
                                <span class="ml-3"> Loan dashboard</span>
                            </div>
                        </div>
                    </a>
                </li>
                <li :class="{ 'bg-slate-600': isActive('/cards') }" class="group">
                    <a href="/cards" class="flex items-center p-3 h-[51px] relative hover:bg-blue-800 hover:text-white">
                        <i class="fa-regular fa-credit-card ml-2 w-6"></i>
                        <span v-if="!isCollapsed" class="ml-3 whitespace-nowrap">Cards dashboard</span>
                        <!-- Tooltip -->
                        <div v-if="isCollapsed" class="absolute left-0 hidden group-hover:flex z-50 items-center h-full">
                            <div class="whitespace-nowrap bg-blue-800 text-white shadow-md rounded-l py-1 h-full w-64 flex items-center">
                                <i class="fa-regular fa-credit-card ml-5 w-6"></i>
                                <span class="ml-3"> Cards dashboard </span>
                            </div>
                        </div>
                    </a>
                </li>
                <li :class="{ 'bg-slate-600': isActive('/statistic') }" class="group">
                    <a href="/statistic" class="flex items-center p-3 h-[51px] relative hover:bg-blue-800 hover:text-white">
                        <i class="fa-solid fa-chart-simple ml-2 w-6"></i>
                        <span v-if="!isCollapsed" class="ml-3">Statistics</span>
                        <!-- Tooltip -->
                        <div v-if="isCollapsed" class="absolute left-0 hidden group-hover:flex z-50 items-center h-full">
                            <div class="whitespace-nowrap bg-blue-800 text-white shadow-md rounded-l py-1 h-full w-64 flex items-center">
                                <i class="fa-solid fa-chart-simple ml-5 w-6"></i>
                                <span class="ml-3"> Statistics </span>
                            </div>
                        </div>
                    </a>
                </li>
                <li :class="{ 'bg-slate-600': isActive('/forms') }" class="group">
                    <a href="/forms" class="flex items-center p-3 h-[51px] relative hover:bg-blue-800 hover:text-white">
                        <i class="fa-regular fa-file-lines ml-2 w-6"></i>
                        <span v-if="!isCollapsed" class="ml-3">Forms</span>
                        <!-- Tooltip -->
                        <div v-if="isCollapsed" class="absolute left-0 hidden group-hover:flex z-50 items-center h-full">
                            <div class="whitespace-nowrap bg-blue-800 text-white shadow-md rounded-l py-1 h-full w-64 flex items-center">
                                <i class="fa-regular fa-file-lines ml-5 w-6"></i>
                                <span class="ml-3"> Forms </span>
                            </div>
                        </div>
                    </a>
                </li>
                <li :class="{ 'bg-slate-600': isActive('/sites') }" class="group">
                    <a href="/sites" class="flex items-center p-3 h-[51px] relative hover:bg-blue-800 hover:text-white">
                        <i class="fa-solid fa-network-wired  ml-2 w-6"></i>
                        <span v-if="!isCollapsed" class="ml-3">Sites</span>
                        <!-- Tooltip -->
                        <div v-if="isCollapsed" class="absolute left-0 hidden group-hover:flex z-50 items-center h-full">
                            <div class="whitespace-nowrap bg-blue-800 text-white shadow-md rounded-l py-1 h-full w-64 flex items-center">
                                <i class="fa-solid fa-network-wired  ml-5 w-6"></i>
                                <span class="ml-3"> Sites </span>
                            </div>
                        </div>
                    </a>
                </li>
                <li :class="{ 'bg-slate-600': isActive('/servers') }" class="group">
                    <a href="/servers" class="flex items-center p-3 h-[51px] relative hover:bg-blue-800 hover:text-white">
                        <i class="fa-solid fa-gear ml-2 w-6"></i>
                        <span v-if="!isCollapsed" class="ml-3">Servers</span>
                        <!-- Tooltip -->
                        <div v-if="isCollapsed" class="absolute left-0 hidden group-hover:flex z-50 items-center h-full">
                            <div class="whitespace-nowrap bg-blue-800 text-white shadow-md rounded-l py-1 h-full w-64 flex items-center">
                                <i class="fa-solid fa-gear ml-5 w-6"></i>
                                <span class="ml-3"> Servers </span>
                            </div>
                        </div>
                    </a>
                </li>
            </ul>
        </nav>
        <div :class="[isFullyCollapsed ? 'hidden' : 'p-4', 'bg-slate-80 bg-slate-800 cursor-pointer hover:bg-slate-900 hover:text-white']" @click="toggle">
            <button class="w-full flex justify-end">
                <svg :class="isCollapsed ? 'transform rotate-180' : ''" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-6 w-6">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
        </div>
    </aside>
</template>
  
<script setup>
import { ref, defineProps, defineEmits } from 'vue';

const props = defineProps({
    isCollapsed: null,
    isFullyCollapsed: null
});

const currentPath = ref(window.location.pathname);

const isActive = (basePath) => {
    const isRoot = currentPath.value === '/' && basePath === '/home';
    return isRoot || currentPath.value.startsWith(basePath);
};

const emit = defineEmits(['toggle', 'toggle-full']);

const toggle = () => {
    emit('toggle');
};

</script>
