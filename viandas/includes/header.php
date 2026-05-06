<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ViandaLibre</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/style.css">
    <script src="<?php echo BASE_URL; ?>/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand" href="<?php echo route_url('/'); ?>">ViandaLibre</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo route_url('/'); ?>">Catálogo</a>
                        </li>
                        <?php if (isLoggedIn()): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo route_url('/admin/viandas'); ?>">Admin Viandas</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo route_url('/admin/comandas'); ?>">Admin Comandas</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo route_url('/admin/logout'); ?>">Logout</a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo route_url('/admin/login'); ?>">Admin</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <main class="container mt-4">