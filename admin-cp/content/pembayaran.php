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
                                konfirmasi_pembayaran.tanggal
                            FROM
                                konfirmasi_pembayaran
                                INNER JOIN users ON konfirmasi_pembayaran.user_id = users.user_id
                                INNER JOIN keranjang ON konfirmasi_pembayaran.id_keranjang = keranjang.id
                                INNER JOIN item_keranjang ON keranjang.id = item_keranjang.keranjang_id 
                            GROUP BY
                                konfirmasi_pembayaran.kode_pembayaran order by konfirmasi_pembayaran.id desc");

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
                        <th>Total Item</th>
                        <th>Total Belanja</th>
                        <th>Nominal Transfer</th>
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
                            <td><?php echo $pembayaran['kode_pembayaran']; ?></td>
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
                            <td class="text-center"><?php echo $pembayaran['total_barang'] ?></td>
                            <td><?php echo number_format($pembayaran['total_belanja'], 0, ',', '.'); ?></td>
                            <td><?php echo number_format($pembayaran['nominal_transfer'], 0, ',', '.'); ?></td>
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
// Handle untuk approve dan reject pembayaran
if (isset($_GET['act']) && $_GET['act'] == 'approve') {
    $kode_pembayaran = $_GET['id'];
    $id_keranjang = $_GET['id_keranjang'];

    // Update status pembayaran menjadi 'diterima'
    $query = "UPDATE konfirmasi_pembayaran SET status_pembayaran = 'approved' WHERE kode_pembayaran = '$kode_pembayaran'";
    // exit;
    if (mysqli_query($conn, $query)) {
        $update = mysqli_query($conn, "UPDATE item_keranjang set success='ya' where keranjang_id='$id_keranjang'");

        echo "<script>alert('Pembayaran berhasil diterima!'); window.location.href = 'index.php?menu=konfirmasi_pembayaran';</script>";
    } else {
        echo "<script>alert('Gagal menerima pembayaran!'); window.location.href = 'index.php?menu=konfirmasi_pembayaran';</script>";
    }
}

if (isset($_GET['act']) && $_GET['act'] == 'reject') {
    $kode_pembayaran = $_GET['id'];

    // Update status pembayaran menjadi 'ditolak'
    $query = "UPDATE konfirmasi_pembayaran SET status_pembayaran = 'reject' WHERE kode_pembayaran = '$kode_pembayaran'";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Pembayaran berhasil ditolak!'); window.location.href = 'index.php?menu=konfirmasi_pembayaran';</script>";
    } else {
        echo "<script>alert('Gagal menolak pembayaran!'); window.location.href = 'index.php?menu=konfirmasi_pembayaran';</script>";
    }
}
?>