$ = require('jquery');
require("jquery-mask-plugin")
require('daterangepicker');
require('popper.js');
require('bootstrap');




$(document).ready(function() {
    $('#form_auctionStep').mask('000000000000000.00', {reverse: true});
    $('#form_starting_price').mask('000000000000000.00', {reverse: true});
    $('#form_deposit_amount').mask('000000000000000.00', {reverse: true});
});

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

const localeParams = {
    format: 'DD.MM.YYYY HH:mm',
    applyLabel: 'Применить',
    cancelLabel: 'Отменить',
    firstDay: 1,
    daysOfWeek: daysOfWeekRu,
    monthNames: monthNamesRu
}

$(document).ready(function() {
    const nowDate = new Date();
    const today = new Date(nowDate.getFullYear(), nowDate.getMonth(), nowDate.getDate(), 0, 0, 0, 0);


    $('#form_organizerPhone').mask('+0-(000)-000-0000');

    $('#form_startDateOfApplications').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        timePicker: true,
        timePicker24Hour: true,
        minDate: today,
        minYear: 2015,
        maxYear: 2030,
        locale: localeParams
    });

    $('#form_closingDateOfApplications').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        timePicker: true,
        timePicker24Hour: true,
        minDate: today,
        minYear: 2015,
        maxYear: 2030,
        locale: localeParams
    });

    $('#form_summingUpApplications').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        timePicker: true,
        timePicker24Hour: true,
        minDate: today,
        minYear: 2015,
        maxYear: 2030,
        locale: localeParams
    });

    $('#form_startTradingDate').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        timePicker: true,
        timePicker24Hour: true,
        minDate: today,
        minYear: 2015,
        maxYear: 2030,
        locale: localeParams
    });

    $('#form_advancePaymentTime').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        timePicker: true,
        timePicker24Hour: true,
        minDate: today,
        minYear: 2015,
        maxYear: 2030,
        locale: localeParams
    });

    $('#form_dateEnforcementProceedings').mask('00.00.0000');
});