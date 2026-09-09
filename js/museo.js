const catalogo = [
    {
        titulo: "Sabrina's Gengar", set: "Gym Challenge", year: "2000", artist: "Ken Sugimori",
        imagen: "https://images.pokemontcg.io/gym2/29_hires.png",
        descripcion: "Una pieza maestra del arte vintage. Gengar se funde con las sombras del gimnasio de Ciudad Azafrán. Su rareza y el tono siniestro la convierten en una reliquia atemporal."
    },
    {
        titulo: "Shining Magikarp", set: "Celebrations", year: "2021", artist: "Ken Sugimori",
        imagen: "https://images.pokemontcg.io/cel25c/66_hires.png",
        descripcion: "Una brillante reedición del clásico Neo Revelation. Ver a Magikarp dorado desafiando las expectativas representa el espíritu puro del coleccionismo Pokémon."
    },
    {
        titulo: "Eevee ex (SAR)", set: "Terastal Festival", year: "2024", artist: "Teeziro",
        imagen: "https://images.pokemontcg.io/sv8/188_hires.png", 
        descripcion: "Una obra de arte contemporánea deslumbrante. El uso del color y el efecto cristalino elevan a Eevee, demostrando la increíble evolución técnica de los ilustradores actuales."
    },
    {
        titulo: "Rayquaza & Deoxys", set: "Clash at Summit", year: "2010", artist: "Shinji Higuchi",
        imagen: "https://images.pokemontcg.io/hgss4/89_hires.png", 
        descripcion: "Las cartas LEGEND rompieron las reglas del diseño al requerir dos piezas físicas para formar una sola ilustración. Esta obra maestra captura una batalla espacial épica."
    }
];

const pantallaStart = document.getElementById('pantalla-start');
const pantallaMuseo = document.getElementById('pantalla-museo');
const track = document.getElementById('gallery-track');
const bg = document.getElementById('museum-bg');

let indiceActual = 0;
let hablando = false;

// 1. CONSTRUIR EL PASILLO AL CARGAR
function construirMuseo() {
    catalogo.forEach((obra, index) => {
        const exhibit = document.createElement('div');
        exhibit.className = 'exhibit';
        exhibit.innerHTML = `
            <div class="artwork-container">
                <img src="${obra.imagen}" class="card-img" alt="${obra.titulo}">
            </div>
            <div class="plaque-container">
                <h1 class="card-title">${obra.titulo}</h1>
                <div class="card-meta">${obra.set} • ${obra.year}<br>Ilustrador: ${obra.artist}</div>
                <div class="card-description">${obra.descripcion}</div>
                <button class="btn-audioguide" id="audio-btn-${index}" onclick="reproducirAudio(${index})">▶ REPRODUCIR AUDIOGUÍA</button>
            </div>
        `;
        track.appendChild(exhibit);
    });
}
construirMuseo();

pantallaStart.addEventListener('click', () => {
    pantallaStart.style.display = 'none';
    pantallaMuseo.classList.remove('oculto');
    actualizarPosicion(true); // Posiciona en la primera carta suavemente
});

// 2. FÍSICAS DE ARRASTRE PARALLAX
let isDragging = false;
let startPos = 0;
let currentTranslate = 0;
let prevTranslate = 0;

// Calcula cuánto mide cada carta más su separación para saber cuánto mover
function getExhibitWidth() {
    let base = window.innerWidth > window.innerHeight ? window.innerWidth : window.innerHeight;
    return base * 0.95; // 80vw carta + 15vw gap = 95% del ancho de la pantalla girada
}

document.addEventListener('touchstart', e => {
    if (pantallaMuseo.classList.contains('oculto')) return;
    isDragging = true;
    let touch = e.touches[0];
    let isPortrait = window.innerHeight > window.innerWidth;
    
    // Si el móvil está vertical (girado por CSS), el movimiento físico vertical mueve el pasillo
    startPos = isPortrait ? touch.clientY : touch.clientX;
    
    // Quitamos la transición para que siga al dedo instantáneamente
    track.style.transition = 'none';
    bg.style.transition = 'none';
});

document.addEventListener('touchmove', e => {
    if (!isDragging) return;
    let touch = e.touches[0];
    let isPortrait = window.innerHeight > window.innerWidth;
    let currentPos = isPortrait ? touch.clientY : touch.clientX;
    
    // Calculamos el arrastre
    let diff = currentPos - startPos;
    
    // Si está en Portrait (rotado 90deg), arrastrar hacia arriba (diff negativo) equivale a mover a la izquierda visualmente
    if(isPortrait) { diff = -diff; }

    currentTranslate = prevTranslate + diff;
    
    // Aplicamos el Parallax: la pista sigue al dedo, el fondo se mueve a un 25% de velocidad
    track.style.transform = `translateX(${currentTranslate}px)`;
    bg.style.transform = `translateX(${currentTranslate * 0.25}px)`; 
});

document.addEventListener('touchend', () => {
    if (!isDragging) return;
    isDragging = false;
    
    // Averiguamos en qué carta ha soltado el dedo para imantarlo a ella
    const anchoObra = getExhibitWidth();
    indiceActual = Math.round(Math.abs(currentTranslate) / anchoObra);
    
    // Límites (no pasar de la primera ni de la última)
    if (indiceActual < 0) indiceActual = 0;
    if (indiceActual >= catalogo.length) indiceActual = catalogo.length - 1;

    actualizarPosicion(true);
});

function actualizarPosicion(smooth = true) {
    const anchoObra = getExhibitWidth();
    currentTranslate = -indiceActual * anchoObra;
    prevTranslate = currentTranslate;

    if (smooth) {
        track.style.transition = 'transform 0.4s cubic-bezier(0.25, 1, 0.5, 1)';
        bg.style.transition = 'transform 0.4s cubic-bezier(0.25, 1, 0.5, 1)';
    }

    // Movemos pista y fondo
    track.style.transform = `translateX(${currentTranslate}px)`;
    bg.style.transform = `translateX(${currentTranslate * 0.25}px)`; 
    
    // Si cambiamos de sala, callamos el audio
    if(hablando) { window.speechSynthesis.cancel(); hablando = false; resetBotonesAudio(); }
}

// 3. AUDIO
function reproducirAudio(index) {
    const btn = document.getElementById(`audio-btn-${index}`);
    if ('speechSynthesis' in window) {
        if (hablando) {
            window.speechSynthesis.cancel();
            resetBotonesAudio();
            hablando = false;
        } else {
            resetBotonesAudio();
            const obra = catalogo[index];
            const mensaje = new SpeechSynthesisUtterance(`${obra.titulo}. ${obra.descripcion}`);
            mensaje.lang = 'es-ES'; mensaje.rate = 0.9;
            
            window.speechSynthesis.speak(mensaje);
            btn.innerText = "⏹ DETENER";
            hablando = true;

            mensaje.onend = () => { resetBotonesAudio(); hablando = false; };
        }
    }
}

function resetBotonesAudio() {
    document.querySelectorAll('.btn-audioguide').forEach(b => b.innerText = "▶ REPRODUCIR AUDIOGUÍA");
}