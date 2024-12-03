// Example JavaScript for additional interactivity (if needed)
const navItems = document.querySelectorAll(".nav-item");

navItems.forEach((item) => {
  item.addEventListener("mouseover", () => {
    item.classList.add("show-tooltip");
  });

  item.addEventListener("mouseout", () => {
    item.classList.remove("show-tooltip");
  });
});
function getParameterByName(name) {
  var url = window.location.href;
  name = name.replace(/[\[\]]/g, "\\$&");
  var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
    results = regex.exec(url);
  if (!results) return null;
  if (!results[2]) return "";
  return decodeURIComponent(results[2].replace(/\+/g, " "));
}

$(document).ready(function () {
  $("#hasil_cek").hide();
  let menu = getParameterByName("menu");

  if (menu == "produk") {
    let idp = getParameterByName("produkid");
    // URL API Anda
    const apiURL = "api.php?menu=tanggal_produk&id=" + idp;

    // Ambil data tanggal yang tidak tersedia dari API
    $.ajax({
      url: apiURL,
      method: "GET",
      dataType: "json",
      success: function (data) {
        // Ambil daftar tanggal tidak tersedia dari respons
        const availableDates = data.data;

        // Inisialisasi Flatpickr dengan tanggal tidak tersedia
        flatpickr("#flatpickr", {
          dateFormat: "Y-m-d", // Format tanggal
          // Enable hanya tanggal yang tersedia
          enable: availableDates, // Hanya tanggal yang ada dalam array data yang bisa dipilih
          disableMobile: true, // Opsional: Menonaktifkan tampilan mobile di Flatpickr
        });
      },
      error: function (xhr, status, error) {
        console.error("Error fetching data from API:", error);
        alert("Gagal mengambil data tanggal dari server.");
      },
    });

    // Validasi form saat submit
    $("#formPesan").submit(function (e) {
      var tanggal = $("#flatpickr").val();

      // Validasi jika tanggal kosong
      if (!tanggal) {
        alert("Tanggal harus dipilih.");
        e.preventDefault(); // Mencegah form untuk dikirim
        return false;
      }
    });

    $("#flatpickr").on("change", function () {
      $("#hasil_cek").show();
    });
  }
});
