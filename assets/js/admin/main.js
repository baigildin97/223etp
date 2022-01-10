const $ = require('jquery');
require('daterangepicker');
require('popper.js');
require('bootstrap');
require("jquery-mask-plugin")

window.moment = require('moment');

const monthNamesRu = [
        "Январь",
        "Февраль",
        "Март",
        "Апрель",
        "Май",
        "Июнь",
        "Июль",
        "Август",
        "Сентябрь",
        "Октябрь",
        "Ноябрь",
        "Декабрь"
]

const daysOfWeekRu = [
        "Вс",
        "Пн",
        "Вт",
        "Ср",
        "Чт",
        "Пт",
        "Сб"
]
const m = moment(new Date(0));
$('#form_period').daterangepicker({
    singleDatePicker: true,
    showDropdowns: true,
    minYear: 2015,
    maxYear: 2030,
    locale: {
        format: 'DD.MM.YYYY',
        applyLabel: 'Применить',
        cancelLabel: 'Отменить',
        customRangeLabel: 'Пользовательский диапазон',
        firstDay: 1,
        daysOfWeek: daysOfWeekRu,
        monthNames: monthNamesRu
    }
});

$(document).ready(function() {
    $('#form_delayedPublication').mask('00.00.0000');
});