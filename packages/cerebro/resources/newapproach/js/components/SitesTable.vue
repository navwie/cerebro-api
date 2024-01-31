<template>
    <div class="bg-white shadow-md rounded">
        <div class="border-b border-gray-200">
            <div class="bg-gray-100 text-black text-lg px-4 py-2 rounded-t-md">Sites</div>
        </div>
        <div ref="tableRef" class="p-4 bootstrap-5">
            <table class="table table-responsive-sm display" id="sites-table" style="width: 100%;" @click="handleTableClick">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th><input type="search" placeholder="Domain Name" @input="debouncedFilter(1, $event)" @click="Common.stopPropagation" /></th>
                        <th><input type="search" placeholder="Form Name" @input="debouncedFilter(2, $event)" @click="Common.stopPropagation" /></th>
                        <th><input type="search" placeholder="Server" @input="debouncedFilter(3, $event)" @click="Common.stopPropagation" /></th>
                        <th><input type="search" placeholder="Theme" @input="debouncedFilter(4, $event)" @click="Common.stopPropagation" /></th>
                        <th>Certificate</th>
                        <th>CertStatus</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <ModalHtmlContent :visible="Common.isModalHtml.value" :title="Common.modalHtmlTitle.value" :message="Common.modalHtmlMessage.value" @update:visible="Common.isModalHtml.value = $event" />
    <ModalAlert :visible="Common.isModalAlert.value" :title="Common.modalAlertTitle.value" :message="Common.modalAlertMessage.value" :type="Common.modalAlertType.value" @update:visible="Common.isModalAlert.value = $event" />
    <ModalConfirmation :visible="Common.isModalConfirm.value" :title="Common.confirmationTitle.value" :message="Common.confirmationMessage.value" @confirm="Common.confirmActionCallback.value" @update:visible="Common.isModalConfirm.value = $event" />
</template>

<script setup>

import { ref, onMounted, onUnmounted } from 'vue';
import DataTable from 'datatables.net';
import axios from 'axios';
import ModalAlert from '../components/modals/Alert.vue';
import ModalHtmlContent from '../components/modals/HtmlContent.vue';
import ModalConfirmation from '../components/modals/Confirmation.vue';
import * as Common from './../common.js';
import { useToast } from './../services/toastService.js';

const { addToast } = useToast();
const tableRef = ref(null);
const dataTableInstance = ref(null);
const searchTimeout = ref(null);
const siteId = ref(null);

const options = {
    serverSide: true,
    ajax: {
        url: '/sites-crud',
        type: 'GET',
    },
    lengthMenu: [5, 10, 20, 50, 100, 250, 500],
    pageLength: 10,
    dom: 'lBrtip',
    buttons: [],
    processing: true,
    renderer: 'tailwindcss',
    columnDefs: [
        {
            targets: [0],
            visible: false
        },
        {
            targets: [1],
            visible: true,
            sortable: true,
            searchable: true,
            render: (data, type, row) => {
                return `
                    <span class="inline-flex items-center text-blue-500 hover:text-blue-700 hover:underline">
                        <a href="//${data}" target="_blank" >${data} </a>
                    </span>
                    <i class="fas fa-copy cursor-pointer text-blue-500 copy-domain-name"></i>
                `;
            }
        },
        {
            targets: [5],
            visible: true,
            sortable: false,
            searchable: false,
            width: '18%',
            render: (data, type, row) => {
                if (row[5] === 1) {
                    return `<button class="px-4 py-2 text-sm font-medium text-green-700 bg-white border border-green-500 rounded hover:bg-green-50 disabled:opacity-50" disabled>
                                SSL-READY
                            </button>`;
                }
                if (row[7] === '' || row[7] === null) {
                    return `<button class="request-cert px-4 py-2 text-sm font-medium text-red-700 bg-white border border-red-500 rounded hover:bg-red-50" data-site-id="${row[0]}">
                                Request Cert
                            </button>`;
                }

                if (row[7] !== '' || row[7] !== null) {
                    return `<button class="get-cname px-4 py-2 text-sm font-medium text-blue-700 bg-white border border-blue-500 rounded hover:bg-blue-50" data-site-id="${row[0]}">
                                Get CNAME Data
                            </button>`;
                }
            }
        },
        {
            targets: [6],
            visible: true,
            sortable: true,
            searchable: true
        },
        {
            targets: [7],
            visible: true,
            sortable: false,
            searchable: false,
            render: (data, type, row, meta) => {
                const deleteButton = '<i role="button" class="fa-regular fa-trash-can" data-id="' + row[0] + '"></i>';
                const editButton = '<a href="/sites/' + row[0] + '"><i class="fa-solid fa-pencil mr-3" data-id="' + row[0] + '"></i></a>';
                return editButton + deleteButton;
            }
        }
    ],
    scroller: {
        loadingIndicator: true
    },

    searching: true,
    drawCallback: () => {
        document.querySelectorAll('.copy-domain-name').forEach((el) => {
            el.addEventListener('click', () => {
                // console.log(el.previousElementSibling)
                const domain = el.previousElementSibling.textContent?.trim();
                navigator.clipboard.writeText(domain);
                addToast('Domain Name copied.');
            });
        })
    }
};

const refreshTable = () => {
    if (dataTableInstance.value) {
        dataTableInstance.value.ajax.reload(null, false);
    }
};

const initializeDataTable = () => {
    if (tableRef.value) {
        const table = tableRef.value.querySelector('#sites-table');
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

    if (target.matches('.request-cert')) {
        const siteId = target.getAttribute('data-site-id');
        requestCert(siteId);
    }

    if (target.matches('.get-cname')) {
        const siteId = target.getAttribute('data-site-id');
        getCnameData(siteId);
    }
};

const setupCreateButton = () => {
    const dropdownContainer = document.createElement('div');
    dropdownContainer.classList.add('relative', 'float-right');

    dropdownContainer.innerHTML = `
        <button class="dropdown-button bg-blue-600 text-white py-2 px-4 rounded inline-flex items-center focus:outline-none focus:shadow-outline" type="button">
            <span> Create </span>
            <i class="fas fa-caret-down ml-2"></i>
        </button>
        <div class="dropdown-menu absolute hidden text-gray-700 pt-1 w-[7rem] bg-white border rounded shadow-lg">
            <a class="dropdown-item rounded-t bg-white hover:bg-gray-100 py-2 px-4 block whitespace-no-wrap" href="#" data-option="loan">Loan Site</a>
            <a class="dropdown-item rounded-b bg-white hover:bg-gray-100 py-2 px-4 block whitespace-no-wrap" href="#" data-option="card">Card Site</a>
        </div>
    `;

    const sitesTableLengthElement = document.querySelector('#sites-table_length');
    if (sitesTableLengthElement) {
        sitesTableLengthElement.appendChild(dropdownContainer);
        sitesTableLengthElement.classList.add('w-full');

        setupDropdown(dropdownContainer);
    }
};

const setupDropdown = (dropdownContainer) => {
    const dropdownButton = dropdownContainer.querySelector('.dropdown-button');
    const dropdownMenu = dropdownContainer.querySelector('.dropdown-menu');

    dropdownButton.addEventListener('click', () => {
        dropdownMenu.classList.toggle('hidden');
    });

    window.addEventListener('click', (event) => {
        if (!dropdownContainer.contains(event.target)) {
            dropdownMenu.classList.add('hidden');
        }
    });

    const loanOption = dropdownContainer.querySelector('a[data-option="loan"]');
    const cardOption = dropdownContainer.querySelector('a[data-option="card"]');

    loanOption.addEventListener('click', (event) => {
        event.preventDefault();
        onCreateButtonClick('loan');
    });

    cardOption.addEventListener('click', (event) => {
        event.preventDefault();
        onCreateButtonClick('card');
    });
};

const onCreateButtonClick = (type) => {
    window.location.href = `${window.location.protocol}//${window.location.host}/sites/create?type=${type}`;
};

const requestCert = async (siteId) => {
    try {
        const response = await axios.post(`/sites/${siteId}`);

        if (response.data.status === 200) {
            Common.showModalAlert({
                type: 'success',
                title: 'Success',
                message: 'Certificate has been requested successfully!',
            });
            if (dataTableInstance.value) {
                dataTableInstance.value.ajax.reload();
            }
        } else if (response.data.status === 500) {
            Common.showModalAlert({
                type: 'error',
                title: 'Error occurred',
                message: response.data.message,
            });
        }
    } catch (error) {
        Common.showModalAlert({
            type: 'error',
            title: 'Error occurred',
            message: error.message,
        });
        console.error(error);
    }
};

const getCnameData = async (siteId) => {
    try {
        const response = await axios.post(`/cname/${siteId}`);

        if (response.data.status === 200) {
            updateCertificateModal(response.data);

        } else if (response.data.status === 500) {
            Common.showModalAlert({
                type: 'warning',
                title: 'Information',
                message: response.data.message,
            });
        }
    } catch (error) {
        Common.showModalAlert({
            type: 'error',
            title: 'Error occurred',
            message: error.message,
        });
        console.error(error);
    }
};

const updateCertificateModal = async (data) => {
    let message = '';
    for (const el of data.data) {
        message += `
            <div class="flex flex-col mt-5">
                <div class="flex items-center mb-4">
                    <label for="cnameName" class="block text-sm font-medium text-gray-700">CNAME Name:</label>
                    <span class="cnameName ml-2 text-sm font-medium text-gray-900">${el.name}</span>
                    <button class="copyCnameName ml-2 text-sm" type="button">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
                <div class="flex items-center">
                    <label for="cnameValue" class="block text-sm font-medium text-gray-700">CNAME Value:</label>
                    <span class="cnameValue ml-2 text-sm font-medium text-gray-900">${el.value}</span>
                    <button class="copyCnameValue ml-2 text-sm" type="button">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
            </div>`
    }

    await Common.showModalHtml({
        type: 'info',
        title: 'Here are the CNAME DNS record details, you need to put them into the domain records in your DNS management system',
        message,
    });

    document.querySelectorAll('.copyCnameValue').forEach((el) => {
        el.addEventListener('click', (event) => {
            const cvalue = event.target.parentElement.previousElementSibling.textContent.trim();
            navigator.clipboard.writeText(cvalue);
            addToast('Your CNAME Value copied.');
        })
    })

    document.querySelectorAll('.copyCnameName').forEach((el) => {
        el.addEventListener('click', (event) => {
            const cname = event.target.parentElement.previousElementSibling.textContent.trim();
            navigator.clipboard.writeText(cname);
            addToast('Your CNAME Name copied.');
        })
    })
};

const confirmDelete = async () => {
    const id = siteId.value;

    try {
        const response = await axios.delete(`/sites-crud/${id}`);

        if (response.data.status !== 200) {
            throw new Error('Response was not ok');
        }
        refreshTable();
        addToast('Site deleted successfully', 'warning');
    } catch (error) {
        Common.showModalAlert({
            type: 'error',
            title: 'Error occurred',
            message: error.message,
        });
        console.error('Deleting error:', error);
    }

    siteId.value = null;
};

const deleteRow = (id) => {
    siteId.value = id;
    Common.showModalConfirmation({ callback: confirmDelete });
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
