// $Id: uc_cart.js,v 1.8.2.6 2009/07/21 14:37:20 islandusurper Exp $

function SumarCart(){
    var TOTALTTP = 0;
    var TOTALMONEDA = 0;
    var SIMBOL = "";
    $('.TotalTitiPuntos .losMonosDelCart').each(
        function(index) {
            TOTALTTP += parseFloat($(this).html());
        }
        );
    $('.price .uc-price').each(
        function(index) {
            SIMBOL = $(this).html().substring(0,1);
            var NUMERO = $(this).html().substring(1, $(this).html().length);
            TOTALMONEDA += parseFloat(NUMERO);
        }
        );
    
    $("#ElTotalEnPuntos .losMonosDelCart").html(TOTALTTP);
    $("#ElTotalEnMoneda .uc-price").html((SIMBOL + TOTALMONEDA.toFixed(2)));
}

function eliminarRegalo(event){
       
    var correo = $(this).parent().find(".nombre").text();
    var nid = $(this).parent().attr('class');
      
    var tam = $("#"+nid+" li").size();
              
    //si solo hay un elemento en la lista, entonces se esconde el eliminar de la lista
    if (tam == 1  ){
            
        $(this).parent().parent().children(':first-child').find(".eliminar").hide();
            
           
    }
    else{  
       
        //decremento la cantidad en QTy
        var test = $(this).parent().parent().parent().parent().find(".qty").find(".form-item").find(".form-text").val();
        --test;

        $(this).parent().parent().parent().parent().find(".qty").find(".form-item").find(".form-text").val(test);
        
        var valor = $(this).parent().parent().parent().parent().find(".PrecioTitiPuntos").find(".losMonosDelCart").html();
        $(this).parent().parent().parent().parent().find(".TotalTitiPuntos").find(".losMonosDelCart").html(valor*test);
        
        var moneda = $(this).parent().parent().parent().parent().find(".PrecioMoneda").find(".uc-price").attr("title");
        var simbolo = $(this).parent().parent().parent().parent().find(".PrecioMoneda").find(".uc-price").html().substr(0, 1);
        $(this).parent().parent().parent().parent().find(".price").find(".uc-price").html(simbolo + (moneda * test).toFixed(2));
        SumarCart();
        
        //alert($("#ElTotalEnPuntos .losMonosDelCart"));
        
        var root = location.protocol + '//' + location.host + '/';
        
        //no he encontrado aun algo que me de la ruta raiz completa asi que hay que agregarle /tiendavirtual
        
        //local
        root = root + 'regalos_del';
        //arriba
        //root = root + '/regalos_del';
        
        var peticion_del =  $.ajax({  
            
            //la url tiene que ir completa o se pone a mariquear
            //url: 'http://localhost:8080/tiendavirtual/regalos_del',
            url: root,
            type: 'POST',   
            data: {
                correo: correo, 
                nid: nid
            },                               
            error: function(){ 
                alert('se ha producido un error, no se ha podido eliminar el regalo'); 
            }

        }); 

        $(this).parent().remove();
    }
    event.preventDefault();
}

function agregarRegalo($objeto){
    
    var singleValues = $objeto.val();
           
    if($objeto.val().length > 0 )      {
        //validar el correo electronico
        var RegExPattern = /[\w-\.]{3,}@([\w-]{2,}\.)*([\w-]{2,}\.)[\w-]{2,4}/ ;
        if ((singleValues.match(RegExPattern)) && (singleValues != '')) {  
                
            var nid = $objeto.parent().parent().find(".ul_gift").attr('id');
               
            //cotejo con las direcciones de correo introducidas para asegurarme de que no hay duplicados 
            var flag = 1;
            $("#"+nid).children().each(function(){
                   
                var child = $objeto;
                var val = child.find(".nombre").text();
                   
                if(val == singleValues){
                    flag = 0;
                    alert(singleValues+" ya fue introducido");
                }
            }); 
               
            //si no hay duplicado de correo
            if (flag == 1){
                
                var correo = $objeto.val();
               
                var size = $("#"+nid+" li").size();
               
                //si solo hay un elemento en la lista, entonces se despliega el eliminar de la lista
                if (size == 1){
                
                    $objeto.parent().parent().find(".ul_gift").find("."+nid).find(".eliminar").show();
                }
              
                //despliego en pantalla el nuevo correo
                var ruta = "sites/all/themes/titi_theme/images/menitos.png";
                var nuevo = $("<li class=\""+nid+"\" ><div class=\"nombre\" >" + singleValues + "</div><div class =\"eliminar\"> <img src=\""+ ruta +"\"> </div> </li> " );
               
                $(nuevo).find('.eliminar').click(eliminarRegalo);
               
                $objeto.parent().parent().find(".ul_gift").append(nuevo); 
                
                
                //aumento la cantidad en QTy
                var test = $objeto.parent().parent().parent().find(".qty").find(".form-item").find(".form-text").val();
                ++test;
                $objeto.parent().parent().parent().find(".qty").find(".form-item").find(".form-text").val(test);
                /*para calcular los precios por medio de jquery*/
                var valor = $objeto.parent().parent().parent().find(".PrecioTitiPuntos").find(".losMonosDelCart").html();
                $objeto.parent().parent().parent().find(".TotalTitiPuntos").find(".losMonosDelCart").html(valor*test);
                    
                var moneda = $objeto.parent().parent().parent().find(".PrecioMoneda").find(".uc-price").attr("title");
                var simbolo = $objeto.parent().parent().parent().find(".PrecioMoneda").find(".uc-price").html().substr(0, 1);
                $objeto.parent().parent().parent().find(".price").find(".uc-price").html(simbolo + (moneda * test).toFixed(2));
                SumarCart();
                //mando por post los valores para ser almacenados en la base
                var root = location.protocol + '//' + location.host + '/';
                    
                //local
                root = root + 'regalos_add';
                //arriba
                //root = root + '/regalos_del';
                    
                var peticion_add =  $.ajax({  
                    //la url tiene que ir completa o se pone a mariquear
                    //url: 'http://localhost:8080/tiendavirtual/regalos_add',
                    url: root,
                    //url: '/regalos',
                    type: 'POST',   
                    data: {
                        correo: correo, 
                        nid: nid
                    },
                                
                    error: function(){ 
                        alert('se ha producido un error, no se ha podido agregar el regalo'); 
                            
                    }
                                
                }); 
                $objeto.val("");
            }
        }
           
        else{
            alert(singleValues + ' no es una dirección de correo válida');
        }
    }
    
}

/**
 * @file
 * Adds effects and behaviors to elements on the cart page.
 */

/**
 * Scan the DOM and displays the cancel and continue buttons.
 */
Drupal.behaviors.uc_productos_regalos_addGift = function(context) {
   
    $(".txt_gift").keypress(function(event){
        if (event.which == '13') {
            agregarRegalo($(this));
            event.preventDefault();
        }
    });
    $(".txt_gift").blur(function(event){
        agregarRegalo($(this));
    });
}

Drupal.behaviors.uc_productos_regalos_delGift = function(context) {
    
    $(".eliminar").click(eliminarRegalo);
}
