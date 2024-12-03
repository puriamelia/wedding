<?php
session_start();
include_once "./../koneksi/db.php";  // Pastikan file koneksi ke database sudah ada
include_once "./../function/global.php";  // Pastikan file global.php sudah ada

// Jika form login disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mengambil data dari form login
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Cek apakah email dan password diisi
    if (empty($email) || empty($password)) {
        echo "<script>alert('Email dan Password harus diisi!');</script>";
    } else {
        // Mengamankan input dari SQL Injection
        $email = mysqli_real_escape_string($conn, $email);

        // Query untuk mencari user berdasarkan email atau username
        $query = "SELECT * FROM users WHERE (email = '$email' OR username = '$email' ) and role !='user' LIMIT 1";

        // Eksekusi query
        $result = mysqli_query($conn, $query);

        // Mengecek apakah email atau username ditemukan
        if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);

            // Memeriksa password (pastikan password di database sudah dienkripsi)
            if (password_verify($password, $row['password'])) {
                // Simpan data user ke session
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['role'] = $row['role'];  // Role (user/admin/staff) disimpan di session

                // Redirect sesuai role
                if ($row['role'] == 'admin') {
                    pindah_halaman("index.php");  // Redirect ke halaman admin
                } else {
                    pindah_halaman("index.php");   // Redirect ke halaman user biasa
                }
            } else {
                echo "<script>alert('Password salah!');</script>";
            }
        } else {
            echo "<script>alert('Email atau Username tidak ditemukan!');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <link rel="apple-touch-icon" sizes="76x76" href="assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="assets/img/favicon.png">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>Login - Admin</title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no'
        name='viewport' />
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet" />
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" rel="stylesheet">
    <!-- CSS Files -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet" />
    <link href="assets/css/custom-style.css" rel="stylesheet" />
</head>

<body class="bg-soft-pink">

    <section class="vh-100">
        <div class="container py-5 h-100">
            <div class="row d-flex align-items-center justify-content-center h-100">
                <div class="col-md-8 col-lg-7 col-xl-6">
                    <img src="./assets/draw2.svg"
                        class="img-fluid" alt="Phone image">
                </div>
                <div class="col-md-7 col-lg-5 col-xl-5 offset-xl-1">
                    <!-- Label FAME -->
                    <div class="text-center mb-4">
                        <h2 class="font-weight-bold">FAME</h2>
                    </div>
                    <form method="POST" action="">
                        <!-- Email input -->
                        <div class="form-outline mb-4">
                            <input type="text" name="email" id="form1Example13" value="<?= @$_POST['email'] ?>"
                                class="form-control form-control-lg" required />
                            <label class="form-label" for="form1Example13">Email Atau Username</label>
                        </div>

                        <!-- Password input -->
                        <div class="form-outline mb-4">
                            <input type="password" name="password" id="form1Example23"
                                class="form-control form-control-lg" required />
                            <label class="form-label" for="form1Example23">Kata Sandi</label>
                        </div>

                        <!-- Submit button -->
                        <button type="submit" class="btn btn-primary btn-lg btn-block">Masuk</button>

                        <div class="text-center mt-3">
                            <p><a href="forgot-password.php">Lupa kata sandi?</a></p>
                            <p>Belum memiliki akun? <a href="register.php">Daftar</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- JS Files -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>