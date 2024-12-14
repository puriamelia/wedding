<section class="header-profile" style="background-color: #FFF3EF;padding: 50px;">
    <div class="container section-title">
        <h2 class="text-center text-header mt-3">Keranjang</h2>
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
                            sum(i.jumlah) as jumlah_total,
                            (i.harga) as harga,
                            sum(i.subtotal) as sub_total ,
                            p.product_name,
                            p.product_photo,
                            p.description,
                            v.`name` ,
                            i.jam_acara
                        FROM
                            item_keranjang AS i
                            INNER JOIN keranjang AS k ON i.keranjang_id = k.id
                            INNER JOIN products AS p ON i.produk_id = p.product_id
                            INNER JOIN vendors AS v ON p.vendor_id = v.vendor_id
                            where (k.user_id='$kode' or k.session_id='$kode') and k.status is null group by produk_id,tanggal_acara
                            ");

    $total_keranjang = mysqli_num_rows($q);
    if (isset($_POST['proses_cek'])) {

        $ppr = mysqli_fetch_assoc($q);
        $keranjang_id =  $ppr['keranjang_id'];
        mysqli_query($conn, "UPDATE item_keranjang set checkout=NULL  where keranjang_id='$keranjang_id'");
        $selectedProducts = isset($_POST['selected_products']) ? $_POST['selected_products'] : [];
        $tanggalAcara = isset($_POST['tgl']) ? $_POST['tgl'] : [];

        if (!empty($selectedProducts)) {
            foreach ($selectedProducts as $index => $productId) {
                // Pastikan tanggal acara sesuai indeks checkbox yang terpilih
                $tanggal = $tanggalAcara[$index] ?? null;

                // Output atau proses data
                $productIdEscaped = mysqli_real_escape_string($conn, $productId);
                $tanggalEscaped = mysqli_real_escape_string($conn, $tanggal);

                // Query untuk mengupdate `checkout` menjadi "ya"
                $query = "UPDATE item_keranjang SET checkout = 'ya' 
                          WHERE id = '$productIdEscaped' AND tanggal_acara = '$tanggalEscaped'";
                $ekse = mysqli_query($conn, $query);
                // Eksekusi query
            }
            if ($ekse) {
                echo "Produk ID: " . htmlspecialchars($productId) . " berhasil diupdate.<br>";
            } else {
                echo "Gagal mengupdate Produk ID: " . htmlspecialchars($productId) . ". Error: " . mysqli_error($conn) . "<br>";
            }
        } else {
            echo "Tidak ada produk yang dipilih.";
        }

        pindah_halaman("index.php?menu=pembayaran");
    }

    ?>

    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="container">
                    <?php
                    if ($total_keranjang > 0) {
                    ?>
                        <form action="" method="post">
                            <?php
                            $total_qty = 0;
                            $total_semua = 0;
                            while ($data = mysqli_fetch_assoc($q)) {
                                // ID unik untuk checkbox
                                $checkbox_id = "checkbox_" . $data['id'];
                                $qty = $data['jumlah_total'];
                                $harga = $data['harga'];
                                $sub_total = $data['sub_total'];

                                $total_qty  = $total_qty + $qty;
                                $total_semua = $total_semua + $sub_total;
                            ?>
                                <div class="row mt-3 align-items-center">
                                    <div class="col-12 ">
                                        <label for="<?= $checkbox_id ?>"
                                            class="d-flex flex-wrap align-items-center w-100 p-2 rounded cursor-pointer"
                                            style="gap: 10px; overflow: hidden;">
                                            <div class="form-check flex-shrink-0">

                                                <input class="form-check-input large-checkbox" type="checkbox"
                                                    name="selected_products[]" checked value="<?= $data['id'] ?>"
                                                    id="<?= $checkbox_id ?>">
                                                <input type="hidden" name="tgl[]" value="<?= $data['tanggal_acara'] ?>">
                                            </div>

                                           <div class="col-12">
                                           <div class="row ">
                                                <div class="col-12 col-md-4 ">
                                                    <img src="assets/img/product/<?= $data['product_photo'] ?>"
                                                        class="img border rounded" alt="<?= $data['product_name'] ?>"
                                                        style="width:150px">
                                                </div>
                                                <div class="col-12 col-md-12 ">
                                                    <h3 class=""><?= $data['product_name'] ?></h3>
                                                    <p class=""><?= $data['description'] ?></p>

                                                    <!-- Dua kolom: Kiri untuk informasi, kanan untuk input tanggal acara -->
                                                    <div class="row mt-3">
                                                        <!-- Kolom Kiri (informasi) -->
                                                        <div class="col-12 col-md-3">
                                                            <p class="m-0"><strong>Harga:</strong> <?= rupiah($harga) ?></p>
                                                            <p class="m-0"><strong>QTY:</strong> <?= rupiah($qty) ?>
                                                            </p>
                                                        </div>

                                                        <!-- Kolom Kanan (input tanggal acara) -->
                                                        <div class="col-12 col-md-6">
                                                            <p class="m-0"><strong>Tanggal Acara :
                                                                </strong> <?= formatTanggal($data['tanggal_acara']) ?>
                                                            </p>
                                                            <p class="m-0"><strong>Jam Acara :
                                                                </strong> <?= ($data['jam_acara']) ?>
                                                            </p>
                                                            <p class="m-0">
                                                                <strong>Total :</strong> <?= rupiah($sub_total) ?>
                                                            </p>
                                                        </div>
                                                        
                                                    </div>
                                                    <div class="col-12 col-md-3">
                                                            <a href="index.php?menu=keranjang&id=<?= enkrip($data['id']) ?>&hapus"
                                                                class="btn btn-danger btn-sm"
                                                                onclick="return confirm('Apakah yakin akan hapus produk ini?')"><i
                                                                    class="fa fa-trash"></i> Hapus</a>
                                                        </div>
                                                </div>
                                            </div>
                                           </div>
                                        </label>
                                    </div>
                                </div>

                            <?php
                            }
                            ?>
                            <div class="container mt-4 ">
                                <div class="row justify-content-center">
                                    <div class="col-12 col-md-12">
                                        <div class="card shadow-sm border-0">
                                            <div class="card-body text-center">
                                                <h4 class="card-title mb-3">Total </h4>
                                                <div class="d-flex justify-content-between">
                                                    <p class="mb-1">Total Item:</p>
                                                    <p class="mb-1"><strong id="total-items"><?= $total_qty ?></strong></p>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <p class="mb-1">Total Harga:</p>
                                                    <p class="mb-1"><strong
                                                            id="total-price"><?= rupiah($total_semua) ?></strong></p>
                                                </div>
                                                <?php
                                                if (isset($_SESSION['user_id'])) {
                                                ?>
                                                    <button class="tombol mt-3 w-100" name='proses_cek'
                                                        id="checkout-button">Lanjut ke
                                                        Pembayaran <i class="fa fa-shopping-cart"></i></button>
                                                <?php
                                                } else {
                                                ?>
                                                    <a href='login.php?checkout' class="tombol mt-3 w-100"
                                                        id="checkout-button">Login Dulu untuk
                                                        melanjutkan <i class="fa fa-shopping-cart"></i></a>
                                                <?php
                                                }

                                                ?>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>


                    <?php
                    } else {
                    ?>
                        <div class="row">
                            <div class="col-12">
                                <p class="lead">
                                    Belum ada data, yuk tambahin produk ke keranjang <br>


                                </p>
                                <a href="index.php" class="tombol "><i class="fa fa-shopping-cart"></i> Halaman Awal</a>
                            </div>
                        </div>
                    <?php
                    }

                    ?>

                </div>

            </div>
        </div>
    </div>
</section>