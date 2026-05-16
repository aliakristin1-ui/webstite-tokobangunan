<?php
session_start();

include "fungsi.php";
include "Dml.php";

$db = new Dml();

$cart = $_SESSION['cart'] ?? [];

if (empty($cart)) {

    header("Location: keranjang.php");
    exit;
}

$total = 0;

foreach ($cart as $c) {

    $total += $c['harga'] * $c['qty'];
}

if (isset($_POST['checkout'])) {

    $nama   = trim($_POST['nama']);
    $hp     = trim($_POST['hp']);
    $alamat = trim($_POST['alamat']);
    $email  = trim($_POST['email']);

    if ($nama != '' && $hp != '' && $alamat != '') {

        $cek = $db->query("
            SELECT *
            FROM pelanggan
            WHERE no_hp = ?
        ", [$hp]);

        if (empty($cek)) {

            $db->execute("
                INSERT INTO pelanggan
                (
                    nama,
                    no_hp,
                    alamat,
                    email
                )
                VALUES
                (
                    ?,
                    ?,
                    ?,
                    ?
                )
            ", [
                $nama,
                $hp,
                $alamat,
                $email
            ]);

            $id_pelanggan = $db->getInsertId();

        } else {

            $id_pelanggan = $cek[0]['id_pelanggan'];
        }

        $db->execute("
            INSERT INTO pesanan_online
            (
                id_pelanggan,
                tanggal,
                total,
                status
            )
            VALUES
            (
                ?,
                NOW(),
                ?,
                'pending'
            )
        ", [
            $id_pelanggan,
            $total
        ]);

        $id_pesanan = $db->getInsertId();

        foreach ($cart as $c) {

            $id_produk = $c['id_produk'];
            $qty       = $c['qty'];
            $harga     = $c['harga'];
            $subtotal  = $harga * $qty;

            $db->execute("
                INSERT INTO detail_pesanan_online
                (
                    id_pesanan,
                    id_produk,
                    jumlah,
                    harga,
                    subtotal
                )
                VALUES
                (
                    ?,
                    ?,
                    ?,
                    ?,
                    ?
                )
            ", [

                $id_pesanan,
                $id_produk,
                $qty,
                $harga,
                $subtotal

            ]);

            $db->execute("
                INSERT INTO stok_mutasi
                (
                    id_produk,
                    tanggal,
                    jenis,
                    jumlah,
                    referensi,
                    keterangan
                )
                VALUES
                (
                    ?,
                    NOW(),
                    'keluar',
                    ?,
                    ?,
                    ?
                )
            ", [

                $id_produk,
                $qty,
                'PO-' . $id_pesanan,
                'Pesanan Online #' . $id_pesanan

            ]);
        }

        unset($_SESSION['cart']);

        $_SESSION['id_pesanan_terakhir'] = $id_pesanan;

        header("Location: pesanan-berhasil.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Checkout</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
    background:#f5f6fa;
}

.checkout-box{
    max-width:700px;
    margin:auto;
}

</style>

</head>
<body>

<div class="container py-5">

    <div class="checkout-box">

        <div class="card border-0 shadow-sm">

            <div class="card-body p-4">

                <h3 class="mb-4">
                    Checkout Pesanan
                </h3>

                <form method="post">

                    <div class="mb-3">

                        <label class="form-label">
                            Nama Lengkap
                        </label>

                        <input type="text"
                               name="nama"
                               class="form-control"
                               required>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Nomor HP
                        </label>

                        <input type="text"
                               name="hp"
                               class="form-control"
                               required>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Email
                        </label>

                        <input type="email"
                               name="email"
                               class="form-control"
                               placeholder="contoh@gmail.com">

                    </div>

                    <div class="mb-4">

                        <label class="form-label">
                            Alamat Lengkap
                        </label>

                        <textarea name="alamat"
                                  class="form-control"
                                  rows="4"
                                  required></textarea>

                    </div>

                    <div class="alert alert-success">

                        <h5 class="mb-0">
                            Total Belanja:
                            <strong>
                                <?php echo rupiah($total); ?>
                            </strong>
                        </h5>

                    </div>

                    <button type="submit"
                            name="checkout"
                            class="btn btn-success w-100">

                        Buat Pesanan

                    </button>

                </form>

            </div>

        </div>

    </div>

</div>

</body>
</html>