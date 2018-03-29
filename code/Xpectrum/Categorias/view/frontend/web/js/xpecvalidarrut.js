require([
    "jquery","Validador",'mage/calendar'
], function($){
    var d = new Date();
    var n = d.getFullYear();
    var min=n-80;
    var max=n-7;
    var range=min+":"+max;
    $.extend(true, $, {
        calendarConfig: {
            changeYear      : true,
            changeMonth     : true,
            yearRange       : range,
            monthNamesShort : [ "ene","feb","mar","abr","may","jun","jul","ago","sep","oct","nov","dic" ],
            dayNames        : [ "domingo","lunes","martes","miércoles","jueves","viernes","sábado" ],
            dayNamesShort   : [ "dom","lun","mar","mié","jue","vie","sáb" ],
            dayNamesMin     : [ "D","L","M","M","J","V","S" ],
            defaultDate     : new Date(2000,00,01)
        }
    });
    $(document).ready(function($) {
        var cargore=false;
        var checksu=false;
        var checksex=false;
        var placeholerdir='Avenida del Parque 4314';
        var maxlengthdireccion=40;
        var idcomuna=parseInt($("select[name='comuna']").val());
        $("#rut").attr('placeholder','RUT Ej: 12345678-9');
        $("#rut").attr('maxlength',10);
        $("input[name='telefono']").attr('placeholder','Ingrese un número de contacto');//Placeholder Numero de contacto
        $("input[name='street[0]']").attr('placeholder',placeholerdir);//Placeholder Dirección
        $("input[name='street[]']").attr('placeholder',placeholerdir);//Placeholder Dirección
        $("#complemento").attr('maxlength',10);
        $("#telefono").attr('maxlength',9);
        if($('.form-address-edit').length){
            var action=$('.form-address-edit').attr('action');
            var arr=action.split("id");
            if (typeof arr[1]==='undefined'){
                var valreg=$("select[name='region_id']").val();
                $("select[name='comuna']").html('<option ="">Seleccione</option>');
            }
        }
        //$("select[name='comuna']").html('<option ="">Seleccione</option>');
        $("input[name='street[]']").attr('maxlength',maxlengthdireccion);//Longitud de campo direccion
        var opt="<option value=''>Seleccione sexo</option>";
        $("select[name='gender']").find('option').each(function () {
            if($(this).val()!=""){
                opt=opt+"<option value='"+$(this).val()+"'>"+$(this).text()+"</option>";
            }
        });
        var sexsel=$("select[name='gender']").val();
        $("select[name='gender']").html(opt);
        $("select[name='gender']").val(sexsel);

        $(document).on("blur","#telefono",function(){// Aqui se valida Numero de contacto
            var celular=$(this).val().trim();
            if(celular!=""){
                if(celular.length==9 || celular.length==8 ){
                    var res = parseInt(celular.substring(0,1));
                    if(isNaN(res) || (res!=9 && res!=2) ){
                        $("#telefono-error").remove();
                        $("#telefono").addClass('mage-error');
                        $("#telefono").parent('.control').append('<div for="telefono" generated="true" class="mage-error" id="telefono-error">Número de contacto Invalido.</div>');
                    }else{
                        $("#telefono-error").remove();
                        $("#telefono").removeClass('mage-error');
                    }
                }else{
                    $("#telefono-error").remove();
                    $("#telefono").addClass('mage-error');
                    $("#telefono").parent('.control').append('<div for="telefono" generated="true" class="mage-error" id="telefono-error">Longitud minima es de 8.</div>');
                }
            }
        });
        $("#rut").blur(function(){
            var rut=$(this).val();
            if(rut.length){
                if( window.Fn.validaRut(rut) ){
                    $("#rut-error").remove();
                    $("#rut").removeClass('mage-error');
                }else{
                    $("#rut-error").remove();
                    $("#rut").addClass('mage-error');
                    $(this).parent('.control').append('<div for="rut" generated="true" class="mage-error" id="rut-error">Rut Invalido.</div>');
                }
            }
        });
        $("body").mouseover(function(){
            if(!cargore){
                if($("select[name='region_id']").length){
                    $("select[name='custom_attributes[comuna]']").html('<option ="">Seleccione</option>');
                    $("select[name='custom_attributes[comuna]']").removeClass('mage-error');
                    $("#comuna-error").remove();
                    $("select[name='region_id']").removeClass('mage-error');
                    $("#region-error").remove();
                    if($('.form-shipping-address').length){
                        $("select[name='region_id']").val('');
                    }
                    cargore=true;
                }
            }
            if(!checksu){
                if( $("input[name='is_subscribed']").length ){
                    checksu=true;
                    var e = jQuery.Event("click");
                    $("input[name='is_subscribed']").trigger( e );
                }
            }
            if(!checksex){
                var opt="<option value=''>Seleccione sexo</option>";
                $("select[name='custom_attributes[sexo]']").find('option').each(function () {
                    if($(this).val()!=""){
                        opt=opt+"<option value='"+$(this).val()+"'>"+$(this).text()+"</option>";
                    }
                });
                $("select[name='custom_attributes[sexo]']").html(opt);
                opt="<option value=''>Seleccione sexo</option>";
                $("select[name='gender']").find('option').each(function () {
                    if($(this).val()!=""){
                        opt=opt+"<option value='"+$(this).val()+"'>"+$(this).text()+"</option>";
                    }
                });
                var sexsel=$("select[name='gender']").val();
                $("select[name='gender']").html(opt);
                $("select[name='gender']").val(sexsel);
                checksex=true;
            }
            $("#rut").attr('maxlength',10);
            $("input[name='custom_attributes[rut]']").attr('placeholder','RUT Ej: 12345678-9');
            $("input[name='custom_attributes[rut]']").attr('maxlength',10);
            $("input[name='custom_attributes[celular]']").attr('maxlength',9);
            $("input[name='street[0]']").attr('maxlength',maxlengthdireccion);//Longitud de campo direccion
            $("input[name='telephone").attr('placeholder','Ingrese un número de contacto');//Placeholder Numero de contacto
            $("input[name='street[0]']").attr('placeholder',placeholerdir);//Placeholder Dirección
            $("input[name='telephone']").attr('maxlength',9);
            $("input[name='street[]']").attr('placeholder',placeholerdir);//Placeholder Dirección
        });
        $("body").keydown(function(){
            if(!cargore){
                if($("select[name='region_id']").length){
                    $("select[name='custom_attributes[comuna]']").html('<option ="">Seleccione</option>');
                    $("select[name='custom_attributes[comuna]']").removeClass('mage-error');
                    $("#comuna-error").remove();
                    $("select[name='region_id']").removeClass('mage-error');
                    $("#region-error").remove();
                    if($('.form-shipping-address').length){
                        $("select[name='region_id']").val('');
                    }
                    cargore=true;
                }
            }
            if(!checksu){
                if( $("input[name='is_subscribed']").length ){
                    checksu=true;
                    var e = jQuery.Event("click");
                    $("input[name='is_subscribed']").trigger( e );
                }
            }
            if(!checksex){
                var opt="<option value=''>Seleccione sexo</option>";
                $("select[name='custom_attributes[sexo]']").find('option').each(function () {
                    if($(this).val()!=""){
                        opt=opt+"<option value='"+$(this).val()+"'>"+$(this).text()+"</option>";
                    }
                });
                $("select[name='custom_attributes[sexo]']").html(opt);
                opt="<option value=''>Seleccione sexo</option>";
                $("select[name='gender']").find('option').each(function () {
                    if($(this).val()!=""){
                        opt=opt+"<option value='"+$(this).val()+"'>"+$(this).text()+"</option>";
                    }
                });
                var sexsel=$("select[name='gender']").val();
                $("select[name='gender']").html(opt);
                $("select[name='gender']").val(sexsel);
                checksex=true;
            }
            $("#rut").attr('maxlength',10);
            $("input[name='custom_attributes[rut]']").attr('placeholder','RUT Ej: 12345678-9');
            $("input[name='custom_attributes[rut]']").attr('maxlength',10);
            $("input[name='custom_attributes[celular]']").attr('maxlength',9);
            $("input[name='telephone").attr('placeholder','Ingrese un número de contacto');//Placeholder Numero de contacto
            $("input[name='telephone']").attr('maxlength',9);
            $("input[name='street[0]']").attr('placeholder',placeholerdir);//Placeholder Dirección
            $("input[name='street[]']").attr('placeholder',placeholerdir);//Placeholder Dirección
            //$("select[name='comuna']").html('<option ="">Seleccione</option>');
        });
        $(document).on("blur","input[name='custom_attributes[rut]']",function(){
            $("input[name='custom_attributes[rut]']").attr('placeholder','RUT Ej: 12345678-9');
            var rut=$(this).val();
            if(rut.length){
                if( window.Fn.validaRut(rut) ){
                    $("#rut-error").remove();
                    $(this).removeClass('mage-error');
                    if(!$("#rut").length){
                        $('.form-shipping-address').append('<input id="rut" type="hidden" name="rut"  >');
                    }
                    $("#rut").val(rut);
                }else{
                    $("#rut-error").remove();
                    $(this).addClass('mage-error');
                    $(this).parent('.control').append('<div for="rut" generated="true" class="mage-error" id="rut-error">Rut Invalido.</div>');
                    $("input[name='custom_attributes[rut]']").val('');
                    var e = jQuery.Event("keyup");
                    e.which = 77; // # Some key code value
                    $("input[name='custom_attributes[rut]']").trigger( e );
                }
            }
        });
        $(document).on("change","input[name='custom_attributes[fecha_de_nacimiento]']",function(){
            var fecha=$(this).val();
            var date_regex = /^(0[1-9]|1[0-2])\/(0[1-9]|1\d|2\d|3[01])\/(19|20)\d{2}$/ ;
            if(!(date_regex.test(fecha))){
                sw=false;
                $("#fecha-error").remove();
                $(this).addClass('mage-error');
                $(this).parent('.control').append('<div for="rut" generated="true" class="mage-error" id="fecha-error">Fecha Invalida.</div>');
            }else{
                $("#fecha-error").remove();
            }
        });
        $(document).on("change","input[name='dob']",function(){
            var fecha=$(this).val();
            var date_regex =/^([0-9]{2})\-([0-9]{2})\-([0-9]{4})$/;
            if(fecha.trim()!=""){
                if(!(date_regex.test(fecha) )){
                    $("#fecha-error").remove();
                    $(this).addClass('mage-error');
                    $(this).parent('.control').append('<div for="fecha" generated="true" class="mage-error" id="fecha-error">Fecha Invalida.</div>');
                }else{
                    $(this).removeClass('mage-error');
                    $("#fecha-error").remove();
                }
            }else{
                $("#fecha-error").remove();
                $(this).addClass('mage-error');
                $(this).parent('.control').append('<div for="fecha" generated="true" class="mage-error" id="fecha-error">Seleccione fecha de nacimiento.</div>');
            }
            
        });
        $(document).on("change","select[name='gender']",function(){
            var sexo=$("select[name='gender'] option:selected").text();
            if(sexo.trim()==""){
                $("#sexo-error").remove();
                $("select[name='gender']").addClass('mage-error');
                $("select[name='gender']").parent('.control').append('<div for="sexo" generated="true" class="mage-error" id="sexo-error">Seleccione sexo.</div>');
            }else{
                $("#sexo-error").remove();
                $("select[name='gender']").removeClass('mage-error');
            }
        });
        $(document).on("change","select[name='region_id']",function(){
            var region=$.trim($(this).val());
            if(region==""){
                $("#region-error").remove();
                $("select[name='region_id']").addClass('mage-error');
                $("select[name='region_id']").parent('.control').append('<div for="region" generated="true" class="mage-error" id="region-error">Seleccione una Región.</div>');
            }else{
                $("#region-error").remove();
                $("select[name='region_id']").removeClass('mage-error');
            }
        });
        $(document).on("change","select[name='custom_attributes[comuna]']",function(){
            var comuna=$.trim($(this).val());
            if(comuna==""){
                $("#comuna-error").remove();
                $("select[name='custom_attributes[comuna]']").addClass('mage-error');
                $("select[name='custom_attributes[comuna]']").parent('.control').append('<div for="comuna" generated="true" class="mage-error" id="comuna-error">Seleccione una Comuna.</div>');
            }else{
                $("#comuna-error").remove();
                $("select[name='custom_attributes[comuna]']").removeClass('mage-error');
            }
        });
        $(document).on("blur","input[name='telephone']",function(){
            var telefono=$(this).val().trim();
            if(telefono.length==9 || telefono.length==8 ){
                var res = parseInt(telefono.substring(0,1));
                if(isNaN(res) || (res!=9 && res!=2 ) ){
                    $("#telephone-error").remove();
                    $("#telefono-error").remove();
                    $("input[name='telephone']").addClass('mage-error');
                    $("input[name='telephone']").parent('.control').append('<div for="telefono" generated="true" class="mage-error" id="telefono-error">Número de contacto Invalido.</div>');
                }else{
                    $("#telephone-error").remove();
                    $("#telefono-error").remove();
                    $("input[name='telephone']").removeClass('mage-error');
                }
            }else{
                $("#telephone-error").remove();
                $("#telefono-error").remove();
                $("input[name='telephone']").addClass('mage-error');
                $("input[name='telephone']").parent('.control').append('<div for="telefono" generated="true" class="mage-error" id="telefono-error">Longitud minima es de 8.</div>');
            }
        });
        $(document).on("click","button.continue.primary",function(e){
            if(!$('.shipping-address-items').length){
                if(!validateAll()){
                    e.preventDefault();
                }
            }
        });
        $(document).on("click","button.action.submit.primary",function(e){
            if(!validate2All()){
                e.preventDefault();
            }
        });
        $(document).on("click",".action-save-address",function(e){
            var ev = jQuery.Event("click");
            setTimeout(function(){
                if($("input[name='telephone']").length){
                    var telefono=$("input[name='telephone']").val().trim();
                    if(telefono.length==9 || telefono.length==8 ){
                        var res = parseInt(telefono.substring(0,1));
                        if(isNaN(res) || (res!=9 && res!=2 ) ){
                            $("#telephone-error").remove();
                            $("#telefono-error").remove();
                            $("input[name='telephone']").addClass('mage-error');
                            $("input[name='telephone']").parent('.control').append('<div for="telephone" generated="true" class="mage-error" id="telephone-error">Número de contacto Invalido.</div>');
                            $('.shipping-address-item.selected-item .edit-address-link').trigger(ev);
                        }else{
                            $("#telephone-error").remove();
                            $("#telefono-error").remove();
                            $("input[name='telephone']").removeClass('mage-error');
                        }
                    }else{
                        $("#telephone-error").remove();
                        $("#telefono-error").remove();
                        $("input[name='telephone']").addClass('mage-error');
                        $("input[name='telephone']").parent('.control').append('<div for="telephone" generated="true" class="mage-error" id="telephone-error">Longitud minima es de 8.</div>');
                        $('.shipping-address-item.selected-item .edit-address-link').trigger(ev);
                    }
                }
                if($("select[name='custom_attributes[comuna]']").length){
                    if($("select[name='custom_attributes[comuna]']").val()==""){
                        $("#comuna-error").remove();
                        $("select[name='custom_attributes[comuna]']").addClass('mage-error');
                        $("select[name='custom_attributes[comuna]']").parent('.control').append('<div for="comuna" generated="true" class="mage-error" id="comuna-error">Seleccione Comuna.</div>');
                        $('.shipping-address-item.selected-item .edit-address-link').trigger(ev);
                    }else{
                        $("#comuna-error").remove();
                        $("select[name='custom_attributes[comuna]']").removeClass('mage-error');
                    }
                }
            },1000);
        });
        $(".form-address-edit").submit(function( event ) {
            var rut=$("#rut").val();
            if($("#rut").length && !window.Fn.validaRut(rut)){
                event.preventDefault();
                setTimeout(function(){
                    $("#rut-error").remove();
                    $("#rut").parent('.control').append('<div for="rut" generated="true" class="mage-error" id="rut-error">Rut Invalido.</div>');
                    $("#rut").addClass('mage-error');
                    $("#rut").focus();
                }, 1000);
                return false;
            }        
        });
        $(".form-edit-account").submit(function( event ) {
            var rut=$("#rut").val();
            if(!window.Fn.validaRut(rut)){
                event.preventDefault();
                setTimeout(function(){
                    $("#rut-error").remove();
                    $("#rut").parent('.control').append('<div for="rut" generated="true" class="mage-error" id="rut-error">Rut Invalido.</div>');
                    $("#rut").addClass('mage-error');
                    $("#rut").focus();
                }, 1000);
                return false;
            }        
        });
        function validateAll(){
            var sw=true;
            $(document).off("change","select[name='custom_attributes[sexo]']");
            $(document).on("change","select[name='custom_attributes[sexo]']",function(){
                var sexo=$("select[name='custom_attributes[sexo]'] option:selected").text();
                if(sexo.trim()==""){
                    sw=false;
                    $("#sexo-error").remove();
                    $("select[name='custom_attributes[sexo]']").addClass('mage-error');
                    $("select[name='custom_attributes[sexo]']").parent('.control').append('<div for="sexo" generated="true" class="mage-error" id="sexo-error">Seleccione sexo.</div>');
                }else{
                    $("#sexo-error").remove();
                    $("select[name='custom_attributes[sexo]']").removeClass('mage-error');
                }
            });
            if($("input[name='custom_attributes[rut]']").length){
                var rut=$.trim($("input[name='custom_attributes[rut]']").val());
                if(rut!="" && window.Fn.validaRut(rut) ){
                    $("#rut-error").remove();
                    $("input[name='custom_attributes[rut]']").removeClass('mage-error');
                }else{
                    sw=false;
                    $("#rut-error").remove();
                    $("input[name='custom_attributes[rut]']").addClass('mage-error');
                    $("input[name='custom_attributes[rut]']").parent('.control').append('<div for="rut" generated="true" class="mage-error" id="rut-error">Rut Invalido.</div>');
                    $("input[name='custom_attributes[rut]']").focus();
                }
            }
            if($("input[name='custom_attributes[fecha_de_nacimiento]']").length){
                var fecha=$("input[name='custom_attributes[fecha_de_nacimiento]']").val();
                var date_regex = /^(0[1-9]|1[0-2])\/(0[1-9]|1\d|2\d|3[01])\/(19|20)\d{2}$/ ;
                if(!(date_regex.test(fecha))){
                    sw=false;
                    $("#fecha-error").remove();
                    $("input[name='custom_attributes[fecha_de_nacimiento]']").addClass('mage-error');
                    $("input[name='custom_attributes[fecha_de_nacimiento]']").parent('.control').append('<div for="fecha" generated="true" class="mage-error" id="fecha-error">Fecha Invalida.</div>');
                    $("input[name='custom_attributes[fecha_de_nacimiento]']").focus();
                }
            }
            
            if($("select[name='custom_attributes[sexo]']").length){
                var sexo=$("select[name='custom_attributes[sexo]'] option:selected").text();
                if(sexo.trim()==""){
                    sw=false;
                    $("#sexo-error").remove();
                    $("select[name='custom_attributes[sexo]']").addClass('mage-error');
                    $("select[name='custom_attributes[sexo]']").parent('.control').append('<div for="sexo" generated="true" class="mage-error" id="sexo-error">Seleccione sexo.</div>');
                    $("select[name='custom_attributes[sexo]']").focus();
                }
            }
            
            var telefono=$("input[name='telephone']").val();
            if(telefono.length==9 || telefono.length==8){// Aqui se valida Numero de contacto
                var res = parseInt(telefono.substring(0,1));
                if(isNaN(res) || (res!=9 && res!=2 ) ){
                    sw=false;
                    $("#telefono-error").remove();
                    $("#telephone-error").remove();
                    $("input[name='telephone']").addClass('mage-error');
                    $("input[name='telephone']").parent('.control').append('<div for="telefono" generated="true" class="mage-error" id="telefono-error">Número de contacto Invalido.</div>');
                    $("input[name='telephone']").focus();
                }else{
                    $("#telephone-error").remove();
                    $("#telefono-error").remove();
                    $("input[name='telephone']").removeClass('mage-error');
                }
            }else{
                sw=false;
                $("#telefono-error").remove();
                $("#telephone-error").remove();
                $("input[name='telephone']").addClass('mage-error');
                $("input[name='telephone']").parent('.control').append('<div for="telefono" generated="true" class="mage-error" id="telefono-error">Longitud minima es de 8.</div>');
                $("input[name='telephone']").focus();
            }
            if($("select[name='custom_attributes[comuna]']").length){
                if($.trim($("select[name='custom_attributes[comuna]']").val())==""){
                    $("#comuna-error").remove();
                    $("select[name='custom_attributes[comuna]']").addClass('mage-error');
                    $("select[name='custom_attributes[comuna]']").parent('.control').append('<div for="comuna" generated="true" class="mage-error" id="comuna-error">Seleccione una Comuna.</div>');
                    $("select[name='custom_attributes[comuna]']").focus();
                    sw=false;
                }
            }
            if($("select[name='region_id']").length){
                if($.trim($("select[name='region_id']").val())==""){
                    $("#region-error").remove();
                    $("select[name='region_id']").addClass('mage-error');
                    $("select[name='region_id']").parent('.control').append('<div for="region" generated="true" class="mage-error" id="region-error">Seleccione una Región.</div>');
                    $("select[name='region_id']").focus();
                    sw=false;
                }
            }
            return sw;
        }
        function validate2All(){
            var sw=true;
            if($("input[name='dob']").length){
                var fecha=$.trim($("input[name='dob']").val());
                var date_regex =/^([0-9]{2})\-([0-9]{2})\-([0-9]{4})$/;
                if(fecha!=""){
                    if($("input[name='dob']").length && !(date_regex.test(fecha))){
                        sw=false;
                        $("#fecha-error").remove();
                        $("input[name='dob']").addClass('mage-error');
                        $("input[name='dob']").parent('.control').append('<div for="fecha" generated="true" class="mage-error" id="fecha-error">Fecha Invalida.</div>');
                        $("input[name='dob']").focus();
                    }else{
                        $(this).removeClass('mage-error');
                        $("#fecha-error").remove();
                    }
                }else{
                    sw=false;
                    $("#fecha-error").remove();
                    $("input[name='dob']").addClass('mage-error');
                    $("input[name='dob']").parent('.control').append('<div for="fecha" generated="true" class="mage-error" id="fecha-error">Seleccione fecha de nacimiento.</div>');
                    $("input[name='dob']").focus();
                }
            }
            if($("select[name='gender']").length){
                var sexo=$("select[name='gender'] option:selected").text();
                if($("select[name='gender']").length && sexo.trim()==""){
                    sw=false;
                    $("#sexo-error").remove();
                    $("select[name='gender']").addClass('mage-error');
                    $("select[name='gender']").parent('.control').append('<div for="sexo" generated="true" class="mage-error" id="sexo-error">Seleccione sexo.</div>');
                    $("select[name='gender']").focus();
                }else{
                    $("#sexo-error").remove();
                    $("select[name='gender']").removeClass('mage-error');
                }
            }
            if($("select[name='comuna']").length){
                var comuna=$("select[name='comuna']").val();
                if(comuna.trim()=="" || comuna=="Seleccione" ){
                    sw=false;
                    $("#comuna-error").remove();
                    $("select[name='comuna']").addClass('mage-error');
                    $("select[name='comuna']").parent('.control').append('<div for="comuna" generated="true" class="mage-error" id="comuna-error">Seleccione Comuna.</div>');
                    $("select[name='comuna']").focus();
                }else{
                    $("#comuna-error").remove();
                    $("select[name='comuna']").removeClass('mage-error');
                }
            }
            if($("#rut").length){
                var rut=$("#rut").val();
                if($("#rut").length && !window.Fn.validaRut(rut)){
                    sw=false;
                    $("#rut-error").remove();
                    $("#rut").parent('.control').append('<div for="rut" generated="true" class="mage-error" id="rut-error">Rut Invalido.</div>');
                    $("#rut").addClass('mage-error');
                    $("#rut").focus();
                }else{
                    $("#rut-error").remove();
                    $("#rut").removeClass('mage-error');
                }
            }
            if($("#telephone").length ){
                var celular=$.trim($("#telephone").val());
                if(celular.length==9 || celular.length==8){//Aqui se valida Numero de contacto
                    var res = parseInt(celular.substring(0,1));
                    if(isNaN(res) || (res!=9 && res!=2 )){
                        sw=false;
                        $("#telephone-error").remove();
                        $("#telephone").addClass('mage-error');
                        $("#telephone").parent('.control').append('<div for="telephone" generated="true" class="mage-error" id="telephone-error">Número de contacto Invalido.</div>');
                    }else{
                        $("#telephone-error").remove();
                        $("#telephone").removeClass('mage-error');
                    }
                }else{
                    sw=false;
                    $("#telephone-error").remove();
                    $("#telephone").addClass('mage-error');
                    $("#telephone").parent('.control').append('<div for="telephone" generated="true" class="mage-error" id="telephone-error">Longitud minima es de 8.</div>');
                }
            }else{
                if($("#telefono").length ){
                    var celular=$.trim($("#telefono").val());
                    if(celular.length==9 || celular.length==8){//Aqui se valida Numero de contacto
                        var res = parseInt(celular.substring(0,1));
                        if(isNaN(res) || (res!=9 && res!=2 )){
                            sw=false;
                            $("#telefono-error").remove();
                            $("#telefono").addClass('mage-error');
                            $("#telefono").parent('.control').append('<div for="telefono" generated="true" class="mage-error" id="telefono-error">Número de contacto Invalido.</div>');
                        }else{
                            $("#telefono-error").remove();
                            $("#telefono").removeClass('mage-error');
                        }
                    }else{
                        sw=false;
                        $("#telefono-error").remove();
                        $("#telefono").addClass('mage-error');
                        $("#telefono").parent('.control').append('<div for="telefono" generated="true" class="mage-error" id="telefono-error">Longitud minima es de 8.</div>');
                    }
                }
            }
            return sw;
        }
        $(document).on("click",".action.save.primary",function(e){
            if(!validarInfoPersonalPcliente()){
                e.preventDefault();
            }
        });
        function validarInfoPersonalPcliente(){
            var sw=true;
            var celular=$.trim($("#telefono").val());
            if(celular!=""){//Aqui se valida Numero de contacto
                if(celular.length==9 || celular.length==8){
                    var res = parseInt(celular.substring(0,1));
                    if(isNaN(res) || (res!=9 && res!=2 ) ){
                        sw=false;
                        $("#telefono-error").remove();
                        $("#telefono").addClass('mage-error');
                        $("#telefono").parent('.control').append('<div for="telefono" generated="true" class="mage-error" id="telefono-error">Numero de contacto Invalido.</div>');
                    }else{
                        $("#telefono-error").remove();
                        $("#telefono").removeClass('mage-error');
                    }
                }else{
                    sw=false;
                    $("#telefono-error").remove();
                    $("#telefono").addClass('mage-error');
                    $("#telefono").parent('.control').append('<div for="celular" generated="true" class="mage-error" id="telefono-error">Longitud minima es de 8.</div>');
                }
            }else{
                sw=false;
                $("#telefono-error").remove();
                $("#telefono").addClass('mage-error');
                $("#telefono").parent('.control').append('<div for="telefono" generated="true" class="mage-error" id="telefono-error">Longitud minima es de 8.</div>');
            }
            var rut=$("#rut").val();
            if(!window.Fn.validaRut(rut)){
                sw=false;
                $("#rut-error").remove();
                $("#rut").parent('.control').append('<div for="rut" generated="true" class="mage-error" id="rut-error">Rut Invalido.</div>');
                $("#rut").addClass('mage-error');
                $("#rut").focus();
            }else{
                $("#rut-error").remove();
                $("#rut").removeClass('mage-error');
            }
            var nacimiento=$("#dob").val().trim();
            if(nacimiento==""){
                $("#fecha-error").remove();
                $("#dob").parent('.control').append('<div for="fecha" generated="true" class="mage-error" id="fecha-error">Seleccione fecha de nacimiento.</div>');
                $("#dob").addClass('mage-error');
                $("#dob").focus();
                sw=false;
            }
            return sw;
        }
    });
});
