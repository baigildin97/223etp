require('../css/app.scss');
require('pace-progress');
require('../../public/assets/coreui.bundle.min');
require('svgxuse')
const $ = require('jquery');
require("jquery-mask-plugin")

//Верхний свайп меню
const topMenuValue = localStorage.getItem('top-menu');
if (topMenuValue === 'close'){
    $('#sidebar').removeClass('c-sidebar-lg-show');
}
$('#top-toggle').click(function () {
    if (topMenuValue === 'open') {
        localStorage.removeItem("top-menu");
        localStorage.setItem('top-menu', 'close');
    }else{
        localStorage.removeItem("top-menu");
        localStorage.setItem('top-menu', 'open');
    }
});//Верхний свайп меню

// Нижний свайп меню
const bottomMenuValue = localStorage.getItem('bottom-menu');
if (bottomMenuValue === 'close'){
    $('#sidebar').addClass('c-sidebar-unfoldable');
}
$('#sidebar-minimizer').click(function () {
    if (bottomMenuValue === 'open') {
        localStorage.removeItem("bottom-menu");
        localStorage.setItem('bottom-menu', 'close');
    }else{
        localStorage.removeItem("bottom-menu");
        localStorage.setItem('bottom-menu', 'open');
    }
});// Нижний свайп меню

$('.custom-file-input').on('change', function(event) {
    var inputFile = event.currentTarget;
    $(inputFile).parent()
        .find('.custom-file-label')
        .html(inputFile.files[0].name);
});

$(document).ready(function() {
    $('#form_money').mask('000000000000000.00', {reverse: true});
});
