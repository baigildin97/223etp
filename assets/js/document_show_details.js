$ = require('jquery');

$('.document-details').on('click', function(e){
    var elem = this;
    if ($(elem).attr('data-status') === 'false') {
        var target = $(elem).attr('data-target');
        var url = $(elem).attr('data-document-url');

        $.ajax({
            url: url,
            method: 'GET',
            success: function (data) {
                $(target).html(data);
                $(elem).attr('data-status', 'true');
            },
            error: function (data, status, error) {
                alert(data.status);
                //$(target).html();
            }
        })
    }
});