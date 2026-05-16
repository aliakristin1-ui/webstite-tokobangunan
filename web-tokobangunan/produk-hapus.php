<?php
include "fungsi.php";
validasi();
include "Dml.php";
$db = new Dml();
$id = intval($_GET['id'] ?? 0);
$db->deleteData()->from_into('produk')->where("id_produk=$id")->del();
header('Location: produk.php?success=Produk berhasil dihapus');
exit;