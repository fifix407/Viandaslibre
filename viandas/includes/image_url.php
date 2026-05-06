<?php
/**
 * Helpers para resolver URLs de imágenes de viandas.
 */

if (!function_exists('vianda_image_fallback_data_uri')) {
    function vianda_image_fallback_data_uri() {
        static $cached = null;
        if ($cached !== null) {
            return $cached;
        }

        $svg = "<svg xmlns='http://www.w3.org/2000/svg' width='800' height='600' viewBox='0 0 800 600'>"
            . "<rect width='800' height='600' fill='#f3f4f6'/>"
            . "<rect x='180' y='140' width='440' height='320' rx='20' fill='#e5e7eb'/>"
            . "<circle cx='300' cy='250' r='45' fill='#d1d5db'/>"
            . "<path d='M225 410l110-95 90 75 70-55 80 75H225z' fill='#cbd5e1'/>"
            . "<text x='400' y='500' text-anchor='middle' font-size='30' fill='#6b7280' font-family='Arial, sans-serif'>Sin imagen</text>"
            . "</svg>";

        $cached = 'data:image/svg+xml;charset=UTF-8,' . rawurlencode($svg);
        return $cached;
    }
}

if (!function_exists('vianda_image_base_dir_candidates')) {
    function vianda_image_base_dir_candidates() {
        $projectRoot = dirname(__DIR__);
        return [
            $projectRoot . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'img',
            $projectRoot . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'img',
            $projectRoot . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'img',
            $projectRoot . DIRECTORY_SEPARATOR . 'img',
        ];
    }
}

if (!function_exists('resolve_vianda_image_base_dir')) {
    function resolve_vianda_image_base_dir() {
        foreach (vianda_image_base_dir_candidates() as $candidate) {
            $real = realpath($candidate);
            if ($real !== false && is_dir($real)) {
                return rtrim($real, DIRECTORY_SEPARATOR);
            }
        }
        return null;
    }
}

if (!function_exists('vianda_image_allowed_extensions')) {
    function vianda_image_allowed_extensions() {
        return ['png', 'jpg', 'jpeg', 'webp', 'gif'];
    }
}

if (!function_exists('resolve_vianda_image_file_path')) {
    function resolve_vianda_image_file_path($rawImageValue) {
        $value = trim((string)$rawImageValue);
        if ($value === '') {
            return null;
        }

        $normalized = str_replace('\\', '/', $value);
        $normalized = ltrim($normalized, '/');
        $normalized = ltrim($normalized, './');
        $normalized = preg_replace('#^public/#i', '', $normalized);
        $normalized = preg_replace('#^assets/(img|images)/#i', '', $normalized);

        if ($normalized === '' || strpos($normalized, '..') !== false) {
            return null;
        }

        $baseDir = resolve_vianda_image_base_dir();
        if ($baseDir === null) {
            return null;
        }

        $relativePath = str_replace('/', DIRECTORY_SEPARATOR, $normalized);
        $pathInfo = pathinfo($relativePath);
        $dirname = isset($pathInfo['dirname']) && $pathInfo['dirname'] !== '.'
            ? $pathInfo['dirname']
            : '';
        $filename = $pathInfo['filename'] ?? '';
        $extension = isset($pathInfo['extension']) ? strtolower((string)$pathInfo['extension']) : '';
        if ($filename === '') {
            return null;
        }

        $candidateNames = [$filename];
        if ($extension !== '') {
            $candidateNames[] = $filename . '.' . $extension;
        }

        $extensions = vianda_image_allowed_extensions();
        foreach ($extensions as $ext) {
            $candidateNames[] = $filename . '.' . $ext;
        }
        $candidateNames = array_values(array_unique($candidateNames));

        foreach ($candidateNames as $name) {
            $candidateRelative = $dirname !== ''
                ? $dirname . DIRECTORY_SEPARATOR . $name
                : $name;
            $candidatePath = $baseDir . DIRECTORY_SEPARATOR . $candidateRelative;
            $realCandidate = realpath($candidatePath);
            if ($realCandidate === false) {
                continue;
            }
            if (strpos($realCandidate, $baseDir . DIRECTORY_SEPARATOR) !== 0 || !is_file($realCandidate)) {
                continue;
            }
            return $realCandidate;
        }

        return null;
    }
}

if (!function_exists('build_vianda_image_url')) {
    function build_vianda_image_url($rawImageValue) {
        $value = trim((string)$rawImageValue);
        if ($value === '') {
            return vianda_image_fallback_data_uri();
        }

        if (preg_match('#^(https?:)?//#i', $value) || strpos($value, 'data:') === 0) {
            return $value;
        }

        $normalized = str_replace('\\', '/', $value);
        $normalized = ltrim($normalized, '/');
        $normalized = ltrim($normalized, './');
        $normalized = preg_replace('#^public/#i', '', $normalized);
        $normalized = preg_replace('#^assets/(img|images)/#i', '', $normalized);

        if ($normalized === '' || strpos($normalized, '..') !== false) {
            return vianda_image_fallback_data_uri();
        }

        // Si BASE_URL no está definida (ej: en llamadas directas a la API), 
        // intentamos detectarla dinámicamente para que la URL sea absoluta desde el root.
        $baseUrl = '';
        if (defined('BASE_URL')) {
            $baseUrl = rtrim((string)BASE_URL, '/');
        } else {
            // Lógica simplificada de detección de base para APIs
            $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
            if ($scriptName !== '') {
                $parts = explode('/public/api/', $scriptName);
                if (count($parts) > 1) {
                    $baseUrl = rtrim($parts[0], '/') . '/public';
                } else {
                    // Fallback si no está en /public/api/
                    $baseUrl = rtrim(dirname(dirname($scriptName)), '/');
                }
            }
        }

        // Aseguramos que la URL comience con el baseUrl y apunte al endpoint de imágenes
        return $baseUrl . '/api/vianda_image.php?img=' . rawurlencode($normalized);
    }
}
