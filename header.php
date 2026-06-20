<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MotoGestión Web - Motomanía</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- CSS personalizado MEJORADO -->
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        /* Estilos adicionales para asegurar que los iconos se vean */
        .fas, .far, .fab {
            font-family: "Font Awesome 6 Free";
        }
        .fas {
            font-weight: 900;
        }
        
        /* Ajustes de navbar */
        .navbar-dark.bg-dark {
            background: linear-gradient(135deg, #2d3748 0%, #1a202c 100%) !important;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }
        .navbar-brand {
            font-weight: 700;
            font-size: 1.3rem;
            letter-spacing: -0.5px;
        }
        .navbar-brand span {
            color: #667eea;
        }
        .offcanvas.bg-dark {
            background: linear-gradient(180deg, #1a202c 0%, #2d3748 100%) !important;
        }
        .offcanvas-header .offcanvas-title {
            font-weight: 600;
        }
        .offcanvas-header .offcanvas-title .badge {
            font-size: 11px;
            padding: 4px 12px;
        }
        
        /* Animación de carga para gráficas */
        .chart-container {
            position: relative;
            min-height: 300px;
        }
        .chart-container .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255,255,255,0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
            border-radius: var(--radius);
        }
        .chart-container .loading-overlay .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #e2e8f0;
            border-top-color: #667eea;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>

<?php
// Verificar si el usuario está logueado para mostrar el sidebar
if (isset($_SESSION['user_id']) && basename($_SERVER['PHP_SELF']) != 'login.php') {
    include_once __DIR__ . '/sidebar.php';
}
?>