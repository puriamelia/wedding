<?php
$sql = "SELECT
	k.user_id,
	k.id AS keranjang_id,
	k.`status`,
	k.`date_created`,
	SUM( ik.jumlah ) AS jumlah,
	ik.harga,
	SUM( ik.subtotal ) AS total_harga,
	ik.checkout,
	ik.success,
	ik.selesai,
	ik.produk_id,
	products.product_name,
	products.product_photo,
	konfirmasi_pembayaran.status_pembayaran ,
	konfirmasi_pembayaran.lunas
FROM
	keranjang AS k
	INNER JOIN item_keranjang AS ik ON k.id = ik.keranjang_id
	INNER JOIN products ON ik.produk_id = products.product_id
	LEFT JOIN konfirmasi_pembayaran ON k.id = konfirmasi_pembayaran.id_keranjang 
WHERE
	k.user_id = '$user_id' 
GROUP BY
	k.id
ORDER BY k.id desc
    ";

$riwayatPesanan = mysqli_query($conn, $sql);
?>
<div class="container section-title">
    <h3 class="text-center text-header mt-3">Riwayat Pesanan</h3>
    <hr style="border: none; height: 5px; background-color: #AB7665; margin: 20px auto; width: 8%;">
    <p class="text-center text-muted">Lihat riwayat pesanan Anda, termasuk detail status dan total biaya.</p>
</div>

<div class="container mt-5">
    <div class="row">
        <?php if (mysqli_num_rows($riwayatPesanan) > 0): ?>
            <!-- Loop untuk setiap item -->
            <?php foreach ($riwayatPesanan as $pesanan): ?>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-lg border-0">
                        <img src="assets/img/product/<?= $pesanan['product_photo']; ?>" class="card-img-top"
                            alt="<?= $pesanan['product_name']; ?>"
                            style="height: 200px; object-fit: cover; border-bottom: 4px solid #AB7665;">
                        <div class="card-body">
                            <h5 class="card-title text-truncate"><?= $pesanan['product_name']; ?></h5>
                            <p class="card-text">
                                <strong>Jumlah:</strong> <?= $pesanan['jumlah']; ?><br>
                                <strong>Harga Satuan:</strong> <?= rupiah($pesanan['harga']); ?><br>
                                <strong>Total Harga:</strong> <?= rupiah($pesanan['total_harga']); ?><br>
                                <strong>Lunas:</strong>
                                <span class="badge <?= $pesanan['lunas'] === 'ya' ? 'bg-success' : 'bg-warning'; ?>">
                                    <?= $pesanan['lunas'] === 'ya' ? 'LUNAS' : 'Belum Lunas'; ?>
                                </span> <br>
                                <strong>Status Pembayaran:</strong>
                                <span
                                    class="badge <?= $pesanan['status_pembayaran'] === 'approved' ? 'bg-success' : 'bg-warning'; ?>">
                                    <?= ucfirst($pesanan['status_pembayaran']); ?>
                                </span> <br>
                                <small><?= $pesanan['date_created'] ?></small>
                            </p>
                            <a href="index.php?menu=profile&act=detail_pesanan&id=<?= $pesanan['keranjang_id']; ?>"
                                class="btn btn-primary w-100">
                                Detail Pesanan
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <!-- Akhir Loop -->
        <?php else: ?>
            <div class="col-12">
                <p class="text-center text-muted">Tidak ada riwayat pesanan yang ditemukan.</p>
            </div>
        <?php endif; ?>
    </div>
</div>