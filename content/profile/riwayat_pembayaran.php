<?php
$sql = "SELECT * FROM konfirmasi_pembayaran  WHERE user_id = '$user_id' ORDER BY id DESC";

$result = mysqli_query($conn, $sql);
// echo "sss" . $user_id;
?>
<div class="container section-title">
    <h3 class="text-center text-header mt-3">Riwayat Pembayaran</h3>
    <hr style="border: none; height: 5px; background-color: #AB7665; margin: 20px auto; width: 8%;">
</div>

<div class="container mt-5 mb-5">
    <div class="row text-center">

        <?php if (mysqli_num_rows($result) > 0): ?>
            <table class="table table-bordered table-striped text-center">
                <thead>
                    <tr>
                        <th>Kode Pembayaran</th>
                        <th>Nominal</th>
                        <th>Bukti Pembayaran</th>
                        <th>Invoice</th>
                        <th>Status Pembayaran</th>
                        <th>Lunas</th>
                        <th>Tanggal</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['kode_pembayaran']) ?></td>
                            <td><?= rupiah($row['nominal']) ?></td>
                            <td>
                                <?php if (!empty($row['bukti_pembayaran'])): ?>
                                    <a href="admin-cp/bukti_pembayaran/<?= htmlspecialchars($row['bukti_pembayaran']) ?>"
                                        target="_blank">Lihat Bukti</a>
                                <?php else: ?>
                                    Tidak Ada
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="index.php?menu=profile&act=invoice&id=<?= $row['id'] ?>" class="">Lihat
                                    Invoice</a>




                            </td>
                            <td>
                                <span
                                    class="badge <?= $row['status_pembayaran'] === 'approved' ? 'bg-success' : 'bg-warning' ?>">
                                    <?= htmlspecialchars($row['status_pembayaran'] ?? 'Menunggu Konfirmasi') ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge <?= $row['lunas'] === 'ya' ? 'bg-success' : 'bg-warning' ?>">
                                    <?= $row['lunas'] === 'ya' ? 'LUNAS' : 'BELUM LUNAS' ?>
                                </span> <br>
                                <?php
                                if ($row['lunas'] == 'dp') {
                                ?>
                                    <a href="index.php?menu=profile&act=bayar_sisa&kode=<?= $row['kode_pembayaran'] ?>"
                                        class="">Pembayaran Sisa</a>
                                <?php
                                }
                                ?>
                            </td>
                            <td><?= htmlspecialchars(date("d-m-Y", strtotime($row['tanggal']))) ?></td>
                            <td><?= htmlspecialchars($row['catatan']) ?: '-' ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-warning text-center">
                Tidak ada riwayat pembayaran.
            </div>
        <?php endif; ?>


    </div>
</div>