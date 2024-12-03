<section class="header-profile" style="background-color: #FFF3EF;padding: 50px;">
    <div class="container section-title">
        <h2 class="text-center text-header mt-3">Halaman Pembayaran</h2>
        <hr style="border: none; height: 5px; background-color: #AB7665; margin: 20px auto; width: 8%;">
    </div><!-- End Section Title -->


    <?php


    if (isset($_GET['hapus'])) {
        $id = dekrip($_GET['id']);
        $ha = mysqli_query($conn, "DELETE from item_keranjang where id='$id'");
        if ($ha) {
            pindah_halaman("index.php?menu=keranjang");
            // echo "berhasil hapus";
        } else {
            // echo "gagal dihapus";
        }
    }

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
                            WHERE (k.user_id='$kode' OR k.session_id='$kode') and i.checkout='ya' and i.success is null
                            GROUP BY produk_id, i.tanggal_acara
                        ");

    $total_keranjang = mysqli_num_rows($q);

    ?>

    <div class="container py-5">
        <div class="row">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Nama Produk</th>
                                <th>Vendor</th>
                                <th>Tanggal Acara</th>
                                <th>Jumlah</th>
                                <th>Harga Satuan</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php
                            $total = 0;
                            while ($row = mysqli_fetch_assoc($q)) {
                                $id_keranjang = $row['keranjang_id'];
                                $total = $total + $row['sub_total'];
                            ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['product_name']) ?></td>
                                    <td><?= htmlspecialchars($row['name']) ?></td>
                                    <td><?= formatTanggal($row['tanggal_acara']) ?></td>
                                    <td><?= htmlspecialchars($row['jumlah_total']) ?></td>
                                    <td><?= rupiah($row['harga']) ?></td>
                                    <td><?= rupiah($row['sub_total']) ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-6">
                <h4>Total Keseluruhan: <strong>Rp <?= number_format($total, 0, ',', '.') ?></strong></h4>
                <p class="mt-3">
                    Silakan lakukan pembayaran ke rekening berikut:
                    <br><strong>Bank:</strong> BCA
                    <br><strong>No. Rekening:</strong> 1234567890
                    <br><strong>Atas Nama:</strong> PT Pembayaran Anda
                </p>
            </div>
            <div class="col-md-6 text-end">
                <a href="index.php?menu=keranjang" class="btn btn-secondary">Kembali ke Keranjang</a>
                <a href="index.php?menu=konfirmasi_pembayaran&id=<?= enkrip($id_keranjang) ?>"
                    class="btn btn-primary">Konfirmasi Pembayaran</a>
            </div>
        </div>
    </div>


</section>