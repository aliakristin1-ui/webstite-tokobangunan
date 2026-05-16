<?php
session_start();

include "fungsi.php";

$cart = $_SESSION['cart'] ?? [];

if (isset($_GET['plus'])) {

    $id = $_GET['plus'];

    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]['qty']++;
    }

    header("Location: keranjang.php");
    exit;
}

if (isset($_GET['minus'])) {

    $id = $_GET['minus'];

    if (isset($_SESSION['cart'][$id])) {

        $_SESSION['cart'][$id]['qty']--;

        if ($_SESSION['cart'][$id]['qty'] <= 0) {
            unset($_SESSION['cart'][$id]);
        }
    }

    header("Location: keranjang.php");
    exit;
}

if (isset($_GET['hapus'])) {

    $id = $_GET['hapus'];

    unset($_SESSION['cart'][$id]);

    header("Location: keranjang.php");
    exit;
}

$cart = $_SESSION['cart'] ?? [];

$total = 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Keranjang Belanja</title>

<link rel="icon" type="image/png" href="assets/img/logo.png">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
    background:#f5f6fa;
}

.qty-box{
    display:flex;
    align-items:center;
    gap:10px;
}

.qty-btn{
    width:32px;
    height:32px;
    border:none;
    border-radius:8px;
    background:#198754;
    color:white;
    font-weight:bold;
}
</style>

</head>
<body>

<div class="container py-5">

    <h1 class="mb-4">
        Keranjang Belanja
    </h1>

    <?php if(empty($cart)): ?>

    <div class="alert alert-warning">
        Keranjang masih kosong
    </div>

    <a href="index.php" class="btn btn-success">
        Belanja Sekarang
    </a>

    <?php else: ?>

    <div class="card border-0 shadow-sm">

        <div class="card-body">

            <div class="table-responsive">

                <table class="table align-middle">

                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Harga</th>
                            <th width="180">Qty</th>
                            <th>Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php foreach($cart as $id => $c): ?>

                    <?php
                    $subtotal = $c['harga'] * $c['qty'];
                    $total += $subtotal;
                    ?>

                    <tr>

                        <td>
                            <strong>
                                <?php echo $c['nama_produk']; ?>
                            </strong>
                        </td>

                        <td>
                            <?php echo rupiah($c['harga']); ?>
                        </td>

                        <td>

                            <div class="qty-box">

                                <a href="?minus=<?php echo $id; ?>">
                                    <button class="qty-btn">
                                        -
                                    </button>
                                </a>

                                <strong>
                                    <?php echo $c['qty']; ?>
                                </strong>

                                <a href="?plus=<?php echo $id; ?>">
                                    <button class="qty-btn">
                                        +
                                    </button>
                                </a>

                            </div>

                        </td>

                        <td>
                            <?php echo rupiah($subtotal); ?>
                        </td>

                        <td>

                            <a href="?hapus=<?php echo $id; ?>"
                               class="btn btn-danger btn-sm">

                                Hapus

                            </a>

                        </td>

                    </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

            <hr>

            <div class="d-flex justify-content-between align-items-center">

                <h4>
                    Total:
                    <strong class="text-success">
                        <?php echo rupiah($total); ?>
                    </strong>
                </h4>

                <div>

                    <a href="index.php"
                       class="btn btn-secondary">

                        Lanjut Belanja

                    </a>

                    <a href="checkout.php"
                       class="btn btn-success">

                        Checkout

                    </a>

                </div>

            </div>

        </div>

    </div>

    <?php endif; ?>

</div>

</body>
</html>