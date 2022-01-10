require('../cryptopro/polyfills/addEventListener');
require('../cryptopro/polyfills/promise');
require('../cryptopro/polyfills/forEach');
require('../cryptopro/polyfills/map');
CryptoPro = require('crypto-pro');
$ = require('jquery');
require('bootstrap');



$certList = document.querySelector('.cert-list');

$('#button_get_cert_list').on('click', function(event) {
    event.preventDefault();
    $('#certList').modal({
        backdrop: 'static',
        keyboard: false
    });

    window.CryptoPro.call('getCertsList', true).then(function (list) {
        list.forEach(function (cert) {
            var $certOption = document.createElement('a');
            $certOption.className = 'list-group-item list-group-item-action key-item';
            $certOption.href = "javascript:void(0)";
            $($certOption).attr('data-cert', cert.thumbprint);
            console.log(cert.subjectName);
            $certOption.text = cert.subjectName;
            $certList.appendChild($certOption);
        });
    }, function (error) {
        console.error(error);
    });
});

$(document).ready(function(){
    $('.cert-list').on('click', ".key-item", function () {
        let $thumbprint = $(this).data('cert');
        window.CryptoPro.getCertificate($thumbprint).then(function (certificate) {
            console.log('dsdssddsds')
            certificate.getOwnerInfo().then(function (cc ) {
                $('#form_subjectName').val(window.btoa(unescape(encodeURIComponent(JSON.stringify(cc)))));
            });

            certificate.getIssuerInfo().then(function (cc) {
                $('#form_issuerName').val(window.btoa(unescape(encodeURIComponent(JSON.stringify(cc)))));
            });

            $('#form_thumbprint').val(certificate.thumbprint);
            $('#form_validFrom').val(certificate.validFrom);
            $('#form_validTo').val(certificate.validTo);
        }, function (error) {
            console.error(error);
        });
    });
});