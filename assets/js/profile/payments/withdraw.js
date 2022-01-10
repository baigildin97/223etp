require('../../cryptopro/polyfills/addEventListener');
require('../../cryptopro/polyfills/promise');
require('../../cryptopro/polyfills/forEach');
require('../../cryptopro/polyfills/map');
CryptoPro = require('crypto-pro');
$ = require('jquery');
require('bootstrap');



$certList = document.querySelector('.cert-list');

$('#sign').on('click', function(event) {
    event.preventDefault();
    const $hash = $('#form_hash').val();
    const $thumbprint = $('#sign').data('thumbprint')

    CryptoPro.createSignature($thumbprint, window.btoa($hash), true).then(function (sign) {
        $('#form_sign').val(sign);
        $('form').submit();
    }, function (error) {
        console.error(error);
    });
});

