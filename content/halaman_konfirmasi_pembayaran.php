<section class="header-profile" style="background-color: #FFF3EF;padding: 50px;">
    <div class="container section-title">
        <h2 class="text-center text-header mt-3">Konfirmasi Pembayaran</h2>
        <hr style="border: none; height: 5px; background-color: #AB7665; margin: 20px auto; width: 8%;">
    </div><!-- End Section Title -->


    <?php

    $id_keranjang = dekrip(mysqli_escape_string($conn, $_GET['id']));

    $q = mysqli_query($conn, "SELECT
                                i.keranjang_id,
                                i.id,
                                i.produk_id,
                                i.tanggal_acara,
                                SUM(i.jumlah) AS jumlah_total,
                                (i.harga) AS harga,
                                SUM(i.subtotal) AS sub_total,
                                p.product_name,
                                p.product_photo,
                                p.description,
                                v.`name`
                            FROM
                                item_keranjang AS i
                            INNER JOIN keranjang AS k ON i.keranjang_id = k.id
                            INNER JOIN products AS p ON i.produk_id = p.product_id
                            INNER JOIN vendors AS v ON p.vendor_id = v.vendor_id
                            WHERE  i.keranjang_id='$id_keranjang'
                            GROUP BY produk_id, i.tanggal_acara
                        ");

    $total_keranjang = mysqli_num_rows($q);
    if (isset($_GET['status']) && $_GET['status'] == 'sukses') {
    ?>
        <div class="container my-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card text-center shadow">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0">Pembayaran Berhasil</h4>
                        </div>
                        <div class="card-body">
                            <div class="mb-4 ">
                                <img src="./assets/img/money-wavy-bold.png" alt="Success" class="img-fluid rounded-circle">
                            </div>
                            <h5 class="card-title">Terima Kasih!</h5>
                            <p class="card-text">Pembayaran Anda telah berhasil diproses. Saat ini pembayaran Anda sedang
                                menunggu konfirmasi dari admin.</p>
                            <p class="card-text">Mohon diperhatikan, jika pembayaran DP harus dilunasi selambat-lambatnya
                                H-7 sebelum acara.</p>
                            <p class="text-muted">Silakan cek status pembayaran Anda di menu <strong>Riwayat
                                    Pembayaran</strong>.</p>
                            <a href="index.php" class="tombol">Kembali ke Halaman Utama</a>
                        </div>
                        <div class="card-footer text-muted">
                            Jika ada pertanyaan, hubungi +62 819-1601-7564
                        </div>
                    </div>
                </div>
            </div>

        <?php
    } else {
        ?>
            <div class="container py-5">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="nominal" class="form-label">Jumlah Nominal yang Ditransfer (Rp)</label>
                        <input type="number" class="form-control" id="nominal" name="nominal" placeholder="Contoh: 500000"
                            required>
                    </div>
                    <div class="mb-3">
                        <label for="buktiPembayaran" class="form-label">Upload Bukti Pembayaran</label>
                        <input type="file" class="form-control" id="buktiPembayaran" name="bukti_pembayaran"
                            accept="image/*" required>
                    </div>
                    <div class="mb-3">
                        <p>Pilih Metode Pembayaran:</p>
                        <input type="radio" class="form-check-input" required id="full" name="metode_pembayaran" value="ya">
                        <label for="full">Full</label>
                        <input type="radio" class="form-check-input" required id="setengah" name="metode_pembayaran"
                            value="dp">
                        <label for="setengah">Setengah/DP</label>
                    </div>
                    <div class="mb-3">
                        <label for="catatan" class="form-label">Catatan Pembayaran</label>
                        <textarea class="form-control" id="catatan" name="catatan" rows="4"
                            placeholder="Masukkan catatan tambahan jika ada"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="catatan" class="form-label">Alamat untuk Acara</label>
                        <textarea class="form-control" id="catatan" name="alamat" rows="4"
                            placeholder="Masukkan Alamat untuk acara yang akan diselenggarakan dengan vendor kami"></textarea>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="index.php?menu=pembayaran" class="btn btn-secondary">Kembali</a>
                        <button type="submit" name="konfirmasi" class="btn btn-primary">Konfirmasi Pembayaran</button>
                    </div>
                </form>
            </div>
        <?php
    }

        ?>




</section>

<?php
if (isset($_POST['konfirmasi'])) {
    // Ambil data dari form
    $nominal = $_POST['nominal'];
    $catatan = $_POST['catatan'];
    $metode_pembayaran = $_POST['metode_pembayaran'];
    // $metode_pembayaran = $_POST['metode_pembayaran'];
    $alamat = $_POST['alamat']; // Alamat dari form
    $status_pembayaran = 'pending'; // Default status pembayaran
    $tanggal = date('Y-m-d'); // Tanggal sekarang
    $date_created = date('Y-m-d H:i:s');

    // Tentukan folder tujuan upload
    $target_dir = "admin-cp/bukti_pembayaran/";

    // Ambil nomor urut terakhir dari kode_pembayaran
    $result = mysqli_query($conn, "SELECT kode_pembayaran FROM konfirmasi_pembayaran ORDER BY id DESC LIMIT 1");
    $row = mysqli_fetch_assoc($result);

    if ($row && !empty($row['kode_pembayaran'])) {
        // Ambil bagian nomor urut dari kode_pembayaran terakhir
        $last_code = $row['kode_pembayaran'];
        $last_number = intval(substr($last_code, -3)); // Ambil 3 digit terakhir
        $nourut = str_pad($last_number + 1, 3, '0', STR_PAD_LEFT); // Tambahkan 1 dan format 3 digit
    } else {
        $nourut = '001'; // Default jika belum ada kode_pembayaran
    }

    // Buat kode pembayaran baru
    $timestamp = date('YmdHi'); // Format: tahunbulantanggaljammenit
    $kode_pembayaran = $timestamp . '-' . $nourut;

    // Buat nama file untuk bukti pembayaran
    $file_extension = strtolower(pathinfo($_FILES["bukti_pembayaran"]["name"], PATHINFO_EXTENSION));
    $bukti_pembayaran = $kode_pembayaran . '.' . $file_extension; // Contoh: 202411221530-001.jpg

    // Full path untuk upload
    $target_file = $target_dir . $bukti_pembayaran;

    $uploadOk = 1;

    // Periksa apakah file adalah gambar
    $check = getimagesize($_FILES["bukti_pembayaran"]["tmp_name"]);
    if ($check === false) {
        echo "File yang diunggah bukan gambar.";
        $uploadOk = 0;
    }

    // Periksa ukuran file (contoh: maks 2MB)
    if ($_FILES["bukti_pembayaran"]["size"] > 2000000) {
        echo "Ukuran file terlalu besar. Maksimal 2MB.";
        $uploadOk = 0;
    }

    // Batasi jenis file yang diizinkan
    if (!in_array($file_extension, ['jpg', 'png', 'jpeg', 'gif'])) {
        echo "Hanya file JPG, JPEG, PNG, dan GIF yang diperbolehkan.";
        $uploadOk = 0;
    }

    // Cek apakah ada error
    if ($uploadOk == 0) {
        echo "Maaf, bukti pembayaran gagal diunggah.";
    } else {
        // Upload file
        if (move_uploaded_file($_FILES["bukti_pembayaran"]["tmp_name"], $target_file)) {
            // Simpan data ke database
            $sql = "INSERT INTO konfirmasi_pembayaran 
                        (kode_pembayaran, nominal, bukti_pembayaran, catatan, alamat, status_pembayaran, tanggal, id_keranjang, date_created, user_id,lunas) 
                        VALUES ('$kode_pembayaran', '$nominal', '$bukti_pembayaran', '$catatan', '$alamat', '$status_pembayaran', '$tanggal', '$id_keranjang', '$date_created', '$kode','$metode_pembayaran')";

            if (mysqli_query($conn, $sql)) {
                mysqli_query($conn, "UPDATE keranjang set status='konfirmasi' where id='$id_keranjang'");
                echo "Konfirmasi pembayaran berhasil disimpan.";
                pindah_halaman("index.php?menu=konfirmasi_pembayaran&status=sukses&id=$id_keranjang");
            } else {
                echo "Error: " . $sql . "<br>" . mysqli_error($conn);
            }
        } else {
            echo "Maaf, terjadi kesalahan saat mengunggah file Anda.";
        }
    }
}
?>