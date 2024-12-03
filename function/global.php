<?php
function pindah_halaman($url)
{
?>
<script>
location.href = '<?= $url ?>';
</script>
<?php
}
function rupiah($angka)
{

    $hasil_rupiah = "Rp " . number_format($angka, 0, ',', '.');
    return $hasil_rupiah;
}
function enkrip($text)
{
    return base64_encode($text);
}
function dekrip($text)
{
    return base64_decode($text);
}

function formatTanggal($tanggal)
{
    // Mengubah tanggal menjadi format yang diinginkan
    $tanggalFormat = date('l, d F Y', strtotime($tanggal));

    // Mengubah nama hari dan bulan menjadi format Indonesia
    $hariIndonesia = array(
        'Monday'    => 'Senin',
        'Tuesday'   => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday'  => 'Kamis',
        'Friday'    => 'Jumat',
        'Saturday'  => 'Sabtu',
        'Sunday'    => 'Minggu'
    );

    $bulanIndonesia = array(
        'January'   => 'Januari',
        'February'  => 'Februari',
        'March'     => 'Maret',
        'April'     => 'April',
        'May'       => 'Mei',
        'June'      => 'Juni',
        'July'      => 'Juli',
        'August'    => 'Agustus',
        'September' => 'September',
        'October'   => 'Oktober',
        'November'  => 'November',
        'December'  => 'Desember'
    );

    // Ganti nama hari dan bulan ke dalam bahasa Indonesia
    $tanggalFormat = str_replace(array_keys($hariIndonesia), array_values($hariIndonesia), $tanggalFormat);
    $tanggalFormat = str_replace(array_keys($bulanIndonesia), array_values($bulanIndonesia), $tanggalFormat);

    return $tanggalFormat;
}
    
?>