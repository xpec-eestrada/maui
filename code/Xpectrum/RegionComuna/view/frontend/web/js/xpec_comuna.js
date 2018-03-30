require([
    "jquery",'domReady!'
], function($){
    'use strict';
    $(document).ready(function($,ko) {
        var sw=false;
        var regioninit=false;
        var baseUrl = document.location.origin;
        $('body').mouseover(function(){
            if(!sw){
                if($("select[name='xpec_region']").length){
                    $(document).off("change","select[name='xpec_region']");
                    $("select[name='xpec_region']").change(function(){

                        $("input[name='postcode']").val('0');
                        var e = jQuery.Event("keyup");
                        e.which = 77; // # Some key code value
                        $("input[name='postcode']").trigger( e );

                        $("select[name='xpec_region']").attr('disabled','disabled');
                        $("select[name='region_id']").attr('disabled','disabled');
                        
                        var id_region=$(this).val();
                        $.ajax({
                            url: baseUrl+'/regioncomunas/index/ajaxcomuna/',
                            data: {
                                id_region:id_region
                            },
                            type: "POST",
                            dataType: 'json'
                        }).done(function (data) {
                            var options='';
                            if(data.result.length){
                                $.each(data.result,function(index,item){
                                    options=options+"<option value='"+item.value+"'>"+item.label+"</option>";
                                });
                            }

                            $("select[name='region_id']").html(options);
                            $("select[name='xpec_region']").removeAttr("disabled");
                            $("select[name='region_id']").removeAttr("disabled");
                        });
                    });
                    $("select[name='region_id']").change(function(){
                        $("input[name='city']").val($(this).find("option:selected").text());
                        var e = jQuery.Event("keyup");
                        e.which = 77; // # Some key code value
                        $("input[name='city']").trigger( e );

                    });
                    sw=true;
                }
            }
        });
    
        $(document).off("keyup","input[name='postcode']");
        $(document).on("keyup","input[name='postcode']",function(event) {
        });
        

    });
    
});