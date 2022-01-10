require('./cryptopro/polyfills/promise');
require('./cryptopro/polyfills/forEach');
$ = require('jquery');
require('bootstrap');
cryptoPro = require('crypto-pro');
Ladda = require('ladda');


$('form').on('submit', function(e){
    e.preventDefault();
    const $thumbprint = $('#sign_profile').data('thumbprint');
    const $hash = $('#sign_profile').data('hash');
    cryptoPro.createSignature($thumbprint, window.btoa($hash)).then(function (sign) {
        $('#form_sign').val(sign);
        $('form').unbind('submit').submit();
    });
});