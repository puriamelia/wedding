<?php
$addwhere = "";
if (isset($_GET['filter'])) {
    $nama_pencarian = mysqli_escape_string($conn, $_GET['filter']);
    $addwhere = " WHERE p.product_name like '%$nama_pencarian%'";
}
?>
<section id="rekomendasi" class="team section mb-5">
    <!-- Section Title -->
    <div class="container section-title">
        <h2 class="text-center text-header mt-3">Semua Produk</h2>
        <hr style="border: none; height: 5px; background-color: #AB7665; margin: 20px auto; width: 8%;">
    </div><!-- End Section Title -->

    <div class="container">
        <div class="col-12">
            <form action="index.php" method="GET">
                <input type="hidden" name="menu" value='semua-produk'>
                <input type="text" name="filter" value="<?= $nama_pencarian ? $nama_pencarian : '' ?>"
                    placeholder="Cari sesuatu..." class="search-input" required>
                <button type="submit" class="search-btn">Cari</button>
            </form>
        </div>
        <hr>

        <div class="row" style="display: flex; flex-wrap: wrap; gap: 50px;">
            <?php

            // Pastikan koneksi database sudah terhubung
            // $conn harus sudah didefinisikan sebelumnya

            // Query untuk mengambil semua produk dengan detail vendor
            $q_produk = mysqli_query($conn, "SELECT 
                                                p.product_id, 
                                                p.vendor_id, 
                                                p.product_name, 
                                                p.product_photo, 
                                                p.description, 
                                                p.price, 
                                                p.stock, 
                                                p.total_viewer, 
                                                p.created_at, 
                                                p.updated_at, 
                                                v.`name` AS vendor_name, 
                                                v.contact_number, 
                                                v.email, 
                                                v.website 
                                            FROM
                                                products AS p
                                            INNER JOIN 
                                                vendors AS v 
                                            ON 
                                                p.vendor_id = v.vendor_id 
                                                $addwhere
                                            ORDER BY
                                                p.product_id DESC");
            if (mysqli_num_rows($q_produk) > 0) {


                while ($produk = mysqli_fetch_assoc($q_produk)) {
            ?>
                    <div class="col-lg-2 col-md-4 col-sm-6 d-flex">
                        <div class="team-member text-center w-100">
                            <!-- Link ke Detail Produk -->
                            <a href="index.php?menu=produk&nama_produk=<?= htmlspecialchars($produk['product_name']) ?>&produkid=<?= enkrip($produk['product_id']) ?>"
                                class="link text-decoration-none">
                                <img src="assets/img/product/<?= htmlspecialchars($produk['product_photo']) ?>"
                                    class="img-fluid" alt="<?= htmlspecialchars($produk['product_name']) ?>"
                                    style="height: 150px; width: 100%; object-fit: cover; border-radius: 3px;">
                                <div class="member-info mt-2">
                                    <h5 class="mb-1" style="color: #AB7665;"><?= htmlspecialchars($produk['product_name']) ?>
                                    </h5>
                                </div>
                            </a>
                            <!-- Link ke Detail Vendor -->
                            <div class="vendor-info mt-2">
                                <small>
                                    by
                                    <a href="index.php?menu=vendor&vendor=<?= enkrip($produk['vendor_id']) ?>"
                                        class="vendor-link" style="color: #6E4D44; text-decoration: none;">
                                        <?= htmlspecialchars($produk['vendor_name']) ?>
                                    </a>
                                </small>
                            </div>
                        </div>
                    </div>
            <?php
                }
            } else {
                echo "Produk yang kamu cari tidak tersedia";
            }
            ?>
            <!-- Menggunakan Flexbox dengan gap -->
        </div>

        <!-- Tombol Lihat Semua Produk (Opsional jika Anda ingin tetap ada) -->
        <!-- 
        <div class="text-center mt-4">
            <a href="index.php?menu=semua-produk" class="btn btn-primary rounded-pill px-4 py-2" 
               style="background-color: #AB7665; border: none; color: white; font-size: 16px; text-transform: uppercase; letter-spacing: 1px; transition: background-color 0.3s;">
                Lihat Semua Produk
            </a>
        </div>
        -->
    </div>
</section>