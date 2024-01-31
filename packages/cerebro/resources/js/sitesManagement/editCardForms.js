import {
    initColorPicker,
    setErrors,
    resetErrors,
    imagePreviewModal,
    addOnchangeRemoveErrorsEvent,
} from './common';
import {addAddItemClickEvent, addAddBenefitClickEvent, addDeleteBenefitClickEvent, addDeleteItemRowClickEvent, addHideDeleteButtonsWhenOneEntity } from './cardFormLogic';

document.addEventListener('DOMContentLoaded', () => {

    addAddItemClickEvent();
    addAddBenefitClickEvent();
    addDeleteBenefitClickEvent();
    addDeleteItemRowClickEvent();
    imagePreviewModal();
    addOnchangeRemoveErrorsEvent();
    addHideDeleteButtonsWhenOneEntity();

    document.querySelector('#btnCreditCardUpdate').onclick = () => {
        $.ajax({
            url: '/cards-crud/' + document.querySelector('[name="id"]').value,
            type: 'POST',
            headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf"]').content},
            data: new FormData(document.querySelector('#creditCardForm')),
            cache: false,
            dataType: false,
            processData: false,
            contentType: false,
        }).done(function (response) {
            if (response.status === 200) {
                location.href = '/sites'
            }
        }).fail(function (error) {
            resetErrors('#creditCardForm');
            setErrors(error.responseJSON.errors, '#creditCardForm');
        });
    };
});
