import { ref, nextTick  } from "vue";

export const isModalAlert = ref(false);
export const modalAlertTitle = ref("");
export const modalAlertMessage = ref("");
export const modalAlertType = ref("");

export const isModalHtml = ref(false);
export const modalHtmlTitle = ref("");
export const modalHtmlMessage = ref("");

export const isModalConfirm = ref(false);
export const confirmationTitle = ref("");
export const confirmationMessage = ref("");
export const confirmActionCallback = ref(() => {});

export const showModalAlert = async ({ type, title, message }) => {
    isModalAlert.value = false;
    await nextTick();
    modalAlertTitle.value = title;
    modalAlertMessage.value = message;
    modalAlertType.value = type;
    isModalAlert.value = true;
};

export const showModalHtml = async ({ type, title, message }) => {
    isModalHtml.value = false;
    await nextTick();
    modalHtmlTitle.value = title;
    modalHtmlMessage.value = message;
    isModalHtml.value = true;
};

export const showModalConfirmation = async ({
    title = "Action confirmation",
    message = "Are you sure you want to perform this action?",
    callback = () => {},
}) => {
    isModalConfirm.value = false;
    await nextTick();
    confirmationTitle.value = title;
    confirmationMessage.value = message;
    confirmActionCallback.value = callback;
    isModalConfirm.value = true;
};

export const resetObject = (obj, value = '') => {
    Object.keys(obj).forEach(key => {
        obj[key] = value;
    });
};

export const stopPropagation = (event) => {
    event.stopPropagation();
};

export const editorStyles = `
    .edit-wrapper { display: block; position: relative; justify-content: center; box-sizing: border-box; }
    .editable-block { border: 2px solid orange !important; box-sizing: border-box; }
    .highlighted-child { border: 2px solid blue !important; }
    .edit-button {
        position: absolute;
        top: 5px;
        left: 5px;
        z-index: 1000;
        font-size: 20px;
        width: 50px;
        height: 50px;
        border-radius: 30%;
        border: 2px solid orange;
        background-color: white;
        display: none;
        align-items: center;
        justify-content: center;
    }
    .number-badge {
        position: absolute;
        top: 5px;
        right: 5px;
        background-color: rgba(0, 0, 0, 0.3);
        color: white;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 35px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.3);
        z-index: 1000;
        line-height: 40px;
    }
`;
