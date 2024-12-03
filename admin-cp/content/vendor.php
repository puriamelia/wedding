<?php
// Cek aksi yang diterima dari URL
$act = isset($_GET['act']) ? $_GET['act'] : '';

// Aksi Tambah Vendor
if ($act == 'tambah') {
    if (isset($_POST['submit'])) {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $contact_number = $_POST['contact_number'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $website = $_POST['website'];
        $url_lokasi = $_POST['url_lokasi'];
        $latitude = !empty($_POST['latitude']) ? $_POST['latitude'] : 0;
        $longitude = !empty($_POST['longitude']) ? $_POST['longitude'] : 0;

        // Foto
        $photo = $_FILES['photo']['name'];
        $target_dir = "../assets/img/vendor/";
        $target_file = $target_dir . basename($photo);
        move_uploaded_file($_FILES['photo']['tmp_name'], $target_file);

        $query = "INSERT INTO vendors (name, description, contact_number, photo, email, website, password, url_lokasi, latitude, logitude) 
                  VALUES ('$name', '$description', '$contact_number', '$photo', '$email', '$website', '$password', '$url_lokasi', '$latitude', '$longitude')";
        if (mysqli_query($conn, $query)) {
            echo "<script>alert('Vendor berhasil ditambahkan'); window.location.href='index.php?menu=vendor';</script>";
        } else {
            echo "Error: " . $query . "<br>" . mysqli_error($conn);
        }
    }
?>
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="card-title">Tambah Vendor</h4>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="name">Nama Vendor</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Deskripsi</label>
                        <textarea class="form-control" name="description" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="contact_number">Nomor Kontak</label>
                        <input type="text" class="form-control" name="contact_number">
                    </div>
                    <div class="form-group">
                        <label for="photo">Foto</label> <small>Pilih photo klik disini</small>
                        <input type="file" class="form-control" name="photo">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="website">Website</label>
                        <input type="text" class="form-control" name="website">
                    </div>
                    <div class="form-group">
                        <label for="url_lokasi">URL Lokasi</label>
                        <input type="text" class="form-control" name="url_lokasi">
                    </div>
                    <div class="form-group">
                        <label for="latitude">Latitude</label>
                        <input type="text" class="form-control" name="latitude">
                    </div>
                    <div class="form-group">
                        <label for="longitude">Longitude</label>
                        <input type="text" class="form-control" name="longitude">
                    </div>
                    <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>
    </div>
<?php
}

// Aksi Edit Vendor
elseif ($act == 'edit') {
    $id = $_GET['id'];
    $query = "SELECT * FROM vendors WHERE vendor_id = '$id'";
    $result = mysqli_query($conn, $query);
    $vendor = mysqli_fetch_assoc($result);

    if (isset($_POST['submit'])) {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $contact_number = $_POST['contact_number'];
        $email = $_POST['email'];
        $website = $_POST['website'];
        $url_lokasi = $_POST['url_lokasi'];
        $latitude = $_POST['latitude'];
        $longitude = $_POST['longitude'];

        // Update foto jika ada perubahan
        if ($_FILES['photo']['name']) {
            $photo = $_FILES['photo']['name'];
            $target_dir = "../assets/img/vendor/";
            $target_file = $target_dir . basename($photo);
            move_uploaded_file($_FILES['photo']['tmp_name'], $target_file);
        } else {
            $photo = $vendor['photo'];
        }

        $query = "UPDATE vendors SET name='$name', description='$description', contact_number='$contact_number', 
                  photo='$photo', email='$email', website='$website', url_lokasi='$url_lokasi', latitude='$latitude', 
                  logitude='$longitude' WHERE vendor_id='$id'";

        if (mysqli_query($conn, $query)) {
            echo "<script>alert('Vendor berhasil diupdate'); window.location.href='index.php?menu=vendor';</script>";
        } else {
            echo "Error: " . $query . "<br>" . mysqli_error($conn);
        }
    }
?>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="card-title">Edit Vendor</h4>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="name">Nama Vendor</label>
                        <input type="text" class="form-control" name="name" value="<?php echo $vendor['name']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Deskripsi</label>
                        <textarea class="form-control" name="description"
                            required><?php echo $vendor['description']; ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="contact_number">Nomor Kontak</label>
                        <input type="text" class="form-control" name="contact_number"
                            value="<?php echo $vendor['contact_number']; ?>">
                    </div>
                    <div class="form-group">
                        <label for="photo">Foto</label>
                        <input type="file" class="form-control" name="photo">
                        <small>Kosongkan jika tidak ingin mengganti foto</small>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" name="email" value="<?php echo $vendor['email']; ?>"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="website">Website</label>
                        <input type="text" class="form-control" name="website" value="<?php echo $vendor['website']; ?>">
                    </div>
                    <div class="form-group">
                        <label for="url_lokasi">URL Lokasi</label>
                        <input type="text" class="form-control" name="url_lokasi"
                            value="<?php echo $vendor['url_lokasi']; ?>">
                    </div>
                    <div class="form-group">
                        <label for="latitude">Latitude</label>
                        <input type="text" class="form-control" name="latitude" value="<?php echo $vendor['latitude']; ?>">
                    </div>
                    <div class="form-group">
                        <label for="longitude">Longitude</label>
                        <input type="text" class="form-control" name="longitude" value="<?php echo $vendor['logitude']; ?>">
                    </div>
                    <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>
    </div>
<?php
}

// Aksi Hapus Vendor
elseif ($act == 'delete') {
    $id = $_GET['id'];
    $query = "DELETE FROM vendors WHERE vendor_id = '$id'";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Vendor berhasil dihapus'); window.location.href='index.php?menu=vendor';</script>";
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($conn);
    }
}

// Tampilkan Daftar Vendor
else {
    $query = "SELECT * FROM vendors";
    $result = mysqli_query($conn, $query);
?>
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h4 class="card-title">Daftar Vendor</h4>
            </div>
            <div class="card-body">
                <a href="index.php?menu=vendor&act=tambah" class="btn btn-primary"><i class="fa fa-plus"></i> Tambah
                    Vendor</a>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Vendor</th>
                            <th>Deskripsi</th>
                            <th>Nomor Kontak</th>
                            <th>Email</th>
                            <th>Website</th>
                            <th>Latitude</th>
                            <th>Longitude</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        while ($vendor = mysqli_fetch_assoc($result)) {
                        ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo $vendor['name']; ?></td>
                                <td><?php echo $vendor['description']; ?></td>
                                <td><?php echo $vendor['contact_number']; ?></td>
                                <td><?php echo $vendor['email']; ?></td>
                                <td><?php echo $vendor['website']; ?></td>
                                <td><?php echo $vendor['latitude']; ?></td>
                                <td><?php echo $vendor['logitude']; ?></td>
                                <td>
                                    <a href="index.php?menu=vendor&act=edit&id=<?php echo $vendor['vendor_id']; ?>"
                                        class="btn btn-warning btn-sm">Edit</a>
                                    <a href="index.php?menu=vendor&act=delete&id=<?php echo $vendor['vendor_id']; ?>"
                                        onclick="return confirm('Apakah Anda yakin ingin menghapus vendor ini?');"
                                        class="btn btn-danger btn-sm">Hapus</a>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php
}
?>