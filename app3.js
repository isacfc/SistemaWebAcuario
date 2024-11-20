let nextButton = document.getElementById('next');
let prevButton = document.getElementById('prev');
let carousel = document.querySelector('.carousel');
let listHTML = document.querySelector('.carousel .list');
let seeMoreButtons = document.querySelectorAll('.seeMore');
let backButton = document.getElementById('back');

const bAdulto = document.getElementById("b-adulto");
const bNino = document.getElementById("b-nino");
const paquetes = document.querySelectorAll('input[name="paquete"]');
const totalPriceElement = document.getElementById("total-price");






nextButton.onclick = function(){
    showSlider('next');
}
prevButton.onclick = function(){
    showSlider('prev');
}
let unAcceppClick;
const showSlider = (type) => {
    nextButton.style.pointerEvents = 'none';
    prevButton.style.pointerEvents = 'none';

    carousel.classList.remove('next', 'prev');
    let items = document.querySelectorAll('.carousel .list .item');
    if(type === 'next'){
        listHTML.appendChild(items[0]);
        carousel.classList.add('next');
    }else{
        listHTML.prepend(items[items.length - 1]);
        carousel.classList.add('prev');
    }
    clearTimeout(unAcceppClick);
    unAcceppClick = setTimeout(()=>{
        nextButton.style.pointerEvents = 'auto';
        prevButton.style.pointerEvents = 'auto';
    }, 2000)
}
seeMoreButtons.forEach((button) => {
    button.onclick = function(){
        carousel.classList.remove('next', 'prev');
        carousel.classList.add('showDetail');
        backButton.style.opacity = "1";
    }
});
backButton.onclick = function(){
    carousel.classList.remove('showDetail');
    backButton.style.opacity = "0";

}




document.getElementById("comprar").addEventListener("click", function (e) {
    const form = document.querySelector("form");

    // Validar manualmente la fecha seleccionada
    const fecha = document.getElementById("fecha").value.trim();
    const today = new Date();
    const inputDate = new Date(fecha);

    if (inputDate < today.setHours(0, 0, 0, 0)) {
        e.preventDefault(); // Evita el envío solo en caso de error
        alert("La fecha del boleto no puede ser anterior o igual a la fecha actual.");
        return;
    }

    // Si el formulario no es válido, deja que el navegador muestre los errores
    if (!form.checkValidity()) {
        // Permite que el navegador muestre los errores de validación nativos
        return;
    }

    // Si todo es válido, envía el formulario
    alert("¡Gracias por elegir Acuario Crystal! Todos los campos son válidos y tu compra está en proceso. Recibirás tus boletos en el correo proporcionado. ¡Esperamos que disfrutes de una experiencia inolvidable con nosotros!.\n\nPara cualquier aclaración comuniquese a los números que aparecen en el inferior de la pantalla");
    form.submit();

});

const preciosPaquetes = {
    1: 80,    // Medusa
    2: 150,   // Cangrejo
    3: 259,   // Tortuga
    4: 586,   // Ballena
    5: 1300   // Tiburón
  };

  const calcularPrecio = () => {
    // Obtén los elementos de número de boletos
    const boletosAdulto = parseInt(document.getElementById("b-adulto").value) || 0;
    const boletosNino = parseInt(document.getElementById("b-nino").value) || 0;
  
    // Encuentra el paquete seleccionado
    const paquetes = document.querySelectorAll('input[name="paquete"]');
    const paqueteSeleccionado = Array.from(paquetes).find((p) => p.checked);
  
    // Si no hay paquete seleccionado, asignar 0 como precio
    const precioPaquete = paqueteSeleccionado ? preciosPaquetes[paqueteSeleccionado.value] : 0;
  
    // Calcula el precio total
    const precioTotal =
      boletosAdulto * precioPaquete +
      boletosNino * (precioPaquete * 0.8); // 20% de descuento para niños
  
    // Muestra el precio en un elemento del DOM
    document.getElementById("precio-total").textContent = `Total: $${precioTotal.toFixed(2)}`;
  };
  
  // Agrega event listeners a los inputs para calcular el precio en tiempo real
  document.getElementById("b-adulto").addEventListener("input", calcularPrecio);
  document.getElementById("b-nino").addEventListener("input", calcularPrecio);
  document.querySelectorAll('input[name="paquete"]').forEach((input) => {
    input.addEventListener("change", calcularPrecio);
  });


document.getElementById("testAlertButton").addEventListener("click", function () {
    alert("¡Gracias por  Acuario Crystal! Todos los campos son válidos y tu compra está en proceso. Recibirás tus boletos en el correo proporcionado. ¡Esperamos que disfrutes de una experiencia inolvidable con nosotros!.\n\nPara cualquier aclaración comuniquese a los números que aparecen en el inferior de la pantalla");
    
});



