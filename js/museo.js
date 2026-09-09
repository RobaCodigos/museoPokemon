// Catálogo del Museo. Añade aquí todas las cartas que quieras.
const catalogo = [
    {
        titulo: "Sabrina's Gengar",
        set: "Gym Challenge", year: "2000", artist: "Ken Sugimori",
        imagen: "https://images.pokemontcg.io/gym2/29_hires.png",
        descripcion: "Una pieza maestra del arte vintage. Gengar se funde con las sombras del gimnasio de Ciudad Azafrán. Su rareza y el tono siniestro la convierten en una reliquia atemporal."
    },
    {
        titulo: "Shining Magikarp",
        set: "Celebrations: Classic", year: "2021", artist: "Ken Sugimori",
        imagen: "https://images.pokemontcg.io/cel25c/66_hires.png",
        descripcion: "Una brillante reedición del clásico Neo Revelation. Ver a Magikarp dorado saltando desafiando las expectativas representa el espíritu puro del coleccionismo Pokémon."
    },
    {
        titulo: "Eevee ex (SAR)",
        set: "Terastal Festival", year: "2024", artist: "Teeziro",
        imagen: "https://images.pokemontcg.io/sv8/188_hires.png", 
        descripcion: "Una obra de arte contemporánea deslumbrante. El uso del color y el efecto cristalino elevan a Eevee, demostrando la increíble evolución técnica de los ilustradores actuales."
    }
];

let indiceActual = 0;
let hablando = false;

// Referencias a elementos
const pantallaStart = document.getElementById('pantalla-start');
const pantallaMuseo = document.getElementById('pantalla-museo');
const salaExhibicion = document.getElementById('sala-exhibicion');

pantallaStart.addEventListener('click', () => {
    pantallaStart.style.display = 'none';
    pantallaMuseo.classList.remove('oculto');
    cargarObra(indiceActual);
});

function cargarObra(indice) {
    const obra = catalogo[indice];
    
    // Inyectamos el HTML de la obra actual
    salaExhibicion.innerHTML = `
        <div class="artwork-container">
            <img src="${obra.imagen}" class="card-img" alt="${obra.titulo}">
        </div>
        <div class="plaque-container">
            <h1 class="card-title">${obra.titulo}</h1>
            <div class="card-meta">
                ${obra.set} • ${obra.year}<br>Ilustrador: ${obra.artist}
            </div>
            <div class="card-description">${obra.descripcion}</div>
            <button class="btn-audioguide" onclick="reproducirAudio()">▶ REPRODUCIR AUDIOGUÍA</button>
        </div>
    `;
}

// LÓGICA DE AUDIO
window.reproducirAudio = function() {
    const btn = document.querySelector('.btn-audioguide');
    if ('speechSynthesis' in window) {
        if (hablando) {
            window.speechSynthesis.cancel();
            btn.innerText = "▶ REPRODUCIR AUDIOGUÍA";
            hablando = false;
        } else {
            const obra = catalogo[indiceActual];
            const mensaje = new SpeechSynthesisUtterance(`${obra.titulo}. ${obra.descripcion}`);
            mensaje.lang = 'es-ES'; mensaje.rate = 0.9;
            
            window.speechSynthesis.speak(mensaje);
            btn.innerText = "⏹ DETENER";
            hablando = true;

            mensaje.onend = () => { btn.innerText = "▶ REPRODUCIR AUDIOGUÍA"; hablando = false; };
        }
    }
};

// LÓGICA INFALIBLE PARA DESLIZAR EL DEDO (Soporta el giro CSS)
let startX, startY;
document.addEventListener('touchstart', e => {
    startX = e.touches[0].clientX;
    startY = e.touches[0].clientY;
});

document.addEventListener('touchend', e => {
    let endX = e.changedTouches[0].clientX;
    let endY = e.changedTouches[0].clientY;
    
    let diffX = endX - startX;
    let diffY = endY - startY;

    // Detectamos el gesto más largo (sea en X o Y, adaptándose a cómo esté girado el móvil)
    if (Math.abs(diffX) > 50 || Math.abs(diffY) > 50) {
        // Cortamos el audio si cambias de sala
        if(hablando) { window.speechSynthesis.cancel(); hablando = false; }
        
        salaExhibicion.classList.add('fade-out'); // Efecto de fundido
        
        setTimeout(() => {
            if (Math.abs(diffX) > Math.abs(diffY)) {
                // Swipe físico horizontal
                if (diffX > 0) indiceActual = (indiceActual > 0) ? indiceActual - 1 : catalogo.length - 1;
                else indiceActual = (indiceActual < catalogo.length - 1) ? indiceActual + 1 : 0;
            } else {
                // Swipe físico vertical (útil por el giro de CSS)
                if (diffY > 0) indiceActual = (indiceActual > 0) ? indiceActual - 1 : catalogo.length - 1;
                else indiceActual = (indiceActual < catalogo.length - 1) ? indiceActual + 1 : 0;
            }
            
            cargarObra(indiceActual);
            salaExhibicion.classList.remove('fade-out');
        }, 300); // Tiempo de la transición oscura
    }
});