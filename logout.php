<?php
session_start();
include_once "./koneksi/db.php";
include_once "./function/global.php";

session_destroy();
pindah_halaman("index.php");
