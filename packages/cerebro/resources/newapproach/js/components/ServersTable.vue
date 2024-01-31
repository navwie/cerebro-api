<template>
    <div class="bg-white shadow-md rounded">
        <div class="border-b border-gray-200">
            <div class="bg-gray-100 text-black text-lg px-4 py-2 rounded-t-md">Servers</div>
        </div>
        <div ref="tableRef" class="p-4">
            <table class="table table-responsive-sm display" id="servers-table" style="width: 100%" @click="handleTableClick">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th><input type="search" placeholder="Name" @input="debouncedFilter(1, $event)" @click="Common.stopPropagation" /></th>
                        <th><input type="search" placeholder="IP Address" @input="debouncedFilter(2, $event)" @click="Common.stopPropagation" /></th>
                        <th>Is Active</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <ModarlServerForm :visible="isModalServerFormVisible" @close="closeModalServerForm" @formSubmitted="refreshTable" :serverId="serverId" @resetServerId="serverId = null" :mode="mode" />
    <ModalAlert :visible="Common.isModalAlert.value" :title="Common.modalAlertTitle.value" :message="Common.modalAlertMessage.value" :type="Common.modalAlertType.value" @update:visible="Common.isModalAlert.value = $event" />
    <ModalConfirmation :visible="Common.isModalConfirm.value" :title="Common.confirmationTitle.value" :message="Common.confirmationMessage.value" @confirm="Common.confirmActionCallback.value" @update:visible="Common.isModalConfirm.value = $event" />
</template>

<script setup>

import { ref, onMounted, onUnmounted } from 'vue';
import DataTable from 'datatables.net';
import axios from 'axios';
import ModalAlert from '../components/modals/Alert.vue';
import ModalConfirmation from '../components/modals/Confirmation.vue';
import ModarlServerForm from '../components/modals/ServerForm.vue';
import * as Common from './../common.js';
import { useToast } from './../services/toastService.js';

const { addToast } = useToast();
const tableRef = ref(null);
const dataTableInstance = ref(null);
const searchTimeout = ref(null);
const isModalServerFormVisible = ref(false);
const mode = ref('create');
const idToDelete = ref(null);
const serverId = ref(null);

const options = {
    serverSide: true,
    ajax: {
        url: '/servers-crud',
        type: 'GET',
    },
    lengthMenu: [5, 10, 20, 50, 100, 250, 500],
    pageLength: 10,
    dom: 'lrtip',
    buttons: [],
    processing: true,
    renderer: 'tailwindcss',
    columnDefs: [{
        targets: [0],
        visible: false,
        searchable: false,
        sortable: false,
    },
    {
        targets: 4,
        visible: true,
        searchable: false,
        sortable: false,
        render: (data, type, row, meta) => {
            const deleteIcon = '<i role="button" class="fa-regular fa-trash-can" data-id="' + row[0] + '"></i>';
            const editIcon = '<i class="fa-solid fa-pencil mr-3" role="button" data-id="' + row[0] + '"></i>';
            return editIcon + deleteIcon;
        }
    }
    ],
    scroller: {
        loadingIndicator: true
    },

    searching: true,
};

const refreshTable = () => {
    if (dataTableInstance.value) {
        dataTableInstance.value.ajax.reload(null, false);
    }
};

const initializeDataTable = () => {
    if (tableRef.value) {
        const table = tableRef.value.querySelector('#servers-table');
        dataTableInstance.value = new DataTable(table, options);
        setupCreateButton();
    }
};

const filterColumn = (columnIndex, value) => {
    dataTableInstance.value.column(columnIndex).search(value).draw();
};

const debouncedFilter = (columnIndex, event) => {
    const value = event.target.value;
    clearTimeout(searchTimeout.value);

    searchTimeout.value = setTimeout(() => {
        filterColumn(columnIndex, value);
    }, 700);
};

const handleTableClick = (event) => {
    const target = event.target;

    if (target.matches('.fa-trash-can')) {
        const id = target.getAttribute('data-id');
        deleteRow(id);
    }

    if (target.matches('.fa-pencil')) {
        const id = target.getAttribute('data-id');
        editRow(id);
    }
};

const onCreateButtonClick = () => {
    mode.value = 'create';
    isModalServerFormVisible.value = true;
};

const setupCreateButton = () => {
    const button = document.createElement('button');
    button.type = 'button';
    button.innerHTML = '<i class="fa-solid fa-circle-plus"></i> Create';
    button.classList.add('bg-blue-600', 'text-white', 'py-2', 'px-4', 'rounded', 'float-right');
    button.addEventListener('click', onCreateButtonClick);

    const usersTableLengthElement = document.querySelector('#servers-table_length');

    if (usersTableLengthElement) {
        usersTableLengthElement.appendChild(button);
        usersTableLengthElement.classList.add('w-full');
    }
};

const closeModalServerForm = () => {
    isModalServerFormVisible.value = false;
};

const confirmDelete = async () => {
    const id = idToDelete.value;

    try {
        const response = await axios.delete(`/servers-crud/${id}`);

        if (response.status !== 200) {
            throw new Error('Response was not ok');
        }

        addToast('Server deleted successfully', 'success');

        if (dataTableInstance) {
            dataTableInstance.value.ajax.reload(null, false);
        }
    } catch (error) {

        Common.showModalAlert({
            type: 'error',
            title: 'Error occurred',
            message: error.message,
        });

        console.error('Deleting error:', error);
    }

    idToDelete.value = null;
};

const deleteRow = (id) => {
    idToDelete.value = id;
    Common.showModalConfirmation({ callback: confirmDelete });
};

const editRow = (id) => {
    console.log('Edit row with id:', id);
    isModalServerFormVisible.value = true;
    serverId.value = id;
    mode.value = 'edit';
};

const cleanup = () => {
    if (searchTimeout.value) {
        clearTimeout(searchTimeout.value);
    }
};

onMounted(() => {
    initializeDataTable();
});

onUnmounted(() => {
    cleanup();
});

</script>
