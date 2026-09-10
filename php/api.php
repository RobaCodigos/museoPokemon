<?php
/**
 * API Backend - Museo Pokémon
 * Almacenamiento: JSONBin.io  (lectura y escritura)
 * Imágenes      : servidor local en img/cards/
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

// ─────────────────────────────────────────────────────────────────────────────
// CONFIGURACIÓN JSONBIN
// ─────────────────────────────────────────────────────────────────────────────
define('JB_BIN_ID',  '6aa26828ffd5d16053f558da');
define('JB_KEY',     '$2a$10$lntiSbMqKlrKhjvk4ohkk.anTQ2w976hTiyQhBGwW3j9Pwa80Ryuu');
define('JB_GET_URL', 'https://api.jsonbin.io/v3/b/' . JB_BIN_ID);
define('JB_PUT_URL', 'https://api.jsonbin.io/v3/b/' . JB_BIN_ID);

// Ruta local de imágenes (se mantiene en el servidor)
define('IMG_PATH', __DIR__ . '/../img/cards/');
define('IMG_WEB',  'img/cards/');
define('MAX_SIZE', 8 * 1024 * 1024); // 8 MB

$action = $_POST['action'] ?? $_GET['action'] ?? '';

// ─────────────────────────────────────────────────────────────────────────────
// JSONBIN: LEER
// ─────────────────────────────────────────────────────────────────────────────
function jb_leer(): array {
    $ctx = stream_context_create([
        'http' => [
            'method'  => 'GET',
            'header'  => "X-Master-Key: " . JB_KEY . "\r\nX-Bin-Meta: false\r\n",
            'timeout' => 10,
        ],
    ]);

    $raw = @file_get_contents(JB_GET_URL, false, $ctx);

    if ($raw === false) {
        // Fallback a archivo local si JSONBin no responde
        $local = __DIR__ . '/../data/cartas.json';
        if (file_exists($local)) {
            $raw = file_get_contents($local);
        } else {
            return [];
        }
    }

    $data = json_decode($raw, true);

    // JSONBin devuelve { "record": [...] } con X-Bin-Meta: false devuelve el array directamente
    if (isset($data['record'])) $data = $data['record'];
    return is_array($data) ? $data : [];
}

// ─────────────────────────────────────────────────────────────────────────────
// JSONBIN: ESCRIBIR (PUT reemplaza todo el bin)
// ─────────────────────────────────────────────────────────────────────────────
function jb_guardar(array $data): bool {
    $body = json_encode(array_values($data), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    $ctx = stream_context_create([
        'http' => [
            'method'  => 'PUT',
            'header'  => "Content-Type: application/json\r\nX-Master-Key: " . JB_KEY . "\r\n",
            'content' => $body,
            'timeout' => 15,
        ],
    ]);

    $res = @file_get_contents(JB_PUT_URL, false, $ctx);

    if ($res === false) return false;

    // También guardar copia local de seguridad
    $local = __DIR__ . '/../data/cartas.json';
    @file_put_contents($local, $body);

    return true;
}

// ─────────────────────────────────────────────────────────────────────────────
// SUBIDA DE IMAGEN AL SERVIDOR LOCAL
// ─────────────────────────────────────────────────────────────────────────────
function subirImagen(string $campo, string $slug, string $sufijo): string {
    if (!isset($_FILES[$campo]) || $_FILES[$campo]['error'] !== UPLOAD_ERR_OK) return '';

    $file = $_FILES[$campo];
    if ($file['size'] > MAX_SIZE) {
        echo json_encode(['ok' => false, 'error' => "Imagen '$campo' demasiado grande (máx 8 MB)"]);
        exit;
    }

    $ext     = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    if (!in_array($ext, $allowed)) {
        echo json_encode(['ok' => false, 'error' => "Formato no permitido: $ext"]);
        exit;
    }

    $nombre  = $slug . '_' . $sufijo . '.' . $ext;
    $destino = IMG_PATH . $nombre;

    if (!move_uploaded_file($file['tmp_name'], $destino)) {
        echo json_encode(['ok' => false, 'error' => "No se pudo guardar la imagen '$campo' en el servidor"]);
        exit;
    }

    return IMG_WEB . $nombre;
}

// ─────────────────────────────────────────────────────────────────────────────
// GENERAR SLUG
// ─────────────────────────────────────────────────────────────────────────────
function generarSlug(string $texto): string {
    $texto = mb_strtolower($texto, 'UTF-8');
    $texto = strtr($texto, ['á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ñ'=>'n','ü'=>'u','&'=>'and']);
    $texto = preg_replace('/[^a-z0-9\s-]/', '', $texto);
    $texto = preg_replace('/[\s-]+/', '-', trim($texto));
    return substr($texto, 0, 60);
}

// ─────────────────────────────────────────════════════════════════════════════
// RUTAS
// ─────────────────────────────────────────────────────────────────────────────
switch ($action) {

    // ── LISTAR ──────────────────────────────────────────────────────────────
    case 'listar':
        $data = jb_leer();
        echo json_encode(['ok' => true, 'data' => $data, 'total' => count($data)]);
        break;

    // ── CREAR ───────────────────────────────────────────────────────────────
    case 'crear':
        $titulo   = trim($_POST['titulo']       ?? '');
        $sala     = trim($_POST['sala']         ?? '');
        $set      = trim($_POST['set']          ?? '');
        $year     = trim($_POST['year']         ?? '');
        $artist   = trim($_POST['artist']       ?? '');
        $rarity   = trim($_POST['rarity']       ?? '');
        $desc     = trim($_POST['descripcion']  ?? '');
        $fb       = trim($_POST['fallback']     ?? '');
        $idManual = trim($_POST['id']           ?? '');

        if (!$titulo || !$sala) {
            echo json_encode(['ok' => false, 'error' => 'Título y sala son obligatorios']);
            exit;
        }

        $slug = $idManual ?: generarSlug($titulo . '-' . $year);

        $catalogo = jb_leer();

        // Verificar ID duplicado
        foreach ($catalogo as $c) {
            if (($c['id'] ?? '') === $slug) {
                echo json_encode(['ok' => false, 'error' => "Ya existe una carta con id: $slug"]);
                exit;
            }
        }

        // Subir imágenes al servidor
        $imgFrente = subirImagen('img_frente', $slug, 'frente');
        $imgReves  = subirImagen('img_reves',  $slug, 'reves');

        $carta = [
            'id'               => $slug,
            'sala'             => $sala,
            'titulo'           => $titulo,
            'set'              => $set,
            'year'             => $year,
            'artist'           => $artist,
            'rarity'           => $rarity,
            'imagen'           => $imgFrente,
            'imagen_reves'     => $imgReves,
            'imagen_fallback'  => $fb,
            'descripcion'      => $desc,
        ];

        $catalogo[] = $carta;

        if (!jb_guardar($catalogo)) {
            echo json_encode(['ok' => false, 'error' => 'Error al guardar en JSONBin. Verifica la conexión.']);
            exit;
        }

        echo json_encode(['ok' => true, 'carta' => $carta]);
        break;

    // ── EDITAR ──────────────────────────────────────────────────────────────
    case 'editar':
        $id     = trim($_POST['id']          ?? '');
        $titulo = trim($_POST['titulo']      ?? '');
        $sala   = trim($_POST['sala']        ?? '');
        $set    = trim($_POST['set']         ?? '');
        $year   = trim($_POST['year']        ?? '');
        $artist = trim($_POST['artist']      ?? '');
        $rarity = trim($_POST['rarity']      ?? '');
        $desc   = trim($_POST['descripcion'] ?? '');
        $fb     = trim($_POST['fallback']    ?? '');

        if (!$id) {
            echo json_encode(['ok' => false, 'error' => 'ID requerido']);
            exit;
        }

        $catalogo = jb_leer();
        $found    = false;

        foreach ($catalogo as &$carta) {
            if (($carta['id'] ?? '') !== $id) continue;
            $found = true;

            if ($sala)   $carta['sala']        = $sala;
            if ($titulo) $carta['titulo']      = $titulo;
            if ($set)    $carta['set']         = $set;
            if ($year)   $carta['year']        = $year;
            if ($artist) $carta['artist']      = $artist;
            if ($rarity) $carta['rarity']      = $rarity;
            if ($desc)   $carta['descripcion'] = $desc;
            if ($fb)     $carta['imagen_fallback'] = $fb;

            // Reemplazar imágenes solo si se sube nueva
            $nuevaFrente = subirImagen('img_frente', $id, 'frente');
            $nuevoReves  = subirImagen('img_reves',  $id, 'reves');
            if ($nuevaFrente) $carta['imagen']       = $nuevaFrente;
            if ($nuevoReves)  $carta['imagen_reves'] = $nuevoReves;
            break;
        }
        unset($carta);

        if (!$found) {
            echo json_encode(['ok' => false, 'error' => 'Carta no encontrada']);
            exit;
        }

        if (!jb_guardar($catalogo)) {
            echo json_encode(['ok' => false, 'error' => 'Error al guardar en JSONBin']);
            exit;
        }

        echo json_encode(['ok' => true]);
        break;

    // ── ELIMINAR ────────────────────────────────────────────────────────────
    case 'eliminar':
        $id = trim($_POST['id'] ?? '');

        if (!$id) {
            echo json_encode(['ok' => false, 'error' => 'ID requerido']);
            exit;
        }

        $catalogo = jb_leer();
        $nuevos   = array_values(array_filter($catalogo, fn($c) => ($c['id'] ?? '') !== $id));

        if (count($nuevos) === count($catalogo)) {
            echo json_encode(['ok' => false, 'error' => 'Carta no encontrada en el catálogo']);
            exit;
        }

        if (!jb_guardar($nuevos)) {
            echo json_encode(['ok' => false, 'error' => 'Error al guardar en JSONBin']);
            exit;
        }

        echo json_encode(['ok' => true, 'eliminadas' => count($catalogo) - count($nuevos)]);
        break;

    // ── SINCRONIZAR local → JSONBin ─────────────────────────────────────────
    case 'sincronizar':
        $local = __DIR__ . '/../data/cartas.json';
        if (!file_exists($local)) {
            echo json_encode(['ok' => false, 'error' => 'No existe data/cartas.json local']);
            exit;
        }
        $data = json_decode(file_get_contents($local), true);
        if (!is_array($data)) {
            echo json_encode(['ok' => false, 'error' => 'JSON local inválido']);
            exit;
        }
        if (!jb_guardar($data)) {
            echo json_encode(['ok' => false, 'error' => 'Error al subir a JSONBin']);
            exit;
        }
        echo json_encode(['ok' => true, 'subidas' => count($data), 'mensaje' => 'Catálogo local sincronizado con JSONBin']);
        break;

    default:
        echo json_encode(['ok' => false, 'error' => 'Acción desconocida: ' . htmlspecialchars($action)]);
}
