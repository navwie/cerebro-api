<template>
    <div v-if="visible" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex justify-center items-center">
        <form>
            <div class="space-y-1 relative w-full max-w-6xl bg-white rounded-lg shadow-xl">
                <div class="border-b border-gray-300">
                    <div class="flex justify-between items-center bg-slate-700 text-white rounded-t-lg p-4">
                        <h2 class="text-lg font-medium text-white">{{ userId ? 'Edit User' : 'Create New User' }}</h2>
                        <button @click="cancel" type="button" class=" hover:text-gray-200">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="relative" :class="{ 'blur-block': isDataExchanging }">
                    <div class="flex flex-wrap -mx-3 px-4 py-4">
                        <div class="w-full md:w-1/2 px-3">
                            <TextInput v-model="formData.name" label="Name" id="usersModalName" name="name" :errorMessage="formErrors.name" />
                        </div>
                        <div class="w-full md:w-1/2 px-3">
                            <TextInput v-model="formData.email" label="Email address" type="email" id="usersModalEmail" name="email" :errorMessage="formErrors.email" />
                            <div v-if="props.mode === 'edit'">
                                <span role="button" class="cursor-pointer hover:underline" @click="resendVerifyEmail">Click here</span> to send a confirmation email.
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-wrap -mx-3 px-4">
                        <div class="w-full md:w-1/2 px-3">
                            <TextInput v-model="formData.email_ccpa" label="Email CCPA" type="email" id="usersModalEmailCcpa" name="email_ccpa" :errorMessage="formErrors.email_ccpa" />
                        </div>
                    </div>
                    <div class="flex flex-wrap -mx-3 px-4">
                        <div class="w-full md:w-1/2 px-3">
                            <TextInput v-model="formData.password" label="Password" type="password" id="usersModalPassword" name="password" :errorMessage="formErrors.password" />
                        </div>
                        <div class="w-full md:w-1/2 px-3">
                            <TextInput v-model="formData.password_confirmation" label="Confirm password" type="password" id="usersModalPasswordConfirm" name="password_confirmation" :errorMessage="formErrors.password_confirmation" />
                        </div>
                    </div>
                    <div class="flex flex-wrap -mx-3 px-4">
                        <div class="w-full md:w-1/4 px-3 md:mb-0">
                            <label class="block text-gray-700 text-sm font-bold mb-1">Processing time</label>
                            <div class="flex -mx-2 items-end">
                                <div class="w-1/2 px-2">
                                    <NumberInput v-model="formData.processing_time_min" min="0" label="min" labelPosition="bottom" id="usersModalProcessingTimeMin" name="processing_time_min" :errorMessage="formErrors.processing_time_min" />
                                </div>
                                <div class="w-1/2 px-2">
                                    <NumberInput v-model="formData.processing_time_sec" min="0" label="sec" labelPosition="bottom" id="usersModalProcessingTimeSec" name="processing_time_sec" :errorMessage="formErrors.processing_time_sec" />
                                </div>
                            </div>
                        </div>
                        <div class="w-full md:w-1/4 px-3">
                            <NumberInput v-model="formData.min_price" min="0" label="Min price" id="usersModalMinPrice" name="min_price" :errorMessage="formErrors.min_price" />
                        </div>
                        <div class="w-full md:w-1/4 px-3">
                            <NumberInput v-model="formData.lead_min_price" min="0" label="Lead min price" id="usersModalLeadMinPrice" name="lead_min_price" :errorMessage="formErrors.lead_min_price" />
                        </div>
                    </div>
                    <div class="flex flex-wrap -mx-3 px-4">
                        <div class="w-full md:w-1/4 px-3 md:mb-0">
                            <Select label="Role" v-model="formData.role" :options="[{ value: 'user', text: 'User' }, { value: 'admin', text: 'Admin' }]" :error="formErrors.role" id="usersModalRole" name="role" />
                        </div>
                    </div>
                    <div class="flex flex-wrap -mx-3 px-4">
                        <div class="w-full md:w-1/4 px-3">
                            <TextInput v-model="formData.personal_channel_id" label="Personal channel id" id="usersModalPersonalChannelId" name="personal_channel_id" :errorMessage="formErrors.personal_channel_id" />
                        </div>
                        <div class="w-full md:w-1/4 px-3">
                            <TextInput v-model="formData.lead_channel_id" label="Lead channel id" id="usersModalLeadChannelId" name="lead_channel_id" :errorMessage="formErrors.lead_channel_id" />
                        </div>
                        <div class="w-full md:w-1/4 px-3">
                            <NumberInput v-model="formData.post_back_amount" min="0" label="Post back amount" id="usersModalPostBackAmount" name="post_back_amount" :errorMessage="formErrors.post_back_amount" />
                        </div>
                        <div class="w-full md:w-1/4 px-3">
                            <NumberInput v-model="formData.personal_min_req" min="0" label="Personal min req" id="usersModalPersonalMinReq" name="personal_min_req" :errorMessage="formErrors.personal_min_req" />
                        </div>
                    </div>
                    <div class="flex flex-wrap -mx-3 px-4">
                        <div class="w-full md:w-1/2 px-3">
                            <TextInput v-model="formData.personal_password" label="Personal password" id="usersModalPersonalPassword" name="personal_password" :errorMessage="formErrors.personal_password" />
                        </div>
                        <div class="w-full md:w-1/2 px-3">
                            <TextInput v-model="formData.lead_password" label="Lead password" id="usersModalLeadPassword" name="lead_password" :errorMessage="formErrors.lead_password" />
                        </div>
                    </div>
                    <div class="flex flex-wrap -mx-3 px-4">
                        <div class="w-full px-3 mb-0">
                            <TextInput v-model="formData.post_back_url" label="You can use next placeholders: [amount], [clickId], and [transactionId]" id="usersModalPostBackUrl" name="post_back_url" :errorMessage="formErrors.post_back_url" />
                        </div>
                    </div>
                </div>
                <div class="border-t border-gray-300 p-4">
                    <div class="flex justify-end">
                        <button v-if="props.mode === 'edit'" type="button" @click="regenerateToken" class="mx-3 px-4 py-2 bg-orange-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-opacity-75"> Regenerate Token </button>
                        <button type="button" @click="cancel" class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-75"> Close </button>
                        <SubmitButton @click.prevent="submitForm" buttonText="Save" :isLoading="isLoading" :isDisabled="isDataExchanging" />
                    </div>
                </div>
            </div>
        </form>
    </div>
    <ModalAlert :visible="Common.isModalAlert.value" :title="Common.modalAlertTitle.value" :message="Common.modalAlertMessage.value" :type="Common.modalAlertType.value" @update:visible="Common.isModalAlert.value = $event" />
    <ModalConfirmation :visible="Common.isModalConfirm.value" :title="Common.confirmationTitle.value" :message="Common.confirmationMessage.value" @confirm="Common.confirmActionCallback.value" @update:visible="Common.isModalConfirm.value = $event" />
</template>

<script setup>
import { ref, reactive, watchEffect, onMounted } from 'vue';
import axios from 'axios';
import { useCsrfStore } from './../../stores/csrfStore';
import TextInput from './../controls/TextInput.vue';
import NumberInput from './../controls/NumberInput.vue';
import Select from './../controls/Select.vue';
import SubmitButton from './../controls/SubmitButton.vue';
import ModalConfirmation from './../modals/Confirmation.vue';
import ModalAlert from './../modals/Alert.vue';
import * as Common from './../../common.js';
import { useToast } from './../../services/toastService.js';
const { addToast } = useToast();

const props = defineProps({
    visible: null,
    userId: null,
    mode: {
        default: 'create'
    }
});

const emit = defineEmits(['close', 'formSubmitted', 'resetUserId']);
const isLoading = ref(false);
const isDataExchanging = ref(false);
const errorDefaultTitle = 'Error occurred';
const formKey = ref(0);
const formErrors = ref({});
const csrfStore = useCsrfStore();

const formData = reactive({
    name: '',
    email: '',
    email_ccpa: '',
    password: '',
    password_confirmation: '',
    processing_time_min: null,
    processing_time_sec: null,
    min_price: null,
    lead_min_price: null,
    role: 'user',
    personal_channel_id: '',
    lead_channel_id: '',
    post_back_amount: null,
    personal_min_req: null,
    personal_password: '',
    lead_password: '',
    post_back_url: '',
    id: props.userId,
    email_verified_at: '',
    _token: '',
});

const refreshFormKey = () => {
    formKey.value++;
};

const resetForm = () => {
    Common.resetObject(formData, '');
    Common.resetObject(formErrors, '');
};

const submitForm = () => {
    if (props.mode === 'edit') {
        const message = 'When the email is changed, all tokens cease to be valid, and the email must be verified again. New tokens cannot be created without email confirmation. Do you confirm saving the form?';
        Common.showModalConfirmation({ message, callback: confirmSubmit });
    } else {
        confirmSubmit();
    }
};

const confirmSubmit = async () => {
    isLoading.value = true;
    isDataExchanging.value = true;

    try {
        let response;

        if (props.mode === 'edit') {
            const formDataObj = new FormData();
            Object.keys(formData).forEach(key => {
                formDataObj.append(key, formData[key]);
            });

            const headers = { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'multipart/form-data' };
            formDataObj.append('_method', 'PUT');
            response = await axios.post(`/users/${props.userId}`, formDataObj, { headers });
        }

        if (props.mode === 'create') {
            response = await axios.post('/users', formData);
        }

        if (response && response.data.status === 200) {
            if (props.mode === 'edit') {
                addToast('User`s data updated', 'info');
            }

            if (props.mode === 'create') {
                addToast('New user created', 'success');
            }

            emit('formSubmitted');
            emit('resetUserId');
            emit('close');
            resetForm();
            refreshFormKey();
        }
    } catch (error) {
        if (error.response && error.response.status === 422) {
            Object.assign(formErrors, error.response.data.errors);
            console.log(error.response.data.errors)
        } else {
            Common.showModalAlert({
                type: 'error',
                title: errorDefaultTitle,
                message: error.message,
            });
            console.error('Error occurred during form submission:', error);
        }
    } finally {
        isLoading.value = false;
        isDataExchanging.value = false;
    }
};

const cancel = () => {
    emit('close');
    emit('resetUserId');
    resetForm();
    refreshFormKey();
};

const resendVerifyEmail = () => {
    const message = 'When the email is changed, all tokens cease to be valid, and the email must be verified again. New tokens cannot be created without email confirmation. Do you confirm saving the form?';
    Common.showModalConfirmation({ message, callback: confirmResendVerifyEmail })

};

const confirmResendVerifyEmail = async () => {
    try {
        isDataExchanging.value = true;
        const csrfToken = csrfStore.token;

        const response = await axios.post(`/email/resend/${props.userId}`, {
            _token: csrfToken
        });

        if (response.status === 200) {
            // Common.showModalAlert({
            //     type: 'success',
            //     title: 'Email Sent',
            //     message: 'Verification email has been resent successfully.',
            // });

            addToast('Verification email has been resent successfully', 'info');
        }
    } catch (error) {
        Common.showModalAlert({
            type: 'error',
            title: 'Error occurred',
            message: error.message,
        });
        console.error('Error during resending verification email:', error);
    } finally {
        isDataExchanging.value = false;
    }
};

const regenerateToken = () => {
    const message = 'Do you confirm that you want to regenerate token? This action remove the remaining tokens. Email for this form is not confirmed!';
    Common.showModalConfirmation({ message, callback: confirmRegenerateToken });
};

const confirmRegenerateToken = async () => {
    try {
        const csrfToken = csrfStore.token;
        isDataExchanging.value = true;

        const response = await axios.post(`/users/${props.userId}/regenerate`, {
            _token: csrfToken
        });

        if (response.data.status === 200) {
            Common.showModalAlert({
                type: 'info',
                title: 'Token regenerated',
                message: response.data.token,
            });

            // console.log(response.data);
        }
    } catch (error) {
        Common.showModalAlert({
            type: 'error',
            title: 'Error occurred',
            message: error.message,
        });

        console.error('Error during token regeneration:', error);
    } finally {
        isDataExchanging.value = false;
    }
};

const fillForm = (data) => {
    const sec = parseInt(data.processing_time % 60) || null;
    const min = parseInt((data.processing_time - sec) / 60) || null
    formData.processing_time_sec = sec;
    formData.processing_time_min = min;
    formData.name = data.name;
    formData.email = data.email;
    formData.email_ccpa = data.email_ccpa;
    formData.password = '';
    formData.password_confirmation = '';
    formData.min_price = data.min_price ? parseFloat(data.min_price) : null;
    formData.lead_min_price = data.lead_min_price ? parseFloat(data.lead_min_price) : null;
    formData.role = data.role || 'user';
    formData.personal_channel_id = data.personal_channel_id || '';
    formData.lead_channel_id = data.lead_channel_id || '';
    formData.post_back_amount = data.post_back_amount ? parseFloat(data.post_back_amount) : null;
    formData.personal_min_req = data.personal_min_req ? parseFloat(data.personal_min_req) : null;
    formData.personal_password = data.personal_password || '';
    formData.lead_password = data.lead_password || '';
    formData.post_back_url = data.post_back_url || '';
    formData.id = props.userId;
};

const loadData = async () => {
    if (props.mode === 'edit' && props.userId) {
        try {
            isDataExchanging.value = true;
            const response = await axios.get(`/users/${props.userId}`);

            if (response.data.status === 200) {
                fillForm(response.data.data);
            }
        } catch (error) {
            Common.showModalAlert({
                type: 'error',
                title: 'Error occurred',
                message: error.message,
            });

            console.error('Error occurred during fetching user data:', error);
        } finally {
            isDataExchanging.value = false;
        }
    } else if (props.mode === 'create') {
        resetForm();
    }
};

watchEffect(() => {
    loadData();
});

onMounted(() => {
    // addToast('Message 1');

    // setTimeout(() => {
    //     addToast('Message 2');
    // },1000)

    // Common.showModalAlert({
    //     type: 'success',
    //     title: 'Error occurred',
    //     message: 'Message Message Message Message Message Message Message Message Message ',
    // });
});

</script> 
