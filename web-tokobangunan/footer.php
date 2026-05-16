<?php include_once "assets/vendor/vendor.php"; ?>

<footer class="text-center text-muted py-3 mt-4" style="font-size:0.8rem; border-top: 1px solid #eee;">
    &copy; <?php echo date('Y'); ?> <strong>OMM-App</strong> &mdash; Toko Bangunan Our Muda Maju. All rights reserved.
</footer>

<!-- Vendor JS -->
<?php foreach ($vendor_js as $name => $url): ?>
<script src="<?= $url ?>"></script><!-- <?= $name ?> -->
<?php endforeach; ?>

<!-- App JS -->
<script src="assets/js/main.js"></script>

<!-- Notifikasi otomatis dari query string -->
<?php if (isset($_GET['success'])): ?>
<script>notif('success','<?php echo htmlspecialchars($_GET["success"]); ?>');</script>
<?php endif; ?>
<?php if (isset($_GET['error'])): ?>
<script>notif('error','<?php echo htmlspecialchars($_GET["error"]); ?>');</script>
<?php endif; ?>

</body>
</html>
