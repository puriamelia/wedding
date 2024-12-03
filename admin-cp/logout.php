<?php
session_start();
include_once "./../koneksi/db.php";  // Pastikan file koneksi ke database sudah ada
include_once "./../function/global.php";  // Pastikan file global.php sudah ada
session_destroy();
pindah_halaman("login.php");
