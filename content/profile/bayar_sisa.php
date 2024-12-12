<?php
$kode = mysqli_escape_string($conn, $_GET['kode']);
$sql = "SELECT * FROM konfirmasi_pembayaran  WHERE user_id = '$user_id' and kode_pembayaran ='$kode' ORDER BY tanggal DESC";
$result = mysqli_query($conn, $sql);
$result = mysqli_fetch_assoc($result);
$total_tf = $result['nominal'];

$belanja = mysqli_query($conn, "SELECT
                                konfirmasi_pembayaran.kode_pembayaran,
                                users.nama,
                                konfirmasi_pembayaran.bukti_pembayaran,
                                konfirmasi_pembayaran.catatan,
                                konfirmasi_pembayaran.alamat,
                                konfirmasi_pembayaran.status_pembayaran,
                                konfirmasi_pembayaran.tanggal,
                                konfirmasi_pembayaran.id_keranjang,
                                keranjang.id,
                                count(*) as total_barang,
                                sum( item_keranjang.subtotal ) AS total_belanja,
                                konfirmasi_pembayaran.nominal AS nominal_transfer ,
                                konfirmasi_pembayaran.tanggal,
                                konfirmasi_pembayaran.lunas
                            FROM
                                konfirmasi_pembayaran
                                INNER JOIN users ON konfirmasi_pembayaran.user_id = users.user_id
                                INNER JOIN keranjang ON konfirmasi_pembayaran.id_keranjang = keranjang.id
                                INNER JOIN item_keranjang ON keranjang.id = item_keranjang.keranjang_id

                            WHERE  konfirmasi_pembayaran.kode_pembayaran='$kode'
                            GROUP BY konfirmasi_pembayaran.kode_pembayaran
                                 order by konfirmasi_pembayaran.id desc");
$belanja = mysqli_fetch_assoc($belanja);
$total_kekurangan = $belanja['total_belanja'];



$total_kekurangan = $total_tf - $total_kekurangan;
?>
<div class="container section-title">
    <h3 class="text-center text-header mt-3">Pembayaran Pelunasan</h3>
    <hr style="border: none; height: 5px; background-color: #AB7665; margin: 20px auto; width: 8%;">
</div>

<div class="container mt-5 mb-5">
    <div class="row text-center">
        <div class="container py-5">
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="kode" class="form-label">Kode Pembayaran</label>
                    <input type="text" class="form-control" id="kode" name="kode" value="<?= $kode ?>" readonly>
                </div>
                <div class="mb-3">
                    <label for="kode" class="form-label">Sisa yang harus dibayar</label>
                    <span style="font-weight: bold;">
                        <?= rupiah($total_kekurangan) ?>
                    </span>
                </div>
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
                    <label for="catatan" class="form-label">Catatan Pembayaran</label>
                    <textarea class="form-control" id="catatan" name="catatan" rows="4"
                        placeholder="Masukkan catatan tambahan jika ada"></textarea>
                </div>
                <div class="d-flex justify-content-between">
                    <button type="submit" name="konfirmasi" class="btn btn-primary">Konfirmasi Pembayaran</button>
                </div>
            </form>
        </div>

    </div>
</div>

<?php
if (isset($_POST['konfirmasi'])) {
    // Ambil data dari form
    $kode = $_POST['kode'];
    $nominal = $_POST['nominal'];
    $catatan = $_POST['catatan'];
    $metode_pembayaran = $_POST['metode_pembayaran'];
    // $metode_pembayaran = $_POST['metode_pembayaran'];
    $status_pembayaran = 'pending'; // Default status pembayaran
    $tanggal = date('Y-m-d'); // Tanggal sekarang
    $date_created = date('Y-m-d H:i:s');

    // Tentukan folder tujuan upload
    $target_dir = "admin-cp/bukti_pembayaran/";



    // Buat kode pembayaran baru
    $timestamp = date('YmdHi'); // Format: tahunbulantanggaljammenit
    $kode_pembayaran = $kode;

    // Buat nama file untuk bukti pembayaran
    $file_extension = strtolower(pathinfo($_FILES["bukti_pembayaran"]["name"], PATHINFO_EXTENSION));
    $bukti_pembayaran = $kode_pembayaran . '-pelunasan.' . $file_extension; // Contoh: 202411221530-001.jpg

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
            $sql = "INSERT INTO riwayat_konfirmasi_pembayaran 
                        (kode_pembayaran, nominal, bukti_pembayaran, catatan, status_pembayaran, tanggal, date_created) 
                        VALUES ('$kode_pembayaran', '$nominal', '$bukti_pembayaran', '$catatan',  '$status_pembayaran', '$tanggal',  '$date_created')";

            if (mysqli_query($conn, $sql)) {
                // mysqli_query($conn, "UPDATE keranjang set status='konfirmasi' where id='$id_keranjang'");
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