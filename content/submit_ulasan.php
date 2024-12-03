<?php
$id = dekrip($_GET['id']);
// Query untuk mendapatkan detail produk berdasarkan item keranjang
$q = mysqli_query($conn, "SELECT
                            k.*,
                            p.*,
                            v.*,
                            i.*,
                            p.rating AS rating_produk 
                        FROM
                            item_keranjang AS i
                            INNER JOIN keranjang AS k ON i.keranjang_id = k.id
                            INNER JOIN products AS p ON i.produk_id = p.product_id
                            INNER JOIN vendors AS v ON p.vendor_id = v.vendor_id 
                        WHERE
                            i.id='$id' AND i.success='ya'
                        GROUP BY
                            produk_id,
                            i.tanggal_acara ");

$row = mysqli_fetch_assoc($q); // Ambil satu hasil saja

// Periksa apakah form sudah disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_produk = $_POST['product_id'];
    $id_user = $user_id;
    $ulasan = mysqli_real_escape_string($conn, $_POST['review']);
    $rating = $_POST['rating'];
    $rating_sebelum = (int)$_POST['rating_sebelum'];
    $id_keranjang = $_POST['id_keranjang'];
    $date_created = date('Y-m-d H:i:s'); // Waktu sekarang

    // Query untuk menyimpan data ulasan
    $sql = "INSERT INTO ulasan (id_produk, id_user, ulasan, rating, date_created,id_keranjang) 
            VALUES ('$id_produk', '$id_user', '$ulasan', '$rating', '$date_created','$id_keranjang')";


    if (mysqli_query($conn, $sql)) {
        // Hitung rata-rata baru dari tabel ulasan setelah ulasan dihapus

        $query = "SELECT AVG(rating) AS avg_rating,COUNT(*) as total FROM ulasan WHERE id_produk = '$id_produk'";
        $result = mysqli_query($conn, $query);
        $row = mysqli_fetch_assoc($result);


        // Jika tidak ada ulasan, set rata-rata ke 0
        $avg_rating = ($row['avg_rating'] ?? 0) + $rating_sebelum;
        $jumlah_rating = $row['total'] ?? 0;
        $rating_baru = ($avg_rating + $rating_sebelum) / ($jumlah_rating + 1);
        // Update tabel products

        $update_query = "UPDATE products SET rating = '$rating_baru' WHERE product_id = '$id_produk'";
        mysqli_query($conn, $update_query);

        pindah_halaman("index.php?menu=profile&act=beri_rating&success");
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<?php if ($row) { ?>
    <div class="container section-title">
        <h3 class="text-center text-header mt-3">Beri Ulasan</h3>
        <hr style="border: none; height: 5px; background-color: #AB7665; margin: 20px auto; width: 8%;">
    </div>

    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-12 mb-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Form Ulasan Produk</h5>
                        <form action="" method="POST">
                            <!-- Nama Produk (Disabled) -->
                            <div class="mb-3">
                                <label for="product_name" class="form-label">Nama Produk:</label>
                                <input type="text" class="form-control" id="product_name"
                                    value="<?php echo $row['product_name']; ?>" disabled>
                                <input type="text" class="form-control" id="rating_sebelum" name="rating_sebelum"
                                    value="<?php echo $row['rating_produk']; ?>">
                            </div>
                            <!-- Hidden input untuk ID produk -->
                            <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
                            <input type="hidden" name="id_keranjang" value="<?php echo $row['keranjang_id']; ?>">

                            <!-- Rating -->
                            <div class="mb-3">
                                <label for="rating_<?php echo $row['product_id']; ?>" class="form-label">Rating:</label>
                                <div class="rating d-flex align-items-center">
                                    <input type="radio" required name="rating" value="5"
                                        id="star5_<?php echo $row['product_id']; ?>">
                                    <label for="star5_<?php echo $row['product_id']; ?>" title="5 stars">⭐ (5)</label>
                                    <input type="radio" required name="rating" value="4"
                                        id="star4_<?php echo $row['product_id']; ?>">
                                    <label for="star4_<?php echo $row['product_id']; ?>" title="4 stars">⭐ (4)</label>
                                    <input type="radio" required name="rating" value="3"
                                        id="star3_<?php echo $row['product_id']; ?>">
                                    <label for="star3_<?php echo $row['product_id']; ?>" title="3 stars">⭐ (3)</label>
                                    <input type="radio" required name="rating" value="2"
                                        id="star2_<?php echo $row['product_id']; ?>">
                                    <label for="star2_<?php echo $row['product_id']; ?>" title="2 stars">⭐ (2)</label>
                                    <input type="radio" required name="rating" value="1"
                                        id="star1_<?php echo $row['product_id']; ?>">
                                    <label for="star1_<?php echo $row['product_id']; ?>" title="1 star">⭐ (1)</label>
                                </div>
                            </div>

                            <!-- Ulasan -->
                            <div class="mb-3">
                                <label for="review_<?php echo $row['product_id']; ?>" class="form-label">Ulasan:</label>
                                <textarea class="form-control" name="review" id="review_<?php echo $row['product_id']; ?>"
                                    rows="3" placeholder="Tulis ulasan Anda"></textarea>
                            </div>

                            <!-- Tombol Submit -->
                            <button type="submit" class="btn btn-primary">Kirim Ulasan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } else { ?>
    <div class="container mt-5">
        <div class="alert alert-danger text-center">
            Item keranjang tidak ditemukan atau tidak valid.
        </div>
    </div>
<?php } ?>