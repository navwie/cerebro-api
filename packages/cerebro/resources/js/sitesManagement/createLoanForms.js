import {
    initColorPicker,
    setErrors,
    resetErrors,
    addOnThemeChangeEventClick,
    addOnchangeRemoveErrorsEvent
} from './common';
import {showHideFields} from './loanFormLogic';

document.addEventListener('DOMContentLoaded', () => {
    initColorPicker()
    hideImageLinks();
    showHideFields(document.querySelector('#sitesModalTheme').value);
    addOnThemeChangeEventClick('#sitesModalTheme');
    addOnchangeRemoveErrorsEvent();
});

function hideImageLinks() {
    document.querySelectorAll('[id*="-link-wrapper"]').forEach((el) => {
        el.style.display = 'none';
    });
}

document.querySelector('#btnLoanSiteCreate').onclick = () => {
    sendCreateRequest()
};

function sendCreateRequest() {
    $.ajax({
        url: '/sites-crud',
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
