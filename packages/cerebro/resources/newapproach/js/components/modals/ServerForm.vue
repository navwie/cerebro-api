<template>
    <div v-if="visible" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex justify-center items-center">
        <form>
            <div class="space-y-1 relative w-full max-w-6xl bg-white rounded-lg shadow-xl">
                <div class="border-b border-gray-300">
                    <div class="flex justify-between items-center bg-slate-700 text-white rounded-t-lg p-4">
                        <h2 class="text-lg font-medium text-white">
                            {{ serverId ? "Edit Server" : "Create New Server" }}
                        </h2>
                        <button @click="cancel" type="button" class="hover:text-gray-200">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="relative px-4 py-4" :class="{ 'blur-block': isDataExchanging }">
                    <div class="mb-4">
                        <TextInput v-model="formData.name" label="Name" id="serverName" name="name" :errorMessage="formErrors.name" />
                    </div>
                    <div class="mb-4">
                        <TextInput v-model="formData.ip_address" label="IP Address" id="serverIpAddress" name="ip_address" :errorMessage="formErrors.ip_address" />
                    </div>
                </div>
                <div class="border-t border-gray-300 p-4">
                    <div class="flex justify-end">
                        <button @click="cancel" type="button" class="px-4 py-2 text-white bg-gray-500 rounded-md hover:bg-gray-700"> Close </button>
                        <SubmitButton @click.prevent="submitForm" buttonText="Save" :isLoading="isLoading" :isDisabled="isDataExchanging" />
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<script setup>
import { ref, reactive, watchEffect } from "vue";
import axios from "axios";
import TextInput from "./../controls/TextInput.vue";
import SubmitButton from "./../controls/SubmitButton.vue";
import * as Common from "./../../common.js";

const isDataExchanging = ref(false);
const isLoading = ref(false);
const formKey = ref(0);
const errorDefaultTitle = "Error occurred";

const props = defineProps({
    visible: null,
    serverId: null,
    mode: {
        default: "create",
    },
});

const formData = reactive({
    name: "",
    ip_address: "",
    id: null,
});

const refreshFormKey = () => {
    formKey.value++;
};

const formErrors = ref({});

const emit = defineEmits(["close", "formSubmitted", "resetServerId"]);

const cancel = () => {
    emit("close");
    emit("resetServerId");
    resetForm();
    refreshFormKey();
};

const submitForm = async () => {
    isLoading.value = true;
    isDataExchanging.value = true;

    const formDataObj = new FormData();

    Object.keys(formData).forEach((key) => {
        formDataObj.append(key, formData[key]);
    });

    try {
        let response, message;

        if (props.mode === "create") {
            response = await axios.post("/servers-crud", formDataObj);
            message = "Server successfully created";
        }

        if (props.mode === "edit") {
            response = await axios.post(`/servers-crud/${props.serverId}`, formDataObj);
            message = "Server successfully updated";
        }

        if (response && response.data.status === 200) {
            emit("formSubmitted");
            emit("resetServerId");
            emit("close");
            resetForm();
            refreshFormKey();
            Common.showModalAlert({ type: "success", title: "Success!", message });
        }
    } catch (error) {
        if (error.response && error.response.status === 422) {
            Object.assign(formErrors, error.response.data.errors);
            console.log(error.response.data.errors);
        } else {
            Common.showModalAlert({
                type: "error",
                title: errorDefaultTitle,
                message: error,
            });
            console.error("Error occurred during form submission:", error);
        }
    } finally {
        isLoading.value = false;
        isDataExchanging.value = false;
    }
};

const resetForm = () => {
    Common.resetObject(formData, "");
    Common.resetObject(formErrors, "");
};

const fillForm = (data) => {
    formData.name = data.name;
    formData.ip_address = data.ip_address;
    formData.id = props.serverId;
};

const loadData = async () => {
    if (props.mode === "edit" && props.serverId) {
        try {
            isDataExchanging.value = true;
            const response = await axios.get(`/servers-crud/${props.serverId}`);

            if (response.data.status === 200) {
                fillForm(response.data.data);
            }
        } catch (error) {
            Common.showModalAlert({
                type: "error",
                title: errorDefaultTitle,
                message: error,
            });

            console.error("Error occurred during fetching user data:", error);
        } finally {
            isDataExchanging.value = false;
        }
    } else if (props.mode === "create") {
        resetForm();
    }
};

watchEffect(() => {
    loadData();
});
</script>
