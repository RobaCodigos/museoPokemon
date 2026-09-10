<?php
/**
 * Script de migración: Añade el campo "sala" al cartas.json
 * basándose en el campo "categoria" existente.
 * Ejecutar UNA sola vez desde el navegador: http://localhost/museoPokemon/php/migrar_sala.php
 */

$jsonPath = __DIR__ . '/../data/cartas.json';
$raw  = file_get_contents($jsonPath);
$data = json_decode($raw, true);

if (!is_array($data)) {
    die('Error leyendo el JSON');
}

// Mapa categoria → sala
function categoriaASala(string $cat): string {
    $cat = mb_strtolower($cat);
    if (str_contains($cat, 'clásica') || str_contains($cat, 'vintage') || str_contains($cat, 'clasica')) return 'sala1';
    if (str_contains($cat, 'legend')  || str_contains($cat, 'especial') || str_contains($cat, 'mega'))    return 'sala2';
    if (str_contains($cat, 'moderna') || str_contains($cat, 'brillo')   || str_contains($cat, 'alt')
     || str_contains($cat, 'secret')  || str_contains($cat, 'graded')   || str_contains($cat, 'promo'))   return 'sala3';
    return 'sala1'; // default
}

$cambios = 0;
foreach ($data as &$carta) {
    if (!isset($carta['sala']) || $carta['sala'] === '') {
        $carta['sala'] = categoriaASala($carta['categoria'] ?? '');
        $cambios++;
    }
}
unset($carta);

file_put_contents($jsonPath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

echo "<h2>✅ Migración completada</h2>";
echo "<p>Se añadió el campo <code>sala</code> a <strong>$cambios</strong> cartas.</p>";
echo "<pre>";
foreach ($data as $c) {
    echo $c['id'] . ' → ' . $c['sala'] . "\n";
}
echo "</pre>";
echo '<p><a href="../admin.php">→ Ir al Panel Admin</a></p>';
