<template>
    <div class="bg-white shadow-md rounded h-full">
        <ModalHtml :visible="Common.isModalHtml.value" :title="Common.modalHtmlTitle.value" :message="Common.modalHtmlMessage.value" />
        <div class="border-b border-gray-200">
            <div class="bg-gray-100 text-black text-lg px-4 py-2 rounded-t-md">
                <span v-if="mode === 'Create'">Create New Site</span>
                <span v-else>Edit Site</span>
            </div>
        </div>
        <div class="flex justify-between items-end w-full p-4" :class="{ 'pointer-events-none opacity-50': isLoading }">
            <div class="flex space-x-2 w-3/4">
                <div class="px-2 w-1/4">
                    <label for="domainName" class="block text-sm font-medium text-gray-700">Domain Name</label>
                    <input type="text" id="domainName" maxlength="254" v-model="domainName" :disabled="mode === 'Edit'" placeholder="Enter domain name" class="mt-1 block w-full border-gray-300 shadow-sm sm:text-sm rounded-md disabled:bg-gray-200">
                    <span class="text-red-500 text-sm">{{ domainNameError }}</span>
                </div>
                <div class="px-2 w-1/4">
                    <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                    <input type="text" id="title" maxlength="254" v-model="title" @change="titleHandler" placeholder="Enter title" class="mt-1 block w-full border-gray-300 shadow-sm sm:text-sm rounded-md">
                    <span class="text-red-500 text-sm">{{ titleError }}</span>
                </div>
                <div class="px-2 w-1/4">
                    <label for="formSelector" class="block text-sm font-medium text-gray-700">Form</label>
                    <select id="formSelector" v-model="selectedForm" :disabled="mode === 'Edit'" placeholder="Select form" class="mt-1 block w-full border-gray-300 shadow-sm sm:text-sm rounded-md disabled:bg-gray-200">
                        <option disabled value="" selected>Select form</option>
                        <option v-for="form in forms" :key="form.id" :value="form.id">{{ form.name }}</option>
                    </select>
                    <span class="text-red-500 text-sm">{{ selectedFormError }}</span>
                </div>
                <div class="px-2 w-1/4">
                    <label for="serverSelector" class="block text-sm font-medium text-gray-700">Server</label>
                    <select id="serverSelector" v-model="selectedServer" :disabled="mode === 'Edit'" class="mt-1 block w-full border-gray-300 shadow-sm sm:text-sm rounded-md h-10 disabled:bg-gray-200">
                        <option disabled value="" selected>Select server</option>
                        <option v-for="server in servers" :key="server.id" :value="server.id" class="py-1">{{ server.name }}</option>
                    </select>
                    <span class="text-red-500 text-sm">{{ selectedServerError }}</span>
                </div>
                <div class="px-2 w-1/4">
                    <label for="themeSelector" class="block text-sm font-medium text-gray-700">Theme</label>
                    <select id="themeSelector" v-model="selectedTheme" @change="onThemeChange" class="mt-1 block w-full border-gray-300 shadow-sm sm:text-sm rounded-md h-10">
                        <option disabled value="" selected>Select theme</option>
                        <option v-for="theme in loanFormThemes" :key="theme.id" :value="theme.id" class="py-1">{{ theme.name }}</option>
                    </select>
                </div>
                <div class="px-2 w-1/4">
                    <label for="viewSelector" class="block text-sm font-medium text-gray-700">View</label>
                    <select id="viewSelector" v-model="currentView" class="mt-1 block w-full border-gray-300 shadow-sm sm:text-sm rounded-md">
                        <option value="desktop">Desktop</option>
                        <option value="mobile">Mobile</option>
                    </select>
                </div>
            </div>
            <div v-if="siteContent" class="settings-panel flex space-x-2">
                <button @click="isGeneralSettingsVisible = true" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"> General Settings </button>
                <button @click="editSite" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Edit</button>
                <button @click="resetStyles" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Reset</button>
                <button @click="saveSite" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded disabled flex items-center whitespace-nowrap">
                    <span>Save</span>
                    <span v-if="isLoading" class="ml-2">
                        <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0116 0H4z"></path>
                        </svg>
                    </span>
                </button>
            </div>
        </div>
        <div :class="{ 'blur-block': isLoading || isThemeChanging }" class="iframe-wrap flex justify-center items-center border-t-2">
            <iframe ref="iframe" :style="iframeStyle" class="border border-gray-100" :srcdoc="siteContent" @load="applyThemeSettings"></iframe>
        </div>
        <ElementSettings :isVisible="isElementSettingsVisible" :currentEdittableBlockIndex="currentEdittableBlockIndex" :editableElementStyles="editableElementStyles" :currentEditingElementId="currentEditingElementId" @update:isVisible="isElementSettingsVisible = $event" @apply-styles="handleApplyElementStyles" />
        <GeneralSettings v-if="themeSettings.value?.general" :isVisible="isGeneralSettingsVisible" :generalStyles="themeSettings.value.general" @update:isVisible="isGeneralSettingsVisible = $event" @apply-general-styles="handleApplyGeneralStyles" />
    </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, nextTick } from 'vue';
import axios from 'axios';
import { usePage } from '@inertiajs/inertia-vue3';

import ElementSettings from '../components/sitemanager/ElementSettings.vue';
import GeneralSettings from '../components/sitemanager/GeneralSettings.vue';
import ModalHtml from './../components/modals/HtmlContent.vue';
import * as Common from './../common.js';
import { useToast } from './../services/toastService.js';
const { addToast } = useToast();
const isLoading = ref(false);
const isThemeChanging = ref(false);
const page = usePage();
const isAdmin = ref(false);
const domainName = ref('');
const title = ref('');
const forms = ref([]);
const selectedForm = ref('');
const servers = ref([]);
const selectedServer = ref('');
const loanFormThemes = ref([]);
const selectedTheme = ref('');
const creditCardThemes = ref([]);
const site = ref([]);
const siteItems = ref([]);
const siteContent = ref('');
const iframe = ref(null);
const imageFiles = ref({});
const fileIdMapping = ref({});
const currentEditingElementId = ref('');
const editableElementStyles = reactive({});
const inEditMode = ref(false);
const originalThemeSettings = ref({});
const mode = ref(null); // Create or Edit
const domainNameError = ref('');
const titleError = ref('');
const selectedFormError = ref('');
const selectedServerError = ref('');
const editableElementPrefix = 'EE_';
const editableChildElementPrefix = 'CE_';
const isElementSettingsVisible = ref(false);
const isGeneralSettingsVisible = ref(false);
const currentView = ref('desktop');
let editBadgeIndex = 0;
const currentEdittableBlockIndex = ref(null);

const iframeStyle = computed(() => {
    switch (currentView.value) {
        case 'mobile':
            return 'width: 390px; height: 100%;';
        default:
            return 'width: 1440px; height: 100%;';
    }
});

const themeSettings = reactive({
    elements: {},
    general: {}
});

const generalStyles = ref(themeSettings.general);

const isValidDomainName = (domain) => {
    const pattern = /^[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,})$/;
    return pattern.test(domain);
};

const validateFields = () => {
    let isValid = true;

    if (!domainName.value) {
        domainNameError.value = 'Domain Name is required';
        isValid = false;
    } else if (!isValidDomainName(domainName.value)) {
        domainNameError.value = 'Invalid domain name format';
        isValid = false;
    } else {
        domainNameError.value = '';
    }

    if (!title.value) {
        titleError.value = 'Title is required';
        isValid = false;
    } else {
        titleError.value = '';
    }

    if (!selectedForm.value) {
        selectedFormError.value = 'Form selection is required';
        isValid = false;
    } else {
        selectedFormError.value = '';
    }

    if (!selectedServer.value) {
        selectedServerError.value = 'Server selection is required';
        isValid = false;
    } else {
        selectedServerError.value = '';
    }

    return isValid;
};

const applyThemeSettings = () => {

    if (!iframe.value || !themeSettings.value) {
        return;
    }

    const iframeDoc = iframe.value.contentWindow.document;

    if (themeSettings.value.elements) {
        for (const [elementId, styles] of Object.entries(themeSettings.value.elements)) {
            if (!elementId.startsWith(editableElementPrefix)) continue;

            const element = iframeDoc.getElementById(elementId);
            if (element) {
                applyStylesToElement(element, styles);
            }
        }
    }

    if (themeSettings.value.general) {
        const generalStyleElements = iframeDoc.querySelectorAll('[class^="GSC_"]');
        for (const element of generalStyleElements) {
            const elementStyleProperties = new Set(element.style.cssText.split(';')
                .map(style => style.split(': ')[0].trim()));

            const newStyle = Object.entries(themeSettings.value.general)
                .filter(([key, value]) => typeof value === 'object' && value.variable && !!value.value && elementStyleProperties.has(value.variable))
                .map(([key, value]) => `${value.variable}: ${value.value}`)
                .join('; ');
            if (newStyle) {
                element.style.cssText += newStyle;
            }
        }
    }
};

const applyStylesToElement = (element, elementStyles) => {

    for (const [prop, value] of Object.entries(elementStyles)) {

        // if (!!value === false) {
        //     continue;
        // }
        if (prop.startsWith(':')) {
            for (const innerKey in value) {
                if (innerKey !== 'label' && value[innerKey].variable) {
                    if (innerKey === 'fill') {
                        element.fill = value;
                    } else {
                        element.style.setProperty(value[innerKey].variable, value[innerKey].value);
                    }
                }
            }
        } else if (prop === 'content') {
            element.textContent = value;
        } else if (prop === 'src') {
            element.src = value;
        } else {
            // console.log(prop, value)
            element.style[prop] = value;
        }
    }

    for (const [prop, styleObject] of Object.entries(elementStyles)) {
        if (prop === 'color' && styleObject) {
            const svgElements = element.querySelectorAll('svg');
            svgElements.forEach(svg => {
                // svg.style.fill = value;
                // svg.style.stroke = value;
                const paths = svg.querySelectorAll('path');
                // console.log(path)
                paths.forEach(path => {
                    path.setAttribute('stroke', styleObject);
                });
            });
        }
        if (prop.startsWith(editableChildElementPrefix) && prop !== 'label') {
            const childElements = element.querySelectorAll(`.${prop}`);
            for (const childElement of childElements) {
                // console.log(childElement)
                for (const [styleName, styleValue] of Object.entries(styleObject)) {
                    // console.log(styleName, styleValue)
                    if (styleName !== 'label') {
                        if (styleName === 'src') {

                            childElement.src = styleValue
                        } else {
                            childElement.style[styleName] = styleValue;
                        }
                    }
                }
            }
        }
    }
};

const cleanState = async () => {
    imageFiles.value = {};
    fileIdMapping.value = {};
    themeSettings.value = {};
    originalThemeSettings.value = {};
    siteContent.value = '';
    generalStyles.value = {};
};

const onThemeChange = async () => {
    isLoading.value = true;
    inEditMode.value = false;
    await cleanState();
    if (mode.value === 'Edit') {
        try {
            isThemeChanging.value = true;
            const requestData = { themeName: selectedTheme.value, siteId: site.value.id };
            const response = await axios.post('/sites/merge-settings', requestData);
            themeSettings.value = response.data;
            originalThemeSettings.value = themeSettings.value;
            siteContent.value = response.data.htmlContent;
            generalStyles.value = themeSettings.value.general || {};
            applyThemeSettings();
        } catch (error) {
            console.error('Error on theme change:', error);
            Common.showModalAlert({
                type: 'error',
                title: 'Error occurred',
                message: error.message,
            });
        }
        finally {
            isThemeChanging.value = false;
        }
    } else {
        await fetchThemeSettings();
        applyThemeSettings();
    }
    isLoading.value = false;
};

const fetchThemeSettings = async () => {
    if (!!selectedTheme.value === false) {
        return;
    }

    try {
        const requestData = { themeName: selectedTheme.value };
        const response = await axios.post('/sites/get-settings', requestData);
        themeSettings.value = response.data;
        originalThemeSettings.value = response.data;
        siteContent.value = response.data.htmlContent;
        nextTick(() => {
            generalStyles.value = themeSettings.value.general || {};
        });
    } catch (error) {
        console.error('Error fetching theme settings:', error);
        Common.showModalAlert({
            type: 'error',
            title: 'Error occurred',
            message: error.message,
        });
    }
};

const editSite = () => {
    inEditMode.value = !inEditMode.value;
    if (inEditMode.value) {
        enableEditing();
    } else {
        disableEditing();
    }
};

const enableEditing = () => {
    editBadgeIndex = 0;
    const iframeDoc = iframe.value.contentWindow.document;
    const style = document.createElement('style');
    style.id = 'support';

    style.textContent = Common.editorStyles;
    iframeDoc.head.appendChild(style);

    const editableElements = iframeDoc.querySelectorAll(`[id^="${editableElementPrefix}"]`);

    for (const el of editableElements) {
        el.classList.add('editable-block');
        highlightChildren(el, editableChildElementPrefix, 'highlighted-child');
        createEditButton(el, ++editBadgeIndex);
    }
};

const disableEditing = () => {
    editBadgeIndex = 0;
    const iframeDoc = iframe.value.contentWindow.document;

    const addedStyle = iframeDoc.head.querySelector('#support');
    if (addedStyle) {
        iframeDoc.head.removeChild(addedStyle);
    }

    const editButtons = iframeDoc.querySelectorAll('.edit-button');
    editButtons.forEach(button => {
        button.removeEventListener('click', buttonClickHandler);
        button.remove();
    });

    const badges = iframeDoc.querySelectorAll('.number-badge');
    badges.forEach(badge => badge.remove());

    const wrappers = iframeDoc.querySelectorAll('.edit-wrapper');
    wrappers.forEach(wrapper => {
        while (wrapper.firstChild) {
            wrapper.parentNode.insertBefore(wrapper.firstChild, wrapper);
        }
        wrapper.remove();
    });
};

const buttonClickHandler = (e) => {
    e.stopPropagation();
    e.preventDefault();
};

const highlightChildren = (parent, classPrefix, highlightClass) => {
    const childElements = parent.querySelectorAll(`[class^="${classPrefix}"]`);

    for (const child of childElements) {
        if (!child.closest(`[id^="${editableElementPrefix}"]`) || child.closest(`[id^="${editableElementPrefix}"]`) === parent) {
            child.classList.add(highlightClass);
        }
    }
};

const createNumberBadge = (wrapper, index) => {
    const numberBadge = document.createElement('div');
    numberBadge.textContent = index;
    numberBadge.classList.add('number-badge');
    wrapper.appendChild(numberBadge);
};

const createEditButton = (element, index) => {
    // console.log(element);

    const editButton = document.createElement('button');
    editButton.innerHTML = '✏️';
    editButton.classList.add('edit-button');

    editButton.addEventListener('click', (e) => {
        e.stopPropagation();
        e.preventDefault();
        editElementStyles(element, index);
    });

    const isClosedElement = ['IMG', 'INPUT', 'HR'].includes(element.tagName);

    if (isClosedElement) {
        const wrapper = document.createElement('div');
        wrapper.classList.add('edit-wrapper');

        element.parentNode.insertBefore(wrapper, element);
        wrapper.appendChild(element);
        wrapper.appendChild(editButton);

        wrapper.addEventListener('mouseenter', () => {
            editButton.style.display = 'block';
        });
        wrapper.addEventListener('mouseleave', () => {
            editButton.style.display = 'none';
        });
    } else {
        element.appendChild(editButton);
        element.style.position = 'relative';

        element.addEventListener('mouseenter', () => {
            editButton.style.display = 'block';
        });
        element.addEventListener('mouseleave', () => {
            editButton.style.display = 'none';
        });

        createNumberBadge(isClosedElement ? wrapper : element, index);
    }

};

const cleanEditableElementStyles = () => {
    for (const key in editableElementStyles) {
        delete editableElementStyles[key];
    }
};

const editElementStyles = (element, index) => {
    cleanEditableElementStyles();

    const elementId = element.id;
    currentEditingElementId.value = elementId;
    const elementStyles = themeSettings.value.elements[elementId];

    if (!elementStyles) {
        return
    }

    for (const [prop, value] of Object.entries(elementStyles)) {
        if (prop === 'content') {
            editableElementStyles['content'] = value;
        } else if (prop === 'src') {
            editableElementStyles['src'] = value;
        } else if (prop === 'background-image') {
            const urlMatch = value.match(/url\("?(.*?)"?\)/);
            editableElementStyles['background-image'] = urlMatch ? urlMatch[1] : '';
        } else {
            editableElementStyles[prop] = value;
        }
    }

    currentEdittableBlockIndex.value = index;
    isElementSettingsVisible.value = true;
};

const handleApplyElementStyles = ({ styles, elementId, imageFiles: incomingImageFiles }) => {

    const element = iframe.value.contentWindow.document.getElementById(elementId);
    if (!element) {
        console.error('Element not found:', elementId);
        return;
    }
    // console.log(styles)
    for (const [property, value] of Object.entries(styles)) {
        if (property.startsWith(editableChildElementPrefix)) {
            const childElements = element.getElementsByClassName(property);
            for (const childElement of childElements) {
                for (const [cssProperty, cssValue] of Object.entries(value)) {

                    if (cssProperty !== 'label') {
                        if ((cssProperty === 'src' || cssProperty === 'background-image') && incomingImageFiles[property]) {
                            const file = incomingImageFiles[property];

                            if (!!file === false) continue;

                            const fileKey = `${elementId}-${property}-${file.name}`;

                            imageFiles.value[fileKey] = file;
                            const reader = new FileReader();
                            reader.onload = e => {
                                const url = e.target.result;
                                if (childElement.tagName === 'IMG') {
                                    childElement.src = url;
                                } else {
                                    childElement.style.backgroundImage = `url('${url}')`;
                                }
                            };
                            reader.readAsDataURL(file);

                            if (!fileIdMapping.value[elementId]) {
                                fileIdMapping.value[elementId] = {};
                            }
                            if (!fileIdMapping.value[elementId][property]) {
                                fileIdMapping.value[elementId][property] = {};
                            }
                            fileIdMapping.value[elementId][property][cssProperty] = fileKey;
                        } else {
                            if (!!value) {
                                childElement.style[cssProperty] = cssValue;
                            }
                        }
                    }
                }
            }
        }

        if (property === 'color' && value) {
            const svgElements = element.querySelectorAll('svg');
            svgElements.forEach(svg => {
                // svg.style.fill = value;
                // svg.style.stroke = value;
                const paths = svg.querySelectorAll('path');
                // console.log(path)
                paths.forEach(path => {
                    // path.setAttribute('fill', value);
                    path.setAttribute('stroke', value);
                });
            });
        }

        if (property.startsWith(':')) { // Pseudo classes processing
            for (const innerKey in value) {
                if (innerKey !== 'label' && value[innerKey].variable) {
                    // console.log(value[innerKey].variable, value[innerKey].value)
                    element.style.setProperty(value[innerKey].variable, value[innerKey].value);
                }
            }
        }

        if (property === 'content' && !!value) {
            element.textContent = value;
        }


        if ((property === 'background-image' || property === 'src') && incomingImageFiles[property]) {
            const file = incomingImageFiles[property];

            if (!!file === false) continue;

            const fileKey = `${elementId}-${file.name}`;
            imageFiles.value[fileKey] = file;

            const reader = new FileReader();
            reader.onload = e => {
                const url = e.target.result;
                if (property === 'src' && element.tagName === 'IMG') {
                    element.src = url;
                } else if (property === 'background-image') {
                    element.style.backgroundImage = `url('${url}')`;
                }
            };
            reader.readAsDataURL(file);

            if (!fileIdMapping.value[elementId]) {
                fileIdMapping.value[elementId] = {};
            }
            fileIdMapping.value[elementId][property] = fileKey;
            // console.log(fileKey)
        } else {
            if (!!value) {
                element.style[property] = value;
            }
        }

        if (themeSettings.value.elements[elementId] && !!value) {
            // console.log(property, value)
            themeSettings.value.elements[elementId][property] = value;
        }
    }
};

const handleApplyGeneralStyles = ({ styles, imageFiles: incomingImageFiles }) => {
    const gscElements = iframe.value.contentWindow.document.querySelectorAll('[class^="GSC_"]');

    const tempStyles = new Map();

    for (const element of gscElements) {
        const styleAttr = element.getAttribute('style');

        if (!!styleAttr === false) {
            continue;
        }

        const stylePairs = styleAttr.split(';');

        for (const style of stylePairs) {
            const [variable, value] = style.split(':').map(s => s.trim());

            if (variable && value) {
                tempStyles[variable] = value;
            }
        }
    }

    for (const styleObj of Object.values(styles)) {
        if (styleObj.variable && styleObj.value && tempStyles.hasOwnProperty(styleObj.variable)) {
            tempStyles[styleObj.variable] = styleObj.value;
        }
    }

    for (const element of gscElements) {
        const styleAttr = element.getAttribute('style');
        if (styleAttr) {
            const stylePairs = styleAttr.split(';');

            for (const style of stylePairs) {
                const [variable, value] = style.split(':').map(s => s.trim());
                if (variable && value) {
                    tempStyles.set(variable, value);
                }
            }
        }
    }

    Object.values(styles).forEach(styleObj => {
        if (styleObj.variable && styleObj.value && tempStyles.has(styleObj.variable)) {
            tempStyles.set(styleObj.variable, styleObj.value);
        }
    });

    gscElements.forEach(element => {
        const elementStyles = new Map();
        const styleAttr = element.getAttribute('style');
        if (styleAttr) {
            const stylePairs = styleAttr.split(';');
            stylePairs.forEach(style => {
                const [variable, value] = style.split(':').map(s => s.trim());
                if (variable && value) {
                    elementStyles.set(variable, tempStyles.get(variable) || value);
                }
            });
        }

        const newStyle = Array.from(elementStyles).map(([variable, value]) => `${variable}: ${value}`).join('; ');
        element.setAttribute('style', newStyle);
    });

    if (incomingImageFiles && incomingImageFiles['favicon']) {
        const file = incomingImageFiles['favicon'];
        const fileKey = `favicon-${file.name}`;
        imageFiles.value[fileKey] = file;
        if (!fileIdMapping.value['favicon']) {
            fileIdMapping.value['favicon'] = {};
        }
        fileIdMapping.value['favicon'] = fileKey;
    }

    themeSettings.value.general = styles;
};

const refreshIframe = () => {
    if (iframe.value) {
        iframe.value.srcdoc = siteContent.value;
    }
};

const resetStyles = () => {
    inEditMode.value = false;

    themeSettings.value = { ...originalThemeSettings.value };
    if (iframe.value) {
        refreshIframe();
    }
};

const saveSite = async () => {
    try {
        inEditMode.value = false;
        disableEditing();
        clearErrorHighlights();
        isLoading.value = true;
        const { htmlContent, ...noHtmlContent } = themeSettings.value;
        const dataToSend = JSON.stringify(noHtmlContent);
        const formData = new FormData();
        const url = '/sites-crud';
        formData.append('themeSettings', dataToSend);
        formData.append('fileIdMapping', JSON.stringify(fileIdMapping.value));
        formData.append('selectedTheme', selectedTheme.value);

        for (const [key, file] of Object.entries(imageFiles.value)) {
            formData.append(key, file);
        }

        if (mode.value === 'Edit') {
            formData.append('siteId', site.value.id);
            formData.append('title', title.value);
        } else if (mode.value === 'Create') {
            if (!validateFields()) return;

            formData.append('domainName', domainName.value);
            formData.append('title', title.value);
            formData.append('selectedForm', selectedForm.value);
            formData.append('selectedServer', selectedServer.value);
            // url = '/sites-crud';
        }

        const response = await axios.post(url, formData);
        addToast(response.data.success);

        if (mode.value === 'Create') {
            mode.value = 'Edit';
            site.value.id = response.data.siteId;
            const newUrl = `/sites/${response.data.siteId}`;
            window.history.pushState({ path: newUrl }, '', newUrl);
        }
    } catch (error) {
        let errors = {};
        if (error.response && error.response.data && error.response.data.errors) {
            errors = error.response.data.errors;
        }

        addToast("Changes weren't saved", 'error');

        const errorListHtml = highlightErrorElements(errors);

        if (!!errorListHtml) {
            Common.showModalHtml({
                type: 'error',
                title: 'General settings error',
                message: errorListHtml,
            });
        }

        console.error('Error saving site:', error);
    } finally {
        isLoading.value = false;
    }
};

const clearErrorHighlights = () => {
    if (!iframe.value) return;

    const highlightedElements = iframe.value.contentWindow.document.querySelectorAll('[style*="border"]');
    highlightedElements.forEach(element => {
        element.style.removeProperty('border');
    });

    const errorLabels = iframe.value.contentWindow.document.querySelectorAll('.error-label');
    for (const label of errorLabels) {
        label.remove();
    }
};

const highlightErrorElements = (errors) => {
    if (!iframe.value) return '';

    let errorMessages = {};
    let errorMessageHtml = '';

    const createOrUpdateErrorLabel = (text, element) => {
        let label = element.querySelector('.error-label');
        if (!label) {
            label = document.createElement('div');
            label.classList.add('error-label');
            label.style.position = 'absolute';
            label.style.backgroundColor = 'rgba(255, 0, 0, 0.7)';
            label.style.color = 'white';
            label.style.padding = '2px 5px';
            label.style.fontSize = '15px';
            label.style.zIndex = '1000';
            label.style.top = '0';
            label.style.left = '0';
            label.style.whiteSpace = 'pre-line';

            element.style.position = 'relative';
            element.appendChild(label);
        }

        label.textContent += text + '\n';
    };

    for (const errorKey in errors) {
        const parts = errorKey.split('->');
        const section = parts[0];
        const errorDetail = parts.slice(1).join('->');

        if (!errorMessages[section]) {
            errorMessages[section] = {};
        }

        errorMessages[section][errorDetail] = errors[errorKey].join(', ');

        if (section === 'themeSettings' || section === 'fileIdMapping') {
            const elementId = parts[section === 'themeSettings' ? 2 : 1];
            const element = iframe.value.contentWindow.document.getElementById(elementId);
            if (element) {
                element.style.setProperty('border', '2px solid red', 'important');
                const errorText = `${errors[errorKey].join(', ')}`;
                createOrUpdateErrorLabel(errorText, element);
            }
        }

        if ((section === 'themeSettings' && parts[1] === 'general') ||
            (section === 'fileIdMapping' && parts[1] === 'favicon')) {
            errorMessageHtml += `${errors[errorKey].join(', ')}<br>`;
        }
    }

    return errorMessageHtml;
};

const applyStylesToIframe = () => {
    const iframeDoc = iframe.value.contentWindow.document;

    const links = iframeDoc.querySelectorAll('a');
    for (const link of links) {
        link.addEventListener('click', event => {
            event.preventDefault();
        });
    }
    iframeDoc.querySelectorAll('.app_name').forEach((el) => {
        el.innerHTML = title.value;
    })
};

const titleHandler = () => {
    if (!iframe.value) return '';
    iframe.value.contentWindow.document.querySelectorAll('.app_name').forEach((el) => {
        el.innerHTML = title.value;
    })
}

onMounted(() => {
    isAdmin.value = !!page.props.value.user.roles.find(x => x.name === 'admin');
    forms.value = page.props.value.forms || [];
    servers.value = page.props.value.servers || [];
    loanFormThemes.value = page.props.value.loanFormThemes || [];
    creditCardThemes.value = page.props.value.creditCardThemes || [];
    site.value = page.props.value.site || [];
    siteItems.value = page.props.value.siteItems || [];
    mode.value = page.props.value.mode || 'Create';

    if (mode.value === 'Edit' && site.value) {
        domainName.value = site.value.domain_name || '';
        title.value = site.value.title || '';
        selectedForm.value = site.value.form_id || null;
        selectedServer.value = site.value.server_id || null;
        selectedTheme.value = site.value.theme || null;
        onThemeChange();
    } else {
        fetchThemeSettings();
    }
    iframe.value.onload = applyStylesToIframe;
    // console.log(site.value)
});

</script>
