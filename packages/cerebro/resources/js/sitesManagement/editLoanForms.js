import {
    imagePreviewModal,
    addOnThemeChangeEventClick, resetErrors, setErrors, addOnchangeRemoveErrorsEvent,
} from './common';

import {showHideFields} from "./loanFormLogic";

document.addEventListener('DOMContentLoaded', () => {

    showHideFields(document.querySelector('#sitesModalTheme').value);
    imagePreviewModal();
    addOnThemeChangeEventClick('#sitesModalTheme');
    addOnchangeRemoveErrorsEvent();

    document.querySelector('#btnLoanSiteUpdate').onclick = () => {
        sendEditRequest()
    };
});

function sendEditRequest(id) {
    $.ajax({
        url: '/sites-crud/' + document.querySelector('[name="id"]').value,
        type: 'POST',
        headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf"]').content},
        data: new FormData(document.getElementById('sitesForm')),
        cache: false,
        dataType: false,
        processData: false,
        contentType: false,
    }).done(function (response) {
        if (response.status === 200) {
            location.href = '/sites'
        }
    }).fail(function (error) {
        resetErrors('#sitesForm');
        setErrors(error.responseJSON.errors, '#sitesForm');
    });
}
