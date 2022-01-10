require('bootstrap');
$ = require('jquery');
require('./cryptopro/polyfills/addEventListener');
require('./cryptopro/polyfills/forEach');
require('./cryptopro/polyfills/map');
CryptoPro = require('crypto-pro');

$certList = document.querySelector('.cert-list');

window.CryptoPro.getUserCertificates(true).then(function (list) {

    list.forEach(function (cert) {
        var $certOption = document.createElement('a');
        $certOption.className = 'list-group-item list-group-item-action key-item';
        $certOption.href = "javascript:void(0)";
        $($certOption).attr('data-cert', cert.thumbprint);
        $certOption.text = cert.subjectName;
        $certList.appendChild($certOption);
    });
}, function (error) {
    console.error(error);
});

$(document).ready(function(){
    $('.cert-list').on('click', ".key-item", function () {
        let $thumbprint = $(this).data('cert');
        CryptoPro.getCertificate($thumbprint).then(function (certificate) {
            CryptoPro.createSignature(
                $thumbprint,
                window.btoa($('#form_dataHash').val())
            ).then(function (sign) {
                $('#form_sign').val(sign);
                $("#add-certificate").removeAttr("disabled");
            });
        }, function (error) {
            console.error(error);
        });
    });
});