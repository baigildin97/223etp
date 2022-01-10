require('./cryptopro/polyfills/promise');
require('./cryptopro/polyfills/forEach');
$ = require('jquery');
require('bootstrap');
cryptoPro = require('crypto-pro');
Ladda = require('ladda');

Ladda.bind(".sign_lot_file");
$('.sign_lot_file').on('click', function(e){
    e.preventDefault();
    const $hash = $(this).data('file-hash');
    const $certificate = $('#documentBlock').data('certificate');
    document.file = this;

    cryptoPro.createSignature($certificate, window.btoa($hash))
        .then(function(cert){
            $.ajax({
                url: '/lot/sign-uploaded-file',
                method: 'POST',
                data: {fileId: $(document.file).attr('data-file-id'), sign: cert},
                success: function(data){
                    $(document.file).replaceWith('<span class="badge badge-success">Подписан</span>');
                    Ladda.stopAll();
                },
                error: function(e){
                    console.error(e);
                }
            });
        }, function(e){alert(e)
            Ladda.stopAll();
        });
});