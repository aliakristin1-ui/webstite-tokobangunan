/* ============================================================
   main.js — Script utama OMM-App Toko Bangunan
   ============================================================ */

/* ---------- Sidebar toggle ---------- */
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const main    = document.getElementById('main');
    if (window.innerWidth > 768) {
        sidebar.classList.toggle('hide');
        main && main.classList.toggle('full');
    } else {
        sidebar.classList.toggle('show');
    }
}

/* ---------- DataTables default init ---------- */
$(document).ready(function () {
    $('.datatable').DataTable({
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        },
        responsive: true,
        pageLength: 10,
    });
});

/* ---------- SweetAlert helpers ---------- */
function hapus(url, nama) {
    Swal.fire({
        title: 'Hapus Data?',
        html: 'Data <b>' + nama + '</b> akan dihapus permanen!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor:  '#6c757d',
        confirmButtonText:  '<i class="bi bi-trash"></i> Ya, Hapus!',
        cancelButtonText:   'Batal'
    }).then(function (r) {
        if (r.isConfirmed) window.location.href = url;
    });
}

function notif(type, msg) {
    Swal.fire({ icon: type, title: msg, timer: 2000, showConfirmButton: false });
}
