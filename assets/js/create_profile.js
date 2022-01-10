$ = require('jquery');
require("jquery-mask-plugin")
require('daterangepicker');
require('popper.js');
require('bootstrap');
window.moment = require('moment');

$(document).ready(function() {
    $('#form_issuanceDate').mask('00.00.0000');
    $('#form_phone').mask('+0(000)000-0000');

    $('#form_passportSeries').mask('0000');
    $('#form_passportNumber').mask('000000');
    $('#form_passportUnitCode').mask('000-000');
    $('#form_unitCode').mask('000-000');
    $('#form_kpp').mask('000000000');

    $('#form_ogrn').mask('0000000000000');
    $('#form_ogrnip').mask('000000000000000');

    $('#form_inn').mask('000000000000');
    $('#form_representativeInn').mask('000000000000');

    $('#form_factIndex').mask('000000');
    $('#form_legalIndex').mask('000000');
});


const changeCertificate = function () {
    let id = $( "#form_certificate option:selected" ).val()

    $.ajax({
        url: '/profile/create/getinfocertificate/' + id,
        method: 'POST',
        dataType: "json",
        success: function (data) {
            $("#form_inn").val(data.inn)
            $("#form_snils").val(data.snils)
            if (data.type_profile === 'LEGAL_ENTITY'){
                $("#position").show()
                $("#form_position").val(data.position)
            }

            if (data.type_profile === 'INDIVIDUAL_ENTREPRENEUR'){
                $("#position").show()
                $("#form_position").val(data.position)
            }

            if (data.type_profile === 'INDIVIDUAL'){
                $("#position").hide()
            }

        },
        error: function(data) {
            console.log(data);
        }
    })}
$('#form_certificate').on('change', changeCertificate);


$(document).ready(changeCertificate);

$('#address_matches').on('click', function () {
    if ( $(this).is(':checked') ) {
        $("#form_fact_addreses").hide();
        $("#form_factIndex").val($("#form_legalIndex").val());
        $("#form_factCountry").val($("#form_legalCountry").val());
        $("#form_factRegion").val($("#form_legalRegion").val());
        $("#form_factCity").val($("#form_legalCity").val());
        $("#form_factStreet").val($("#form_legalStreet").val());
        $("#form_factHouse").val($("#form_legalHouse").val());
    } else {
        $("#form_factIndex").val("");
        $("#form_factRegion").val("");
        $("#form_factCity").val("");
        $("#form_factStreet").val("");
        $("#form_factHouse").val("");
        $("#form_fact_addreses").show();
    }
})


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
    format: 'DD.MM.YYYY',
    applyLabel: 'Применить',
    cancelLabel: 'Отменить',
    firstDay: 1,
    monthNames: monthNamesRu,
    daysOfWeek: daysOfWeekRu
}

$(document).ready(function() {
    const nowDate = new Date();
    const today = new Date(nowDate.getFullYear(), nowDate.getMonth(), nowDate.getDate(), 0, 0, 0, 0);

    $('#form_passportIssuanceDate').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        minDate: 1901,
        minYear: 1901,
        showMonthAfterYear:true,
        maxYear: parseInt(moment().format('YYYY'),10),
        locale: localeParams
    });


    $('#form_issuanceDate').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        minDate: 1901,
        minYear: 1901,
        showMonthAfterYear:true,
        maxYear: parseInt(moment().format('YYYY'),10),
        locale: localeParams
    });

    $('#form_birthDay').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        minDate: 1901,
        minYear: 1901,
        showMonthAfterYear:true,
        maxYear: parseInt(moment().format('YYYY'),10),
        locale: localeParams
    });


});