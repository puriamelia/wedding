<section id="mua" class="header-profile" style="background-color: #FFF3EF; padding: 60px 0;">
    <div class="container">
        <div class="row align-items-center justify-content-center">
            <!-- Gambar Bulat di Kiri -->
            <div class="col-md-3 text-center text-md-start mb-4 mb-md-0">
                <img class="rounded-circle shadow-lg" src="assets/img/profilebulat.png" alt="Profile Picture"
                    width="220" height="220" style="border: 6px solid #AB7665; transition: transform 0.3s ease;">
            </div>

            <!-- Teks di Kanan -->
            <div class="col-md-7">
                <h4 class="fw-bold mb-3"
                    style="color: #AB7665; font-size: 30px; text-transform: uppercase; letter-spacing: 1px;">
                    Apa itu Makeup Artist (MUA)?
                </h4>
                <p class="lead" style="text-align: justify; color: #6E4D44; font-size: 17px; line-height: 1.8;">
                    Makeup Artist (MUA) adalah seorang profesional yang ahli dalam mengaplikasikan kosmetik dan produk
                    kecantikan untuk meningkatkan penampilan seseorang.
                    Mereka bekerja di berbagai acara seperti pernikahan, pemotretan, acara televisi, dan film. Dengan
                    kreativitas dan keterampilannya,
                    seorang MUA mampu menciptakan berbagai gaya yang memukau, mulai dari tampilan natural yang sederhana
                    hingga gaya glamor yang mewah.
                </p>
                <div class="d-flex align-items-center mt-4">
                </div>
            </div>
        </div>
</section>



<section id="kategori" class="team section mb-5">
    <!-- Section Title -->
    <div class="container section-title">
        <h2 class="text-center text-header mt-3">Makeup by Categories</h2>
        <hr style="border: none; height: 5px; background-color: #AB7665; margin: 20px auto; width: 8%;">
    </div><!-- End Section Title -->

    <div class="container">
        <div class="row" style="display: flex; flex-wrap: wrap; gap: 0px;">
            <?php
            $q_kategori = mysqli_query($conn, "SELECT
                                                        category_id, 
                                                        category_name, 
                                                        category_photo, 
                                                        created_at, 
                                                        updated_at
                                                    FROM
                                                        categories ");
            while ($kat = mysqli_fetch_assoc($q_kategori)) {
            ?>
                <div class="col-lg-2 col-md-4 col-sm-6 d-flex">
                    <div class="team-member text-center w-100">
                        <a href="index.php?menu=kategori&kategori=<?= $kat['category_name'] ?>" class="link">
                            <img src="assets/img/kategori/<?= $kat['category_photo'] ?>" class="img-fluid"
                                style="height: 150px; width: 100%; object-fit: cover; border-radius: 3px;"
                                alt="<?= $kat['category_name'] ?>">
                            <div class="member-info">
                                <p href="#"><?= $kat['category_name'] ?></p>
                            </div>
                        </a>
                    </div>
                </div>
            <?php
            }
            ?>
            <!-- Using Flexbox with gap -->


        </div>
    </div>
</section>
<?php

$query = mysqli_query($conn, "SELECT 
                                p.product_id,
                                p.product_name,
                                p.product_photo,
                                p.description,
                                p.price,
                                v.name AS vendor_name,
                                v.vendor_id
                              FROM products AS p
                              INNER JOIN vendors AS v ON p.vendor_id = v.vendor_id
                              WHERE p.banner = 'ya' LIMIT 1");

$bannerProduct = mysqli_fetch_assoc($query);
?>
<section id="deal" class="cta-section py-5 mb-5" style="background-color: #FFEDF5;">
    <div class="container">
        <div class="row align-items-center g-5">
            <!-- Text on the Left -->
            <div class="col-md-6 text-md-end text-center">
                <h2 class="cta-title"
                    style="color: #C94F84; font-weight: 400; font-family: 'Italianno', cursive; font-size: 5rem;">
                    Promo Minggu Ini!
                </h2>
                <p class="mt-3 fs-4" style="color: #6C2C4F;">
                    Nikmati penawaran spesial untuk <strong><?= $bannerProduct['product_name']; ?></strong>.
                    Jangan lewatkan kesempatan ini untuk mendapatkan produk favorit Anda!
                </p>
                <a href="index.php?menu=produk&nama_produk=<?= ($bannerProduct['product_name']) ?>&produkid=<?= enkrip($bannerProduct['product_id']) ?>"
                    class="btn btn-lg btn-outline-pink text-uppercase fw-bold mt-3">
                    <i class="fa fa-calendar-o me-2"></i> Lihat Produk
                </a>
            </div>

            <!-- Circular Photo on the Right -->
            <div class="col-md-6 text-center">
                <div class="position-relative">
                    <img src="assets/img/product/<?= $bannerProduct['product_photo']; ?>"
                        alt="<?= $bannerProduct['product_name']; ?>" class="rounded-circle shadow-lg"
                        style="width: 300px; height: 300px; object-fit: cover;">
                    <div
                        class="position-absolute bottom-0 start-50 translate-middle-x bg-white text-center rounded-pill px-4 py-2 shadow-sm">
                        <span style="color: #C94F84; font-weight: bold;">
                            <a style="text-decoration: none;" class="link"
                                href="index.php?menu=vendor&vendor=<?= enkrip($bannerProduct['vendor_id']) ?>">
                                dari <?= $bannerProduct['vendor_name']; ?>
                            </a>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<section id="rekomendasi" class="team section mb-5">
    <!-- Section Title -->
    <div class="container section-title">
        <h2 class="text-center text-header mt-3">Rekomendasi</h2>
        <hr style="border: none; height: 5px; background-color: #AB7665; margin: 20px auto; width: 8%;">
    </div><!-- End Section Title -->

    <div class="container">
        <div class="row" style="display: flex; flex-wrap: wrap; gap: 50px;">
            <?php
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
                                            ORDER BY        
                                                p.total_viewer DESC limit 10");
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
            ?>
            <!-- Using Flexbox with gap -->




        </div>
    </div>
    <div class="text-center mt-4">
        <a href="index.php?menu=semua-produk" class="btn btn-primary rounded-pill px-4 py-2"
            style="background-color: #AB7665; border: none; color: white; font-size: 16px; text-transform: uppercase; letter-spacing: 1px; transition: background-color 0.3s;">
            Lihat Semua Produk
        </a>
    </div>

</section>