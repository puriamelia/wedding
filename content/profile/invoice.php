<?php
// Ambil data pembayaran berdasarkan ID pembayaran (atau kondisi lain sesuai kebutuhan)
$payment_id = $_GET['id']; // Pastikan validasi dilakukan untuk menghindari SQL Injection
// Query untuk mendapatkan data pembayaran
$sql_payment = "
    SELECT kp.*, GROUP_CONCAT(DISTINCT ik.produk_id) AS produk_ids 
    FROM konfirmasi_pembayaran kp 
    LEFT JOIN item_keranjang ik ON kp.id_keranjang = ik.keranjang_id 
    WHERE kp.id = '$payment_id'
";
$result_payment = mysqli_query($conn, $sql_payment);
$payment = mysqli_fetch_assoc($result_payment);

if (!$payment) {
    echo "<div class='alert alert-danger text-center'>Invoice tidak ditemukan.</div>";
    exit;
}

// Query untuk mendapatkan data produk
$produk_ids = $payment['produk_ids'];
$sql_products = "
    SELECT ik.*, p.product_name, p.price, p.product_photo 
    FROM item_keranjang ik 
    INNER JOIN products p ON ik.produk_id = p.product_id 
    WHERE ik.keranjang_id = '{$payment['id_keranjang']}'
";
$result_products = mysqli_query($conn, $sql_products);

?>
<style>
    .invoice-box {
        max-width: 800px;
        margin: auto;
        padding: 30px;
        border: 1px solid #eee;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
        font-size: 16px;
        line-height: 24px;
        font-family: 'Helvetica', 'Arial', sans-serif;
        color: #555;
    }

    .invoice-box table {
        width: 100%;
        line-height: inherit;
        text-align: left;
    }

    .invoice-box table td {
        padding: 5px;
        vertical-align: top;
    }

    .invoice-box table tr td:nth-child(2) {
        text-align: right;
    }

    .invoice-box table tr.top table td {
        padding-bottom: 20px;
    }

    .invoice-box table tr.heading td {
        background: #f2f2f2;
        border-bottom: 1px solid #ddd;
        font-weight: bold;
    }

    .invoice-box table tr.details td {
        padding-bottom: 20px;
    }

    .invoice-box table tr.item td {
        border-bottom: 1px solid #eee;
    }

    .invoice-box table tr.item.last td {
        border-bottom: none;
    }

    .invoice-box table tr.total td:nth-child(2) {
        border-top: 2px solid #eee;
        font-weight: bold;
    }
</style>
<div class=" mb-5">


    <div style="text-align: center; margin-top: 20px;">
        <a href="index.php?menu=profile&act=riwayat_pembayaran" class="btn btn-danger"><i class="fa fa-angle-left"></i>
            Kembali</a>
        <button class="btn btn-success" onclick="printInvoice()"><i class="fa fa-print"></i> Print Invoice</button>
    </div>

</div>
<div class="invoice-box mb-5">
    <table>
        <tr class="top">
            <td colspan="2">
                <table>
                    <tr>
                        <td>
                            <h2>Invoice</h2>
                            <b>Kode Pembayaran:</b> <?= htmlspecialchars($payment['kode_pembayaran']) ?><br>
                            <b>Tanggal:</b> <?= htmlspecialchars(date("d-m-Y", strtotime($payment['tanggal']))) ?><br>
                            <b>Alamat:</b> <?= htmlspecialchars($payment['alamat']) ?>
                        </td>
                        <td style="text-align: right;">
                            <h4>Fame</h4>
                            Bandung, Jawa Barat<br>
                            Email: info@fame.com<br>
                            Telepon: +62 819-1601-7564
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr class="heading">
            <td>Produk</td>
            <td>Subtotal</td>
        </tr>

        <?php
        $total = 0;
        while ($product = mysqli_fetch_assoc($result_products)):
            $total += $product['subtotal'];
        ?>
            <tr class="item">
                <td>
                    <b><?= htmlspecialchars($product['product_name']) ?></b><br>
                    Jumlah: <?= $product['jumlah'] ?> x <?= rupiah($product['harga']) ?>
                </td>
                <td><?= rupiah($product['subtotal']) ?></td>
            </tr>
        <?php endwhile; ?>

        <tr class="total">
            <td>Total:</td>
            <td><?= rupiah($total) ?></td>
        </tr>
    </table>

    <br>
    <h5>Status Pembayaran:</h5>
    <span class="badge <?= $payment['status_pembayaran'] === 'approved' ? 'bg-success' : 'bg-warning' ?>">
        <?= htmlspecialchars($payment['status_pembayaran'] ?? 'Menunggu Konfirmasi') ?>
    </span>

    <br><br>
    <p><b>Catatan:</b> <?= htmlspecialchars($payment['catatan']) ?: '-' ?></p>

    <br>
    <p style="text-align: center; color: #777; font-size: 12px;">
        Terima kasih telah melakukan pembayaran! Jika ada pertanyaan, silakan hubungi kami.
    </p>
</div>


<script>
    function printInvoice() {
        // Ambil elemen invoice-box
        const printContent = document.querySelector('.invoice-box').outerHTML;
        const printWindow = window.open('', '_blank'); // Buka jendela baru
        printWindow.document.open();
        // Buat halaman dengan elemen yang diambil
        printWindow.document.write(`
                <html>
                    <head>
                        <title>Cetak Invoice</title>
                        <style>
                            body { font-family: Arial, sans-serif; margin: 0; padding: 0; }
                            .invoice-box { max-width: 800px; margin: auto; padding: 20px; border: 1px solid #eee; box-shadow: 0 0 10px rgba(0, 0, 0, 0.15); font-size: 16px; line-height: 24px; color: #555; }
                            .invoice-box table { width: 100%; line-height: inherit; text-align: left; }
                            .invoice-box table td { padding: 5px; vertical-align: top; }
                            .invoice-box table tr td:nth-child(2) { text-align: right; }
                            .badge.bg-success { color: white; background-color: green; padding: 5px 10px; border-radius: 5px; }
                            .badge.bg-warning { color: black; background-color: yellow; padding: 5px 10px; border-radius: 5px; }
                        </style>
                    </head>
                    <body onload="window.print(); window.close();">
                        ${printContent}
                    </body>
                </html>
            `);
        printWindow.document.close();
    }
</script>