require([
  'jquery'
], function($){
    var hasBeenTrigged = false;
    $(window).scroll(function() {
        if($(window).width() >= 768){
            if ($(this).scrollTop() >= 10 && !hasBeenTrigged) {
                $(".header.content").addClass("orderItems");
                hasBeenTrigged = true;
            }else if($(this).scrollTop() < 10 && hasBeenTrigged){
                $(".header.content").removeClass("orderItems");
                hasBeenTrigged = false;
            }
        }
    });
    
    var newsTimer = 0;
    var newsItem = document.getElementsByClassName("topLinks destacadoLi")[0];
    if(newsItem != null){
        var newsItems = newsItem.getElementsByTagName("li");
        setInterval(function(){
            for(var i = 0; i<newsItems.length;i++){
                newsItems[i].style.visibility = "hidden";
                newsItems[i].style.opacity = "0";
            }
            newsItems[newsTimer].style.visibility = "visible";
            newsItems[newsTimer].style.opacity = "1";
            
            if((newsTimer + 1) == newsItems.length){
                newsTimer = 0;
            }else{
                newsTimer++;
            }
        }, 4000);
    }
});
