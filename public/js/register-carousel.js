document.addEventListener('DOMContentLoaded', () => {
    const dataElement = document.getElementById('carousel-data');
    if (!dataElement) return;

    // Parseamos los datos JSON que pasamos desde Twig
    let slides;
    try {
        slides = JSON.parse(dataElement.textContent);
    } catch (e) {
        console.error("Error leyendo datos del carrusel", e);
        return;
    }
    if (slides.length <= 1) return;

    let currentIndex = 0;
    const imgBg = document.getElementById('slider-image-bg');
    const imgFg = document.getElementById('slider-image-fg');
    const textContainer = document.getElementById('slider-text-container');
    const titleElement = document.getElementById('slider-title');
    const descElement = document.getElementById('slider-desc');

    // Mantenemos un control de qué imagen está al frente (true = imgFg, false = imgBg)
    let isFgActive = true;

    // Ejecutar cada 15 segundos (15000 milisegundos)
    setInterval(() => {
        currentIndex = (currentIndex + 1) % slides.length;
        const nextSlide = slides[currentIndex];

        // 1. Ocultar el texto flotante suavemente
        textContainer.style.opacity = '0';

        // 2. Transición cruzada (Crossfade) de las imágenes
        if (isFgActive) {
            // Actualmente se ve la de enfrente (imgFg). Poner nueva imagen en el fondo (imgBg).
            imgBg.src = nextSlide.img;
            
            // Ocultar suavemente la del frente para revelar el fondo.
            setTimeout(() => {
                imgFg.style.opacity = '0'; 
            }, 50); // pequeño retraso para asegurar que source cambió
        } else {
            // Actualmente se ve el fondo (imgBg). Poner nueva imagen en el frente y desvanecerla hacia adentro.
            imgFg.src = nextSlide.img;
            
            setTimeout(() => {
                imgFg.style.opacity = '1'; 
            }, 50);
        }

        isFgActive = !isFgActive;

        // 3. Después de un ratito (la mitad de la transición), cambiar y mostrar de nuevo el texto
        setTimeout(() => {
            titleElement.textContent = nextSlide.title;
            if (nextSlide.desc) {
                descElement.style.display = 'block';
                descElement.textContent = nextSlide.desc;
            } else {
                descElement.style.display = 'none';
            }
            textContainer.style.opacity = '1';
        }, 500);

    }, 15000); 
});
