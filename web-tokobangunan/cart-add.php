<?php
session_start();

include "Dml.php";

$db = new Dml();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header("Location: index.php");
    exit;
}

$produk = $db->queryOne("
    SELECT *
    FROM produk
    WHERE id_produk='$id'
");

if (!$produk) {
    header("Location: index.php");
    exit;
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_SESSION['cart'][$id])) {

    $_SESSION['cart'][$id]['qty']++;

} else {

    $_SESSION['cart'][$id] = [
        'id_produk' => $produk['id_produk'],
        'nama_produk' => $produk['nama_produk'],
        'harga' => $produk['harga_jual'],
        'qty' => 1
    ];
}

header("Location: keranjang.php");
exit;
?>