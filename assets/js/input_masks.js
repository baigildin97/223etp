$ = require('jquery');
require("jquery-mask-plugin")

$(document).ready(function() {
    $('#form_bankBik').mask('000000000');
    $('#form_paymentAccount').mask('00000000000000000000');
    $('#form_correspondentAccount').mask('00000000000000000000');
});