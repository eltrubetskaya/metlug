$(document).ready(function() {
    $('#head').addClass('col-lg-24 col-md-24');
    $('#head').css('z-index','1');
    setTimeout(function(){
        $(".leftcurtain").stop().animate({
                width:'60px'
        }, 2000 );
        $(".rightcurtain").stop().animate({
                width:'60px'
        },2000 );
        },1000)
});
