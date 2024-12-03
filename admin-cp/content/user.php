<?php
// Cek aksi yang diterima dari URL
$act = isset($_GET['act']) ? $_GET['act'] : '';

// Aksi Tambah Pengguna
if ($act == 'tambah') {
    if (isset($_POST['submit'])) {
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Menggunakan password hashing
        $nama = $_POST['nama'];
        $email = $_POST['email'];
        $no_hp = $_POST['no_hp'];
        $alamat = $_POST['alamat'];
        $role = $_POST['role'];

        $query = "INSERT INTO users (username, password, nama, email, no_hp, alamat, role) 
                  VALUES ('$username', '$password', '$nama', '$email', '$no_hp', '$alamat', '$role')";
        if (mysqli_query($conn, $query)) {
            echo "<script>alert('User berhasil ditambahkan'); window.location.href='index.php?menu=user';</script>";
        } else {
            echo "Error: " . $query . "<br>" . mysqli_error($conn);
        }
    }
?>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="card-title">Tambah Pengguna</h4>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="nama">Nama</label>
                        <input type="text" class="form-control" name="nama" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="no_hp">No HP</label>
                        <input type="text" class="form-control" name="no_hp" required>
                    </div>
                    <div class="form-group">
                        <label for="alamat">Alamat</label>
                        <textarea class="form-control" name="alamat" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="role">Role</label>
                        <select name="role" class="form-control">
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                            <option value="vendor">Vendor</option>
                        </select>
                    </div>
                    <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>
    </div>
<?php
}

// Aksi Edit Pengguna
elseif ($act == 'edit') {
    $id = $_GET['id'];
    $query = "SELECT * FROM users WHERE user_id = '$id'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    if (isset($_POST['submit'])) {
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $nama = $_POST['nama'];
        $email = $_POST['email'];
        $no_hp = $_POST['no_hp'];
        $alamat = $_POST['alamat'];
        $role = $_POST['role'];

        $query = "UPDATE users SET username='$username', password='$password', nama='$nama', email='$email', 
                  no_hp='$no_hp', alamat='$alamat', role='$role' WHERE user_id='$id'";

        if (mysqli_query($conn, $query)) {
            echo "<script>alert('User berhasil diupdate'); window.location.href='index.php?menu=user';</script>";
        } else {
            echo "Error: " . $query . "<br>" . mysqli_error($conn);
        }
    }
?>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="card-title">Edit Pengguna</h4>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" name="username" value="<?php echo $user['username']; ?>"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" name="password">
                        <small>Isi jika ingin mengganti password</small>
                    </div>
                    <div class="form-group">
                        <label for="nama">Nama</label>
                        <input type="text" class="form-control" name="nama" value="<?php echo $user['nama']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" name="email" value="<?php echo $user['email']; ?>"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="no_hp">No HP</label>
                        <input type="text" class="form-control" name="no_hp" value="<?php echo $user['no_hp']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="alamat">Alamat</label>
                        <textarea class="form-control" name="alamat" required><?php echo $user['alamat']; ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="role">Role</label>
                        <select name="role" class="form-control">
                            <option value="user" <?php echo $user['role'] == 'user' ? 'selected' : ''; ?>>User</option>
                            <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                            <option value="vendor" <?php echo $user['role'] == 'vendor' ? 'selected' : ''; ?>>Vendor
                            </option>
                        </select>
                    </div>
                    <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>
    </div>
<?php
}

// Aksi Hapus Pengguna
elseif ($act == 'delete') {
    $id = $_GET['id'];
    $query = "DELETE FROM users WHERE user_id = '$id'";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('User berhasil dihapus'); window.location.href='index.php?menu=user';</script>";
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($conn);
    }
} else if ($act == 'select_vendor') {
    $result_vendors = mysqli_query($conn, "SELECT * FROM vendors ");
    $id = $_GET['id'];
    $query = "SELECT * FROM users WHERE user_id = '$id'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['vendor_id'])) {
        $vendor_id = intval($_POST['vendor_id']);

        // Simpan vendor_id ke user
        $query_update = "UPDATE vendors SET user_id = '$id' WHERE vendor_id = '$vendor_id'";
        if (mysqli_query($conn, $query_update)) {
            echo "<script>alert('Vendor berhasil dipilih!'); window.location.href='index.php?menu=user';</script>";
        } else {
            echo "<script>alert('Gagal memilih vendor! Silakan coba lagi.');</script>";
        }
    }

?>
    <div class="container mt-5">
        <h3>Pilih Vendor untuk Pengguna</h3>
        <div class="card">
            <div class="card-header bg-info text-white">
                <h4 class="card-title">Detail Pengguna</h4>
            </div>
            <div class="card-body">
                <p><strong>Nama:</strong> <?php echo htmlspecialchars($user['nama']); ?></p>
                <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header bg-primary text-white">
                <h4 class="card-title">Daftar Vendor</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="vendor_id">Pilih Vendor</label>
                        <select class="form-control" id="vendor_id" name="vendor_id" required>
                            <option value="">-- Pilih Vendor --</option>
                            <?php while ($vendor = mysqli_fetch_assoc($result_vendors)) { ?>
                                <option value="<?php echo $vendor['vendor_id']; ?>">
                                    <?php echo htmlspecialchars($vendor['name']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success">Simpan</button>
                    <a href="index.php?menu=user" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>

<?php
} else {
    $query = "SELECT * FROM users";
    $result1 = mysqli_query($conn, $query);
?>
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="card-title">Daftar Pengguna</h4>
            </div>
            <div class="card-body">
                <a href="index.php?menu=user&act=tambah" class="btn btn-success mb-3"><i class="fa fa-plus"></i> Tambah
                    Pengguna</a>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>No HP</th>
                            <th>Role</th>
                            <th>Vendor</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        while ($user1 = mysqli_fetch_assoc($result1)) {
                        ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo $user1['nama']; ?></td>
                                <td><?php echo $user1['username']; ?></td>
                                <td><?php echo $user1['email']; ?></td>
                                <td><?php echo $user1['no_hp']; ?></td>
                                <td><?php echo ucfirst($user1['role']); ?></td>
                                <td>
                                    <?php if ($user1['role'] == 'vendor') {
                                        $id = $user1['user_id'];
                                        $query = "SELECT * FROM vendors WHERE user_id = '$id'";
                                        $result = mysqli_query($conn, $query);
                                        $vendor = mysqli_fetch_assoc($result);
                                        if (mysqli_num_rows($result) > 0) {
                                            echo $vendor['name'];
                                        } else {
                                            echo "vendor belum ditetapkan"; ?>

                                        <?php
                                        }
                                        ?> <br>
                                        <a href="index.php?menu=user&act=select_vendor&id=<?php echo $user1['user_id']; ?>"
                                            class="btn btn-info btn-sm">Pilih Vendor</a>
                                    <?php
                                    }
                                    ?>
                                </td>
                                <td>
                                    <a href="index.php?menu=user&act=edit&id=<?php echo $user1['user_id']; ?>"
                                        class="btn btn-warning btn-sm">Edit</a>
                                    <a href="index.php?menu=user&act=delete&id=<?php echo $user1['user_id']; ?>"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?')">Hapus</a>
                                    <?php if ($user1['role'] == 'vendor') { ?>

                                    <?php } ?>

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