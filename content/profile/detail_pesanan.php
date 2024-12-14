<?php
// Ambil ID Pesanan dari URL
$item_id = $_GET['id'] ?? null;

// Query untuk mendapatkan detail pesanan
$sql = "SELECT
            products.product_name AS nama_produk,
            products.categori AS kategori,
            products.product_photo AS foto_produk,
            vendors.NAME AS nama_vendor,
            item_keranjang.subtotal,
            item_keranjang.tanggal_acara,
            item_keranjang.jam_acara,
            konfirmasi_pembayaran.alamat AS alamat_acara,
            konfirmasi_pembayaran.status_pembayaran,
            users.nama AS nama_pemesan,
            users.no_hp AS no_hp_pemesan,
            item_keranjang.selesai AS status_acara,
            item_keranjang.id AS id_item,
            products.product_id, item_keranjang.keranjang_id,
            konfirmasi_pembayaran.lunas
        FROM
            item_keranjang
            INNER JOIN products ON item_keranjang.produk_id = products.product_id
            INNER JOIN vendors ON products.vendor_id = vendors.vendor_id
            INNER JOIN keranjang ON item_keranjang.keranjang_id = keranjang.id
            INNER JOIN users ON keranjang.user_id = users.user_id
            LEFT JOIN konfirmasi_pembayaran ON keranjang.id = konfirmasi_pembayaran.id_keranjang 
        WHERE
            item_keranjang.keranjang_id = '$item_id' 
        ORDER BY
            item_keranjang.id DESC";

$detailPesanan = mysqli_query($conn, $sql);

// Redirect jika tidak ada data pesanan
if (mysqli_num_rows($detailPesanan) === 0) {
    header("Location: index.php?menu=profile&act=riwayat_pesanan");
    exit();
}

// Fungsi untuk menandai acara selesai
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_item = $_POST['id_item'];
    $update_sql = "UPDATE item_keranjang SET selesai = 'ya' WHERE id = '$id_item'";
    mysqli_query($conn, $update_sql);
    pindah_halaman("index.php?menu=profile&act=detail_pesanan&id=$item_id");
}
?>

<div class="container mt-5">
    <h3 class="text-center text-header mt-3">Detail Pesanan Anda</h3>
    <hr style="border: none; height: 5px; background-color: #AB7665; margin: 20px auto; width: 8%;">
    <p class="text-center text-muted mb-4">Detail lengkap produk yang Anda pesan</p>

    <div class="row g-3">
        <?php while ($pesanan = mysqli_fetch_assoc($detailPesanan)): ?>
            <div class="col-sm-4 col-md-4 col-lg-3 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <img src="assets/img/product/<?= $pesanan['foto_produk']; ?>" class="card-img-top"
                        alt="<?= $pesanan['nama_produk']; ?>" style="height: 200px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title text-truncate"><?= $pesanan['nama_produk']; ?></h5>
                        <div class="mb-2">
                            <strong>Kategori:</strong> <?= $pesanan['kategori']; ?><br>
                            <strong>Vendor:</strong> <?= $pesanan['nama_vendor']; ?>
                        </div>
                        <div class="mb-2">
                            <strong>Jam Acara:</strong> <?= ($pesanan['jam_acara']); ?><br>
                            <strong>Tanggal Acara:</strong> <?= formatTanggal($pesanan['tanggal_acara']); ?><br>
                            <strong>Alamat Acara:</strong> <?= $pesanan['alamat_acara']; ?>
                        </div>
                        <div class="mb-2">
                            <strong>Status Pembayaran:</strong>
                            <span class="badge <?= $pesanan['lunas'] === 'ya' ? 'bg-success' : 'bg-danger'; ?>">
                                <?= $pesanan['lunas'] === 'ya' ? 'Lunas' : 'Belum Lunas'; ?>
                            </span>
                        </div>
                        <div class="mb-2">
                            <strong>Status Acara:</strong>
                            <span class="badge <?= $pesanan['status_acara'] === 'ya' ? 'bg-success' : 'bg-warning'; ?>">
                                <?= ucfirst($pesanan['status_acara'] === 'ya' ? 'Selesai' : 'Belum'); ?>
                            </span>
                        </div>
                        <div class="mb-2">
                            <strong>Subtotal:</strong> <?= rupiah($pesanan['subtotal']); ?>
                        </div>

                        <!-- Tombol Tandai Selesai -->
                        <?php if ($pesanan['status_acara'] !== 'ya' && $pesanan['lunas'] == 'ya'): ?>
                            <form method="POST">
                                <input type="hidden" name="id_item" value="<?= $pesanan['id_item']; ?>">
                                <button onclick="return confirm('Apakah anda yakin akan menandai acara ini telah selesai?')"
                                    type="submit" class="btn btn-success w-100 mt-2">Tandai Selesai</button>
                            </form>
                        <?php endif; ?>

                        <!-- Tombol untuk Berikan Ulasan -->
                        <?php
                        $check_review_query = "SELECT * FROM ulasan WHERE id_produk = '{$pesanan['product_id']}' AND id_keranjang='{$pesanan['keranjang_id']}' AND id_user = '{$user_id}'";
                        $check_review_result = mysqli_query($conn, $check_review_query);
                        $is_reviewed = mysqli_num_rows($check_review_result) > 0;
                        ?>
                        <div class="text-center mt-2">
                            <?php if ($pesanan['status_acara'] == 'ya' && !$is_reviewed): ?>
                                <a href="index.php?menu=profile&act=submit_ulasan&id=<?= enkrip($pesanan['id_item']) ?>"
                                    class="btn btn-primary btn-sm w-100">Beri Ulasan</a>
                            <?php elseif ($pesanan['status_acara'] == 'ya'): ?>
                                <span class="text-success">Pesanan sudah diulas</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>