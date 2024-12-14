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

    
    $("#flatpickr").on("change", function () {
      let tgl = this.value;
      let idp = getParameterByName("produkid");
      // URL API Anda
      const apiURL = "api.php?menu=jam&id=" + idp + "&tanggal=" + tgl;
    
      $.ajax({
        url: apiURL,
        method: "GET",
        dataType: "json",
        success: function (data) {
          if (data.success) {
            const jam = data.data;
    
            // Validasi jika jam kosong
            if (!jam || jam.trim() === "") {
              $("#pesan").html("Jadwal Tidak Tersedia");
              $("#timeOptions").html(""); // Bersihkan elemen select jika ada
              return;
            }

            $("#hasil_cek").show();
    
            $("#pesan").html("Jadwal Tersedia");
            const times = jam.split(",");
    
            // Membuat elemen select dengan opsi
            let selectHtml = '<select id="timeSelect" name="jam" class="form-control">';
            selectHtml += `<option value="">Silahkan Pilih Jam</option>`;
            times.forEach((time) => {
              selectHtml += `<option value="${time}">${time}</option>`;
            });
            selectHtml += "</select>";
    
            // Menambahkan elemen select ke dalam div dengan id "timeOptions"
            $("#timeOptions").html(selectHtml);
          } else {
            $("#pesan").html("Jadwal Tidak Tersedia");
            $("#timeOptions").html(""); // Bersihkan elemen select jika ada
          }
        },
        error: function (xhr, status, error) {
          console.error("Error fetching data from API:", error);
          alert("Gagal mengambil data tanggal dari server.");
        },
      });
    });
    

    // Validasi form saat submit
    $("#formPesan").submit(function (e) {
      var tanggal = $("#flatpickr").val();
      var jam = $("#timeSelect").val(); // Ambil nilai dari select time

      // Validasi jika tanggal kosong
      if (!tanggal) {
        alert("Tanggal harus dipilih.");
        e.preventDefault(); // Mencegah form untuk dikirim
        return false;
      }

      // Validasi jika jam kosong
      if (!jam) {
        alert("Jam harus dipilih.");
        e.preventDefault(); // Mencegah form untuk dikirim
        return false;
      }
      
    });

    

  }
});



