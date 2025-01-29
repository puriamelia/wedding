<?php

$act = isset($_GET['act']) ? $_GET['act'] : '';
if ($act == 'balasan') {
    // Ambil ID Diskusi dari URL
    $id_diskusi = $_GET['id'];



    // Query untuk mengambil detail diskusi berdasarkan ID
    $detail_sql = "SELECT 
                            d.id,
                            d.diskusi,
                            d.date_created,
                            p.product_name,
                            p.product_photo,
                            p.categori
                       FROM diskusi AS d
                       INNER JOIN products AS p ON d.id_produk = p.product_id
                       WHERE d.id = '$id_diskusi'";
    $detail_result = mysqli_query($conn, $detail_sql);
    $detail = mysqli_fetch_assoc($detail_result);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $balasan = mysqli_real_escape_string($conn, $_POST['balasan']);
        $date_now = date('Y-m-d H:i:s');

        // Ambil nama vendor berdasarkan user_id
        $vendor_query = "SELECT name FROM vendors WHERE user_id = '$id_user'";
        $vendor_result = mysqli_query($conn, $vendor_query);
        $vendor_data = mysqli_fetch_assoc($vendor_result);
        $name = $vendor_data['name']; // Nama vendor

        // Insert balasan ke tabel balasan
        $insert_sql = "INSERT INTO balasan (diskusi_id, user_id, user_name, balasan, date_created, dibaca,vendor) 
                       VALUES ('$id_diskusi', '$id_user', '$name', '$balasan', '$date_now', 'ya','ya')";
        if (mysqli_query($conn, $insert_sql)) {
            echo "<div class='alert alert-success'>Balasan berhasil dikirim!</div>";
        } else {
            echo "<div class='alert alert-danger'>Gagal mengirim balasan. Silakan coba lagi.</div>";
            echo mysqli_error($conn);
        }
    }
    $balasan_sql = "SELECT user_name, balasan, date_created, vendor , dibaca
                        FROM balasan 
                        WHERE diskusi_id = '$id_diskusi'
                        ORDER BY date_created ASC";
    $balasan_result = mysqli_query($conn, $balasan_sql);



?>
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h4 class="card-title">Detail Diskusi</h4>
            </div>
            <div class="card-body">
                <h5>Nama Produk: <?= $detail['product_name'] ?></h5>
                <img src="../assets/img/product/<?= $detail['product_photo'] ?>" alt="Foto Produk"
                    class="img-thumbnail mb-3" style="width: 100px;">
                <p><strong>Kategori:</strong> <?= $detail['categori'] ?></p>
                <p><strong>Diskusi:</strong> <?= $detail['diskusi'] ?></p>
                <p><strong>Tanggal Dibuat:</strong> <?= $detail['date_created'] ?></p>
                <hr>
                <!-- List Balasan -->
                <h5>Balasan</h5>
                <ul class="list-group mb-4">
                    <?php
                    if (mysqli_num_rows($balasan_result) > 0) {
                        while ($balasan_row = mysqli_fetch_assoc($balasan_result)) {
                            $is_vendor = $balasan_row['vendor'] == 'ya' ? 'badge bg-primary' : 'badge bg-secondary text-light';
                    ?>
                            <li class="list-group-item <?= $balasan_row['dibaca'] == 'ya' ? '' : 'bg-warning' ?>">
                                <span class="<?= $is_vendor ?>"><?= $balasan_row['user_name']; ?></span>
                                <br>
                                <small class="text-muted"><i><?= $balasan_row['date_created']; ?></i></small>
                                <p class="mb-0"><?= $balasan_row['balasan']; ?></p>
                            </li>
                    <?php
                        }
                    } else {
                        echo "<li class='list-group-item'>Belum ada balasan.</li> <hr/>";
                    }
                    ?>
                </ul>

                <h5>Kirim Balasan</h5>
                <form action="" method="POST">
                    <div class="form-group mb-3">
                        <label for="balasan">Balasan</label>
                        <textarea name="balasan" id="balasan" class="form-control" rows="4" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Kirim Balasan</button>
                    <a href="index.php?menu=diskusi" class="btn btn-secondary">Kembali</a>
                </form>
            </div>
        </div>
    </div>
<?php


    /// UPDATE STATUS DIBACA OLEH ADMIN

    $update_sql = "UPDATE diskusi SET dibaca_admin = 'ya' WHERE id = '$id_diskusi'";
    mysqli_query($conn, $update_sql);

    $update_sql = "UPDATE balasan SET dibaca = 'ya' WHERE diskusi_id = '$id_diskusi' and vendor is null ";
    mysqli_query($conn, $update_sql);
} else {
    $sql = "SELECT
                p.product_id,
                p.product_name,
                p.product_photo,
                p.categori,
                d.diskusi,
                d.user_name AS username,
                d.id AS diskusi_id,
                d.id ,
                d.date_created,
                d.dibaca_admin,
                IFNULL( b.total_dibalas, 0 ) AS total_dibalas,
                IFNULL( nb.total_belum_dibaca, 0 ) AS total_belum_dibaca 
            FROM
                diskusi AS d
                INNER JOIN products AS p ON d.id_produk = p.product_id
                INNER JOIN vendors AS v ON p.vendor_id = v.vendor_id
                LEFT JOIN ( SELECT diskusi_id, COUNT(*) AS total_dibalas FROM balasan GROUP BY diskusi_id ) AS b ON d.id = b.diskusi_id
                LEFT JOIN ( SELECT diskusi_id, COUNT(*) AS total_belum_dibaca FROM balasan WHERE vendor IS NULL AND dibaca = 'belum' GROUP BY diskusi_id ) AS nb ON d.id = nb.diskusi_id 
            WHERE
                v.user_id = '$id_user' 
            ORDER BY
                d.date_created DESC;
;

";
    $result = mysqli_query($conn, $sql);

?>
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="card-title">DISKUSI PRODUK</h4>
            </div>
            <div class="card-body">
                <!-- Tabel Data -->
                <table class="table table-bordered table-hover table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Nama Produk</th>
                            <th>Kategori</th>
                            <th>Nama User</th>
                            <th>Diskusi</th>
                            <th>Tanggal Dibuat</th>
                            <th>Dibaca</th>
                            <th>Total Balasan</th>
                            <th>Balasan Baru</th>
                            <th>Act</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Periksa jika ada data
                        if (mysqli_num_rows($result) > 0) {
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($result)) {
                        ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= $row['product_name']; ?></td>
                                    <td><?= $row['categori']; ?></td>
                                    <td><?= $row['username']; ?></td>
                                    <td><?= $row['diskusi']; ?></td>
                                    <td><?= ($row['date_created']); ?></td>
                                    <td><?= $row['dibaca_admin'] == 'ya' ? 'Sudah' : 'Belum'; ?></td>
                                    <td><?= $row['total_dibalas'] ?></td>
                                    <td><?= $row['total_belum_dibaca'] ?></td>
                                    <td>
                                        <a href="index.php?menu=diskusi&act=balasan&id=<?= $row['id'] ?>"
                                            class="btn btn-success">Detail dan balas</a>

                                    </td>
                                </tr>
                        <?php
                            }
                        } else {
                            echo "<tr><td colspan='8' class='text-center'>Tidak ada data diskusi.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>


            </div>
        </div>
    </div>
<?php
}
?>