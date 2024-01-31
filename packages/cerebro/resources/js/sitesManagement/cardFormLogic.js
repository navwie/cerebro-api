import {addOnchangeRemoveErrorsEvent} from "./common";

export function addAddItemClickEvent() {
    document.querySelector('#add-item-button').onclick = () => {
        let itemRow = document.querySelectorAll('.item-row')
        itemRow = itemRow[itemRow.length - 1];
        itemRow.after(itemRow.cloneNode(true));
        let legends = document.querySelectorAll('.legend');
        let lastLegend = legends[legends.length - 1];
        let maxItemNumber = Number(0);
        document.querySelectorAll('.item-row').forEach((el) => {
            let number = Number(el.querySelector('.item-number').innerText);
            if (maxItemNumber < number) {
                maxItemNumber = number;
            }
        });
        maxItemNumber++;

        lastLegend.querySelector('.legend-label').innerHTML = 'Item #' + '<span class="item-number">' + maxItemNumber + '</span>';

        let newRow = document.querySelectorAll('.item-row');
        newRow = newRow[newRow.length - 1];

        newRow.querySelector('.item-id').setAttribute('name', 'card_item[' + maxItemNumber + '][id]');
        newRow.querySelector('.item-name').setAttribute('name', 'card_item[' + maxItemNumber + '][name]');
        newRow.querySelector('.item-image').setAttribute('name', 'card_item[' + maxItemNumber + '][image]');
        newRow.querySelector('.item-stars').setAttribute('name', 'card_item[' + maxItemNumber + '][stars]');
        newRow.querySelector('.item-button-first-color').setAttribute('name', 'card_item[' + maxItemNumber + '][btn_color_first]');
        newRow.querySelector('.item-button-second-color').setAttribute('name', 'card_item[' + maxItemNumber + '][btn_color_second]');
        newRow.querySelector('.item-button-text').setAttribute('name', 'card_item[' + maxItemNumber + '][btn_text]');
        newRow.querySelector('.item-button-url').setAttribute('name', 'card_item[' + maxItemNumber + '][btn_url]');
        newRow.querySelector('.item-description').setAttribute('name', 'card_item[' + maxItemNumber + '][description]');

        newRow.querySelectorAll('.item-benefit-input').forEach((el) => {
            let index = Number(el.closest('.item-benefit').querySelector('.benefit-number').innerText);
            el.setAttribute('name', 'card_item[' + maxItemNumber + '][benefits][' + (index - 1) + ']');
        });

        newRow.querySelectorAll('input').forEach((el) => {
            el.classList.remove('is-invalid');
        });

        newRow.querySelectorAll('textarea').forEach((el) => {
            el.classList.remove('is-invalid');
        });

        addAddBenefitClickEvent();
        addDeleteItemRowClickEvent();
        addDeleteBenefitClickEvent();
        addOnchangeRemoveErrorsEvent();


        newRow.querySelectorAll('input').forEach((el) => {
            el.value = '';
        });

        newRow.querySelectorAll('textarea').forEach((el) => {
            el.value = '';
        });

        newRow.querySelectorAll('.jscolor').forEach((el) => {
            new jscolor(el, {});
            el.jscolor.fromString('#000000');
        });

        newRow.querySelector('.image-preview').style.display = 'none';
        addHideDeleteButtonsWhenOneEntity();
    }
}


export function addAddBenefitClickEvent() {
    document.querySelectorAll('.add-item-benefit-button').forEach((el) => el.onclick = (event) => {
        let itemRow = event.target.closest('.item-row');
        let itemNumber = Number(itemRow.querySelector('.item-number').innerText);
        let benefitRow = itemRow.querySelectorAll('.item-benefit');
        benefitRow = benefitRow[benefitRow.length - 1];
        benefitRow.after(benefitRow.cloneNode(true));
        benefitRow = itemRow.querySelectorAll('.item-benefit');

        let maxNumber = Number(0);
        benefitRow.forEach((el) => {
            let number = Number(el.querySelector('.benefit-number').innerText);
            if (maxNumber < number) {
                maxNumber = number;
            }
        });
        maxNumber = maxNumber + 1;
        benefitRow[benefitRow.length - 1].querySelector('.benefit-label').innerHTML = 'Benefit #<span class="benefit-number">' + maxNumber + '</span>';
        benefitRow[benefitRow.length - 1].querySelectorAll('.item-benefit-input').forEach((el) => {
            let index = Number(el.closest('.item-benefit').querySelector('.benefit-number').innerText);
            el.setAttribute('name', 'card_item[' + itemNumber + '][benefits][' + (index - 1) + ']');
        });
        addDeleteBenefitClickEvent();
        addOnchangeRemoveErrorsEvent();
        addHideDeleteButtonsWhenOneEntity();
        benefitRow[benefitRow.length - 1].querySelectorAll('input').forEach((el) => {
            el.value = '';
        });
    });
}

export function addDeleteItemRowClickEvent() {
    document.querySelectorAll('.delete-item-button').forEach((el) => el.onclick = (event) => {
        let itemRows = document.querySelectorAll('.item-row');
        let itemRow = event.target.closest('.item-row');

        if (itemRows.length > 1) {
            itemRow.remove();
        }
        addHideDeleteButtonsWhenOneEntity();
    });
}

export function addDeleteBenefitClickEvent() {
    document.querySelectorAll('.delete-item-benefit-button').forEach((el) => el.onclick = (event) => {
        let benefits = event.target.closest('.item-row').querySelectorAll('.item-benefit');
        if (benefits.length > 1) {
            event.target.closest('.item-benefit').remove();
        }
        addHideDeleteButtonsWhenOneEntity();
    });

}

export function addHideDeleteButtonsWhenOneEntity(){
    let deleteItemButton = document.querySelectorAll('.delete-item-button');

    if (deleteItemButton.length === 1) {
        deleteItemButton[0].style.display = 'none';
    } else {
        deleteItemButton.forEach((el) => {
            el.style.display = 'block';
        });
    }

    document.querySelectorAll('.item-row').forEach((el) => {
        let deleteBenefitButton = el.querySelectorAll('.delete-item-benefit-button');

        if (deleteBenefitButton.length === 1) {
            deleteBenefitButton[0].style.display = 'none';
        } else {
            deleteBenefitButton.forEach((el) => {
                el.style.display = 'block';
            });
        }
    });
}
