<?php
include "fungsi.php";
validasi();
include "Dml.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $db = new Dml();

    $foto = '';

    if (!empty($_FILES['foto']['name'])) {

        $foto = time().'_'.$_FILES['foto']['name'];

        move_uploaded_file(
            $_FILES['foto']['tmp_name'],
            'upload/'.$foto
        );
    }

    $db->insert([
        'nama_produk' => $_POST['nama_produk'],
        'kategori'    => $_POST['kategori'],
        'satuan'      => $_POST['satuan'],
        'harga_beli'  => $_POST['harga_beli'],
        'harga_jual'  => $_POST['harga_jual'],
        'barcode'     => $_POST['barcode'],
        'foto'        => $foto,
    ])->from_into('produk')->create();

    header('Location: produk.php?success=Produk berhasil ditambahkan');
    exit;
}

include "header.php";
?>

<main id="main">

<div class="mb-4">

    <h4 class="page-title mb-0">
        <i class="bi bi-plus-circle me-2"></i>
        Tambah Produk
    </h4>

    <div class="page-breadcrumb">
        <a href="beranda.php">Beranda</a> /
        <a href="produk.php">Produk</a> /
        Tambah
    </div>

</div>

<div class="row justify-content-center">

<div class="col-lg-7">

<div class="card">

    <div class="card-header bg-white py-3">
        <span class="fw-bold text-dark">
            Form Tambah Produk Baru
        </span>
    </div>

    <div class="card-body">

        <form method="post" enctype="multipart/form-data">

            <div class="mb-3">

                <label class="form-label fw-semibold">
                    Nama Produk
                    <span class="text-danger">*</span>
                </label>

                <input type="text"
                       name="nama_produk"
                       class="form-control"
                       placeholder="Contoh: Semen Tiga Roda 50kg"
                       required>

            </div>

            <div class="row g-3 mb-3">

                <div class="col-md-6">

                    <label class="form-label fw-semibold">
                        Kategori
                        <span class="text-danger">*</span>
                    </label>

                    <input type="text"
                           name="kategori"
                           class="form-control"
                           placeholder="Semen, Besi, Cat..."
                           list="kat-list"
                           required>

                    <datalist id="kat-list">
                        <option value="Semen">
                        <option value="Besi & Baja">
                        <option value="Cat">
                        <option value="Bata & Genteng">
                        <option value="Kayu & Papan">
                        <option value="Pipa & Sanitasi">
                        <option value="Keramik & Granit">
                        <option value="Alat & Perkakas">
                        <option value="Listrik & Elektronik">
                        <option value="Lainnya">
                    </datalist>

                </div>

                <div class="col-md-6">

                    <label class="form-label fw-semibold">
                        Satuan
                        <span class="text-danger">*</span>
                    </label>

                    <input type="text"
                           name="satuan"
                           class="form-control"
                           placeholder="Sak, Batang, m2, Liter..."
                           list="sat-list"
                           required>

                    <datalist id="sat-list">
                        <option value="Sak">
                        <option value="Batang">
                        <option value="m2">
                        <option value="Liter">
                        <option value="Kg">
                        <option value="Pcs">
                        <option value="Dus">
                        <option value="Roll">
                        <option value="Set">
                    </datalist>

                </div>

            </div>

            <div class="row g-3 mb-3">

                <div class="col-md-6">

                    <label class="form-label fw-semibold">
                        Harga Beli (Rp)
                        <span class="text-danger">*</span>
                    </label>

                    <input type="number"
                           name="harga_beli"
                           class="form-control"
                           placeholder="0"
                           min="0"
                           required>

                </div>

                <div class="col-md-6">

                    <label class="form-label fw-semibold">
                        Harga Jual (Rp)
                        <span class="text-danger">*</span>
                    </label>

                    <input type="number"
                           name="harga_jual"
                           class="form-control"
                           placeholder="0"
                           min="0"
                           required>

                </div>

            </div>

            <div class="mb-3">

                <label class="form-label fw-semibold">
                    Barcode / Kode Produk
                    <span class="text-danger">*</span>
                </label>

                <input type="text"
                       name="barcode"
                       class="form-control"
                       placeholder="Scan atau ketik barcode"
                       required>

            </div>

            <div class="mb-4">

                <label class="form-label fw-semibold">
                    Foto Produk
                </label>

                <input type="file"
                       name="foto"
                       class="form-control">

            </div>

            <div class="d-flex gap-2">

                <button type="submit"
                        class="btn btn-btj">

                    <i class="bi bi-check-circle me-1"></i>
                    Simpan Produk

                </button>

                <a href="produk.php"
                   class="btn btn-outline-secondary">

                    <i class="bi bi-x me-1"></i>
                    Batal

                </a>

            </div>

        </form>

    </div>

</div>

</div>

</div>

</main>

<?php include "footer.php"; ?>