require('./cryptopro/polyfills/addEventListener');
require('./cryptopro/polyfills/promise');
require('./cryptopro/polyfills/forEach');
require('./cryptopro/polyfills/map');
CryptoPro = require('crypto-pro');
$ = require('jquery');

$certList = document.querySelector('.cert-list');

CryptoPro.getUserCertificates(true).then(function (list) {
    let certList = $('.cert-list');
    list.forEach(function (cert) {
        var certOption = document.createElement('a');
        certOption.className = 'list-group-item list-group-item-action key-item';
        certOption.href = "javascript:void(0)";
        $(certOption).attr('data-cert', cert.thumbprint);
        certOption.text = cert.subjectName;
        $(certList).append(certOption);
    });
}, function (error) {
    console.error(error);
});


$(document).ready(function(){
    $('.cert-list').on('click', '.key-item', function(){
        $("#sign_in").attr("disabled", true);
        document.certThumbprint = $(this).data('cert')
        let data = $('#form_data').val();
        CryptoPro.createSignature(document.certThumbprint, window.btoa(data))
            .then(function (cert) {
                $('#form_signedData').val(cert);
                $("#sign_in").removeAttr("disabled");
            }, function (e) {
                alert(e)
            });
    })
})
