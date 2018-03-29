require([
    "jquery","fancybox"
], function($,fancybox){
    var swload=false;
    $('body').append('<div class="load-background-xpec"></div>');
    $('body').append('<div class="load-xpec"></div>');
    setTimeout(function(){
        $('.load-background-xpec').remove();
        $('.load-xpec').remove();
        $('.data.item.title.active').append('<div class="infoact"></div>');
        $('.data.item.title').append('<div class="xpec-separa"></div>');
        $('.data.item.content').append('<div class="xpec-separa2"></div>');
        var urlguia=$('.url-guia-talla').val();
        if(urlguia!=""){
            $('.swatch-attribute.size').append('<div class="guiatallas"><a href="#modal-preview" class="fancybox">Guía de Tallas</a></div>');
            $('td[data-th="Guia de Talla"]').html('<a href="#modal-preview" class="fancybox">Guía de Tallas</a>');
        }
        $(document).on('click', '.data.switch', function(event) {
            var newh=parseInt($(event.target).parent().next().height())+72;
            $('.product.info.detailed').css({'height':newh+'px'});
            $('.infoact').remove();
            $(event.target).parent().append('<div class="infoact"></div>');
        });
        $(document).on('click', '.data.item.title', function(event) {
            var newh=parseInt($(this).next().height())+72;
            $('.product.info.detailed').css({'height':newh+'px'});
            $('.infoact').remove();
            $(this).append('<div class="infoact"></div>');
        });
        $(document).on('keyup', '.data.item.title', function(event) {
            var newh=parseInt($(this).next().height())+72;
            $('.product.info.detailed').css({'height':newh+'px'});
            $('.infoact').remove();
            $(this).append('<div class="infoact"></div>');
        });
        $( window ).resize(function() {
            var newh=parseInt($('.data.item.title.active').next().height())+92;
            $('.product.info.detailed').css({'height':newh+'px'});
        });
        $("#review-form").find('button.submit').click(function(){
            var newh=parseInt($(this).parents('.data.item.content').height())+140;
            $('.product.info.detailed').css({'height':newh+'px'});
        });
        $('body').append('<div id="modal-preview" style="display:none; z-index:5;"><div class="contenido-fancybox"><img src="/pub/media/wysiwyg/Guia_Talla/'+urlguia+'" /></div></div>');
        $('.fancybox').fancybox({
            maxWidth	: 800,
            maxHeight	: 600,
            width		: '70%',
            height		: '70%',
            autoSize	: true,
            closeClick	: true,
        });
        swload=true;
    }, 4000);
    (function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/es_ES/sdk.js#xfbml=1&version=v2.9&appId=639569382801292";
    fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));

    $('body').mouseover(function(){
        if(swload){
            $('.product.info.detailed').css({'display':'block'});
            var newh=parseInt($('.data.item.title.active').next().height())+102;
            $('.product.info.detailed').css({'height':newh+'px'});
            $('.titulo-bestseller').css({'display':'block'});
            $('.container-bestseller').css({'display':'block'});
        }
    });
    $('body').keydown(function(){
        if(swload){
            $('.product.info.detailed').css({'display':'block'});
            var newh=parseInt($('.data.item.title.active').next().height())+102;
            $('.product.info.detailed').css({'height':newh+'px'});
            $('.titulo-bestseller').css({'display':'block'});
            $('.container-bestseller').css({'display':'block'});
        }
    });
});