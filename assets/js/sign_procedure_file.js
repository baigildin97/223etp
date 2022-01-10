require('./cryptopro/polyfills/promise');
require('./cryptopro/polyfills/forEach');
$ = require('jquery');
require('bootstrap');
cryptoPro = require('crypto-pro');
Ladda = require('ladda');

Ladda.bind(".sign_procedure_file");
$('.sign_procedure_file').on('click', function(e){
    e.preventDefault();
    hash = $(this).next().val();
    document.file = this;
    certificate = $('#documentBlock').attr('data-certificate');
    cryptoPro.createSignature(certificate, window.btoa(hash))
        .then(function(cert){
            $.ajax({
                url: '/procedure/sign-uploaded-file',
                method: 'POST',
                data: {fileId: $(document.file).attr('data-file-id'), sign: cert},
                success: function(data){
                    Ladda.stopAll();
                    var filesSigned, category, filesSignedAmount, type;

                    type = $(document.file).attr('data-file-type');
                    category = $('a[href="#' + type + '"]');
                    filesSigned = category.children('span.files-signed');
                    filesSignedAmount = parseInt($(filesSigned).text());

                    $(filesSigned).text(filesSignedAmount + 1);

                    if (filesSignedAmount + 1 === parseInt($(filesSigned).next().text()))
                        $(category).removeAttr('style').attr('style', 'color: #005500;');

                    $(document.file).prev().removeClass('badge-danger').addClass('badge-success').text('Подписан: '+data.signed_at).show();
                    $(document.file).remove();
                    delete document.file;
                },
                error: function(e){
                    console.error(e);
                }
            });
        }, function(e){alert(e)
            Ladda.stopAll();
        });
});

$(function() {
    $('a[data-toggle="tab"]').on('click', function(e) {
        window.localStorage.setItem('activeTabProcedure', $(e.target).attr('href'));
    });
    var activeTab = window.localStorage.getItem('activeTabProcedure');
    if (activeTab) {
        $('#tab-procedure-files a[href="' + activeTab + '"]').tab('show');
    }
});