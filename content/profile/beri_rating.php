<div class="container section-title">
    <h3 class="text-center text-header mt-3">Beri Ulasan Produk</h3>
    <hr style="border: none; height: 5px; background-color: #AB7665; margin: 20px auto; width: 8%;">
</div>
<?php
$q = mysqli_query($conn, "SELECT
                            k.*,
                            p.*,
                            v.*,
                            i.*,
                            i.id AS id_item 

                        FROM
                            item_keranjang AS i
                            INNER JOIN keranjang AS k ON i.keranjang_id = k.id
                            INNER JOIN products AS p ON i.produk_id = p.product_id
                            INNER JOIN vendors AS v ON p.vendor_id = v.vendor_id 
                        WHERE
                            k.user_id= '$user_id' and i.success='ya' and i.selesai='ya'
                        GROUP BY
                            i.id,i.produk_id,
                            i.tanggal_acara ");
?>
<div class="container mt-5 mb-5">
    <div class="row">
        <?php
        // Tampilkan pesan sukses jika ada parameter `success` di URL
        if (isset($_GET['success'])): ?>
            <div class="alert alert-success" role="alert">
                Sudah berhasil memberikan ulasan dan rating pada produk. <br> <br> <b>Terima kasih</b>
            </div>
        <?php endif; ?>

        <?php if (mysqli_num_rows($q) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($q)): ?>
                <?php
                // Query untuk memeriksa apakah produk sudah diulas oleh user
                $product_id = $row['product_id'];
                $keranjang_id = $row['keranjang_id'];
                $user_id = $user_id; // Pastikan $user_id sudah didefinisikan sebelumnya

                $check_review_query = "SELECT * FROM ulasan WHERE id_produk = '$product_id' and id_keranjang='$keranjang_id' AND id_user = '$user_id'";
                // echo $check_review_query;
                $check_review_result = mysqli_query($conn, $check_review_query);
                $is_reviewed = mysqli_num_rows($check_review_result) > 0;
                // echo $is_reviewed;

                // Ambil ulasan dan rating jika sudah diulas
                $ulasan = $is_reviewed ? mysqli_fetch_assoc($check_review_result) : null;
                $rating = $ulasan ? (int) $ulasan['rating'] : 0;  // Rating yang dikirim melalui form
                $ulasan_text = $ulasan ? $ulasan['ulasan'] : '';  // Ulasan produk
                ?>

                <div class="col-md-4 col-lg-4 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <!-- Bagian Gambar dan Informasi Produk -->
                            <div class="d-flex align-items-center mb-3">
                                <!-- Gambar Produk -->
                                <img src="./assets/img/product/<?= htmlspecialchars($row['product_photo']) ?>"
                                    style="height: 100px;width: 120px;" alt="<?= htmlspecialchars($row['product_name']) ?>"
                                    class="img-fluid product-img card-img-top" />

                                <div class="ml-3">
                                    <h5 class="product-name"><a class="link"
                                            href="index.php?menu=produk&nama_produk=<?= htmlspecialchars($row['categori']) ?>&produkid=<?= enkrip($row['product_id']) ?>="><?= htmlspecialchars($row['product_name']) ?></a>
                                    </h5>
                                    <p class="vendor-name"><a class="link"
                                            href="index.php?menu=vendor&vendor=<?= enkrip($row['vendor_id']) ?>"><?= htmlspecialchars($row['name']) ?></a>
                                    </p>
                                    <p class="product-price"><?= rupiah($row['subtotal']) ?></p>
                                </div>
                            </div>

                            <!-- Bagian Ulasan dan Rating -->
                            <div class="text-center mb-3">
                                <?php if (!$is_reviewed): ?>
                                    <p class="text-warning">Kamu belum memberikan rating</p>
                                <?php else: ?>
                                    <p class="text-success">Produk sudah diulas</p>

                                    <!-- Tampilkan Rating dengan Bintang -->
                                    <div class="rating">
                                        <?php
                                        $max_rating = 5;
                                        for ($i = 1; $i <= $max_rating; $i++) {
                                            if ($i <= $rating) {
                                                echo "<span class='star filled'>★</span>"; // Bintang penuh
                                            } else {
                                                echo "<span class='star empty'>☆</span>"; // Bintang kosong
                                            }
                                        }
                                        ?>
                                    </div>

                                    <!-- Tampilkan Ulasan Jika Ada -->
                                    <?php if ($ulasan_text): ?>
                                        <p class="review-text"><?= nl2br(htmlspecialchars($ulasan_text)) ?></p>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>

                            <!-- Link untuk memberikan ulasan -->
                            <div class="text-center">
                                <?php if (!$is_reviewed): ?>
                                    <a href="index.php?menu=profile&act=submit_ulasan&id=<?= enkrip($row['id']) ?>"
                                        class="btn btn-primary btn-block">Beri Ulasan</a>
                                <?php else: ?>
                                    <span class="text-success">Terima kasih atas ulasan Anda!</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-center w-100">Keranjang Anda kosong.</p>
        <?php endif; ?>
    </div>
</div>

<style>
    /* Gaya Umum untuk Gambar Produk */
    .product-img {
        width: 100px;
        height: auto;
        border-radius: 8px;
        margin-right: 20px;
    }

    /* Nama Produk dan Vendor */
    .product-name {
        font-size: 1.2rem;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .vendor-name {
        font-size: 1rem;
        color: #555;
    }

    .product-price {
        font-size: 1.1rem;
        font-weight: bold;
        color: #333;
        margin-top: 5px;
    }

    /* Bintang Rating */
    .rating {
        font-size: 1.5rem;
        margin-top: 10px;
    }

    .star {
        color: #FFD700;
        /* Warna Bintang */
        font-size: 1.6rem;
        margin: 0 3px;
    }

    .star.filled {
        color: #FFD700;
        /* Bintang Penuh */
    }

    .star.empty {
        color: #ddd;
        /* Bintang Kosong */
    }

    /* Bagian yang Menampilkan Ulasan */
    .text-warning {
        color: #f39c12;
    }

    .text-success {
        color: #28a745;
    }

    .text-center {
        text-align: center;
    }

    .mt-2 {
        margin-top: 10px;
    }

    .mt-3 {
        margin-top: 20px;
    }

    /* Ulasan Teks */
    .review-text {
        font-style: italic;
        color: #555;
        margin-top: 10px;
        line-height: 1.5;
    }

    /* Tombol Ulasan */
    .btn-primary {
        background-color: #007bff;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 4px;
        text-decoration: none;
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }

    .shadow-sm {
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
</style>