<?php

$vendor_id = dekrip(mysqli_escape_string($conn, $_GET['vendor']));
$q = mysqli_query($conn, "SELECT vendor_id,  `name`,  description,  contact_number,  photo,  website,  email,  `password`,  url_lokasi,  latitude,  logitude,  rating,  created_at,  updated_at FROM
                                vendors
                                where vendor_id='$vendor_id'
                            ");
if (isset($_GET['vendor'])) {
} else {
    pindah_halaman("index.php");
}
if (mysqli_num_rows($q) > 0) {
} else {
    pindah_halaman("index.php");
}
$vendor = mysqli_fetch_assoc($q);

?>

<section id='mua' class="header-profile" style="background-color: #FFF3EF;padding: 50px;">
    <div class="container">
        <div class="row">
            <div class="col-lg-2 col-md-6 col-sm-12">
                <img src="assets/img/vendor/<?= $vendor['photo'] ?>"
                    style="border-radius: 50%;width: 100px;float: right;" alt="<?= $vendor['category_name'] ?>">
            </div>
            <div class="col-lg-10 col-md-6 col-sm-12">
                <div class="row">
                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <h1 style="color: #AB7665; font-size: 30px;"><?= $vendor['name'] ?> </h1>
                        <p class="rating">Rating: ★★★★☆</p>
                        <a href="<?= $vendor['url_lokasi'] ?>"> <button class="tombol"><i class="fa fa-map"></i>
                                Lokasi</button></a>
                        <button class="tombol"><i class="fa fa-calendar"></i> Jadwal</button>
                    </div>
                    <div class="col-lg-8 col-md-6 col-sm-12">
                        <p class="paragraph"><?= $vendor['description'] ?></p>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<div class="container section-title">
    <h2 class="text-center text-header mt-3">Produk dari <?= $vendor['name'] ?></h2>
    <hr style="border: none; height: 5px; background-color: #AB7665; margin: 20px auto; width: 8%;">
</div>
<div class="container mt-5 mb-5">
    <div class="row">
        <?php
        $q_ven = mysqli_query($conn, "SELECT
                                        product_id,
                                        vendor_id,
                                        product_name,
                                        product_photo,
                                        description,
                                        categori,
                                        price,
                                        stock,
                                        total_viewer,
                                        created_at,
                                        updated_at,
                                        rating 
                                    FROM
                                        products
                                        WHERE vendor_id='$vendor_id'
                                        ");
        if (mysqli_num_rows($q_ven) > 0) {
            while ($produk = mysqli_fetch_assoc($q_ven)) {
        ?>
                <div class="col-md-3 mt-3">
                    <div class="card">
                        <a
                            href="index.php?menu=produk&nama_produk=<?= ($produk['product_name']) ?>&produkid=<?= enkrip($produk['product_id']) ?>">
                            <img src="assets/img/product/<?= $produk['product_photo'] ?>" class="card-img-top"
                                alt="<?= $produk['product_name'] ?>">
                        </a>
                        <div class="card-body">
                            <a href="index.php?menu=produk&nama_produk=<?= ($produk['product_name']) ?>&produkid=<?= enkrip($produk['product_id']) ?>"
                                class='link'>
                                <h6 class="card-title"><?= $produk['product_name'] ?></h6>
                            </a>
                            <p class="card-text"><?= rupiah($produk['price']) ?></p>
                        </div>
                    </div>
                </div>
            <?php
            }
        } else {
            ?>
            <h3 class="text-center fst-italic text-header mt-3">Belum ada produk dari kategori ini</h3>
        <?php
        }

        ?>

    </div>
</div>