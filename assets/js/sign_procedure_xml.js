require('./cryptopro/polyfills/promise');
require('./cryptopro/polyfills/forEach');
$ = require('jquery');
require('bootstrap');
cryptoPro = require('crypto-pro');
Ladda = require('ladda');

$('#form_organizer').on('submit', function(e){
    e.preventDefault();
    const $thumbprint = $('#sign_procedure_notification').data('thumbprint');
    const $hash = $('#sign_procedure_notification').data('hash');
    cryptoPro.createSignature($thumbprint, window.btoa($hash)).then(function (sign) {
        $('#form_organizer_sign').val(sign);
        $('#form_organizer').unbind('submit').submit();
    });
});