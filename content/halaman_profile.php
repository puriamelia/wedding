<?php
if (!isset($_SESSION['user_id'])) {
    pindah_halaman("index.php");
}

$user_id = $_SESSION['user_id']; // Ganti dengan mekanisme login Anda
$query = "SELECT * FROM users WHERE user_id = '$user_id'";
$result = mysqli_query($conn, $query);

if ($result) {
    $user = mysqli_fetch_assoc($result);
} else {
    echo "Gagal mengambil data pengguna: " . mysqli_error($conn);
}
?>
<section id="profile-banner" class="header-profile"
    style="background:url('assets/img/banner_profile.png'); background-size: cover;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-2 col-md-6 col-sm-6 text-center">
                <img src="assets/img/user-circle-fill.png" class="img-fluid rounded-circle"
                    style="max-height: 200px; width: 200px; object-fit: cover;" alt="<?= $user['nama'] ?>">
            </div>
            <div class="col-lg-10 col-md-6 col-sm-6">
                <h1 style="color: #AB7665; font-size: 30px;">Halo, <?= $user['nama'] ?>!</h1>
                <p style="font-size: 16px; color: #333;">Email: <?= $user['email'] ?></p>
                <p class="paragraph">Selamat datang di profil Anda! Ubah informasi Anda dengan mudah di halaman
                    pengaturan.</p>
                <a href="index.php?menu=profile&act=edit_profile" class="tombol ">Edit Profil</a>
            </div>
        </div>
    </div>
</section>

<div class="container section-title">
    <h2 class="text-center text-header mt-3">Menu</h2>
    <hr style="border: none; height: 5px; background-color: #AB7665; margin: 20px auto; width: 8%;">
</div>
<div class="container mt-5 mb-5">
    <div class="row text-center">
        <!-- Keranjang -->
        <div class="col-md-4">
            <a href="index.php?menu=profile&act=keranjang">
                <div>

                    <i class="fa fa-shopping-cart" style="font-size: 60px; color: #3498db;"></i>
                </div>
                <h4 class="mt-3">Keranjang</h4>
            </a>
        </div>

        <!-- Pembayaran -->
        <div class="col-md-4">
            <a href="index.php?menu=profile&act=riwayat_pembayaran">
                <div>

                    <i class="fa fa-credit-card" style="font-size: 60px; color: #2ecc71;"></i>
                </div>
                <h4 class="mt-3">Riwayat Pembayaran</h4>
            </a>
        </div>

        <!-- Beri Rating -->
        <div class="col-md-4">
            <a href="index.php?menu=profile&act=beri_rating">
                <div>
                    <i class="fa fa-star" style="font-size: 60px; color: #f1c40f;"></i>
                </div>
                <h4 class="mt-3">Beri Rating</h4>
            </a>
        </div>
    </div>
</div>


<?php
if (isset($_GET['act'])) {
    $act = $_GET['act'];
    if ($act == 'riwayat_pembayaran') {
        include("./content/profile/riwayat_pembayaran.php");
    } else if ($act == 'beri_rating') {
        include("./content/profile/beri_rating.php");
    } else if ($act == 'edit_profile') {
        include("./content/profile/edit_profile.php");
    } else if ($act == 'submit_ulasan') {
        include("./content/submit_ulasan.php");
    } else if ($act == 'keranjang') {
        include("./content/halaman_keranjang.php");
    } else if ($act == 'invoice') {
        include("./content/profile/invoice.php");
    }
} else {
    include("./content/halaman_keranjang.php");
}
?>