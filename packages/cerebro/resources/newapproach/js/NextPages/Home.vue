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
                <div class="flex flex-col flex-grow">
                    <TriplexSwitch label="Lead Type" id="leadType" activeColor="orange" :buttons="[
                        { text: 'Personal', value: 'personal' },
                        { text: 'All', value: 'all' },
                        { text: 'Payday', value: 'payday' }
                    ]"
                    @update:selected="formData.leadType = $event" />
                </div>
                <div class="flex flex-col flex-grow">
                    <TriplexSwitch label="Action Type" id="leadType" activeColor="blue" :buttons="[
                        { text: 'Full', value: 'full' },
                        { text: 'All', value: 'all' },
                        { text: 'Reapply', value: 'reapply' }
                    ]" 
                    @update:selected="formData.actionType = $event" />
                </div>
                <div v-if="isAdmin" class="flex flex-col flex-grow">
                    <label for="flow" class="block text-gray-700 text-sm font-bold mb-1">Flow</label>
                    <select v-model="selectedFlow" class="form-control block appearance-none w-full bg-gray-100 border border-gray-200 text-gray-700 py-2 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500" id="flow">
                        <option value="">-- Select flow --</option>
                        <option v-for="flow in flows" :key="flow.id" :value="flow.id">{{ flow.title }}</option>
                    </select>
                </div>
                <div v-if="isAdmin" class="flex flex-col flex-grow">
                    <label for="form" class="block text-gray-700 text-sm font-bold mb-1">Form</label>
                    <select v-model="selectedForm" class="form-control block appearance-none w-full bg-gray-100 border border-gray-200 text-gray-700 py-2 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500" id="form">
                        <option value="">-- Select form --</option>
                        <optgroup label="Themes">
                            <option value="lendingsource">Lendingsource</option>
                            <option value="loan5000">Loan 5000</option>
                            <option value="loan10000">Loan 10000</option>
                        </optgroup>
                        <optgroup label="Forms">
                            <option v-for="form in forms" :key="form.id" :value="form.id">{{ form.name }}</option>
                        </optgroup>
                    </select>
                </div>
                <button type="submit" :disabled="isLoading" :class="{ 'cursor-not-allowed opacity-50': isLoading }" class="pl-10 pr-10 flex items-center justify-center px-4 h-[37px] border border-gray-300 rounded hover:bg-gray-50">
                    <i :class="['fas fa-sync-alt', isLoading ? 'animate-spin' : '']"></i>
                </button>
            </form>
        </div>
    </div>
    <div :class="{ 'blur-block': isLoading }" class="grid grid-cols-1 lg:grid-cols-3 md:grid-cols-2 gap-4 mt-4">
        <InfoPanel :settings="responseData.totalIncome" />
        <InfoPanel :settings="responseData.epc" />
        <InfoPanel :settings="responseData.shortStatistic" />
        <InfoPanel :settings="responseData.redirectsRate" />
        <InfoPanel :settings="responseData.clickToSubmit" />
        <InfoPanel :settings="responseData.totalLeads" />
        <InfoPanel :settings="responseData.epl" />
        <InfoPanel :settings="responseData.soldsToSubmit" />
        <InfoPanel :settings="responseData.visitToClicks" />
        <InfoPanel :settings="responseData.denied" />
        <InfoPanel :settings="responseData.ipRisk" />
        <InfoPanel :settings="responseData.searchReapplies" />
    </div>
    <div class="mt-10 bg-white shadow-md rounded" :class="{ 'blur-block': isLoading }">
        <div class="border-b border-gray-200">
            <div class="bg-gray-100 text-black text-lg px-4 py-2 rounded-t-md">Chart</div>
        </div>
        <div ref="chartRef" class="p-4">
            <LineChart :chartData="chartData" :options="chartOptions" />
        </div>
    </div>
    <ModalAlert :visible="Common.isModalAlert.value" :title="Common.modalAlertTitle.value" :message="Common.modalAlertMessage.value" :type="Common.modalAlertType.value" />
</template>
  
<script setup>

import { ref, reactive, onMounted, computed } from 'vue';
import Datepicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import axios from 'axios';
import dayjs from 'dayjs';
import { usePage } from '@inertiajs/inertia-vue3';
import { LineChart } from 'vue-chart-3';
import InfoPanel from './../components/InfoPanel.vue';
import ModalAlert from './../components/modals/Alert.vue';
import TriplexSwitch from './../components/controls/TriplexSwitch.vue';
import * as Common from './../common.js';
import { useToast } from './../services/toastService.js';
import { Chart, registerables } from 'chart.js';
Chart.register(...registerables);

const { addToast } = useToast();
const page = usePage();
const isLoading = ref(false);
const isAdmin = ref(false);
const selectedFlow = ref('');
const selectedForm = ref('');
const flows = ref([]);
const forms = ref([]);

const selectedPeriod = ref('today');
const dateFormat = ref('MM/DD/YYYY');

const formData = reactive({
    start: new Date(),
    stop: new Date(),
    leadType: 'all',
    actionType: 'all',
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
    totalIncome: { title: 'Total Income Statistic', values: [] },
    epc: { title: 'Avg. EPC', values: [] },
    shortStatistic: { title: 'Short Statistic', values: [] },
    redirectsRate: { title: 'Redirects Rate', values: [] },
    clickToSubmit: { title: 'Click To Submit', values: [] },
    totalLeads: { title: 'Total Leads', values: [] },
    epl: { title: 'EPL', values: [] },
    soldsToSubmit: { title: 'Solds To Submit', values: [] },
    visitToClicks: { title: 'Visit To Clicks', values: [] },
    denied: { title: 'Denied', values: [] },
    ipRisk: { title: 'IP Risk', values: [] },
    searchReapplies: { title: 'Search Reapplies', values: [] },
});

const processData = (data) => {
    return {
        totalIncome: {
            title: 'Total Income Statistic',
            values: [{ value: data.total_income, description: 'Selected period percentage' }]
        },
        epc: {
            title: 'Avg. EPC',
            values: [
                { value: data.epc.all_time, description: 'All time' },
                { value: data.epc.period, description: 'Selected period' },
            ]
        },
        shortStatistic: {
            title: 'Short Statistic',
            values: [
                { value: data.unique_visits, description: 'Unique clicks' },
                { value: data.unique_visits, description: 'Total clicks' },
                { value: data.total_earnings, description: 'Total earnings' },
            ]
        },
        redirectsRate: {
            title: 'Redirects Rate',
            values: [
                { value: data.redirectRate, description: 'Selected period percentage' },
                { value: data.redirects, description: 'Selected period absolute value' },
            ]
        },
        clickToSubmit: {
            title: 'Click To Submit',
            values: [
                { value: data.clicks_to_sub, description: 'Clicks' },
                { value: 0, description: 'Submits' },
                { value: '- %', description: 'Clicks to submits' },
            ]
        },
        totalLeads: {
            title: 'Total Leads',
            values: [{ value: data.total_leads, description: 'Selected period' }]
        },
        epl: {
            title: 'Avg. EPC',
            values: [
                { value: data.epl.with, description: 'With' },
                { value: data.epl.without, description: 'Without' },
            ]
        },
        soldsToSubmit: {
            title: 'Solds To Submit',
            values: [
                { value: data.sold_to_submit, description: 'Submits' },
                { value: data.solds_in_period, description: 'Solds' },
                { value: '- %', description: 'Solds To Submits' },
            ]
        },
        visitToClicks: {
            title: 'Visit To Clicks',
            values: [
                { value: data.visits_in_period, description: 'Visits' },
                { value: data.visits_to_clicks, description: 'Clicks' },
                { value: '- %', description: 'Visit To Clicks' },
            ]
        },
        denied: {
            title: 'Denied',
            values: [
                { value: data.denied_total, description: 'Total' },
                { value: data.dnm_errors, description: 'Errors from DNM' },
            ]
        },
        ipRisk: {
            title: 'IP Risk',
            values: [
                { value: data.average_risk, description: 'Average Risk' },
                { value: data.risk_submits, description: 'Risk Submits' },
            ]
        },
        searchReapplies: {
            title: 'Search Reapplies',
            values: [
                { value: data.search_reapply_email_total, description: 'Email Total' },
                { value: data.search_reapply_email_found, description: 'Email Found' },
                { value: data.search_reapply_phone_total, description: 'Phone Total' },
                { value: data.search_reapply_phone_found, description: 'Phone Found' },
            ]
        },
    };
};

const handleSubmit = () => {
    fetchData(true);
    fetchChart();
};

const fetchData = async (showToast = false) => {
    try {
        isLoading.value = true;
    
        const start = dayjs(formData.start).format(dateFormat.value);
        const stop = dayjs(formData.stop).format(dateFormat.value);

        const response = await axios.get('/dashboard', {
            params: {
                start: start,
                stop: stop,
                leadType: formData.leadType,
                actionType: formData.actionType,
                formId: selectedForm.value,
                flowId: selectedFlow.value
            }
        });

        responseData.value = processData(response.data);

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

const chartData = ref({
    datasets: [
        {
            label: 'visitors to submit',
            backgroundColor: 'transparent',
            borderColor: 'red',
            pointBackgroundColor: 'red',
            pointBorderColor: 'red',
            //cubicInterpolationMode: 'monotone',
            tension: 0.4

        }, {
            label: 'solds to submit',
            backgroundColor: 'transparent',
            borderColor: 'green',
            pointBackgroundColor: 'green',
            pointBorderColor: 'green',
            cubicInterpolationMode: 'monotone',
            tension: 0.4

        }, {
            label: 'redirect rates',
            backgroundColor: 'transparent',
            borderColor: 'blue',
            pointBackgroundColor: 'blue',
            pointBorderColor: 'blue',
            cubicInterpolationMode: 'monotone',
            tension: 0.4
        }
    ],
});

const chartOptions = ref({
    responsive: true,
    maintainAspectRatio: false,
    scales: {
        y: {
            suggestedMin: 0,
            suggestedMax: 100
        }
    }
});

const fetchChart = async () => {
    try {
        isLoading.value = true;
        const start = dayjs(formData.start).format(dateFormat.value);
        const stop = dayjs(formData.stop).format(dateFormat.value);

        const response = await axios.get('/dashboard-chart', {
            params: {
                start: start,
                stop: stop,
                leadType: formData.leadType,
                actionType: formData.actionType,
                formId: selectedForm.value,
                flowId: selectedFlow.value
            }
        });

        chartData.value.labels = response.data.visitors.labels;
        chartData.value.datasets[0].data = response.data.visitors.data;
        chartData.value.datasets[1].data = response.data.submits.data;
        chartData.value.datasets[2].data = response.data.redirects.data;

    } catch (error) {
        Common.showModalAlert({
            type: 'error',
            title: 'Error occurred',
            message: error.message,
        });
        console.error('Error fetching chart data:', error);
    } finally {
        isLoading.value = false;
    }
};

onMounted(() => {
    isAdmin.value = !!page.props.value.user.roles.find(x => x.name === 'admin');
    flows.value = page.props.value.flows || [];
    forms.value = page.props.value.forms || [];
    fetchData();
    fetchChart();
});

</script>
