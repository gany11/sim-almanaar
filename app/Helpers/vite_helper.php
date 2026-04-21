<?php

if (!function_exists('vite')) {

    function vite($entry)
    {
        $manifestPath = FCPATH . 'build/.vite/manifest.json';

        // DEV MODE
        if (!file_exists($manifestPath)) {
            return '<script type="module" src="http://localhost:5173/' . $entry . '"></script>';
        }

        $manifest = json_decode(file_get_contents($manifestPath), true);

        if (!isset($manifest[$entry])) return '';

        $html = '';

        // CSS (jika ada)
        if (isset($manifest[$entry]['css'])) {
            foreach ($manifest[$entry]['css'] as $css) {
                $html .= '<link rel="stylesheet" href="' . base_url('build/' . $css) . '">';
            }
        }

        // JS
        $html .= '<script type="module" src="' . base_url('build/' . $manifest[$entry]['file']) . '"></script>';

        return $html;
    }
}