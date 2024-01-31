<template>
    <div class="bg-white shadow-md rounded">
        <div class="border-b border-gray-200">
            <div class="bg-gray-100 text-black text-lg px-4 py-2 rounded-t-md">Forms</div>
        </div>
        <div ref="tableRef" class="p-4 ">
            <table class="table table-responsive-sm display" id="users-table" style="width: 100%;" @click="handleTableClick">
                <thead>
                    <tr>
                        <th>id</th>
                        <th><input type="search" placeholder="Name" @input="debouncedFilter(1, $event)" @click="Common.stopPropagation" /></th>
                        <th><input type="search" placeholder="Email" @input="debouncedFilter(2, $event)" @click="Common.stopPropagation" /></th>
                        <th><input type="search" placeholder="Post back amount" @input="debouncedFilter(3, $event)" @click="Common.stopPropagation" /></th>
                        <th><input type="search" placeholder="Personal min req" @input="debouncedFilter(4, $event)" @click="Common.stopPropagation" /></th>
                        <th>Actions</th>
                        <th>email_verified_at</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <ModalUserForm :visible="isModalUserFormVisible" @close="closeModalUserForm" @formSubmitted="refreshTable" :userId="userId" @resetUserId="userId = null" :mode="mode" />
    <ModalAlert :visible="Common.isModalAlert.value" :title="Common.modalAlertTitle.value" :message="Common.modalAlertMessage.value" :type="Common.modalAlertType.value" @update:visible="Common.isModalAlert.value = $event" />
    <ModalConfirmation :visible="Common.isModalConfirm.value" :title="Common.confirmationTitle.value" :message="Common.confirmationMessage.value" @confirm="Common.confirmActionCallback.value" @update:visible="Common.isModalConfirm.value = $event" />
</template>

<script setup>

import { ref, onMounted, onUnmounted } from 'vue';
import DataTable from 'datatables.net';
import { usePage } from '@inertiajs/inertia-vue3';
import axios from 'axios';
import ModalAlert from '../components/modals/Alert.vue';
import ModalConfirmation from '../components/modals/Confirmation.vue';
import ModalUserForm from '../components/modals/UserForm.vue';
import * as Common from './../common.js';
import { useToast } from './../services/toastService.js';
const { addToast } = useToast();

const page = usePage();
const tableRef = ref(null);
const dataTableInstance = ref(null);
const searchTimeout = ref(null);
const isModalUserFormVisible = ref(false);
const idToDelete = ref(null);
const mode = ref('create');
const userId = ref(null);

const options = {
    serverSide: true,
    ajax: {
        url: '/users',
        type: 'GET',
        dataSrc: 'data',
    },
    lengthMenu: [5, 10, 20, 50, 100, 250, 500],
    pageLength: 10,
    dom: 'lrtip',
    buttons: [],
    processing: true,
    renderer: 'tailwindcss',
    columnDefs: [{
        targets: [0, 6],
        visible: false,
        searchable: false,
        sortable: false,
    },
    {
        targets: 2,
        render: (data, type, row, meta) => {
            let returnData = data;
            returnData += (row[6] == null) ? '' : ' <i class="fa-regular fa-circle-check"></i>';
            return returnData;
        }
    },
    {
        targets: 5,
        sortable: false,
        render: (data, type, row, meta) => {
            const deleteIcon = (page.props.value.user.id == row[0]) ? ("") : ('<i role="button" class="fa-regular fa-trash-can" data-id="' + row[0] + '"></i>');
            const editIcon = '<i class="fa-solid fa-pencil mr-3" role="button" data-id="' + row[0] + '"></i>';
            return editIcon + deleteIcon;
        }
    },
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
        const table = tableRef.value.querySelector('#users-table');
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

const setupCreateButton = () => {
    const button = document.createElement('button');

    button.type = 'button';
    button.innerHTML = '<i class="fa-solid fa-circle-plus"></i> Create';
    button.classList.add('bg-blue-600', 'text-white', 'py-2', 'px-4', 'rounded', 'float-right');
    button.addEventListener('click', onCreateButtonClick);

    const usersTableLengthElement = document.querySelector('#users-table_length');
 
    if (usersTableLengthElement) {
        usersTableLengthElement.appendChild(button);
        usersTableLengthElement.classList.add('w-full');
    }
};

const onCreateButtonClick = () => {
    mode.value = 'create';
    isModalUserFormVisible.value = true;
};

const closeModalUserForm = () => {
    isModalUserFormVisible.value = false;
};

const confirmDelete = async () => {
    const id = idToDelete.value;

    try {
        const response = await axios.delete(`/users/${id}`);

        if (response.data.status !== 200) {
            throw new Error('Response was not ok');
        }

        addToast('User deleted successfully', 'warning');

        if (dataTableInstance) {
            dataTableInstance.value.ajax.reload(null, false);
        }
    } catch (error) {
        console.error('Deleting error:', error);

        Common.showModalAlert({
            type: 'error',
            title: 'Error occurred',
            message: error.message,
        });

    }

    idToDelete.value = null;
};

const deleteRow = (id) => {
    idToDelete.value = id;
    Common.showModalConfirmation({ callback: confirmDelete });
};

const editRow = (id) => {
    console.log('Edit row with id:', id);
    isModalUserFormVisible.value = true;
    userId.value = id;
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
