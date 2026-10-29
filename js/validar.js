function validar(){

    var nombre = $("#name").val();
    var empresa = $("#company").val();
    var email = $("#email").val();
    var telefono = $("#phone").val();
    var mensaje = $("#message").val();
    var emailValido=/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    var soloNumeros=/^[0-9]*$/;

    var nombreEstaValidado=false;
    var empresaEstaValidado=false;
    var telefonoEstaValidado=false;
    var emailEstaValidado=false;

    if(nombre.length==0){
        $("#name-error").fadeIn();
        nombreEstaValidado=false; 
    }else{
        $("#name-error").fadeOut(); 
        nombreEstaValidado=true; 		
    } 
    if(empresa.length==0){
        $("#company-error").fadeIn();
        empresaEstaValidado=false; 
    }else{
        $("#company-error").fadeOut(); 
        empresaEstaValidado=true; 		
    } 

    if(email.length==0){
        $("#email-error2").fadeOut();
        $("#email-error").fadeIn();
        emailEstaValidado=false;
    }else{
        if(email.search(emailValido)){
            $("#email-error").fadeOut();
            $("#email-error2").fadeIn();
            emailEstaValidado=false;
        }else{
            $("#email-error").fadeOut();  	
            $("#email-error2").fadeOut();  	
            emailEstaValidado=true;	
        }
    }

    if(telefono.length>13 || telefono.length==0 || telefono.search(soloNumeros)){
        $("#phone-error").fadeIn();
        telefonoEstaValidado=false;
    }else{
        $("#phone-error").fadeOut();
        telefonoEstaValidado=true;
    }

    if(nombreEstaValidado==true&&empresaEstaValidado==true&&emailEstaValidado==true&&telefonoEstaValidado==true){
        $.ajax({
            url : 'ajax/enviarEmail.php',
            type : 'POST',
            data : {
                'nombre' : nombre,
                'empresa' : empresa,
                'email' : email,
                'telefono' : telefono,
                'mensaje' : mensaje,
            },
            async: false,
            success : function(response) {
                $('submitSuccessMessage').fadeIn();
                $("#name").val('');
                $("#company").val('');
                $("#email").val('');
                $("#phone").val('');
                $("#message").val('');
                alert("Mensaje enviado correctamente.");
            },
            error: function() {
                alert("Mensaje no enviado, intente en otro momento.");
            }
        });
    }
}
