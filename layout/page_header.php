<header class="py-2 border-bottom " style="background-color: #CFA597; z-index: 1000;">
    <div class="container-fluid">
        <nav class="navbar navbar-expand-lg navbar-light">
            <!-- Logo -->
            <a class="navbar-brand text-white" href="index.php"
                style="font-family: 'Italianno', cursive;padding-left: 50px;">
                <h1 style="font-size: 2.5rem; margin: 0;">FAME</h1>
                <h4 style="margin: 0; font-size: 1rem;">Find Artists, Makeup Excellence</h4>
            </a>

            <!-- Toggler for Mobile -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Menu -->
            <div class="collapse navbar-collapse justify-content-between" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="" style="margin:5px;">
                        <a class=" text-white tombol" href="index.php"><i class="fa fa-home"></i> Home</a>
                    </li>
                    <li class="" style="margin:5px;">
                        <a class=" text-white tombol" href="index.php?menu=semua-produk"><i
                                class="fa fa-info-circle"></i>
                            Semua Produk</a>
                    </li>
                    <li class="" style="margin:5px;">
                        <a class=" text-white tombol" href="index.php#kategori"><i class="fa fa-th-large"></i>
                            Kategori</a>
                    </li>
                    <li class="" style="margin:5px;">
                        <a class=" text-white tombol" href="index.php#rekomendasi"><i class="fa fa-heart"></i>
                            Rekomendasi</a>
                    </li>
                    <!-- <li class="" style="margin:5px;">
                        <a class=" text-white tombol" href="#footer"><i class="fa fa-envelope"></i>
                            Contact</a>
                    </li> -->
                </ul>

                <!-- Icons Section -->
                <div class="d-flex align-items-center">
                    <a href="#" class="text-decoration-none pe-3" onclick="openSearch()">
                        <img src="assets/img/search.png" alt="Search" width="36" height="36" class="rounded-circle">
                    </a>
                    <a href="javascript:void(0);" class="position-relative">
                        <span id="cart-total"
                            class="position-absolute top-0 start-0 translate-middle-x badge rounded-pill bg-danger">
                            <?= $hitung_keranjang ?? 0 ?>
                        </span>
                        <img src="assets/img/cart.png" alt="Cart" width="36" height="36" class="rounded-circle"
                            data-bs-toggle="offcanvas" data-bs-target="#shoppingCart" aria-controls="shoppingCart">
                    </a>

                    <?php if (isset($_SESSION['user_id'])) { ?>
                        <a href="index.php?menu=profile" class="text-decoration-none pe-3">
                            <img src="assets/img/user-circle-light.png" alt="User" width="36" height="36"
                                class="rounded-circle">
                        </a>
                        <a href="logout.php" class="text-decoration-none pe-3">
                            <img src="assets/img/sign-out-thin.png" alt="Sign Out" width="36" height="36"
                                class="rounded-circle">
                        </a>
                    <?php } else { ?>
                        <a href="login.php" class="text-decoration-none pe-3">
                            <img src="assets/img/user-circle-light.png" alt="Login" width="36" height="36"
                                class="rounded-circle">
                        </a>
                    <?php } ?>
                </div>
                <div id="searchOverlay" class="overlay">
                    <div class="popup">
                        <span class="close-btn" onclick="closeSearch()">&times;</span>
                        <h3>Pencarian</h3>
                        <form action="index.php" method="GET">
                            <input type="hidden" name="menu" value='semua-produk'>
                            <input type="text" name="filter" placeholder="Cari sesuatu..." class="search-input" required>
                            <button type="submit" class="search-btn">Cari</button>
                        </form>
                    </div>
                </div>

            </div>
        </nav>
    </div>
</header>