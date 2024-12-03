<div class="sidebar" data-color="white" data-active-color="danger">
    <div class="logo">
        <a href="index.php" class="simple-text logo-mini">
            <div class="logo-image-small">
                <img src="assets/img/logo-small.png" alt="Logo">
            </div>
        </a>
        <a href="index.php" class="simple-text logo-normal">
            FAME ADMIN
        </a>
    </div>
    <div class="sidebar-wrapper">
        <ul class="nav">
            <!-- Dashboard -->
            <li class="<?php echo ($_GET['menu'] == 'dashboard') ? 'active' : ''; ?>">
                <a href="index.php">
                    <i class="nc-icon nc-bank"></i>
                    <p>Dashboard</p>
                </a>
            </li>

            <!-- Master Data Menu -->
            <li
                class="nav-item <?php echo (in_array($_GET['menu'], ['kategori', 'produk', 'user', 'vendor'])) ? 'active' : ''; ?>">
                <a href="#masterData" data-toggle="collapse" aria-expanded="false" class="collapsed">
                    <i class="nc-icon nc-layers-3"></i>
                    <p>Master Data</p>
                </a>
                <div id="masterData"
                    class="collapse <?php echo (in_array($_GET['menu'], ['kategori', 'produk', 'user', 'vendor'])) ? 'show' : ''; ?>">
                    <ul class="nav">
                        <li class="<?php echo ($_GET['menu'] == 'kategori') ? 'active' : ''; ?>">
                            <a href="index.php?menu=kategori">
                                <i class="nc-icon nc-circle-10"></i>
                                <p>Kategori</p>
                            </a>
                        </li>
                        <li class="<?php echo ($_GET['menu'] == 'produk') ? 'active' : ''; ?>">
                            <a href="index.php?menu=produk">
                                <i class="nc-icon nc-box"></i>
                                <p>Produk</p>
                            </a>
                        </li>
                        <li class="<?php echo ($_GET['menu'] == 'user') ? 'active' : ''; ?>">
                            <a href="index.php?menu=user">
                                <i class="nc-icon nc-single-02"></i>
                                <p>User</p>
                            </a>
                        </li>
                        <li class="<?php echo ($_GET['menu'] == 'vendor') ? 'active' : ''; ?>">
                            <a href="index.php?menu=vendor">
                                <i class="nc-icon nc-badge"></i>
                                <p>Vendor</p>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Konfirmasi Pembayaran -->
            <li class="<?php echo ($_GET['menu'] == 'konfirmasi_pembayaran') ? 'active' : ''; ?>">
                <a href="index.php?menu=konfirmasi_pembayaran">
                    <i class="nc-icon nc-credit-card"></i>
                    <p>Konfirmasi Pembayaran</p>
                </a>
            </li>
            <li class="<?php echo ($_GET['menu'] == 'pesanan') ? 'active' : ''; ?>">
                <a href="index.php?menu=pesanan">
                    <i class="nc-icon nc-cart-simple"></i>
                    <p>Pesanan</p>
                </a>
            </li>


        </ul>
    </div>
</div>