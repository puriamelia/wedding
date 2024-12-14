<?php

session_start();
include_once "./koneksi/db.php";
include_once "./function/global.php";
if (isset($_GET['menu'])) {
    $menu = $_GET['menu'];
    if ($menu == 'tanggal_produk') {
        $id_produk = dekrip($_GET['id']);

        $sql = "SELECT * 
                        FROM produk_tersedia
                        WHERE product_id = '$id_produk' 
                        AND booked_events < max_events and tanggal >= CURDATE() 
                        ORDER BY tanggal ASC";

        // Jalankan query
        $result = mysqli_query($conn, $sql);

        // Periksa apakah ada hasil
        if (mysqli_num_rows($result) > 0) {
            $available_dates = array();

            // Loop untuk menampilkan data yang ditemukan
            while ($row = mysqli_fetch_assoc($result)) {
                $available_dates[] = $row['tanggal'];  // Masukkan tanggal ke array
            }
            echo  json_encode(['success' => true, 'data' => $available_dates]);
        } else {
            echo  json_encode(['success' => false, 'data' => []]);
        }
    } elseif ($menu == 'jam') {
        $id_produk = dekrip($_GET['id']);
        $tanggal = ($_GET['tanggal']);

        $sql = "SELECT * 
                        FROM produk_tersedia
                        WHERE product_id = '$id_produk' and tanggal='$tanggal'
                        AND booked_events < max_events
                        ORDER BY tanggal ASC";

        // Jalankan query
        $result = mysqli_query($conn, $sql);

        // Periksa apakah ada hasil
        if (mysqli_num_rows($result) > 0) {
            $available_dates = array();

            // Loop untuk menampilkan data yang ditemukan
            $row = mysqli_fetch_assoc($result);
            $jam = $row['jam_tersedia'];  // Masukkan tanggal ke array

            if($jam!=null){

                echo  json_encode(['success' => true, 'data' => $jam]);
            }
            else{
                echo  json_encode(['success' => false, 'data' => []]);

            }
        } else {
            echo  json_encode(['success' => false, 'data' => []]);
        }
    }
} else {
    echo  json_encode(['success' => false]);
}
