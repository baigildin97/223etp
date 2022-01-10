require('./cryptopro/polyfills/promise');
require('./cryptopro/polyfills/forEach');
$ = require('jquery');
require('bootstrap');
cryptoPro = require('crypto-pro');
Ladda = require('ladda');

Ladda.bind("#sign_auction_participant");
$('form').on('submit', function(e){
    e.preventDefault();
    const $thumbprint = $('#sign_auction_participant').data('thumbprint');
    const $hash = $('#sign_auction_participant').data('hash');
    cryptoPro.createSignature($thumbprint, window.btoa($hash)).then(function (sign) {
        $('#form_sign').val(sign);
        $('form').unbind('submit').submit();
    });
});