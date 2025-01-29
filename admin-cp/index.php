<?php
error_reporting(0);
session_start();

include_once "./../koneksi/db.php";
include_once "./../function/global.php";
$role = $_SESSION['role'];
$id_user = $_SESSION['user_id'];

if (!isset($_SESSION['user_id']) && !isset($_SESSION['role'])) {
  // Jika belum login, arahkan ke halaman login
  pindah_halaman("login.php");
  exit(); // Pastikan tidak ada proses lain yang dilakukan setelah redirect
}
if ($role == 'user') {
  pindah_halaman("login.php");
}

$user = mysqli_query($conn, "SELECT users.*  FROM users where user_id='$id_user'");
$user = mysqli_fetch_assoc($user);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <link rel="apple-touch-icon" sizes="76x76" href="assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="assets/img/favicon.png">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <title>Fame - Control Admin</title>
  <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no'
    name='viewport' />
  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet" />
  <link href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" rel="stylesheet">
  <!-- CSS Files -->
  <link href="assets/css/bootstrap.min.css" rel="stylesheet" />
  <link href="assets/css/paper-dashboard.css?v=2.0.1" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

  <!-- Custom Styles -->
  <link href="assets/css/custom-style.css" rel="stylesheet" />

  <style>
    /* Navbar black background */
    .navbar {
      background-color: #b6b8b6;
      /* Background navbar tetap putih */
      color: #000;
      /* Warna teks navbar menjadi hitam */
    }

    .navbar .navbar-brand,
    .navbar .navbar-nav .nav-link {
      color: #000;
      /* Warna teks navbar dan link menjadi hitam */
    }

    .navbar .navbar-brand:hover,
    .navbar .navbar-nav .nav-link:hover {
      color: #fff;
      /* Warna saat hover menjadi sedikit lebih terang (misalnya #555) */
    }

    /* Styling untuk ikon toggler */
    .navbar-toggler-icon {
      background-color: #fff;
      /* Mengubah warna ikon burger menjadi putih */
      width: 30px;
      /* Lebar ikon burger */
      height: 3px;
      /* Tinggi garis hamburger */
      display: block;
      position: relative;
    }

    .navbar-toggler-icon::before,
    .navbar-toggler-icon::after {
      content: '';
      position: absolute;
      width: 100%;
      height: 3px;
      background-color: #fff;
      /* Garis-garis berwarna putih */
      left: 0;
    }

    .navbar-toggler-icon::before {
      top: -8px;
      /* Menempatkan garis pertama di atas */
    }

    .navbar-toggler-icon::after {
      top: 8px;
      /* Menempatkan garis kedua di bawah */
    }

    /* Footer sticky at the bottom */
    .footer {

      position: fixed;
      margin-top: 100px;
      bottom: 0;
      width: 100%;
    }

    /* Custom padding and spacing */
    .footer-nav ul {
      padding-left: 0;
      margin-bottom: 0;
    }

    .footer-nav ul li {
      display: inline-block;
      margin-right: 15px;
    }

    /* Responsive styles for mobile screens */
    @media (max-width: 767px) {
      .footer-nav ul {
        text-align: center;
      }

      .footer-nav ul li {
        display: block;
        margin-bottom: 10px;
      }
    }
  </style>

</head>

<body>

  <div class="wrapper">
    <!-- Sidebar -->

    <?php
    if ($role == 'admin') {
      include "layout/sidebar.php";
    } else {

      include "layout/sidebar_vendor.php";
    }
    ?>

    <!-- Main Panel -->
    <div class="main-panel">
      <!-- Navbar -->
      <nav class="navbar navbar-expand-lg  ">
        <a class="navbar-brand" href="#">Hallo, <?= $user['nama'] ?>(<?= strtoupper($role) ?>)</a>
        <button class="navbar-toggler" type="button" style="background: #000;" data-toggle="collapse"
          data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false"
          aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ml-auto">
            <li class="nav-item active">
              <a class="nav-link" href="#">Dashboard</a>
            </li>
            <!-- <li class="nav-item">
                            <a class="nav-link" href="#">Settings</a>
                        </li> -->
            <li class="nav-item">
              <a class="nav-link" href="logout.php">Logout</a>
            </li>
          </ul>
        </div>
      </nav>

      <!-- Content -->
      <div class="content mt-5">
        <div class="container-fluid">

          <?php
          if (!isset($_GET['menu'])) {
            if ($role == 'admin') {

              include_once("./content/halaman_dashboard.php");
            } else {

              include_once("./content/halaman_dashboard_vendor.php");
            }
          }
          ?>
          <!-- Table of Data -->
          <div class="row mt-4">
            <?php
            if (isset($_GET['menu'])) {
              $menu = mysqli_escape_string($conn, $_GET['menu']);
              if ($role == 'admin') {
                if ($menu == 'kategori') {
                  include_once("./content/kategori.php");
                } else  if ($menu == 'produk') {
                  include_once("./content/produk.php");
                } else  if ($menu == 'user') {
                  include_once("./content/user.php");
                } else  if ($menu == 'vendor') {
                  include_once("./content/vendor.php");
                } else  if ($menu == 'konfirmasi_pembayaran') {
                  include_once("./content/pembayaran.php");
                } else  if ($menu == 'pesanan') {
                  include_once("./content/pesanan.php");
                }
                else  if ($menu == 'pendapatan') {
                  include_once("./content/pendapatan.php");
                }
              } else if ($role == 'vendor') {
                if ($menu == 'produk') {
                  include_once("./content/produk.php");
                } else  if ($menu == 'pesanan') {
                  include_once("./content/pesanan.php");
                }
                 else  if ($menu == 'diskusi') {
                  include_once("./content/diskusi.php");
                }
                 else  if ($menu == 'pendapatan') {
                  include_once("./content/pendapatan.php");
                }
              }
            }
            ?>
          </div>

        </div>
      </div>

      <!-- Footer -->
      <footer class="footer mt-auto py-3 bg-dark text-white">
        <div class="container-fluid">
          <div class="row">
            <!-- Footer Navigation -->

            <!-- Footer Copyright -->
            <div class="col-md-6 text-md-end text-center">
              <span class="copyright">
                © <script>
                  document.write(new Date().getFullYear())
                </script>, dibuat dengan <i class="fa fa-heart text-danger"></i> oleh Tim Fame
              </span>
            </div>
          </div>
        </div>
      </footer>



    </div>
  </div>



  <script src="assets/js/core/jquery.min.js"></script>
  <script src="assets/js/core/popper.min.js"></script>
  <script src="assets/js/core/bootstrap.min.js"></script>
  <script src="assets/js/plugins/perfect-scrollbar.jquery.min.js"></script>
  <!-- Control Center for Paper Dashboard -->
  <script src="assets/js/paper-dashboard.min.js?v=2.0.1" type="text/javascript"></script>


  <!-- Core JS Files -->
  <script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
  <script>
    $(document).ready(function() {
      $('#data_pesanan').DataTable({
        dom: 'Bfrtip', // Menampilkan tombol di atas tabel
        buttons: [{
            extend: 'excelHtml5', // Ekspor ke Excel
            text: 'Export ke Excel' // Tombol kustom (opsional)
          },
          {
            extend: 'pdfHtml5', // Ekspor ke PDF
            text: 'Export ke PDF', // Tombol kustom (opsional)
            orientation: 'landscape', // Atur orientasi menjadi landscape
            pageSize: 'A4', // Pilih ukuran kertas (opsional)
            customize: function(doc) {
              doc.defaultStyle.fontSize = 10; // Atur ukuran font (opsional)
            }
          },
          {
            extend: 'print', // Cetak tabel
            text: 'Cetak Tabel' // Tombol kustom (opsional)
          }
        ]
      });
    });

    $(document).ready(function() {
      $('#data_produk').DataTable({
        dom: 'Bfrtip', // Menampilkan tombol di atas tabel
        buttons: [{
            extend: 'excelHtml5', // Ekspor ke Excel
            text: 'Export ke Excel' // Tombol kustom (opsional)
          },
          {
            extend: 'pdfHtml5', // Ekspor ke PDF
            text: 'Export ke PDF', // Tombol kustom (opsional)
            orientation: 'landscape', // Atur orientasi menjadi landscape
            pageSize: 'A4', // Pilih ukuran kertas (opsional)
            customize: function(doc) {
              doc.defaultStyle.fontSize = 10; // Atur ukuran font (opsional)
            }
          },
          {
            extend: 'print', // Cetak tabel
            text: 'Cetak Tabel' // Tombol kustom (opsional)
          }
        ]
      });
    });

    
$(document).ready(function () {
  $("#jam_acara").on("input", function () {
      const jamAcaraInput = $(this).val();

      // Regular expression untuk validasi format HH:MM
      const jamRegex = /^([01]\d|2[0-3]):[0-5]\d$/;

      // Memisahkan input berdasarkan koma
      const jamList = jamAcaraInput.split(",");

      // Validasi setiap elemen dalam array
      const isValid = jamList.every(function (jam) {
          return jamRegex.test(jam.trim());
      });

      // Memberikan feedback validasi
      if (!isValid && jamAcaraInput.length > 0) {
        $("#tmb_simpan").prop('disabled', true);
          $("#jam_acara_feedback").text("Format jam salah! Gunakan format HH:MM dan pisahkan dengan koma.");
      } else {
          $("#jam_acara_feedback").text(""); // Kosongkan feedback jika valid
          $("#tmb_simpan").prop('disabled', false);
      }
  });
});

  </script>
</body>

</html>