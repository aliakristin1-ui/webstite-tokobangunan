<?php include_once "assets/vendor/vendor.php"; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OMM-App | Toko Bangunan Our Muda Maju</title>
    <link rel="shortcut icon" type="image/png" href="assets/img/logo.png">

    <link href="<?= $vendor_fonts['Google Fonts (Nunito+Poppins)'] ?>" rel="stylesheet">

    <?php foreach ($vendor_css as $name => $url): ?>
    <link href="<?= $url ?>" rel="stylesheet"><!-- <?= $name ?> -->
    <?php endforeach; ?>

    <link href="assets/css/main.css" rel="stylesheet">
</head>
<body>

<header id="header">
    <a href="beranda.php" class="logo-brand">
        <div class="logo-icon"><i class="bi bi-shop"></i></div>
        <div>
            <div class="logo-text">OMM-App</div>
            <div class="logo-sub">OUR MUDA MAJU</div>
        </div>
    </a>
    <i class="bi bi-list toggle-sidebar-btn" onclick="toggleSidebar()"></i>
    <nav class="header-nav">
        <ul class="list-unstyled d-flex align-items-center m-0 gap-2">
            <li class="nav-item">
                <div class="user-badge">
                    <i class="bi bi-person-circle"></i>
                    <?php echo htmlspecialchars($_SESSION['nama_role'] ?? ''); ?>
                </div>
            </li>
            <li class="nav-item">
                <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Keluar</a>
            </li>
        </ul>
    </nav>
</header>

<aside id="sidebar">
    <ul class="sidebar-nav">

        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF'])=='beranda.php'?'active':''; ?>" href="beranda.php">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>

        <?php
        $role    = (int)($_SESSION['id_role'] ?? 0);
        $halaman = basename($_SERVER['PHP_SELF']);
        ?>

        <?php if (isKasir()): ?>
        <li><div class="nav-section">Penjualan</div></li>
        <li class="nav-item">
            <a class="nav-link <?php echo $halaman=='transaksi.php'||$halaman=='transaksi-tambah.php'?'active':''; ?>" href="transaksi.php">
                <i class="bi bi-receipt"></i> Transaksi Penjualan
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo strpos($halaman,'pelanggan')===0?'active':''; ?>" href="pelanggan.php">
                <i class="bi bi-people-fill"></i> Pelanggan
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo strpos($halaman,'pesanan')===0?'active':''; ?>" href="pesanan-online.php">
                <i class="bi bi-bag-check-fill"></i> Pesanan Online
            </a>
        </li>
        <?php endif; ?>

        <?php if (isGudang()): ?>
        <li><div class="nav-section">Inventaris</div></li>
        <li class="nav-item">
            <a class="nav-link <?php echo strpos($halaman,'produk')===0?'active':''; ?>" href="produk.php">
                <i class="bi bi-box-seam-fill"></i> Produk
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo strpos($halaman,'stok')===0?'active':''; ?>" href="stok.php">
                <i class="bi bi-arrow-left-right"></i> Mutasi Stok
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo strpos($halaman,'pengiriman')===0?'active':''; ?>" href="pengiriman.php">
                <i class="bi bi-truck"></i> Pengiriman
            </a>
        </li>
        <?php endif; ?>

        <?php if (isGudang()): ?>
        <li><div class="nav-section">Armada &amp; Logistik</div></li>
        <li class="nav-item">
            <a class="nav-link <?php echo strpos($halaman,'armada')===0?'active':''; ?>" href="armada-truk.php">
                <i class="bi bi-truck-front-fill"></i> Armada Truk
            </a>
        </li>
        <?php endif; ?>

        <?php if (isKasir() || isOwner()): ?>
        <li><div class="nav-section">Keuangan</div></li>
        <li class="nav-item">
            <a class="nav-link <?php echo strpos($halaman,'keuangan')===0?'active':''; ?>" href="keuangan.php">
                <i class="bi bi-cash-coin"></i> Kas &amp; Keuangan
            </a>
        </li>
        <?php endif; ?>
        <?php if (isAdmin() || isOwner()): ?>
        <li class="nav-item">
            <a class="nav-link <?php echo strpos($halaman,'penggajian')===0?'active':''; ?>" href="penggajian.php">
                <i class="bi bi-wallet2"></i> Penggajian
            </a>
        </li>
        <?php endif; ?>

    </ul>
</aside>