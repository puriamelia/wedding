<?php
session_start();
include_once "./koneksi/db.php";
include_once "./function/global.php";
include_once("./layout/page_start.php");
include_once("./layout/page_header.php");
if (isset($_GET['checkout'])) {
    $_SESSION['balikin'] = 'keranjang';
} else {

    $_SESSION['balikin'] = '';
}

?>
<section class="vh-100">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-8 px-0 d-none d-sm-block">
                <img src="assets/img/bg_login.png" alt="Login image" class="w-100 vh-100"
                    style="object-fit: cover; object-position: left;">
            </div>
            <div class="col-sm-4 text-black">

                <div class="">
                    <i class="fas fa-crow fa-2x me-3 pt-5 mt-xl-4" style="color: #709085;"></i>
                    <span class="h1 fw-bold mb-0">Fame</span>
                    <small>Find Artists, Makeup Excellence</small>
                </div>

                <div class="d-flex align-items-center h-custom-2 px-5 ms-xl-4 mt-5 pt-5 pt-xl-0 mt-xl-n5">

                    <form style="width: 23rem;" method="post">

                        <h3 class="fw-normal mb-3 text-center pb-3" style="letter-spacing: 1px;">Log in</h3>

                        <div class="form-outline mb-4">
                            <input type="text" id="form2Example18" name="username_or_email"
                                class="form-control form-control-lg" />
                            <label class="form-label" for="form2Example18">Username/Email address</label>
                        </div>

                        <div class="form-outline mb-4">
                            <input type="password" id="form2Example28" name="password"
                                class="form-control form-control-lg" />
                            <label class="form-label" for="form2Example28">Password</label>
                        </div>

                        <div class="pt-1 mb-4">
                            <button name="login" class="btn btn-info btn-lg btn-block" type="submit">Login</button>
                        </div>
                        <?php
                        // var_dump($_POST);
                        if (isset($_POST['login'])) {
                            $kode = $_SESSION['chart_kode'];
                            $username_or_email = $_POST['username_or_email'];
                            $password = $_POST['password'];

                            // Query untuk memeriksa username atau email
                            $sql = "SELECT * FROM users WHERE( username = '$username_or_email' OR email = '$username_or_email') and role='user'";
                            $result = mysqli_query($conn, $sql);

                            // Cek apakah ada pengguna yang ditemukan
                            if (mysqli_num_rows($result) > 0) {
                                $user = mysqli_fetch_assoc($result);

                                // Verifikasi password
                                if (password_verify($password, $user['password'])) {
                                    // Sukses login, simpan data sesi
                                    $uid = $user['user_id'];
                                    mysqli_query($conn, "UPDATE keranjang set user_id='$uid',date_edited=NOW()  where user_id='$kode' or session_id='$kode'");
                                    $_SESSION['user_id'] = $user['user_id'];
                                    $_SESSION['username'] = $user['username'];
                                    $_SESSION['role'] = $user['role'];

                                    // Redirect ke halaman beranda atau halaman lainnya
                                    if (($_SESSION['balikin'] == 'keranjang')) {
                                        pindah_halaman("index.php?menu=keranjang");
                                    } else {

                                        pindah_halaman("index.php");
                                    }
                                    exit();
                                } else {
                                    $error_message = "Password salah!";
                                }
                            } else {
                                $error_message = "Username atau Email tidak ditemukan!";
                            }
                        ?>
                            <div class="alert alert-danger" role="alert">
                                <?= $error_message ?>
                            </div>

                        <?php
                        }

                        ?>

                        <!-- <p class="small mb-5 pb-lg-2"><a class="text-muted" href="#!">Lupa Password?</a></p> -->
                        <p>Pengguna baru? <a href="daftar.php" class="link-info">Daftar disini</a></p>

                    </form> <br>
                    <hr>

                </div>

            </div>

        </div>
    </div>
</section>


<?php include_once("./layout/page_script.php") ?>