<?php

$id = dekrip(mysqli_escape_string($conn, $_GET['produkid']));

$q = mysqli_query($conn, "SELECT
                            p.product_id,
                            p.vendor_id,
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
                            v.`name`,
                            v.description as dev_vendor,
                            v.contact_number,
                            v.photo,
                            v.email,
                            v.website,
                            v.`password`,
                            v.url_lokasi,
                            v.latitude,
                            v.logitude,
                            v.rating as rating_vendor 
                        FROM
                            products AS p
                            INNER JOIN vendors AS v ON p.vendor_id = v.vendor_id
                        WHERE p.product_id='$id'
                            ");
if (isset($_GET['produkid'])) {
    // Query untuk update total_viewer, menambah 1
    $sql_update = "UPDATE products SET total_viewer = total_viewer + 1 WHERE product_id = $id";

    // Eksekusi query
    if (mysqli_query($conn, $sql_update)) {
        // echo "Viewer berhasil diperbarui!";
    } else {
        // echo "Gagal memperbarui viewer: " . mysqli_error($conn);
    }
} else {
    pindah_halaman("index.php");
}

$produk = mysqli_fetch_assoc($q);
$rating = (int) $produk['rating']; // Misalnya rating yang dikirim melalui form

// Validasi rating untuk memastikan tidak lebih dari 5
if ($rating > 5) {
    $rating = 5; // Set ke 5 jika lebih dari 5
} elseif ($rating < 1) {
    $rating = 1; // Set ke 1 jika rating kurang dari 1
}
$ratin_vendor = (int) $produk['rating_vendor']; // Misalnya ratin_vendor yang dikirim melalui form

// Validasi ratin_vendor untuk memastikan tidak lebih dari 5
if ($ratin_vendor > 5) {
    $ratin_vendor = 5; // Set ke 5 jika lebih dari 5
} elseif ($ratin_vendor < 1) {
    $ratin_vendor = 1; // Set ke 1 jika rating kurang dari 1
}



?>

<section id='mua' class="header-profile" style="background-color: #FFF3EF;padding: 50px;">
    <div class="container">
        <div class="row">
            <div class="col-lg-2 col-md-6 col-sm-12">
                <img src="assets/img/product/<?= $produk['product_photo'] ?>" class="mb-2"
                    style="width: 100%;align-items: center;float: right;" alt="<?= $produk['categori'] ?>">
                <h5 class="text-header">
                    <?= $produk['name'] ?></h5>
                <p class="rating">Rating Vendor: <?php
                                                    $max_rating = 5;
                                                    for ($i = 1; $i <= $max_rating; $i++) {
                                                        if ($i <= $ratin_vendor) {
                                                            echo "★"; // Tampilkan bintang penuh jika rating >= i
                                                        } else {
                                                            echo "☆"; // Tampilkan bintang kosong jika rating < i
                                                        }
                                                    }
                                                    ?></p>
                <a href='index.php?menu=vendor&vendor=<?= enkrip($produk['vendor_id']) ?>' class="link">Kunjungi
                    Toko</a>
            </div>
            <div class="col-lg-10 col-md-6 col-sm-12">
                <div class="row">
                    <div class="col-lg-12 col-md-6 col-sm-12">
                        <h1 style="color: #AB7665; font-size: 30px;"><?= $produk['product_name'] ?> </h1>
                        <p class="rating">Rating Produk: <?php
                                                            $max_rating = 5;
                                                            for ($i = 1; $i <= $max_rating; $i++) {
                                                                if ($i <= $rating) {
                                                                    echo "★"; // Tampilkan bintang penuh jika rating >= i
                                                                } else {
                                                                    echo "☆"; // Tampilkan bintang kosong jika rating < i
                                                                }
                                                            }
                                                            ?>
                        </p>
                        <p class="paragraph"><?= $produk['description'] ?></p>

                        <p class="mt-5">
                        <h4 class="text-header">Harga : <?= rupiah($produk['price']) ?> </h4>

                        </p>
                        <div class="mt-3">

                            <form action="" method="post" id="formPesan">

                                <div class="col-5">
                                    Tanggal
                                    <input type="text" id="flatpickr" name="tanggal" class="form-control" required
                                        placeholder="Cek dan Pilih tanggal"><br>

                                </div>
                                <div class="col-5">
                                    <div id="hasil_cek" class="alert alert-success">
                                        <i class="fa fa-check-circle"></i> Pilihannya tepat! Tanggal ini masih kosong.
                                        Jangan tunda lagi,<strong>Pesan sekarang</strong> dan pastikan tanggal ini
                                        jadi
                                        milik Anda!
                                    </div>
                                </div>
                                <div class="col-5">
                                    <button class="tombol" type="submit" name="tambah"><i
                                            class="fa fa-shopping-cart"></i>
                                        Masukan Keranjang</button>


                                    <button class="tombol" name="pesan_sekarang"><i class="fa fa-calendar"></i> Pesan
                                        Sekarang</button>
                                </div>



                            </form>

                        </div>
                        <?php

                        if (isset($_POST['tambah'])) {
                            $produk_id = dekrip(mysqli_escape_string($conn, $_GET['produkid']));


                            $q = mysqli_query($conn, "SELECT
                                                        p.product_id, p.vendor_id, p.product_name, p.product_photo, p.description, p.categori, p.price, p.stock, p.total_viewer, p.created_at, p.updated_at, p.rating, v.`name`, v.description as dev_vendor, v.contact_number, v.photo, v.email, v.website, v.`password`, v.url_lokasi, v.latitude, v.logitude, v.rating as rating_vendor  FROM
                                                        products AS p
                                                        INNER JOIN vendors AS v ON p.vendor_id = v.vendor_id
                                                    WHERE p.product_id='$id'
                                                        ");
                            $produk = mysqli_fetch_assoc($q);

                            $jumlah = 1;
                            $tgl = mysqli_escape_string($conn, $_POST['tanggal']);
                            $harga = $produk['price'];
                            $subtotal = $jumlah * $harga;
                            $cek_keranjang = mysqli_query($conn, "SELECT * 
                                                                    FROM
                                                                        keranjang
                                                                        where (user_id='$kode' or session_id='$kode') and keranjang.status is null");
                            if (mysqli_num_rows($cek_keranjang) > 0) {
                                $cek_keranjang = mysqli_fetch_assoc($cek_keranjang);
                                $keranjang_id = $cek_keranjang['id'];
                            } else {
                                $sql_insert = "INSERT INTO keranjang (user_id, session_id) VALUES ('$kode', '$kode')";
                                if (mysqli_query($conn, $sql_insert)) {
                                    // echo "Keranjang baru ditambahkan";
                                    $keranjang_id = mysqli_insert_id($conn);
                                } else {
                                    echo "Gagal menambahkan keranjang: " . mysqli_error($conn);
                                }
                            }

                            $sql_item = "SELECT id, jumlah FROM item_keranjang WHERE keranjang_id = '$keranjang_id' AND produk_id = '$produk_id' and tanggal_acara='$tgl'";
                            $result_item = mysqli_query($conn, $sql_item);

                            if (mysqli_num_rows($result_item) > 0) {
                                // Jika item sudah ada, lakukan update
                                $row_item = mysqli_fetch_assoc($result_item);
                                $jumlah_baru = $row_item['jumlah'] + $jumlah; // Tambahkan jumlah baru
                                $subtotal_baru = $jumlah_baru * $harga; // Hitung subtotal baru

                                // Update item di keranjang
                                $sql_update = "UPDATE item_keranjang 
                                               SET jumlah = '$jumlah_baru', subtotal = '$subtotal_baru', date_edited = NOW() 
                                               WHERE id = '{$row_item['id']}'";
                                if (mysqli_query($conn, $sql_update)) {
                                    echo "Produk sudah ditambahkan ke keranjang.";
                                    pindah_halaman("");
                                } else {
                                    // echo "Gagal memperbarui item keranjang: " . mysqli_error($conn);
                                }
                            } else {
                                // Jika item belum ada, lakukan insert
                                $sql_insert = "INSERT INTO item_keranjang (keranjang_id, produk_id, jumlah, harga, subtotal,tanggal_acara) 
                                               VALUES ('$keranjang_id', '$produk_id', '$jumlah', '$harga', '$subtotal','$tgl')";
                                if (mysqli_query($conn, $sql_insert)) {
                                    echo "Produk sudah ditambahkan ke keranjang.";
                                    pindah_halaman("");
                                } else {
                                    echo "Gagal menambahkan item ke keranjang: " . mysqli_error($conn);
                                }
                            }
                        }

                        if (isset($_POST['pesan_sekarang'])) {
                            $produk_id = dekrip(mysqli_escape_string($conn, $_GET['produkid']));


                            $q = mysqli_query($conn, "SELECT
                                                        p.product_id, p.vendor_id, p.product_name, p.product_photo, p.description, p.categori, p.price, p.stock, p.total_viewer, p.created_at, p.updated_at, p.rating, v.`name`, v.description as dev_vendor, v.contact_number, v.photo, v.email, v.website, v.`password`, v.url_lokasi, v.latitude, v.logitude, v.rating as rating_vendor  FROM
                                                        products AS p
                                                        INNER JOIN vendors AS v ON p.vendor_id = v.vendor_id
                                                    WHERE p.product_id='$id'
                                                        ");
                            $produk = mysqli_fetch_assoc($q);

                            $jumlah = 1;
                            $tgl = mysqli_escape_string($conn, $_POST['tanggal']);
                            $harga = $produk['price'];
                            $subtotal = $jumlah * $harga;
                            $cek_keranjang = mysqli_query($conn, "SELECT * 
                                                                    FROM
                                                                        keranjang
                                                                        where (user_id='$kode' or session_id='$kode') and keranjang.status is null");
                            if (mysqli_num_rows($cek_keranjang) > 0) {
                                $cek_keranjang = mysqli_fetch_assoc($cek_keranjang);
                                $keranjang_id = $cek_keranjang['id'];
                            } else {
                                $sql_insert = "INSERT INTO keranjang (user_id, session_id) VALUES ('$kode', '$kode')";
                                if (mysqli_query($conn, $sql_insert)) {
                                    // echo "Keranjang baru ditambahkan";
                                    $keranjang_id = mysqli_insert_id($conn);
                                } else {
                                    echo "Gagal menambahkan keranjang: " . mysqli_error($conn);
                                }
                            }

                            $sql_item = "SELECT id, jumlah FROM item_keranjang WHERE keranjang_id = '$keranjang_id' AND produk_id = '$produk_id' and tanggal_acara='$tgl'";
                            $result_item = mysqli_query($conn, $sql_item);

                            if (mysqli_num_rows($result_item) > 0) {
                                // Jika item sudah ada, lakukan update
                                $row_item = mysqli_fetch_assoc($result_item);
                                $jumlah_baru = $row_item['jumlah'] + $jumlah; // Tambahkan jumlah baru
                                $subtotal_baru = $jumlah_baru * $harga; // Hitung subtotal baru

                                // Update item di keranjang
                                $sql_update = "UPDATE item_keranjang 
                                               SET jumlah = '$jumlah_baru', subtotal = '$subtotal_baru', date_edited = NOW() 
                                               WHERE id = '{$row_item['id']}'";
                                if (mysqli_query($conn, $sql_update)) {
                                    echo "Produk sudah ditambahkan ke keranjang.";
                                    pindah_halaman("");
                                } else {
                                    // echo "Gagal memperbarui item keranjang: " . mysqli_error($conn);
                                }
                            } else {
                                // Jika item belum ada, lakukan insert
                                $sql_insert = "INSERT INTO item_keranjang (keranjang_id, produk_id, jumlah, harga, subtotal,tanggal_acara,checkout) 
                                               VALUES ('$keranjang_id', '$produk_id', '$jumlah', '$harga', '$subtotal','$tgl','ya')";
                                if (mysqli_query($conn, $sql_insert)) {
                                    echo "Produk sudah ditambahkan ke keranjang.";
                                    pindah_halaman("index.php?menu=pembayaran");
                                } else {
                                    echo "Gagal menambahkan item ke keranjang: " . mysqli_error($conn);
                                }
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<div class="container section-title">
    <h2 class="text-center text-header mt-3">Ulasan Produk <?= $produk['product_name'] ?></h2>
    <hr style="border: none; height: 5px; background-color: #AB7665; margin: 20px auto; width: 8%;">
</div>
<div class="container mt-5 mb-5">
    <?php
    // Query untuk mengambil ulasan berdasarkan id_produk
    $query = mysqli_query($conn, "SELECT 
                                    ulasan.ulasan, 
                                    ulasan.rating, 
                                    users.nama AS user_name 
                                FROM 
                                    ulasan 
                                INNER JOIN 
                                    users ON ulasan.id_user = users.user_id
                                WHERE 
                                    ulasan.id_produk = '$id'
                                ORDER BY 
                                    id_ulasan DESC");

    // Mengecek jika ada ulasan
    if (mysqli_num_rows($query) > 0) {
        // Menampilkan setiap ulasan
        while ($row = mysqli_fetch_assoc($query)) {
            $user_name = $row['user_name'];
            $ulasan_text = $row['ulasan'];
            $rating_pro = $row['rating'];

            // Membatasi rating antara 0 dan 5
            if ($rating_pro > 5) {
                $rating_pro = 5; // Set ke 5 jika lebih dari 5
            } elseif ($rating_pro < 1) {
                $rating_pro = 0; // Set ke 1 jika rating kurang dari 1
            }

            // Menentukan jumlah bintang berdasarkan rating
            $full_stars = floor($rating_pro);
            $empty_stars = 5 - $full_stars;
    ?>
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($user_name); ?></h5>
                    <p class="card-text"><?= htmlspecialchars($ulasan_text); ?></p>
                    <div class="rating">
                        <?php
                        // Menampilkan bintang penuh dan kosong
                        for ($i = 1; $i <= 5; $i++) {
                            if ($i <= $rating_pro) {
                                echo "★"; // Tampilkan bintang penuh jika rating >= i
                            } else {
                                echo "☆"; // Tampilkan bintang kosong jika rating < i
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
    <?php
        }
    } else {
        // Jika tidak ada ulasan
        echo '<div class="alert alert-warning" role="alert">
                Belum ada ulasan untuk produk ini. Jadilah yang pertama memberi ulasan!
              </div>';
    }
    ?>
</div>