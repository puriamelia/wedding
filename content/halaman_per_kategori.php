<?php
if (isset($_GET['kategori'])) {
} else {
    pindah_halaman("index.php");
}
$nama_kategori = mysqli_escape_string($conn, $_GET['kategori']);
$q = mysqli_query($conn, "SELECT
                            category_id, category_name, category_photo, created_at, updated_at, category_descripsion  
                            FROM
                            categories AS c
                            WHERE category_name='$nama_kategori'
                            ");
$kat = mysqli_fetch_assoc($q);

?>

<section id='mua' class="header-profile" style="background-color: #FFF3EF;padding: 50px;">
    <div class="container ">
        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6">
                <img src="assets/img/kategori/<?= $kat['category_photo'] ?>" class='img-fluid'
                    style="max-height: 200px; width: 100%; object-fit: cover; border-radius: 3px;"
                    alt="<?= $kat['category_name'] ?>">

            </div>
            <div class="col-lg-9 col-md-6 col-sm-6">
                <!-- <h1><?= $kat['category_name'] ?></h1> -->
                <h1 style="color: #AB7665; font-size: 30px;">Apa itu <?= $kat['category_name'] ?> ?</h1>
                <p class="paragraph"><?= $kat['category_descripsion'] ?></p>
                <!-- <button class=" tombol"><i class="fa fa-map"></i> Lokasi</button> -->
                <!-- <button class="tombol"><i class="fa fa-calendar"></i> Jadwal</button> -->

            </div>
        </div>
    </div>

</section>
<div class="container section-title">
    <h2 class="text-center text-header mt-3">Produk <?= $kat['category_name'] ?></h2>
    <hr style="border: none; height: 5px; background-color: #AB7665; margin: 20px auto; width: 8%;">
</div>
<div class="container mt-5 mb-5">
    <div class="row">
        <?php
        $q_pro = mysqli_query($conn, "SELECT
                                        p.product_id,
                                        p.product_name,
                                        p.product_photo,
                                        p.description,
                                        p.categori,
                                        p.price,
                                        p.stock,
                                        p.total_viewer,
                                        p.created_at,
                                        p.updated_at,
                                        p.rating,
                                        ven.vendor_id,
                                        ven.`name`,
                                        ven.description as ven_description ,
                                        ven.contact_number,
                                        ven.email 
                                    FROM
                                        products AS p
                                        INNER JOIN vendors AS ven ON p.vendor_id = ven.vendor_id
                                        WHERE p.categori='$nama_kategori'
                                        ");
        if (mysqli_num_rows($q_pro) > 0) {
            while ($produk = mysqli_fetch_assoc($q_pro)) {
        ?>
                <div class="col-md-3">
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
                            <p>Dari <a href="index.php?menu=vendor&vendor=<?= enkrip($produk['vendor_id']) ?>">
                                    <?= $produk['name'] ?></a></p>
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