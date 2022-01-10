require('../cryptopro/polyfills/promise');
require('../cryptopro/polyfills/forEach');
$ = require('jquery');
require('bootstrap');
cryptoPro = require('crypto-pro');

$('form').on('submit', function(e){
    e.preventDefault();
    const $thumbprint = $('#sign_organizer_bid').data('thumbprint');
    const $hash = $('#sign_organizer_bid').data('hash');
    CryptoPro.createSignature($thumbprint, window.btoa($hash)).then(function (sign) {
        $('#form_sign').val(sign);
        $('form').unbind('submit').submit();
    });
});
