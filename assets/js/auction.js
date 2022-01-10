const $ = require('jquery');
Ladda = require('ladda');
cryptoPro = require('crypto-pro');
toastr = require('toastr');


timeend = new Date($("#date-closing").data("closing-time") * 1000);
function closingTimeAuction() {
    today = new Date();
    today = Math.floor((timeend - today) / 1000);
    tsec = today % 60; today = Math.floor(today / 60); if(tsec < 10) tsec = '0' + tsec;
    tmin = today % 60; today = Math.floor(today / 60); if( tmin < 10) tmin='0' + tmin;
    thour = today % 24; today = Math.floor(today / 24);
    timestr = tmin +" минут "+ tsec +" секунд";
    $("#date-closing").text(timestr);
    setTimeout(closingTimeAuction, 1000);
}
closingTimeAuction();


function Timer(fn, t) {
    var timerObj = setInterval(fn, t);

    this.stop = function() {
        if (timerObj) {
            clearInterval(timerObj);
            timerObj = null;
        }
        return this;
    }

    this.start = function() {
        if (!timerObj) {
            this.stop();
            timerObj = setInterval(fn, t);
        }
        return this;
    }

    this.reset = function(newT) {
        t = newT;
        return this.stop().start();
    }
}

function getOffersAuction() {
    bidId = $("#offers-list").data("bid-id");
    auctionId = $("#offers-list").data("auction-id");
    lotId = $("#offers-list").data("lot-id");

$.ajax({
    url: "/lot/" + lotId + "/auction/" + auctionId + "/offers",
    dataType: "json",
    success: function (data) {
        if(data.status_auction == 'STATUS_COMPLETED'){
            toastr["warning"]("Аукционный зал закрыт.")
                setTimeout(function () {
                    $(location).attr('href', '/procedure/' + procedureId);
                }, 3000);
        }
        $("#preloader-offers-list").hide(300);
        $("#offers-list").html(data.offersHtml);
        $("#timer").show(300);
        $("#last_offer_cost").text(data.last_offer_cost);
        $("#my_offers_cost").text(data.my_offers_cost);
        $("#current_cost").text(data.last_offer_cost);
        $("#next_cost").text(data.next_cost);
        $("#default_closed_time").text(data.default_closed_time);
        $("#state_percent").text(data.state_percent + "%");
        $("#current_position").text(data.current_position);
        timeend = data.closing_time * 1000;
    },
    error: function(data) {
        console.log(data);
    }
    });
}

if(localStorage.getItem('auctionTimeUpdatePage') && localStorage.getItem('auctionTimeUpdatePage') >= 3000 && localStorage.getItem('auctionTimeUpdatePage') <= 60000){
    var timeInterval = localStorage.getItem('auctionTimeUpdatePage');
}else {
    var timeInterval = 3000;
}


getOffersAuction();
$(".set-new-interval").on("click", function () {
    var newTimer = $("#new-timer").val();
    if(newTimer < 3 || newTimer > 60){
        alert("Допустимый интервал с 3 сек. до 1 минуты.");
    }else{
        timeInterval = newTimer * 1000;
        tSecCounter = newTimer;
        localStorage.setItem('auctionTimeUpdatePage', timeInterval)
    }
})

var tSecCounter =  timeInterval / 1000;
function counterSecUpdatePage(){
    timestr = tSecCounter + " сек.";
    if(tSecCounter == 60) timestr = tSecCounter / 60 + " минута";
    $("#counterTime").text(timestr)
    if(tSecCounter === 0){
        getOffersAuction();
        tSecCounter = timeInterval / 1000;
    }else{
        tSecCounter = tSecCounter - 1;
    }
}
setInterval(counterSecUpdatePage, 1000);

changePriceRange();
function changePriceRange(){
    const price =  document.getElementById("cost").value;
    document.getElementById('price-range').innerText = price + " ₽";
}

Ladda.bind("#sign_bet");
$("#sign_bet").on("click", function () {
    const auctionId = $("#auctionId").val();
    const cost = $("#cost").val();
    const bidId = $("#offers-list").data("bid-id");
    $.ajax({
        url: '/auction/' + auctionId + '/hash/' + bidId,
        method: 'POST',
        dataType: 'json',
        data: JSON.stringify({'cost': cost }),
        success: function (result) {
            sendBet(result.hash);
        },
        error: function(data) {
            console.log(data);
        }
    })
})

function sendBet(hash){
        const thumbprint = $('#thumbprint').val();
        const cost = $("#cost").val();
        const bidId = $("#offers-list").data("bid-id");
    cryptoPro.createSignature(thumbprint, window.btoa(hash)).then(function (sign) {
        let params = {
                hash: hash,
                sign: sign,
                cost: cost,
            }
            $.ajax({
               url: '/auction/' + auctionId + '/bet/' + bidId,
               data: JSON.stringify(params),
               method: 'POST',
               dataType: 'json',
               success: function (result) {
                   if (result.success){
                       toastr["success"](result.success);
                   }else{
                       toastr["error"](result.error);
                   }
                   getOffersAuction();
                   Ladda.stopAll();
                   localStorage.removeItem('hash')
               },
                error: function(data) {
                    console.log(data);
                }
            });
        }, function (e) {
            Ladda.stopAll();
        });
}