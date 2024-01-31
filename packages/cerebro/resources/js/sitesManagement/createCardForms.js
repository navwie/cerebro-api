import {
    initColorPicker,
    setErrors,
    resetErrors,
    addOnchangeRemoveErrorsEvent,
} from './common';
import {addAddItemClickEvent, addAddBenefitClickEvent, addDeleteBenefitClickEvent, addHideDeleteButtonsWhenOneEntity } from './cardFormLogic';


document.addEventListener('DOMContentLoaded', () => {

    addAddItemClickEvent(1);
    initColorPicker();
    addAddBenefitClickEvent();
    addDeleteBenefitClickEvent();
    addDeleteItemRowClickEvent();
    addOnchangeRemoveErrorsEvent();
    addHideDeleteButtonsWhenOneEntity();

    document.querySelector('#btnCreditCardCreate').onclick = () => {
        $.ajax({
            url: '/cards-crud',
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

export function addDeleteItemRowClickEvent() {
    document.querySelectorAll('.delete-item-button').forEach((el) => el.onclick = (event) => {
        let itemRows = document.querySelectorAll('.item-row');
        let itemRow = event.target.closest('.item-row');

        if (itemRows.length > 1) {
            itemRow.remove();
        }
    });
}
