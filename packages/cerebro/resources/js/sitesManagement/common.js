import {showHideFields} from "./loanFormLogic";

export function initColorPicker(){
    document.querySelectorAll('.jscolor').forEach((el) => {
        el.jscolor.fromString('#000000');
    });
}

export  function hideImageLinks() {
    document.querySelectorAll('[id*="-link-wrapper"]').forEach((el) => {
        el.style.display = 'none';
    });
}
export function imagePreviewModal() {
    const imagePreviewModal = new Modal(document.querySelector('#sitesImagePreviewModal'), {
        backdrop: true
    });

    document.querySelectorAll('a.image-preview').forEach((el) => {
        el.onclick = (event) => {
            event.preventDefault();
            document.querySelector('#preview-image').src = el.href;
            imagePreviewModal.show();
        }
    });
}

export function addOnThemeChangeEventClick($el){

    document.querySelector($el).onchange = () => {
        resetErrors('#sitesForm');
        showHideFields(document.querySelector($el).value);
    }
}

export function resetErrors($el) {

    $($el + " .invalid-feedback").html('');
    $($el + ' input').removeClass('is-invalid');
    $($el + ' select').removeClass('is-invalid');
    $($el + ' textarea').removeClass('is-invalid');
}

export function setErrors(errors, $el) {
    let input;
    for (let field in errors) {
        let originalField = null;
        let string;
        if (field.indexOf('.') > -1) {
            originalField = field;
            string = field.split('.');
            if (string.length === 4) {
                field = string[0] + '[' + string[1] + '][' + string[2] + '][' + string[3] + ']';
            } else {
                field = string[0] + '[' + string[1] + '][' + string[2] + ']';
            }
        }
        let $element;
        if(originalField !== null && string[2] === 'description') {
            $element = $el +' textarea[name="' + field + '"]';
        } else {
            $element = $el + ' input[name="' + field + '"]';
        }

        input = $($element);
        input.addClass('is-invalid');

        if (originalField !== null) {
            field = originalField;
        }
        input.next('.invalid-feedback').append(errors[field][0]);
    }

    let select;
    for (let field in errors) {
        select = $($el + ' select[name="' + field + '"]');
        select.addClass('is-invalid');
        for (let i = 0; i < errors[field].length; i++) {
            $("#" + select.attr('id') + "Feedback").append(errors[field][i]);
        }
    }
}

export function addOnchangeRemoveErrorsEvent() {
    document.querySelectorAll('input').forEach((el) => {
        el.onblur = (event) => {
            if (el.value !== '' && !el.classList.contains('jscolor')) {
                event.preventDefault();
                el.classList.remove('is-invalid');
                el.nextElementSibling.innerHTML = '';
            }
        }
    });

    document.querySelectorAll('textarea').forEach((el) => {
        el.onblur = (event) => {
            if (el.value !== '') {
                event.preventDefault();
                el.classList.remove('is-invalid');
                el.nextElementSibling.innerHTML = '';
            }
        }
    });

    document.querySelectorAll('select').forEach((el) => {
        el.onblur = (event) => {
            event.preventDefault();
            el.classList.remove('is-invalid');
            el.nextElementSibling.innerHTML = '';
        }
    });
}

