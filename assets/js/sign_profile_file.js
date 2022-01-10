require('./cryptopro/polyfills/promise');
require('./cryptopro/polyfills/forEach');

$ = require('jquery');
require('bootstrap');
cryptoPro = require('crypto-pro');
Ladda = require('ladda');
jqueryUi = require('jquery-ui');

Ladda.bind(".sign");
$('.sign').on('click', function (e) {
    e.preventDefault();
    hash = $(this).next().val();
    document.file = this;
    certificate = $('#documentBlock').attr('data-certificate');
    profile = $('#documentBlock').attr('data-profile');

    cryptoPro.createSignature(certificate, window.btoa(hash))
        .then(function (cert) {
            $.ajax({
                url: '/profile/' + profile + '/sign-uploaded-file',
                method: 'POST',
                data: {fileId: $(document.file).attr('data-file-id'), sign: cert},
                success: function (data) {

                    Ladda.stopAll();
                    var filesSigned, category, filesSignedAmount, type;

                    type = $(document.file).attr('data-file-type');
                    category = $('a[href="#' + type + '"]');
                    filesSigned = category.children('span.files-signed');
                    filesSignedAmount = parseInt($(filesSigned).text());

                    $(filesSigned).text(filesSignedAmount + 1);

                    if (filesSignedAmount + 1 === parseInt($(filesSigned).next().text()) && type !== 'OTHER')
                        $(category).removeAttr('style').attr('style', 'color: #005500;');

                    $(document.file).prev().removeClass('badge-danger').addClass('badge-success').text('Подписан: '+ data.signed_at).show();
                    $(document.file).remove();
                    delete document.file;
                },
                error: function (e) {
                    console.error(e);
                }
            });
        }, function (e) {
            alert(e)
            Ladda.stopAll();
        });
});

$(function () {
    $('a[data-toggle="tab"]').on('click', function (e) {
        window.localStorage.setItem('activeTab', $(e.target).attr('href'));
    });
    var activeTab = window.localStorage.getItem('activeTab');
    if (activeTab) {
        $('#tab-profile-files a[href="' + activeTab + '"]').tab('show');
    }
});
$.fn.exists = function(){return this.length>0;}

(function () {
    modalRender = function modalRender(action, profile_id) {
        $.ajax({
            url: '/profile/' + profile_id + '/edit/' + action,
            dataType: 'json',
                success: function(data){
                    if($("#"+action+"Modal").exists()) {
                        $("#"+action+"Modal").remove();
                        $("body").append(data.html);
                    }else{
                        $("body").append(data.html);
                    }
                    $("#"+action+"Modal").modal('show');
                },
            error: function () {

            }
        });
    }
}());

$(document).ready(function(){
    $('[data-toggle="popover"]').popover();
});
