<?php
/**
 * vendor.php
 * Daftar terpusat semua CDN library pihak ketiga.
 * Include file ini di header atau footer — jangan hardcode CDN di banyak tempat.
 *
 * CSS  → include di <head>   : include "assets/vendor/vendor.php";  // dengan $vendor_section = 'css'
 * JS   → include sebelum </body> : include "assets/vendor/vendor.php";  // dengan $vendor_section = 'js'
 */

$vendor_css = [
    'Bootstrap 5.3'      => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css',
    'Bootstrap Icons'    => 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css',
    'DataTables BS5'     => 'https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css',
];

$vendor_js = [
    'Bootstrap Bundle'   => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js',
    'jQuery 3.7'         => 'https://code.jquery.com/jquery-3.7.0.min.js',
    'DataTables Core'    => 'https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js',
    'DataTables BS5'     => 'https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js',
    'SweetAlert2'        => 'https://cdn.jsdelivr.net/npm/sweetalert2@11',
    'ApexCharts'         => 'https://cdn.jsdelivr.net/npm/apexcharts',
];

$vendor_fonts = [
    'Google Fonts (Nunito+Poppins)' => 'https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap',
];
