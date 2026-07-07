<?php
/**
 * Genera los íconos de la PWA (llave amarilla sobre fondo gris oscuro).
 * Uso: php scripts/generar-iconos.php
 * Requiere la extensión GD.
 */

const BG = [0x11, 0x18, 0x27];   // gray-900
const FG = [0xfa, 0xcc, 0x15];   // yellow-400
const MASTER = 1024;

/**
 * Dibuja el ícono en un canvas cuadrado.
 *
 * @param float $escala    Fracción del canvas que ocupa el dibujo (zona segura maskable ≈ 0.62)
 * @param bool  $redondear Esquinas redondeadas con fondo transparente afuera
 */
function dibujar(int $size, float $escala, bool $redondear): GdImage
{
    $img = imagecreatetruecolor($size, $size);
    imagesavealpha($img, true);
    imagealphablending($img, false);
    $transparente = imagecolorallocatealpha($img, 0, 0, 0, 127);
    imagefill($img, 0, 0, $transparente);
    imagealphablending($img, true);

    $bg = imagecolorallocate($img, ...BG);
    $fg = imagecolorallocate($img, ...FG);

    // Fondo
    if ($redondear) {
        $r = (int) round($size * 0.22);
        imagefilledrectangle($img, $r, 0, $size - $r - 1, $size - 1, $bg);
        imagefilledrectangle($img, 0, $r, $size - 1, $size - $r - 1, $bg);
        foreach ([[$r, $r], [$size - $r - 1, $r], [$r, $size - $r - 1], [$size - $r - 1, $size - $r - 1]] as [$cx, $cy]) {
            imagefilledellipse($img, $cx, $cy, $r * 2, $r * 2, $bg);
        }
    } else {
        imagefilledrectangle($img, 0, 0, $size - 1, $size - 1, $bg);
    }

    // Coordenadas unitarias del dibujo, centradas y escaladas
    $u = fn(float $x) => (int) round($size / 2 + ($x - 0.5) * $size * $escala);
    $d = fn(float $v) => (int) round($v * $size * $escala);

    // Llave combinada: boca abierta arriba-izquierda, ojal abajo-derecha
    $ax = 0.32; $ay = 0.32;   // centro de la boca
    $bx = 0.70; $by = 0.70;   // centro del ojal
    $rBoca  = 0.17;
    $rOjal  = 0.115;
    $rHueco = 0.055;
    $ancho  = 0.11;           // grosor del mango

    // Mango (rectángulo rotado entre A y B)
    $dx = $bx - $ax; $dy = $by - $ay;
    $len = hypot($dx, $dy);
    $px = -$dy / $len * $ancho / 2; $py = $dx / $len * $ancho / 2;
    imagefilledpolygon($img, [
        $u($ax + $px), $u($ay + $py),
        $u($bx + $px), $u($by + $py),
        $u($bx - $px), $u($by - $py),
        $u($ax - $px), $u($ay - $py),
    ], $fg);

    // Cabeza de la boca
    imagefilledellipse($img, $u($ax), $u($ay), $d($rBoca * 2), $d($rBoca * 2), $fg);

    // Muesca de la boca (corte en color de fondo, hacia arriba-izquierda)
    $nx = -0.7071; $ny = -0.7071;
    $mAncho = 0.10;
    $qx = -$ny * $mAncho / 2; $qy = $nx * $mAncho / 2;
    $ini = 0.03; $fin = 0.30;
    imagefilledpolygon($img, [
        $u($ax + $nx * $ini + $qx), $u($ay + $ny * $ini + $qy),
        $u($ax + $nx * $fin + $qx), $u($ay + $ny * $fin + $qy),
        $u($ax + $nx * $fin - $qx), $u($ay + $ny * $fin - $qy),
        $u($ax + $nx * $ini - $qx), $u($ay + $ny * $ini - $qy),
    ], $bg);

    // Ojal (anillo)
    imagefilledellipse($img, $u($bx), $u($by), $d($rOjal * 2), $d($rOjal * 2), $fg);
    imagefilledellipse($img, $u($bx), $u($by), $d($rHueco * 2), $d($rHueco * 2), $bg);

    return $img;
}

function exportar(GdImage $master, int $size, string $destino): void
{
    $out = imagecreatetruecolor($size, $size);
    imagesavealpha($out, true);
    imagealphablending($out, false);
    imagefill($out, 0, 0, imagecolorallocatealpha($out, 0, 0, 0, 127));
    imagecopyresampled($out, $master, 0, 0, 0, 0, $size, $size, MASTER, MASTER);
    imagepng($out, $destino);
    imagedestroy($out);
    echo "✔ $destino\n";
}

$base = dirname(__DIR__) . '/public';
@mkdir("$base/icons", 0777, true);

// Estándar: esquinas redondeadas, dibujo al 78%
$std = dibujar(MASTER, 0.78, true);
exportar($std, 192, "$base/icons/icon-192.png");
exportar($std, 512, "$base/icons/icon-512.png");
exportar($std, 48, "$base/favicon.png");
imagedestroy($std);

// Maskable: cuadrado pleno, dibujo al 62% (zona segura)
$mask = dibujar(MASTER, 0.62, false);
exportar($mask, 512, "$base/icons/icon-512-maskable.png");
imagedestroy($mask);

// Apple touch: cuadrado pleno sin transparencia (iOS redondea solo)
$apple = dibujar(MASTER, 0.78, false);
exportar($apple, 180, "$base/icons/apple-touch-icon.png");
imagedestroy($apple);

echo "Listo.\n";
