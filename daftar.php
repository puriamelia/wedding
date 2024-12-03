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


if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $nama = $_POST['nama'];
    $email = $_POST['email'];

    // Validasi jika password dan confirm password tidak cocok
    if ($password != $confirm_password) {
        $error_message = "Password dan Konfirmasi Password tidak cocok!";
    } else {
        // Enkripsi password sebelum disimpan di database
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Cek apakah username atau email sudah ada
        $sql = "SELECT * FROM users WHERE username = '$username' OR email = '$email'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            $error_message = "Username atau Email sudah terdaftar!";
        } else {
            // Query untuk memasukkan data user baru
            $sql_insert = "INSERT INTO users (username, password, nama, email, role) 
                           VALUES ('$username', '$hashed_password', '$nama', '$email', 'user')";
            if (mysqli_query($conn, $sql_insert)) {
                $success_message = "Pendaftaran berhasil! Silakan login.";
                // Redirect ke halaman login setelah berhasil daftar
                pindah_halaman("login.php");
                exit();
            } else {
                $error_message = "Terjadi kesalahan saat mendaftar. Silakan coba lagi.";
            }
        }
    }
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

                    <form style="width: 23rem;" method="POST">
                        <h3 class="fw-normal mb-3 text-center pb-3" style="letter-spacing: 1px;">Daftar</h3>

                        <!-- Input Username -->
                        <div class="form-outline mb-4">
                            <input type="text" id="username" name="username" class="form-control form-control-lg"
                                required />
                            <label class="form-label" for="username">Username</label>
                        </div>

                        <!-- Input Nama -->
                        <div class="form-outline mb-4">
                            <input type="text" id="nama" name="nama" class="form-control form-control-lg" required />
                            <label class="form-label" for="nama">Nama Lengkap</label>
                        </div>

                        <!-- Input Email -->
                        <div class="form-outline mb-4">
                            <input type="email" id="email" name="email" class="form-control form-control-lg" required />
                            <label class="form-label" for="email">Email</label>
                        </div>

                        <!-- Input Password -->
                        <div class="form-outline mb-4">
                            <input type="password" id="password" name="password" class="form-control form-control-lg"
                                required />
                            <label class="form-label" for="password">Password</label>
                        </div>

                        <!-- Input Confirm Password -->
                        <div class="form-outline mb-4">
                            <input type="password" id="confirm_password" name="confirm_password"
                                class="form-control form-control-lg" required />
                            <label class="form-label" for="confirm_password">Konfirmasi Password</label>
                        </div>
                        <!-- Menampilkan Error atau Success Message -->
                        <?php if (isset($error_message)) { ?>
                            <div class="alert alert-danger" role="alert">
                                <?= $error_message ?>
                            </div>
                        <?php } elseif (isset($success_message)) { ?>
                            <div class="alert alert-success" role="alert">
                                <?= $success_message ?>
                            </div>
                        <?php } ?>

                        <!-- Button Daftar -->
                        <div class="pt-1 mb-4">
                            <button name="register" class="btn btn-info btn-lg btn-block"
                                onclick="return confirm('Apakh kamu yakin data yg dimasukan sudah benar??')"
                                type="submit">Daftar</button>
                        </div>



                        <p>Sudah punya akun? <a href="login.php" class="link-info">Login disini</a></p>

                    </form>


                    <br>
                    <hr>

                </div>

            </div>

        </div>
    </div>
</section>


<?php include_once("./layout/page_script.php") ?>