<?php
$addwhere = " AND konfirmasi_pembayaran.status_pembayaran='approved' AND vendors.user_id='$id_user' ";
if ($role == 'admin')
    $addwhere = "";
// Query untuk mendapatkan data pesanan
$query = "SELECT
            products.product_name AS nama_produk,
            products.categori AS kategori,
            vendors.name AS nama_vendor,
            item_keranjang.subtotal,
            item_keranjang.tanggal_acara,
            konfirmasi_pembayaran.alamat AS alamat_acara,
            konfirmasi_pembayaran.status_pembayaran,
            users.nama AS nama_pemesan,
            users.no_hp AS no_hp_pemesan,
            item_keranjang.selesai AS status_acara ,
            item_keranjang.id AS id_item
        FROM
            item_keranjang
        INNER JOIN products ON item_keranjang.produk_id = products.product_id
        INNER JOIN vendors ON products.vendor_id = vendors.vendor_id
        INNER JOIN keranjang ON item_keranjang.keranjang_id = keranjang.id
        INNER JOIN users ON keranjang.user_id = users.user_id
        INNER JOIN konfirmasi_pembayaran ON keranjang.id = konfirmasi_pembayaran.id_keranjang
        where 1=1  $addwhere order by item_keranjang.id desc";
// echo $query;
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query gagal: " . mysqli_error($conn));
}
?>

<div class="col-md-12">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4 class="card-title">Pesanan Masuk</h4>
        </div>
        <div class="card-body">
            <?php if (mysqli_num_rows($result) > 0) { ?>
               <div class="table-responsive">
               <table id="data_pesanan" class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>No</th>
                            <th>Nama Produk</th>
                            <th>Kategori</th>
                            <th>Nama Vendor</th>
                            <th>Subtotal</th>
                            <th>Tanggal Acara</th>
                            <th>Alamat Acara</th>
                            <th>Status Pembayaran</th>
                            <th>Nama Pemesan</th>
                            <th>No HP Pemesan</th>
                            <th>Status Acara</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($result)) {
                        ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo htmlspecialchars($row['nama_produk']); ?></td>
                                <td><?php echo htmlspecialchars($row['kategori']); ?></td>
                                <td><?php echo htmlspecialchars($row['nama_vendor']); ?></td>
                                <td><?php echo rupiah($row['subtotal']); ?></td>
                                <td><?php echo (formatTanggal($row['tanggal_acara'])); ?></td>
                                <td><?php echo htmlspecialchars($row['alamat_acara']); ?></td>
                                <td>
                                    <?php echo $row['status_pembayaran'] == 'approved' ? "<span class='badge badge-success'>Lunas</span>" : "<span class='badge badge-danger'>Belum Lunas</span>"; ?>
                                </td>
                                <td><?php echo htmlspecialchars($row['nama_pemesan']); ?></td>
                                <td><?php echo htmlspecialchars($row['no_hp_pemesan']); ?></td>
                                <td>
                                    <?php echo $row['status_acara'] == 'ya' ? "<span class='badge badge-success'>Selesai</span>" : "<span class='badge badge-warning'>Belum Selesai</span>"; ?>
                                </td>
                                <td>
                                    <?php if ($row['status_acara'] != 'ya') { ?>
                                        <a class="btn btn-success btn-sm"
                                            href="index.php?menu=pesanan&act=selesai&id=<?= $row['id_item'] ?>"
                                            onclick="return confirm('Tandai acara ini selesai?')">Tandai Selesai</a>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
               </div>
            <?php } else { ?>
                <div class="alert alert-info">
                    Tidak ada pesanan masuk.
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<?php

// Ambil item ID dari request
if (isset($_GET['id']) && isset($_GET['act']) && $_GET['act'] == 'selesai') {
    $item_id = intval($_GET['id']);

    // Update status_acara menjadi 'ya'
    $query = "UPDATE item_keranjang SET selesai = 'ya' WHERE id = $item_id";
    // echo $query;
    // exit;
    if (mysqli_query($conn, $query)) {
        echo "<script>
                alert('Acara berhasil ditandai selesai.');
                window.location.href = 'index.php?menu=pesanan';
              </script>";
    } else {
        echo "<script>
                alert('Gagal memperbarui status acara.');
                window.location.href = 'index.php?menu=pesanan';
              </script>";
    }
}
?>