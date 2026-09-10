/**
 * MUSEO POKÉMON - LÓGICA DE GALERÍA Y EXPERIENCIA HORIZONTAL
 * Compatible con index.html (pantalla de carga) y sala1/2/3.html (galerías directas).
 */

// ─────────────────────────────────────────────────────────────────────────────
// 1. CONFIGURACIÓN
// ─────────────────────────────────────────────────────────────────────────────
const JSONBIN_URL    = 'https://api.jsonbin.io/v3/b/6aa26828ffd5d16053f558da';
const JSONBIN_SECRET = '$2a$10$lntiSbMqKlrKhjvk4ohkk.anTQ2w976hTiyQhBGwW3j9Pwa80Ryuu';

let catalogo        = [];
let indiceActual    = 0;
let hablando        = false;
let isDragging      = false;
let startX          = 0;
let currentTranslate = 0;
let prevTranslate   = 0;

// ─────────────────────────────────────────────────────────────────────────────
// 2. REFERENCIAS DOM (con seguridad de nulos)
// ─────────────────────────────────────────────────────────────────────────────
const pantallaLoading   = document.getElementById('pantalla-loading');
const pantallaMuseo     = document.getElementById('pantalla-museo');
const track             = document.getElementById('gallery-track');
const bg                = document.getElementById('museum-bg');
const btnEnter          = document.getElementById('btn-enter');
const progressBarFill   = document.getElementById('progress-fill');
const loadingStatusText = document.getElementById('loading-status-text');
const counterDisplay    = document.getElementById('exhibit-counter');
const btnPrev           = document.getElementById('btn-prev');
const btnNext           = document.getElementById('btn-next');

// ─────────────────────────────────────────────────────────────────────────────
// 3. DETECCIÓN DE MODO: ¿Estamos en una sala directa o en index?
// ─────────────────────────────────────────────────────────────────────────────
const esSalaDirecta = track !== null && pantallaLoading === null;

// ─────────────────────────────────────────────────────────────────────────────
// 4. TRANSICIÓN FLUIDA ENTRE SALAS
// ─────────────────────────────────────────────────────────────────────────────
function cambiarDeSala(urlDestino) {
    const overlay = document.getElementById('room-transition-overlay');
    if (overlay) {
        overlay.classList.add('active');
        setTimeout(() => { window.location.href = urlDestino; }, 450);
    } else {
        window.location.href = urlDestino;
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// 5. AUDIOGUÍA (cartas manuales en HTML de salas)
// ─────────────────────────────────────────────────────────────────────────────
function reproducirAudioManual(texto) {
    if (!('speechSynthesis' in window)) return;
    window.speechSynthesis.cancel();
    const msg = new SpeechSynthesisUtterance(texto);
    msg.lang = 'es-ES';
    msg.rate = 0.95;
    window.speechSynthesis.speak(msg);
}

// ─────────────────────────────────────────────────────────────────────────────
// 6. CÁLCULO DE PASO ENTRE CARTAS
// ─────────────────────────────────────────────────────────────────────────────
function getExhibitStepWidth() {
    const first = document.querySelector('.exhibit');
    if (!first) return window.innerWidth * 0.85;
    const gap = parseFloat(getComputedStyle(track).gap) || window.innerWidth * 0.10;
    return first.offsetWidth + gap;
}

// ─────────────────────────────────────────────────────────────────────────────
// 7. NAVEGACIÓN Y POSICIONAMIENTO
// ─────────────────────────────────────────────────────────────────────────────
function actualizarPosicion(smooth = true) {
    if (!track) return;
    const stepWidth = getExhibitStepWidth();
    currentTranslate = -indiceActual * stepWidth;
    prevTranslate    = currentTranslate;

    if (smooth) {
        track.style.transition = 'transform 0.45s cubic-bezier(0.16, 1, 0.3, 1)';
        if (bg) bg.style.transition = 'transform 0.45s cubic-bezier(0.16, 1, 0.3, 1)';
    } else {
        track.style.transition = 'none';
        if (bg) bg.style.transition = 'none';
    }

    track.style.transform = `translateX(${currentTranslate}px)`;
    if (bg) bg.style.transform = `translateX(${currentTranslate * 0.12}px)`;

    // Destacar carta activa
    document.querySelectorAll('.exhibit').forEach((ex, idx) => {
        ex.classList.toggle('inactive', idx !== indiceActual);
    });

    actualizarContador();

    // Detener audio al navegar
    if (hablando) {
        window.speechSynthesis.cancel();
        hablando = false;
        resetBotonesAudio();
    }
}

function actualizarContador() {
    const total = document.querySelectorAll('.exhibit').length;
    if (counterDisplay) {
        const visibles = total - document.querySelectorAll('.room-door-card').length;
        counterDisplay.innerHTML = `CARTA <span>${visibles > 0 ? Math.min(indiceActual + 1, visibles) : 0}</span> DE ${visibles}`;
    }
}

function navegar(direccion) {
    const total = document.querySelectorAll('.exhibit').length;
    const nuevo = indiceActual + direccion;
    if (nuevo >= 0 && nuevo < total) {
        indiceActual = nuevo;
        actualizarPosicion(true);
    }
}

// Botones flecha
if (btnPrev) btnPrev.onclick = () => navegar(-1);
if (btnNext) btnNext.onclick = () => navegar(1);

// Teclado
document.addEventListener('keydown', e => {
    if (e.key === 'ArrowRight') navegar(1);
    if (e.key === 'ArrowLeft')  navegar(-1);
});

// Rueda del ratón
window.addEventListener('wheel', e => {
    if (Math.abs(e.deltaX) > 10 || Math.abs(e.deltaY) > 10) {
        if (e.deltaY > 0 || e.deltaX > 0) navegar(1);
        else navegar(-1);
    }
}, { passive: true });

// ─────────────────────────────────────────────────────────────────────────────
// 8. ARRASTRE TÁCTIL Y DE RATÓN
// ─────────────────────────────────────────────────────────────────────────────
function iniciarArrastre(clientX) {
    if (!track) return;
    isDragging = true;
    startX = clientX;
    track.style.transition = 'none';
    if (bg) bg.style.transition = 'none';
}

function moverArrastre(clientX) {
    if (!isDragging || !track) return;
    const diff = clientX - startX;
    currentTranslate = prevTranslate + diff;
    track.style.transform = `translateX(${currentTranslate}px)`;
    if (bg) bg.style.transform = `translateX(${currentTranslate * 0.12}px)`;
}

function finalizarArrastre() {
    if (!isDragging || !track) return;
    isDragging = false;

    const stepWidth = getExhibitStepWidth();
    const total = document.querySelectorAll('.exhibit').length;
    indiceActual = Math.round(-currentTranslate / stepWidth);
    if (indiceActual < 0) indiceActual = 0;
    if (indiceActual >= total) indiceActual = total - 1;

    actualizarPosicion(true);
}

// Eventos touch
if (track) {
    track.addEventListener('touchstart', e => iniciarArrastre(e.touches[0].clientX), { passive: true });
    track.addEventListener('touchmove',  e => moverArrastre(e.touches[0].clientX),   { passive: true });
    track.addEventListener('touchend',   finalizarArrastre);
    track.addEventListener('mousedown',  e => iniciarArrastre(e.clientX));
}

window.addEventListener('mousemove', e => { if (isDragging) moverArrastre(e.clientX); });
window.addEventListener('mouseup',   () => { if (isDragging) finalizarArrastre(); });
window.addEventListener('resize',    () => actualizarPosicion(false));

// ─────────────────────────────────────────────────────────────────────────────
// 9. EFECTO 3D TILT EN CARTAS (para cartas dinámicas en index)
// ─────────────────────────────────────────────────────────────────────────────
function efectoTilt(e, index) {
    const frame = document.getElementById(`card-frame-${index}`);
    const shine = document.getElementById(`holo-shine-${index}`);
    if (!frame) return;

    const rect = frame.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;
    const rotX = ((y - rect.height / 2) / rect.height / 2) * -12;
    const rotY = ((x - rect.width  / 2) / rect.width  / 2) * 12;

    frame.style.transform = `perspective(1000px) rotateX(${rotX}deg) rotateY(${rotY}deg) scale3d(1.03,1.03,1.03)`;
    if (shine) {
        shine.style.background = `radial-gradient(circle at ${(x / rect.width) * 100}% ${(y / rect.height) * 100}%, rgba(255,255,255,0.5) 0%, rgba(212,175,55,0.25) 40%, transparent 80%)`;
        shine.style.opacity = '0.85';
    }
}

function efectoTiltTouch(e, index) {
    if (e.touches.length > 0) efectoTilt(e.touches[0], index);
}

function resetTilt(index) {
    const frame = document.getElementById(`card-frame-${index}`);
    const shine = document.getElementById(`holo-shine-${index}`);
    if (frame) frame.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) scale3d(1,1,1)';
    if (shine) shine.style.opacity = '0.5';
}

// ─────────────────────────────────────────────────────────────────────────────
// 10. AUDIOGUÍA CON SÍNTESIS DE VOZ (cartas dinámicas)
// ─────────────────────────────────────────────────────────────────────────────
function reproducirAudio(index) {
    const btn = document.getElementById(`audio-btn-${index}`);
    if (!('speechSynthesis' in window)) { alert('Tu navegador no soporta audioguía.'); return; }

    if (hablando) {
        window.speechSynthesis.cancel();
        resetBotonesAudio();
        hablando = false;
    } else {
        resetBotonesAudio();
        const obra = catalogo[index];
        if (!obra) return;
        const texto = `${obra.titulo}. Colección ${obra.set}, año ${obra.year}. Ilustrado por ${obra.artist}. ${obra.descripcion}`;
        const msg = new SpeechSynthesisUtterance(texto);
        msg.lang  = 'es-ES';
        msg.rate  = 0.92;
        window.speechSynthesis.speak(msg);
        if (btn) { btn.classList.add('playing'); btn.innerHTML = '<span>⏹</span> DETENER AUDIOGUÍA'; }
        hablando = true;
        msg.onend = msg.onerror = () => { resetBotonesAudio(); hablando = false; };
    }
}

function resetBotonesAudio() {
    document.querySelectorAll('.btn-audioguide').forEach(b => {
        b.classList.remove('playing');
        b.innerHTML = '<span>▶</span> AUDIOGUÍA ESPAÑOL';
    });
}

// ─────────────────────────────────────────────────────────────────────────────
// 11. PARTÍCULAS DE POLVO DE MUSEO
// ─────────────────────────────────────────────────────────────────────────────
function iniciarParticulasMuseo() {
    const canvas = document.getElementById('bg-particles');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    let W = canvas.width  = window.innerWidth;
    let H = canvas.height = window.innerHeight;

    window.addEventListener('resize', () => {
        W = canvas.width  = window.innerWidth;
        H = canvas.height = window.innerHeight;
    });

    const NUM = Math.min(50, Math.floor(W / 22));

    class Mota {
        constructor() { this.reset(true); }
        reset(initial = false) {
            this.x    = Math.random() * W;
            this.y    = initial ? Math.random() * H : H + 10;
            this.size = Math.random() * 2.4 + 0.5;
            this.vy   = -(Math.random() * 0.3 + 0.08);
            this.vx   = (Math.random() - 0.5) * 0.2;
            this.a    = Math.random() * 0.45 + 0.2;
            this.phase = Math.random() * Math.PI * 2;
            this.pSpeed = Math.random() * 0.018 + 0.007;
            this.sparkle = Math.random() > 0.75;
        }
        update() {
            this.y += this.vy;
            this.x += Math.sin(this.y * 0.006 + this.phase) * 0.3 + this.vx;
            this.phase += this.pSpeed;
            if (this.y < -15 || this.x < -20 || this.x > W + 20) this.reset(false);
        }
        draw() {
            ctx.save();
            const alpha = Math.max(0.08, Math.min(0.8, this.a + Math.sin(this.phase) * 0.2));
            if (this.sparkle) {
                ctx.translate(this.x, this.y);
                ctx.fillStyle = `rgba(249, 232, 162, ${alpha})`;
                ctx.fillRect(-this.size * 2, -0.7, this.size * 4, 1.4);
                ctx.fillRect(-0.7, -this.size * 2, 1.4, this.size * 4);
            } else {
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                ctx.fillStyle = `rgba(245, 230, 185, ${alpha})`;
                ctx.shadowBlur  = this.size * 4;
                ctx.shadowColor = 'rgba(212, 175, 55, 0.6)';
                ctx.fill();
            }
            ctx.restore();
        }
    }

    const motas = Array.from({ length: NUM }, () => new Mota());
    function loop() {
        ctx.clearRect(0, 0, W, H);
        motas.forEach(m => { m.update(); m.draw(); });
        requestAnimationFrame(loop);
    }
    loop();
}

// ─────────────────────────────────────────────────────────────────────────────
// ─────────────────────────────────────────────────────────────────────────────
// 12. PANTALLA DE CARGA Y RENDERIZADO DINÁMICO
// ─────────────────────────────────────────────────────────────────────────────
async function cargarCatalogo() {
    if (pantallaLoading) actualizarProgreso(20, 'Conectando con la exhibición...');
    let datos = null;

    if (JSONBIN_URL && !JSONBIN_URL.includes('YOUR_BIN_ID')) {
        try {
            const headers = JSONBIN_SECRET ? { 'X-Master-Key': JSONBIN_SECRET } : {};
            const res = await fetch(JSONBIN_URL, { headers });
            if (res.ok) {
                const json = await res.json();
                datos = json.record ?? json;
            }
        } catch (e) { console.warn('JSONBin no disponible:', e); }
    }

    if (!datos) {
        if (pantallaLoading) actualizarProgreso(40, 'Cargando catálogo local...');
        try {
            // Check if we need ../ for local json (if in html/ folder)
            const jsonPath = esSalaDirecta ? '../data/cartas.json' : 'data/cartas.json';
            const res = await fetch(jsonPath);
            if (res.ok) datos = await res.json();
        } catch (e) { console.error('Error cargando data/cartas.json:', e); }
    }

    let arrayDatos = Array.isArray(datos) && datos.length > 0 ? datos : [];

    if (esSalaDirecta) {
        // Estamos en sala1.html, sala2.html o sala3.html
        const match = window.location.href.match(/sala[1-3]/);
        const salaActual = match ? match[0] : 'sala1';
        
        // Filtramos para quedarnos solo con las de esta sala
        catalogo = arrayDatos.filter(c => c.sala === salaActual);
        renderizarGaleria(salaActual);
        
        indiceActual = 0;
        actualizarPosicion(false);
        if (track) {
            track.style.opacity = '0';
            setTimeout(() => {
                track.style.transition = 'opacity 0.6s ease';
                track.style.opacity = '1';
            }, 80);
        }
    } else {
        // Estamos en index.html
        catalogo = arrayDatos;
        if (pantallaLoading) actualizarProgreso(100, '¡Colección lista para explorar!');
        if (btnEnter) {
            btnEnter.style.display = 'inline-block';
            btnEnter.style.opacity = '1';
        }
        if (loadingStatusText) loadingStatusText.innerText = 'COLECCIÓN LISTA';
    }
}

function renderizarGaleria(salaActual) {
    if (!track) return;
    
    // Configuración de la siguiente sala (puerta al final)
    const nextRoomInfo = {
        'sala1': { next: 'sala2.html', title: 'Sala 2', desc: 'Avanza a la Sala 2: Reliquias LEGEND para continuar el recorrido.' },
        'sala2': { next: 'sala3.html', title: 'Sala 3', desc: 'Avanza a la Sala 3: Obras Modernas para continuar el recorrido.' },
        'sala3': { next: '../index.html', title: 'Recibidor', desc: 'Has terminado el recorrido. Vuelve al lobby principal.' }
    };
    const info = nextRoomInfo[salaActual];

    // Vaciar pista (eliminar hardcodeadas)
    let html = '';

    // Si no hay cartas en la sala
    if (catalogo.length === 0) {
        html += `
        <div class="exhibit" id="exhibit-0">
            <div class="plaque-container" style="text-align:center; padding: 40px;">
                <h1 class="card-title">SALA EN CONSTRUCCIÓN</h1>
                <div class="card-description">Pronto llegarán nuevas obras a esta sala.</div>
            </div>
        </div>`;
    } else {
        // Renderizar cada carta
        catalogo.forEach((c, idx) => {
            const img = c.imagen || c.imagen_fallback || '';
            const fallback = c.imagen_fallback || '';
            const desc = (c.descripcion || 'Sin descripción').replace(/"/g, '&quot;');
            
            html += `
            <div class="exhibit inactive" id="exhibit-${idx}">
                <div class="artwork-container">
                    <div class="card-frame" id="card-frame-${idx}">
                        <div class="card-img-wrapper">
                            <img src="${img}" class="card-img" alt="${c.titulo}" onerror="this.src='${fallback}'; this.onerror=null;">
                            <div class="holo-shine" id="holo-shine-${idx}"></div>
                        </div>
                    </div>
                </div>
                <div class="plaque-container">
                    <div class="plaque-header-row">
                        <span class="plaque-rarity-badge">${c.rarity || 'Desconocida'}</span>
                    </div>
                    <h1 class="card-title">${c.titulo}</h1>
                    <div class="card-meta">
                        <span>${c.set || '—'}</span> • ${c.year || '—'}<br>Ilustrador: ${c.artist || '—'}
                    </div>
                    <div class="card-description">
                        ${c.descripcion || ''}
                    </div>
                    <button class="btn-audioguide" id="audio-btn-${idx}" onclick="reproducirAudio(${idx})">
                        <span>▶</span> AUDIOGUÍA ESPAÑOL
                    </button>
                </div>
            </div>`;
        });
    }

    // Añadir puerta de siguiente sala al final
    if (info) {
        html += `
        <div class="exhibit room-door-card inactive" id="exhibit-next-room">
            <div class="plaque-container room-door-box">
                <div class="door-icon">🚪</div>
                <h2 class="door-title">${info.title}</h2>
                <p class="door-desc">${info.desc}</p>
                <button class="btn-enter-room" onclick="cambiarDeSala('${info.next}')">ENTRAR A ${info.title.toUpperCase()}</button>
            </div>
        </div>`;
    }

    track.innerHTML = html;
}

if (pantallaLoading) {
    function actualizarProgreso(pct, msg) {
        if (progressBarFill)   progressBarFill.style.width = `${pct}%`;
        if (loadingStatusText) loadingStatusText.innerText = msg;
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// 13. INICIO DE LA APLICACIÓN
// ─────────────────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    iniciarParticulasMuseo();
    cargarCatalogo(); // Ahora esto lo carga todo dinámicamente siempre
});