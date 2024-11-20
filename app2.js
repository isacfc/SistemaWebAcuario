hamburger=document.querySelector(".hamburger");
hamburger.onclick= function(){
    navBar= document.querySelector(".nav-bar");
    navBar.classList.toggle("active");
}

document.getElementById("boton-summit").addEventListener("click", function (e) {
    const form1 = document.querySelector("form");




    if (!form1.checkValidity()) {

        // Permite que el navegador muestre los errores de validación nativos
        return;
    }

    // Si todo es válido, envía el formulario
    alert("¡Gracias por tus comentarios! Apreciamos que te tomes el tiempo para compartir tu experiencia con nosotros. Tus opiniones son muy importantes y nos ayudan a mejorar continuamente. Si necesitas asistencia adicional, no dudes en comunicarte a los números que aparecen en la parte inferior de la pantalla. ¡Esperamos verte pronto!");
    form.submit();

});