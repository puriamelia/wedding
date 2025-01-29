<?php
// Cek aksi yang diterima dari URL
$act = isset($_GET['act']) ? $_GET['act'] : '';
$startDate = isset($_GET['startDate']) ? $_GET['startDate'] : date('Y-m-d');
$endDate = isset($_GET['endDate']) ? $_GET['endDate'] : date('Y-m-d');
$addwhere = " AND konfirmasi_pembayaran.status_pembayaran='approved' AND vendors.user_id='$id_user' ";

if ($role == 'admin') {
    $addwhere = "";
    if(isset($_GET['vendor']) && $_GET['vendor']){
        $id_vendor  = $_GET['vendor'];
        $addwhereVendor  =" AND products.vendor_id='$id_vendor' "; 
    }else{
        $addwhereVendor = '';
    }
}

$q = "	SELECT
            products.product_name AS nama_produk,
            products.categori AS kategori,
            vendors.name AS nama_vendor,
            SUM(item_keranjang.subtotal) AS total_pendapatan,
            COUNT(item_keranjang.id) AS jumlah_pesanan,
            item_keranjang.tanggal_acara,
            item_keranjang.jam_acara,
            konfirmasi_pembayaran.alamat AS alamat_acara,
            konfirmasi_pembayaran.status_pembayaran,
            users.nama AS nama_pemesan,
            users.no_hp AS no_hp_pemesan,
            item_keranjang.selesai AS status_acara,konfirmasi_pembayaran.`tanggal`
            FROM
            item_keranjang
            INNER JOIN products
                ON item_keranjang.produk_id = products.product_id
            INNER JOIN vendors
                ON products.vendor_id = vendors.vendor_id
            INNER JOIN keranjang
                ON item_keranjang.keranjang_id = keranjang.id
            INNER JOIN users
                ON keranjang.user_id = users.user_id
            INNER JOIN konfirmasi_pembayaran
                ON keranjang.id = konfirmasi_pembayaran.id_keranjang
            WHERE konfirmasi_pembayaran.status_pembayaran = 'approved'
            AND item_keranjang.tanggal_acara BETWEEN '$startDate' AND '$endDate'

            $addwhere $addwhereVendor
            GROUP BY products.product_id, item_keranjang.tanggal_acara
            ORDER BY konfirmasi_pembayaran.tanggal asc;";
$data = mysqli_fetch_all(mysqli_query($conn, $q), MYSQLI_ASSOC);
?>


<div class="col-md-12">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4 class="card-title">Laporan Pendapatan</h4>
        </div>
        <div class="col-md-4">
            <form method="GET" class="mb-4">
                <input type="hidden" name="menu" value='pendapatan'>
                <div class="row">
                    <div class="col-md-12">
                        <label for="vendor" class="form-label">Vendor</label>
                        <select id="vendor" name="vendor" class="form-control">
                            <option value="">Semua Vendor</option>
                            <?php
                            // Query untuk mengambil daftar vendor
                            $query = "SELECT v.`vendor_id`, v.`name` FROM vendors v ";
                            $result = mysqli_query($conn, $query); // Pastikan $conn adalah koneksi ke database

                            // Looping untuk menampilkan vendor dalam select options
                            while ($row = mysqli_fetch_assoc($result)) {
                                if ($_GET['vendor'] == $row['vendor_id'])
                                    echo "<option selected value='" . $row['vendor_id'] . "'>" . $row['name'] . "</option>";
                                else
                                    echo "<option value='" . $row['vendor_id'] . "'>" . $row['name'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="startDate" class="form-label">Dari Tanggal</label>
                        <input type="date" id="startDate" name="startDate" value="<?= $startDate ?>" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label for="endDate" class="form-label">Sampai Tanggal</label>
                        <input type="date" id="endDate" name="endDate" value="<?= $endDate ?>" class="form-control">
                    </div>
                    <div class="col-md-12 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Pilih</button>
                    </div>
                    <div class="col-md-12 d-flex align-items-end">
                        <button onclick="printLaporan()" class="btn btn-primary">Cetak Laporan</button>

                    </div>
                </div>
            </form>
        </div>


        <div class="card-body print-area">

            <div class="text-center mb-4">
                <h3>Laporan Pendapatan</h3>
                <h5>Periode: <?= formatTanggal($startDate) ?> - <?= formatTanggal($endDate) ?></h5>
            </div>
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Tanggal</th>
                        <th>Nama Vendor</th>
                        <th>Nama Produk</th>
                        <th>Jumlah Pesanan</th>
                        <th>Total Pendapatan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data)): ?>
                        <?php foreach ($data as $row): ?>
                            <tr>
                                <td><?= formatTanggal($row['tanggal_acara']) ?></td>
                                <td><?= $row['nama_produk'] ?></td>
                                <td><?= $row['nama_vendor'] ?></td>
                                <td class='text-center'><?= $row['jumlah_pesanan'] ?></td>
                                <td><?= rupiah($row['total_pendapatan']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">Tidak ada data ditemukan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>

                <tfoot>
                    <tr>
                        <th colspan="3">Grand Total</th>
                        <th class='text-center'><?= array_sum(array_column($data, 'jumlah_pesanan')) ?></th>
                        <th><?= rupiah(array_sum(array_column($data, 'total_pendapatan'))) ?></th>
                    </tr>
                </tfoot>
            </table>
            <div class="mt-4">
                <h5>Ringkasan Laporan</h5>
                <ul>
                    <li><strong>Total Pendapatan:</strong> <?= rupiah(array_sum(array_column($data, 'total_pendapatan'))) ?></li>
                    <li><strong>Total Pesanan:</strong> <?= array_sum(array_column($data, 'jumlah_pesanan')) ?></li>
                    <li><strong>Jumlah Produk Terjual:</strong> <?= count($data) ?></li>
                </ul>
            </div>

        </div>
    </div>
</div>

<style>
    /* CSS untuk Print Area */
    @media print {
        body * {
            visibility: hidden;
        }

        .print-area,
        .print-area * {
            visibility: visible;
        }

        .print-area {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }

        .card-header,
        .card-footer {
            background-color: #007bff !important;
            color: white !important;
        }

        .btn {
            display: none;
        }
    }
</style>

<script>
    function printLaporan() {
        var printContents = document.querySelector('.print-area').innerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        location.reload(); // Refresh halaman untuk mengembalikan tampilan awal
    }
</script>