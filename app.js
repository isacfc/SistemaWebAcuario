const parallax_el = document.querySelectorAll(".parallax");
const main = document.querySelector(".contenedor-parallax")

let xValue = 0, yValue = 0;
let rotateDegree = 0;

function update(cursorPosition) {
    parallax_el.forEach(el => {
        let speedx = el.dataset.speedx;
        let speedy = el.dataset.speedy;
        let speedz = el.dataset.speedz;
        let rotateSpeed = el.dataset.rotation;
        
        let isInLeft = parseFloat(getComputedStyle(el).left) < window.innerWidth / 2 ? 1 : -1;
        let zValue = (cursorPosition - parseFloat(getComputedStyle(el).left)) * isInLeft * 0.1;

        el.style.transform = `translateX(calc(-50% + ${xValue * speedx}px)) 
        translateY(calc(-50% + ${yValue * speedy}px)) perspective(2300px) 
        translateZ(${zValue * speedz}px) rotateY(${rotateDegree * rotateSpeed}deg)`;
    });
}

update(0);

window.addEventListener("mousemove", (e) => {
    xValue = e.clientX - window.innerWidth / 2;
    yValue = e.clientY - window.innerHeight / 2;

    rotateDegree = (xValue / (window.innerWidth / 2)) * 20;
    update(e.clientX);
});


if(window.innerWidth >= 725){
    main.style.maxHeight = `${window.innerWidth * 0.6}px`;

}else{
    main.style.maxHeight = `${window.innerWidth * 1.6}px`;

}

/* GSAP Animación */
let timeline = gsap.timeline();

parallax_el.forEach((el) => {
    // Convertir el valor de data-distance en un número para cada elemento
    let distance = parseFloat(el.dataset.distance) || 0;

    timeline.from(
        el,
        {
            top: `+=${distance}px`, // Asegurarse de que el valor de data-distance se use como posición inicial
            duration: 2.5,
        }, "l"
    );
});


/********************************* */




