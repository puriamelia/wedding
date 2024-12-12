<?php

// Query untuk mendapatkan daftar pembayaran
$result = mysqli_query($conn, "SELECT
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
                            GROUP BY konfirmasi_pembayaran.kode_pembayaran
                                 order by konfirmasi_pembayaran.id desc");

$date = date("Y-m-d H:i:s");


// Handle untuk approve dan reject pembayaran
if (isset($_GET['act']) && $_GET['act'] == 'approve') {
    $kode_pembayaran = $_GET['id'];
    $id_keranjang = $_GET['id_keranjang'];

    // Update status pembayaran menjadi 'diterima'
    $query = "UPDATE konfirmasi_pembayaran SET status_pembayaran = 'approved' WHERE kode_pembayaran = '$kode_pembayaran'";
    // exit;
    if (mysqli_query($conn, $query)) {
        $update = mysqli_query($conn, "UPDATE item_keranjang set success='ya' where keranjang_id='$id_keranjang'");


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
                                                        WHERE konfirmasi_pembayaran.kode_pembayaran = '$kode_pembayaran'
                                                        GROUP BY konfirmasi_pembayaran.kode_pembayaran
                                                        ORDER BY konfirmasi_pembayaran.id DESC");

        // Mengambil hasil dari query
        $belanja = mysqli_fetch_assoc($belanja);

        // Ambil total belanja dan nominal transfer
        $total_belanja = $belanja['total_belanja'];
        $nominal_transfer = $belanja['nominal_transfer'];

        // Pengecekan apakah nominal transfer lebih besar atau sama dengan total belanja
        if ($nominal_transfer >= $total_belanja) {
            // Update status pembayaran menjadi lunas
            $update_sql = "UPDATE konfirmasi_pembayaran 
                                        SET lunas = 'ya',  date_edited = '$date'
                                        WHERE kode_pembayaran = '$kode_pembayaran'";

            // Eksekusi query update
            if (mysqli_query($conn, $update_sql)) {
                echo "<script>alert('Pembayaran telah disetujui dan status pembayaran menjadi Lunas!'); window.location.href='index.php?menu=konfirmasi_pembayaran';</script>";
            } else {
                // Jika query update gagal
                echo "<script>alert('Terjadi kesalahan saat memperbarui status pembayaran!'); window.history.back();</script>";
            }
        } else {
            // Jika nominal transfer kurang dari total belanja
            echo "<script>alert('Nominal transfer kurang dari total belanja!'); window.history.back();</script>";
        }

        echo "<script>alert('Pembayaran berhasil diterima!'); window.location.href = 'index.php?menu=konfirmasi_pembayaran';</script>";
    } else {
        echo "<script>alert('Gagal menerima pembayaran!'); window.location.href = 'index.php?menu=konfirmasi_pembayaran';</script>";
    }
} else if (isset($_GET['act']) && $_GET['act'] == 'reject') {
    $kode_pembayaran = $_GET['id'];

    // Update status pembayaran menjadi 'ditolak'
    $query = "UPDATE konfirmasi_pembayaran SET status_pembayaran = 'reject' WHERE kode_pembayaran = '$kode_pembayaran'";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Pembayaran berhasil ditolak!'); window.location.href = 'index.php?menu=konfirmasi_pembayaran';</script>";
    } else {
        echo "<script>alert('Gagal menolak pembayaran!'); window.location.href = 'index.php?menu=konfirmasi_pembayaran';</script>";
    }
}
if (isset($_GET['act'])) {
    $act = $_GET['act'];
    if ($act == 'detail_pembayaran') {
        $kode = mysqli_escape_string($conn, $_GET['kode']);
        $user_id = $_SESSION['user_id'];  // Assuming you have a session that stores the user ID

        // Ambil data konfirmasi pembayaran utama
        $sql = "SELECT * FROM konfirmasi_pembayaran WHERE kode_pembayaran ='$kode' ORDER BY tanggal DESC";
        $result = mysqli_query($conn, $sql);
        $payment = mysqli_fetch_assoc($result);

        if (!$payment) {
            echo "Pembayaran tidak ditemukan.";
            exit;
        }

        // Ambil data belanja terkait kode pembayaran
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
                                    sum(item_keranjang.subtotal) AS total_belanja,
                                    konfirmasi_pembayaran.nominal AS nominal_transfer,
                                    konfirmasi_pembayaran.lunas
                                FROM
                                    konfirmasi_pembayaran
                                    INNER JOIN users ON konfirmasi_pembayaran.user_id = users.user_id
                                    INNER JOIN keranjang ON konfirmasi_pembayaran.id_keranjang = keranjang.id
                                    INNER JOIN item_keranjang ON keranjang.id = item_keranjang.keranjang_id
                                WHERE konfirmasi_pembayaran.kode_pembayaran='$kode'
                                GROUP BY konfirmasi_pembayaran.kode_pembayaran
                                ORDER BY konfirmasi_pembayaran.id DESC");
        $belanja_details = mysqli_fetch_assoc($belanja);

        // Ambil riwayat konfirmasi pembayaran
        $riwayat = mysqli_query($conn, "SELECT * FROM riwayat_konfirmasi_pembayaran WHERE kode_pembayaran='$kode' ORDER BY date_created DESC");

        // Hitung sisa pembayaran
        $sisa_bayar = $belanja_details['total_belanja'] - $payment['nominal'];

        // Menampilkan Halaman Detail Pembayaran
?>
        <div class="container my-5">
            <div class="card shadow-lg border-0 rounded">
                <div class="card-header bg-success text-white text-center py-4">
                    <h5 class="mb-0"><i class="bi bi-wallet2"></i> Detail Pembayaran</h5>
                </div>
                <div class="card-body p-4">
                    <h5>Informasi Pembayaran Awal</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th>Kode Pembayaran</th>
                            <td><?= $payment['kode_pembayaran'] ?></td>
                        </tr>
                        <tr>
                            <th>Nama Pengguna</th>
                            <td><?= htmlspecialchars($belanja_details['nama']) ?></td>
                        </tr>
                        <tr>
                            <th>Total Belanja</th>
                            <td><?= rupiah($belanja_details['total_belanja']) ?></td>
                        </tr>
                        <tr>
                            <th>Nominal Transfer</th>
                            <td><?= rupiah($payment['nominal']) ?></td>
                        </tr>
                        <tr>
                            <th>Bukti Pembayaran</th>
                            <td>
                                <a href="bukti_pembayaran/<?= $payment['bukti_pembayaran'] ?>" target="_blank">

                                    Bukti Pembayaran</a>
                            </td>
                            </a>
                        </tr>
                        <tr>
                            <th>Catatan</th>
                            <td><?= htmlspecialchars($payment['catatan']) ?></td>
                        </tr>
                        <tr>
                            <th>Status Pembayaran</th>
                            <td><?= $payment['status_pembayaran'] ?></td>
                        </tr>
                        <tr>
                            <th>Sisa Pembayaran</th>
                            <td><?= rupiah($sisa_bayar) ?></td>
                        </tr>
                        <tr>
                            <th>Status Lunas</th>
                            <td>
                                <?= $payment['lunas'] == 'ya' ? 'LUNAS' : 'BELUM LUNAS' ?>
                            </td>
                        </tr>
                    </table>

                    <h5 class="mt-4">Riwayat Pembayaran</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Nominal Pembayaran</th>
                                <th>Status Pembayaran</th>
                                <th>Bukti Pembayaran</th>
                                <th>Catatan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($riwayat)): ?>
                                <tr>
                                    <td><?= $row['tanggal'] ?></td>
                                    <td><?= rupiah($row['nominal']) ?></td>
                                    <td><?= $row['status_pembayaran'] ?></td>
                                    <td><a href="bukti_pembayaran/<?= $row['bukti_pembayaran'] ?>" target="_blank">Bukti
                                            Pembayaran</a></td>
                                    <td><?= htmlspecialchars($row['catatan']) ?></td>
                                    <td>
                                        <?php if ($row['status_pembayaran'] != 'approve'): ?>
                                            <!-- Tombol Approve Bayar Sisa hanya muncul jika status pembayaran bukan Lunas -->
                                            <a href="index.php?menu=konfirmasi_pembayaran&act=approve_sisa&kode=<?= $kode ?>&id_bayar=<?= $row['id'] ?>"
                                                class="btn btn-success btn-sm"
                                                onclick="return confirm('Apakah yakin akan menyetujui pembayaran ini?')">Approve Bayar
                                                Sisa</a>
                                        <?php else: ?>
                                            <span class="badge bg-success">Lunas</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>

                    <div class="d-flex justify-content-end">
                        <a href="index.php?menu=konfirmasi_pembayaran" class="btn btn-secondary">Kembali ke Riwayat
                            Pembayaran</a>
                    </div>
                </div>
            </div>
        </div>

    <?php
    } else if ($act == 'approve_sisa') {
        if (isset($_GET['act']) && $_GET['act'] == 'approve_sisa' && isset($_GET['id_bayar'])) {
            $id_bayar = mysqli_escape_string($conn, $_GET['id_bayar']);
            $kode_pembayaran = mysqli_escape_string($conn, $_GET['kode']);

            // Mengambil informasi riwayat pembayaran berdasarkan id_bayar
            $query = "SELECT * FROM riwayat_konfirmasi_pembayaran WHERE id = '$id_bayar' AND kode_pembayaran = '$kode_pembayaran'";
            $result = mysqli_query($conn, $query);

            // Pastikan data ditemukan
            if (mysqli_num_rows($result) > 0) {

                $ri = mysqli_fetch_assoc($result);
                $bayar = $ri['nominal'];
                $date = date('Y-m-d H:i:s');
                // Update status pembayaran menjadi 'Lunas'
                $update_sql = "UPDATE riwayat_konfirmasi_pembayaran SET status_pembayaran = 'approve', date_edited = '$date' WHERE id = '$id_bayar' AND kode_pembayaran = '$kode_pembayaran'";

                if (mysqli_query($conn, $update_sql)) {
                    $update_sql = "UPDATE konfirmasi_pembayaran SET nominal = nominal+$bayar, date_edited = '$date' WHERE kode_pembayaran = '$kode_pembayaran'";
                    mysqli_query($conn, $update_sql);
                    // Ambil data belanja
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
                                                        WHERE konfirmasi_pembayaran.kode_pembayaran = '$kode_pembayaran'
                                                        GROUP BY konfirmasi_pembayaran.kode_pembayaran
                                                        ORDER BY konfirmasi_pembayaran.id DESC");

                    // Mengambil hasil dari query
                    $belanja = mysqli_fetch_assoc($belanja);

                    // Ambil total belanja dan nominal transfer
                    $total_belanja = $belanja['total_belanja'];
                    $nominal_transfer = $belanja['nominal_transfer'];

                    // Pengecekan apakah nominal transfer lebih besar atau sama dengan total belanja
                    if ($nominal_transfer >= $total_belanja) {
                        // Update status pembayaran menjadi lunas
                        $update_sql = "UPDATE konfirmasi_pembayaran 
                                        SET lunas = 'ya',  date_edited = '$date'
                                        WHERE kode_pembayaran = '$kode_pembayaran'";

                        // Eksekusi query update
                        if (mysqli_query($conn, $update_sql)) {
                            echo "<script>alert('Pembayaran telah disetujui dan status pembayaran menjadi Lunas!'); window.location.href='index.php?menu=konfirmasi_pembayaran&act=detail_pembayaran&kode=$kode_pembayaran';</script>";
                        } else {
                            // Jika query update gagal
                            echo "<script>alert('Terjadi kesalahan saat memperbarui status pembayaran!'); window.history.back();</script>";
                        }
                    } else {
                        // Jika nominal transfer kurang dari total belanja
                        echo "<script>alert('Nominal transfer kurang dari total belanja!'); window.history.back();</script>";
                    }

                    // Jika update berhasil, tampilkan pesan sukses
                    echo "<script>alert('Berhasil menyetujui pembayaran.'); </script>";
                    pindah_halaman('index.php?menu=konfirmasi_pembayaran&act=detail_pembayaran&kode=' . $kode_pembayaran);
                } else {
                    // Jika ada kesalahan saat update
                    echo "<script>alert('Terjadi kesalahan saat memperbarui status pembayaran.'); window.history.back();</script>";
                }
            } else {
                // Jika tidak ada data dengan ID pembayaran yang sesuai
                echo "<script>alert('Pembayaran tidak ditemukan!'); window.history.back();</script>";
            }
        }
    }
} else {
    ?>
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h4 class="card-title">Daftar Pembayaran</h4>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Pembayaran</th>
                            <th>Nama Pengguna</th>
                            <th>Bukti Pembayaran</th>
                            <th>Tanggal Pembayaran</th>
                            <th>Catatan</th>
                            <th>Status Pembayaran</th>
                            <th>Lunas</th>
                            <th>Total Item</th>
                            <th>Total Belanja</th>
                            <th>Nominal Transfer</th>
                            <th>Kurang/Lebih</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        while ($pembayaran = mysqli_fetch_assoc($result)) {
                        ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td>
                                    <a href="index.php?menu=konfirmasi_pembayaran&act=detail_pembayaran&kode=<?php echo $pembayaran['kode_pembayaran']; ?>"
                                        class=""><?php echo $pembayaran['kode_pembayaran']; ?></a>
                                </td>
                                <td><?php echo $pembayaran['nama']; ?></td>
                                <td><a href="bukti_pembayaran/<?php echo $pembayaran['bukti_pembayaran']; ?>"
                                        target="_blank">Lihat Bukti</a></td>
                                <td><?php echo $pembayaran['tanggal']; ?></td>
                                <td><?php echo $pembayaran['catatan']; ?></td>
                                <td>
                                    <?php
                                    if ($pembayaran['status_pembayaran'] == 'pending') {
                                        echo '<span class="badge bg-warning">Pending</span>';
                                    } else if ($pembayaran['status_pembayaran'] == 'reject') {
                                        echo '<span class="badge bg-danger">Reject</span>';
                                    } else if ($pembayaran['status_pembayaran'] == 'approved') {
                                        echo '<span class="badge bg-success">Diterima</span>';
                                    } else {
                                        echo '<span class="badge bg-warning">Pending</span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if ($pembayaran['lunas'] == 'reject') {
                                        echo '<span class="badge bg-danger">Reject</span>';
                                    } else if ($pembayaran['lunas'] == 'ya') {
                                        echo '<span class="badge bg-success">Lunas</span>';
                                    } else if ($pembayaran['lunas'] == 'dp') {
                                        echo '<span class="badge bg-warning">Down Payment</span>';
                                    } else {
                                        echo '<span class="badge bg-dark text-light">Belum Bayar</span>';
                                    }
                                    ?>

                                </td>
                                <td class="text-center"><?php echo $pembayaran['total_barang'] ?></td>
                                <td><?php echo rupiah($belanja = $pembayaran['total_belanja']); ?></td>
                                <td><?php echo rupiah($bayar = $pembayaran['nominal_transfer']); ?></td>
                                <td><?php echo rupiah($bayar - $belanja) ?></td>
                                <td>
                                    <?php if ($pembayaran['status_pembayaran'] == 'pending') { ?>
                                        <a href="index.php?menu=konfirmasi_pembayaran&act=approve&id=<?php echo $pembayaran['kode_pembayaran']; ?>&id_keranjang=<?= $pembayaran['id_keranjang']; ?>"
                                            class="btn btn-success btn-sm">Approve</a>
                                        <a href="index.php?menu=konfirmasi_pembayaran&act=reject&id=<?php echo $pembayaran['kode_pembayaran']; ?>"
                                            class="btn btn-danger btn-sm">Tolak</a>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php
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