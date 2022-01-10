require('../../cryptopro/polyfills/promise');
require('../../cryptopro/polyfills/forEach');
$ = require('jquery');
require('bootstrap');
cryptoPro = require('crypto-pro');


$('form').on('submit', function(e){
    e.preventDefault();
    const $thumbprint = $('#sign_recall').data('thumbprint');
    const $hash = $('#sign_recall').data('hash');
    cryptoPro.createSignature($thumbprint, window.btoa($hash)).then(function (sign) {
        $('#form_sign').val(sign);
        $('form').unbind('submit').submit();
    });
});