require([
    "jquery"
], function($){
    $(document).ready(function($) {
        var sw=false;
        
            $(document).off("keyup","input[name='postcode']");
            $(document).on("keyup","input[name='postcode']",function(event) {
            });
            $(document).off("change","select[name='custom_attributes[comuna]']");
            $(document).on("change","select[name='custom_attributes[comuna]']",function(event) {
                if(sw){
                    $("input[name='postcode']").val('000');
                    var e = jQuery.Event("keyup");
                    e.which = 77; // # Some key code value
                    $("input[name='postcode']").trigger( e );
                }
                sw=true;
            });

    });
    
});