<?php

// error_reporting(0);
session_start();
// session_destroy();
include_once "./koneksi/db.php";
include_once "./function/global.php";

if (!isset($_SESSION['chart_kode'])) {
    $kodeUnik = uniqid('notlogin-', true);
    $_SESSION['chart_kode'] = $kodeUnik;
}
if (isset($_SESSION['user_id'])) {
    $_SESSION['chart_kode'] = $_SESSION['user_id'];
}

$kode = $_SESSION['chart_kode'];
$hitung = mysqli_query($conn, "SELECT
                                    count(*) AS total_keranjang 
                                FROM
                                    keranjang
                                    INNER JOIN item_keranjang ON keranjang.id = item_keranjang.keranjang_id
                                    where (user_id='$kode' or session_id='$kode') and keranjang.status is null");
$hitung = mysqli_fetch_assoc($hitung);
$hitung_keranjang = $hitung['total_keranjang'];


include_once("./layout/page_start.php");
include_once("./layout/page_header.php");


?>



<?php
if (isset($_GET['menu'])) {
    $menu = mysqli_escape_string($conn, $_GET['menu']);
    if ($menu == 'kategori') {
        include_once("./content/halaman_per_kategori.php");
    } else if ($menu == 'vendor') {
        include_once("./content/halaman_per_vendor.php");
    } else if ($menu == 'produk') {
        include_once("./content/halaman_produk.php");
    } else if ($menu == 'keranjang') {
        include_once("./content/halaman_keranjang.php");
    } else if ($menu == 'pembayaran') {
        include_once("./content/halaman_pembayaran.php");
    } else if ($menu == 'konfirmasi_pembayaran') {
        include_once("./content/halaman_konfirmasi_pembayaran.php");
    } else if ($menu == 'profile') {
        include_once("./content/halaman_profile.php");
    } else if ($menu == 'semua-produk') {
        include_once("./content/halaman_semua_produk.php");
    } else {
        include_once("./layout/banner.php");
        include_once("./content/halaman-awal.php");
    }
} else {

    include_once("./layout/banner.php");
    include_once("./content/halaman-awal.php");
}

if (!isset($_GET['menu'])) {
?>
    <div class="floating-nav">
        <a href="index.php#" class="nav-item" data-tooltip="Home">
            <i class="fa fa-home"></i>
        </a>
        <a href="index.php#mua" class="nav-item" data-tooltip="Apa itu MUA?">
            <i class="fa fa-info-circle"></i>
        </a>
        <a href="index.php#kategori" class="nav-item" data-tooltip="Kategori">
            <i class="fa fa-money"></i>
        </a>
        <a href="index.php#rekomendasi" class="nav-item" data-tooltip="Rekomendasi">
            <i class="fa fa-heart"></i>
        </a>
        <!-- <a href="index.php#deal" class="nav-item" data-tooltip="Promo">
        <i class="fa fa-money"></i>
    </a> -->
        <a href="index.php#footer" class="nav-item" data-tooltip="Contact">
            <i class="fa fa-envelope"></i>
        </a>
    </div>
<?php
}
?>


<div class="offcanvas offcanvas-start" tabindex="-1" id="shoppingCart" aria-labelledby="shoppingCartLabel">
    <div class="offcanvas-header">
        <i class="fa fa-shopping-cart fa-lg"></i> &nbsp;
        <h5 class="offcanvas-title" id="shoppingCartLabel">Daftar Keranjang</h5>
        <hr>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <?php
        
        $sql = mysqli_query($conn, "SELECT
                                    d.keranjang_id,
                                    d.produk_id,
                                    count(d.jumlah) as jumlah,
                                    d.harga,
                                    sum(d.subtotal) as subtotal,
                                    v.`name` AS nama_vendor,
                                    p.product_name 
                                FROM
                                    keranjang AS k
                                    INNER JOIN item_keranjang AS d ON k.id = d.keranjang_id
                                    INNER JOIN products AS p ON d.produk_id = p.product_id
                                    INNER JOIN vendors AS v ON p.vendor_id = v.vendor_id
                                    where (k.user_id='$kode' or k.session_id='$kode') and k.status is null group by produk_id");
        $total_semua = 0;
        while ($cart = mysqli_fetch_array($sql)) {
            $sub = $cart['subtotal'];
            $total_semua = $sub + $total_semua;
        ?>
            <div class="cart-item d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h6><?= $cart['product_name'] ?></h6>
                    <small>dari <?= $cart['nama_vendor'] ?></small>
                    <p> <?= rupiah($sub) ?></p>
                </div>
                <input type="number" class="form-control w-25" readonly value="<?= $cart['jumlah'] ?>" min="1">
            </div>
        <?php
        }
        ?>


        <hr>

        <!-- Total Price -->
        <div class="d-flex justify-content-between">
            <h6>Total:</h6>
            <h6><?= rupiah($total_semua) ?></h6>
        </div>

        <!-- Checkout Button -->
        <div class="d-grid gap-2 mt-3">
            <a href='index.php?menu=keranjang' class="btn btn-success">Atur Keranjang</a>
        </div>
    </div>
</div>
<?php include_once("./layout/page_footer.php") ?>
<?php include_once("./layout/page_script.php") ?>