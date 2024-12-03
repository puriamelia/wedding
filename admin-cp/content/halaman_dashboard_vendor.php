<?php
$query = "SELECT
            products.product_name AS nama_produk,
            products.categori AS kategori,
            vendors.name AS nama_vendor,
            sum(item_keranjang.subtotal) as total_uang_masuk,
            count(item_keranjang.subtotal) as total_pesanan_masuk,
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
        where konfirmasi_pembayaran.status_pembayaran='approved' AND vendors.user_id='$id_user'  order by item_keranjang.id desc";

$total_products_query = "SELECT
                            COUNT(*) AS total_products 
                        FROM
                            products
                            INNER JOIN vendors ON products.vendor_id = vendors.vendor_id
                            WHERE vendors.user_id='$id_user'";
$total_products_result = mysqli_query($conn, $total_products_query);
$total_products = mysqli_fetch_assoc($total_products_result)['total_products'];

$result = mysqli_query($conn, $query);
$stats = mysqli_fetch_assoc($result);
$total_uang = $stats['total_uang_masuk'];
$pesanan_selesai = $stats['total_pesanan_masuk'];


$query_pending = "SELECT count(item_keranjang.subtotal) as total_pesanan_masuk
                        FROM
                            item_keranjang
                        INNER JOIN products ON item_keranjang.produk_id = products.product_id
                        INNER JOIN vendors ON products.vendor_id = vendors.vendor_id
                        INNER JOIN keranjang ON item_keranjang.keranjang_id = keranjang.id
                        INNER JOIN users ON keranjang.user_id = users.user_id
                        INNER JOIN konfirmasi_pembayaran ON keranjang.id = konfirmasi_pembayaran.id_keranjang
                        where konfirmasi_pembayaran.status_pembayaran!='approved' AND vendors.user_id='$id_user'  order by item_keranjang.id desc";

$result_pend = mysqli_query($conn, $query_pending);
$stats_pend = mysqli_fetch_assoc($result_pend)['total_pesanan_masuk'];


?>

<div class="row">
    <!-- Cards for Summary Information -->
    <div class="col-md-3">
        <div class="card card-stats">
            <div class="card-body">
                <div class="row">
                    <div class="col-3">
                        <div class="icon-big text-center icon-warning">
                            <i class="fa fa-money text-warning"></i>
                        </div>
                    </div>
                    <div class="col-9">
                        <div class="numbers">
                            <p class="card-category">Pendapatan</p>
                            <h5 class="card-title">
                                <p><?php echo rupiah($total_uang); ?></p>
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card card-stats">
            <div class="card-body">
                <div class="row">
                    <div class="col-5">
                        <div class="icon-big text-center icon-success">
                            <i class="fa fa-cart-plus text-success"></i>
                        </div>
                    </div>
                    <div class="col-7">
                        <div class="numbers">
                            <p class="card-category">Total Pesanan Diproses</p>
                            <h4 class="card-title"><?php echo number_format($pesanan_selesai); ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-stats">
            <div class="card-body">
                <div class="row">
                    <div class="col-5">
                        <div class="icon-big text-center icon-info">
                            <i class="fa fa-credit-card text-info"></i>
                        </div>
                    </div>
                    <div class="col-7">
                        <div class="numbers">
                            <p class="card-category">Pending Orders</p>
                            <h4 class="card-title"><?php echo number_format($stats_pend); ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-stats">
            <div class="card-body">
                <div class="row">
                    <div class="col-5">
                        <div class="icon-big text-center icon-danger">
                            <i class="fa fa-truck text-danger"></i>
                        </div>
                    </div>
                    <div class="col-7">
                        <div class="numbers">
                            <p class="card-category">Total Produk</p>
                            <h4 class="card-title"><?php echo number_format($total_products); ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>