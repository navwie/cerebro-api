<template>
    <div class="bg-white shadow-md mt-5 rounded">
        <div class="border-b border-gray-200">
            <div class="bg-gray-100 text-black text-lg px-4 py-2 rounded-t-md">Sites</div>
        </div>
        <div ref="tableRef" class="p-4">
            <table class="table table-responsive-sm display" id="servers-table" style="width: 100%">
                <thead>
                    <tr>
                        <th><input type="search" placeholder="Name" @click="Common.stopPropagation" /></th>
                        <th><input type="search" placeholder="Domain" @click="Common.stopPropagation" /></th>
                        <th>Total clicks</th>
                        <th>Uniq clicks</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</template>

<script setup>

import { ref, onMounted, onUnmounted, watch } from 'vue';
import DataTable from 'datatables.net';
import * as Common from './../common.js';

const tableRef = ref(null);
const dataTableInstance = ref(null);
const searchTimeout = ref(null);

const props = defineProps({
    items: Array
});

const options = {
    lengthMenu: [5, 10, 20, 50, 100, 250, 500],
    pageLength: 10,
    dom: 'lBrtip',
    processing: true,
    stateSave: false,
    columnDefs: [
        { sortable: true, targets: [0, 1, 2, 3] },
    ],
    scroller: {
        loadingIndicator: true
    },
    data: props.items,
    columns: [
        { data: 'name' },
        {
            data: 'domain_name',
            fnCreatedCell: (nTd, sData) => {
                let link = document.createElement('a');
                link.href = `//${sData}`;
                link.target = '_blank';
                link.textContent = sData;

                nTd.innerHTML = '';
                nTd.appendChild(link);
            }
        },
        { data: 'total_clicks' },
        { data: 'uniq_clicks' }
    ],
};

const updateDataTable = (newData) => {
    if (dataTableInstance.value) {
        dataTableInstance.value.clear();
        dataTableInstance.value.rows.add(newData);
        dataTableInstance.value.draw();
    }
};

const initializeDataTable = () => {
    if (tableRef.value) {
        const table = tableRef.value.querySelector('#servers-table');
        dataTableInstance.value = new DataTable(table, options);

        const headerInputs = tableRef.value.querySelectorAll('thead input[type="search"]');

        headerInputs.forEach((input, index) => {
            const handleFilterInput = (event) => {
                if (searchTimeout.value) {
                    clearTimeout(searchTimeout.value);
                }
                searchTimeout.value = setTimeout(() => {
                    dataTableInstance.value.column(index).search(event.target.value).draw();
                }, 300);
            };

            input.addEventListener('keyup', handleFilterInput);
            input.addEventListener('change', handleFilterInput);

            input.addEventListener('input', (event) => {
                if (event.target.value === '') {
                    handleFilterInput(event);
                }
            });
        });
    }
};

const cleanup = () => {
    if (searchTimeout.value) {
        clearTimeout(searchTimeout.value);
    }
};

onMounted(() => {
    initializeDataTable()
});

onUnmounted(() => {
    cleanup();
});

watch(() => props.items, (newVal) => {
    updateDataTable(newVal);
});

</script>