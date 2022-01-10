const $ = require('jquery');


(function() {
    read = function read(id_notification = null, type) {
        $.ajax({
            url: '/notifications/read/' + id_notification,
            dataType: 'json',
            success: function (data) {
                $("#n-" + id_notification).html('<i class="fa fa-eye" title="Прочитано"></i>');
                let count_unread_notification =  $("#count_unread_notification").text();
                if(count_unread_notification == 1){
                    $("#notification-widget").removeClass('text-warning');
                    $("#count_unread_notification").remove();
                }else{
                    $("#count_unread_notification").text($("#count_unread_notification").text() - 1);
                }
            },
            error: function () {

            }
        });
    }
}());


