<template>
    <div class="bg-white shadow-md rounded">
        <div class="border-b border-gray-200">
            <div class="bg-gray-100 text-black text-lg px-4 py-2 rounded-t-md">Filter</div>
        </div>
        <div ref="filterRef" class="p-4">
            <form @submit.prevent="handleSubmit" class="flex items-end space-x-4">
                <div class="flex flex-col flex-grow">
                    <label for="start" class="mb-2 text-sm font-medium text-gray-900">From</label>
                    <Datepicker id="start" v-model="formData.start" :enable-time-picker="false" auto-apply autocomplete="off" placeholder="Select start date" />
                </div>
                <div class="flex flex-col flex-grow">
                    <label for="stop" class="mb-2 text-sm font-medium text-gray-900">To</label>
                    <Datepicker id="stop" v-model="formData.stop" :enable-time-picker="false" auto-apply autocomplete="off" placeholder="Select end date" />
                </div>
                <div class="flex flex-col flex-grow">
                    <label for="period" class="block text-gray-700 text-sm font-bold mb-1">Period</label>
                    <select v-model="selectedPeriod" @change="updatePeriod" class="form-control block appearance-none w-full bg-gray-100 border border-gray-200 text-gray-700 py-2 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500" id="period">
                        <option v-for="(option, index) in periodOptions" :key="index" :data-from="option.from" :data-to="option.to">
                            {{ option.text }}
                        </option>
                    </select>
                </div>
                <div v-if="isAdmin" class="flex flex-col flex-grow">
                    <label for="flow" class="block text-gray-700 text-sm font-bold mb-1">Form</label>
                    <select v-model="selectedForm" class="form-control block appearance-none w-full bg-gray-100 border border-gray-200 text-gray-700 py-2 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500" id="flow">
                        <option value="">-- Select form --</option>
                        <option v-for="form in forms" :key="form.id" :value="form.id">{{ form.name }}</option>
                    </select>
                </div>
                <button type="submit" :disabled="isLoading" :class="{ 'cursor-not-allowed opacity-50': isLoading }" class="pl-10 pr-10 flex items-center justify-center px-4 h-[37px] border border-gray-300 rounded hover:bg-gray-50">
                    <i :class="['fas fa-sync-alt', isLoading ? 'animate-spin' : '']"></i>
                </button>
            </form>
        </div>
    </div>
    <div :class="{ 'blur-block': isLoading }" class="grid grid-cols-1 lg:grid-cols-3 md:grid-cols-2 gap-4 mt-4">
        <InfoPanel :settings="responseData.clicks" />
        <InfoPanel :settings="responseData.visitsToSubmits" />
        <InfoPanel :settings="responseData.shortStatistic" />
    </div>
    <div :class="{ 'blur-block': isLoading }">
        <CardsTable :items="items" />
    </div>
    <ModalAlert :visible="Common.isModalAlert.value" :title="Common.modalAlertTitle.value" :message="Common.modalAlertMessage.value" :type="Common.modalAlertType.value" />
</template>
  
<script setup>

import { ref, reactive, onMounted, computed } from 'vue';
import axios from 'axios';
import dayjs from 'dayjs';

import Datepicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import InfoPanel from './../components/InfoPanel.vue';
import ModalAlert from './../components/modals/Alert.vue';
import CardsTable from './../components/CardsTable.vue';
import * as Common from './../common.js';
import { usePage } from '@inertiajs/inertia-vue3';
import { useToast } from './../services/toastService.js';

const { addToast } = useToast();
const page = usePage();
const isLoading = ref(false);
const isAdmin = ref(false);
const selectedForm = ref('');
const forms = ref([]);
const items = ref([]);

const selectedPeriod = ref('today');
const dateFormat = ref('MM/DD/YYYY');

const formData = reactive({
    start: new Date(),
    stop: new Date(),
});

const periodOptions = computed(() => {
    return [
        {
            text: 'Today',
            from: dayjs().startOf('day').format(dateFormat.value),
            to: dayjs().endOf('day').format(dateFormat.value),
        },
        {
            text: 'Yesterday',
            from: dayjs().subtract(1, 'day').startOf('day').format(dateFormat.value),
            to: dayjs().subtract(1, 'day').endOf('day').format(dateFormat.value),
        },
        {
            text: 'This week',
            from: dayjs().startOf('week').format(dateFormat.value),
            to: dayjs().endOf('week').format(dateFormat.value),
        },
        {
            text: 'Last week',
            from: dayjs().subtract(1, 'week').startOf('week').format(dateFormat.value),
            to: dayjs().subtract(1, 'week').endOf('week').format(dateFormat.value),
        },
        {
            text: 'This month',
            from: dayjs().startOf('month').format(dateFormat.value),
            to: dayjs().endOf('month').format(dateFormat.value),
        },
        {
            text: 'Last month',
            from: dayjs().subtract(1, 'month').startOf('month').format(dateFormat.value),
            to: dayjs().subtract(1, 'month').endOf('month').format(dateFormat.value),
        },
        {
            text: 'This year',
            from: dayjs().startOf('year').format(dateFormat.value),
            to: dayjs().endOf('year').format(dateFormat.value),
        },
        {
            text: 'Last year',
            from: dayjs().subtract(1, 'year').startOf('year').format(dateFormat.value),
            to: dayjs().subtract(1, 'year').endOf('year').format(dateFormat.value),
        }
    ];
});

const updatePeriod = () => {
    const period = periodOptions.value.find(p => p.text === selectedPeriod.value);
    if (period) {
        formData.start = dayjs(period.from, dateFormat.value).toDate();
        formData.stop = dayjs(period.to, dateFormat.value).toDate();
    }
};

const responseData = ref({
    clicks: { title: 'Clicks', values: [] },
    visitsToSubmits: { title: 'Visits to Submits', values: [] },
    shortStatistic: { title: 'Short Statistic', values: [] },
});

const processData = (data) => {

    return {
        clicks: {
            title: 'Clicks',
            values: [
                { value: data.uniqueClicksPeriod, description: 'Clicks' },
                { value: data.totalClicksPeriod, description: 'Unique Clicks' },
            ]
        },
        visitsToSubmits: {
            title: 'Visits to Submits',
            values: [
                { value: data.visits, description: 'Visits' },
                { value: data.submits, description: 'Submits' },
                { value: data.visitsToSubmits, description: 'Visit To Submits' },
            ]
        },
        shortStatistic: {
            title: 'Short Statistic',
            values: [
                { value: data.uniqueClicks, description: 'Total Unique Clicks' },
                { value: data.totalClicks, description: 'Total Clicks' },
            ]
        },

    };
};

const handleSubmit = () => {
    fetchData(true);
};

const fetchData = async (showToast = false) => {
    try {
        isLoading.value = true;

        const start = dayjs(formData.start).format(dateFormat.value);
        const stop = dayjs(formData.stop).format(dateFormat.value);

        const response = await axios.get('/get_cards', {
            params: {
                start: start,
                stop: stop,
                formId: selectedForm.value,
            }
        });

        responseData.value = processData(response.data);
        items.value = response.data.items;

        if (showToast) {
            addToast('Page updated');
        }
    } catch (error) {
        Common.showModalAlert({
            type: 'error',
            title: 'Error occurred',
            message: error.message,
        });
        console.error('Error fetching data:', error);
    } finally {
        isLoading.value = false;
    }
};

onMounted(() => {
    isAdmin.value = !!page.props.value.user.roles.find(x => x.name === 'admin');
    forms.value = page.props.value.forms || [];
    fetchData();
});

</script>
  
  