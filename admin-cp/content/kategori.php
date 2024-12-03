<?php
// Mendapatkan aksi yang diinginkan dari URL
$action = isset($_GET['act']) ? $_GET['act'] : ''; // Jika act kosong, berarti tampilkan tabel

// Menangani aksi untuk tambah, edit, dan hapus
if ($action == 'tambah') {
    // Tambah kategori
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $category_name = $_POST['category_name'];
        $category_description = $_POST['category_description'];
        $category_photo = ''; // Default jika tidak ada gambar

        // Handle upload photo
        if (isset($_FILES['category_photo']) && $_FILES['category_photo']['error'] == 0) {
            $photo_name = $_FILES['category_photo']['name'];
            $photo_tmp = $_FILES['category_photo']['tmp_name'];
            $photo_size = $_FILES['category_photo']['size'];

            // Cek ekstensi dan ukuran file
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            $ext = strtolower(pathinfo($photo_name, PATHINFO_EXTENSION));
            if (in_array($ext, $allowed_extensions) && $photo_size < 2 * 1024 * 1024) {
                $photo_new_name = time() . '_' . $photo_name;
                $photo_path = '../assets/img/kategori/' . $photo_new_name;
                move_uploaded_file($photo_tmp, $photo_path); // Pindahkan file ke folder uploads
                $category_photo = $photo_new_name; // Simpan nama foto
            } else {
                echo "Format gambar tidak valid atau ukuran terlalu besar.";
                exit;
            }
        }

        // Query untuk menambah kategori
        $query = "INSERT INTO categories (category_name, category_descripsion, category_photo) 
                  VALUES ('$category_name', '$category_description', '$category_photo')";
        $result = mysqli_query($conn, $query);

        // Redirect jika berhasil menambah kategori
        if ($result) {
            pindah_halaman('index.php?menu=kategori');
        } else {
            echo "Error adding category." . mysqli_error($conn);
        }
    }
?>
    <!-- Form Tambah Kategori -->
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="card-title">Tambah Kategori</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="index.php?menu=kategori&act=tambah" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="category_name">Nama Kategori</label>
                        <input type="text" class="form-control" id="category_name" name="category_name" required>
                    </div>
                    <div class="form-group">
                        <label for="category_description">Deskripsi Kategori</label>
                        <textarea class="form-control" id="category_description" name="category_description"
                            required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="category_photo">Foto Kategori</label>
                        klik ini untuk menambah photo
                        <input type="file" required class="form-control-file" id="category_photo" name="category_photo">
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="index.php?menu=kategori" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>

<?php
} else if ($action == 'edit') {
    // Edit kategori
    $id = $_GET['id'];

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $category_name = $_POST['category_name'];
        $category_description = $_POST['category_description'];
        $category_photo = $_POST['existing_photo']; // Menyimpan foto lama jika tidak ada foto baru

        // Handle upload photo
        if (isset($_FILES['category_photo']) && $_FILES['category_photo']['error'] == 0) {
            $photo_name = $_FILES['category_photo']['name'];
            $photo_tmp = $_FILES['category_photo']['tmp_name'];
            $photo_size = $_FILES['category_photo']['size'];

            // Cek ekstensi dan ukuran file
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            $ext = strtolower(pathinfo($photo_name, PATHINFO_EXTENSION));
            if (in_array($ext, $allowed_extensions) && $photo_size < 2 * 1024 * 1024) {
                $photo_new_name = time() . '_' . $photo_name;
                $photo_path = '../assets/img/kategori/' . $photo_new_name;
                move_uploaded_file($photo_tmp, $photo_path); // Pindahkan file ke folder uploads
                $category_photo = $photo_new_name; // Simpan nama foto
            } else {
                echo "Format gambar tidak valid atau ukuran terlalu besar.";
                exit;
            }
        }

        // Query untuk mengupdate kategori
        $query = "UPDATE categories SET category_name='$category_name', category_descripsion='$category_description', category_photo='$category_photo' WHERE category_id='$id'";
        $result = mysqli_query($conn, $query);

        // Redirect jika berhasil
        if ($result) {
            pindah_halaman('index.php?menu=kategori');
        } else {
            echo "Error updating category.";
        }
    }

    // Ambil data kategori yang akan diedit
    $query = "SELECT * FROM categories WHERE category_id='$id'";
    $result = mysqli_query($conn, $query);
    $category = mysqli_fetch_assoc($result);
?>
    <!-- Form Edit Kategori -->
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="card-title">Edit Kategori</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="index.php?menu=kategori&act=edit&id=<?php echo $category['category_id']; ?>"
                    enctype="multipart/form-data">
                    <input type="hidden" name="existing_photo" value="<?php echo $category['category_photo']; ?>">
                    <div class="form-group">
                        <label for="category_name">Nama Kategori</label>
                        <input type="text" class="form-control" id="category_name" name="category_name"
                            value="<?php echo $category['category_name']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="category_description">Deskripsi Kategori</label>
                        <textarea class="form-control" id="category_description" name="category_description"
                            required><?php echo $category['category_descripsion']; ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="category_photo">Foto Kategori</label>
                        klik ini untuk mengganti photo
                        <input type="file" class="form-control" id="category_photo" name="category_photo">
                        <?php if ($category['category_photo']) { ?>
                            <img src="../assets/img/kategori/<?php echo $category['category_photo']; ?>" alt="Foto Kategori"
                                width="100">
                        <?php } ?>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="index.php?menu=kategori" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>

<?php
} else if ($action == 'hapus') {
    // Hapus kategori
    $id = $_GET['id'];

    // Query untuk menghapus kategori
    $query = "SELECT category_photo FROM categories WHERE category_id='$id'";
    $result = mysqli_query($conn, $query);
    $category = mysqli_fetch_assoc($result);
    if ($category['category_photo']) {
        unlink('../assets/img/kategori/' . $category['category_photo']); // Hapus file foto jika ada
    }

    $query = "DELETE FROM categories WHERE category_id='$id'";
    $result = mysqli_query($conn, $query);

    // Redirect jika berhasil menghapus
    if ($result) {
        pindah_halaman('index.php?menu=kategori');
    } else {
        echo "Error deleting category.";
    }
} else {
    // Menampilkan tabel kategori jika tidak ada act (kosong)
    $query = "SELECT * FROM categories";
    $result = mysqli_query($conn, $query);
?>
    <!-- Tabel Kategori -->
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="card-title">Tabel Kategori</h4>
            </div>
            <div class="card-body">
                <a href="index.php?menu=kategori&act=tambah" class="btn btn-danger"><i class="fa fa-plus"></i>Tambah
                    Categori</a>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nama Kategori</th>
                                <th>Deskripsi</th>
                                <th>Foto</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
                            while ($row = mysqli_fetch_assoc($result)) { ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><?php echo $row['category_name']; ?></td>
                                    <td><?php echo $row['category_descripsion']; ?></td>
                                    <td>
                                        <?php if ($row['category_photo']) { ?>
                                            <img src="../assets/img/kategori/<?php echo $row['category_photo']; ?>" width="50">
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <a href="index.php?menu=kategori&act=edit&id=<?php echo $row['category_id']; ?>"
                                            class="btn btn-sm btn-warning">Edit</a>
                                        <a href="index.php?menu=kategori&act=hapus&id=<?php echo $row['category_id']; ?>"
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')">Hapus</a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php
}
?>