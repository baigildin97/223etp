require('./cryptopro/polyfills/promise');
require('./cryptopro/polyfills/forEach');
$ = require('jquery');
require('bootstrap');
cryptoPro = require('crypto-pro');
Ladda = require('ladda');


$(document).on('click', '#bidStatement', function () {
    $('#bidStatementModal').find('.modal-body').load($(this).data('request-url'));
});

$('#sign_bid').on('click', function(e){
    e.preventDefault();
    const $thumbprint = $('#sign_bid').data('thumbprint');
    const $hash = $('#sign_bid').data('hash');
    cryptoPro.createSignature($thumbprint, window.btoa($hash)).then(function (sign) {
        $('#form_sign').val(sign);
        $('form').unbind('submit').submit();
    });
});

Ladda.bind(".sign_bid_file");
$('.sign_bid_file').on('click', function(e){
    e.preventDefault();
    const $hash = $(this).data('file-hash');
    const $bidId = $(this).data('bid-id');
    const $fileId = $(this).data('file-id');
    const $certificate = $('#documentBlock').data('certificate');
    document.file = this;

    cryptoPro.createSignature($certificate, window.btoa($hash))
        .then(function(cert){
            $.ajax({
                url: '/bid/'+$bidId+'/document/'+$fileId+'/sign',
                method: 'POST',
                data: {sign: cert},
                success: function(data){
                    $(document.file).replaceWith('<span class="badge badge-success">Подписан '+ data.signed_at +'</span>');
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
