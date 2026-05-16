<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set("Asia/Makassar");

function tanggal($tanggal)
{
    $bulan = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];

    if (preg_match('/^\d{4}-\d{2}-\d{2}/', $tanggal)) {
        $tgl       = substr($tanggal, 8, 2);
        $bln_index = (int) substr($tanggal, 5, 2) - 1;
        $bln       = $bulan[$bln_index] ?? '';
        $thn       = substr($tanggal, 0, 4);
        return "$tgl $bln $thn";
    }

    return $tanggal;
}
function rupiah($angka)
{
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

function cekLogin($username, $password)
{
    include_once "Dml.php";

    $obj = new Dml();

    $data_arr = $obj->query(
        "SELECT 
            u.*, 
            k.nama, 
            r.nama_role, 
            r.id_role 
        FROM users u
        JOIN karyawan k ON u.id_karyawan = k.id_karyawan
        JOIN role r ON k.id_role = r.id_role
        WHERE u.username = ?",
        [$username]
    );

    if (empty($data_arr)) {
        return '
        <p class="text-danger text-center">
            <i class="bi bi-exclamation-circle me-1"></i>
            Username tidak ditemukan!
        </p>';
    }

    $data = $data_arr[0];

    if ($data['password'] !== md5($password)) {
        return '
        <p class="text-danger text-center">
            <i class="bi bi-exclamation-circle me-1"></i>
            Password salah!
        </p>';
    }

    $_SESSION['id_user']     = $data['id_user'];
    $_SESSION['username']    = $data['username'];
    $_SESSION['nama']        = $data['nama'];
    $_SESSION['id_role']     = $data['id_role'];
    $_SESSION['nama_role']   = $data['nama_role'];
    $_SESSION['id_karyawan'] = $data['id_karyawan'];
    $_SESSION['_kode']       = "OMM@2026Secure";

    echo "<script>window.location='beranda.php';</script>";
    exit;
}

function validasi()
{
    if (
        !isset($_SESSION['username']) ||
        !isset($_SESSION['id_role'])  ||
        !isset($_SESSION['_kode'])    ||
        $_SESSION['_kode'] !== "OMM@2026Secure"
    ) {
        session_unset();
        session_destroy();
        echo "<script>window.location='login.php';</script>";
        exit;
    }
}

function isOwner()
{
    return isset($_SESSION['id_role']) &&
        (int) $_SESSION['id_role'] === 1;
}

function isAdmin()
{
    return isset($_SESSION['id_role']) &&
        in_array((int) $_SESSION['id_role'], [1, 2]);
}

function isKasir()
{
    return isset($_SESSION['id_role']) &&
        in_array((int) $_SESSION['id_role'], [1, 2, 3]);
}

function isGudang()
{
    return isset($_SESSION['id_role']) &&
        in_array((int) $_SESSION['id_role'], [1, 2, 4]);
}

function isReadOnly()
{
    if (!isOwner()) {
        return false; 
    }

    $halaman_aksi_owner = ['keuangan.php', 'penggajian.php'];
    $halaman_ini        = basename($_SERVER['PHP_SELF']);

    return !in_array($halaman_ini, $halaman_aksi_owner);
}

function getNamaRole($id_role)
{
    $roles = [
        1 => 'Owner',
        2 => 'Admin',
        3 => 'Kasir',
        4 => 'Gudang',
    ];

    return $roles[(int)$id_role] ?? 'Tidak Diketahui';
}

function badge_status($status)
{
    $map = [
        'Sedang diproses'  => 'warning',
        'Dalam pengiriman' => 'info',
        'Selesai'          => 'success',
        'Aktif'            => 'success',
        'Nonaktif'         => 'secondary',
        'belum_lunas'      => 'danger',
        'lunas'            => 'success',
        'pending'          => 'warning',
        'dibatalkan'       => 'danger',
    ];

    $class = $map[$status] ?? 'secondary';

    return "<span class=\"badge bg-{$class}\">{$status}</span>";
}