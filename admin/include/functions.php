<?php
  // untuk users.php dan manage_admin.php
  function addUser($conn, $email, $username, $password, $role) {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO pengguna (email, username, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $email, $username, $hashed_password, $role);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
  }

  function authUser($conn, $email, $password) {
    $stmt = $conn->prepare("SELECT id_pengguna, username, password, email, role FROM pengguna WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
      $user = $result->fetch_assoc();
      if (password_verify($password, $user['password'])) {
        return [
          'status' => 'success',
          'id_pengguna' => $user['id_pengguna'],
          'username' => $user['username'],
          'email' => $user['email'],
          'role' => $user['role']
        ];
      } else {
        return ['status' => 'wrong_password'];
      }
    } else {
      return ['status' => 'email_not_found'];
    }
  }

  function editUser($conn, $id_pengguna, $email, $username, $password = null, $role = null) {
    $stmt = $conn->prepare("SELECT id_pengguna FROM pengguna WHERE email = ? AND id_pengguna != ?");
    $stmt->bind_param("si", $email, $id_pengguna);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
      return ['status' => 'error', 'message' => 'Email sudah dipakai!'];
    }
    $stmt->close();

    // validasi username
    $stmt = $conn->prepare("SELECT id_pengguna FROM pengguna WHERE username = ? AND id_pengguna != ?");
    $stmt->bind_param("si", $username, $id_pengguna);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
      return ['status' => 'error', 'message' => 'Username sudah dipakai'];
    }
    $stmt->close();

    // Validasi perubahan berdasarkan tabel di db
    if ($password && $role) {
      $hashed_password = password_hash($password, PASSWORD_DEFAULT);
      $stmt = $conn->prepare("UPDATE pengguna SET email=?, username=?, password=?, role=? WHERE id_pengguna=?");
      $stmt->bind_param("ssssi", $email, $username, $hashed_password, $role, $id_pengguna);
    } elseif ($password) {
      $hashed_password = password_hash($password, PASSWORD_DEFAULT);
      $stmt = $conn->prepare("UPDATE pengguna SET email=?, username=?, password=? WHERE id_pengguna=?");        $stmt->bind_param("sssi", $email, $username, $hashed_password, $id_pengguna);
    } elseif ($role) {
      $stmt = $conn->prepare("UPDATE pengguna SET email=?, username=?, role=? WHERE id_pengguna=?");
      $stmt->bind_param("sssi", $email, $username, $role, $id_pengguna);
    } else {
      $stmt = $conn->prepare("UPDATE pengguna SET email=?, username=? WHERE id_pengguna=?");
      $stmt->bind_param("ssi", $email, $username, $id_pengguna);
    }

    if ($stmt->execute()) {
      return ['status' => 'success', 'message' => 'User berhasil diperbarui!'];
    } else {
      return ['status' => 'error', 'message' => 'Gagal memperbarui user: ' . $conn->error];
    }
  }

  function deleteUser($conn, $id_pengguna) {
    $stmt = $conn->prepare("DELETE FROM pengguna WHERE id_pengguna=?");
    $stmt->bind_param("i", $id_pengguna);
    if ($stmt->execute()) {
      return ['status' => 'success', 'message' => 'User berhasil dihapus!'];
    } else {
      return ['status' => 'error', 'message' => 'Gagal menghapus user: ' . $conn->error];
    }
  }

  // untuk dasboard.php
  function getDashboardStats($conn) {
    $stats = [
        'total_users' => 0,
        'total_products' => 0,
        'total_stores' => 0,
        'total_reviews' => 0
    ];

    // Total pengguna dengan role user
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM pengguna WHERE role = 'user'");
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
      $stats['total_users'] = $row['total'];
    }
    $stmt->close();

    // Total produk
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM produk");
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
      $stats['total_products'] = $row['total'];
    }
    $stmt->close();

    // Total toko
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM toko");
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
      $stats['total_stores'] = $row['total'];
    }
    $stmt->close();

    // Total ulasan
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM ulasan");
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
      $stats['total_reviews'] = $row['total'];
    }
    $stmt->close();

    return $stats;
  }

    // untuk products.php
  function addStore($conn, $nama_toko, $deskripsi, $alamat, $kontak, $gambar, $created_by) {
    $target_dir = "uploads/stores/";
    $file_extension = pathinfo($gambar["name"], PATHINFO_EXTENSION);
    $new_filename = uniqid() . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;

    if (move_uploaded_file($gambar["tmp_name"], $target_file)) {
      $sql = "INSERT INTO toko (nama_toko, deskripsi, alamat, kontak, gambar, created_by) VALUES (?, ?, ?, ?, ?, ?)";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("sssssi", $nama_toko, $deskripsi, $alamat, $kontak, $target_file, $created_by);
      return $stmt->execute();
    }
    return false;
  }

  function getStoreById($conn, $id_toko) {
    $stmt = $conn->prepare("SELECT * FROM toko WHERE id_toko = ?");
    $stmt->bind_param("i", $id_toko);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
  }

  function editStore($conn, $id_toko, $nama_toko, $deskripsi, $alamat, $kontak, $gambar) {
    $target_file = "";
    if ($gambar['size'] > 0) {
      $target_dir = "uploads/stores/";
      $file_extension = pathinfo($gambar["name"], PATHINFO_EXTENSION);
      $new_filename = uniqid() . '.' . $file_extension;
      $target_file = $target_dir . $new_filename;
        move_uploaded_file($gambar["tmp_name"], $target_file);
    }

    $sql = "UPDATE toko SET nama_toko = ?, deskripsi = ?, alamat = ?, kontak = ?";
    $sql .= $target_file ? ", gambar = ?" : "";
    $sql .= " WHERE id_toko = ?";

    $stmt = $conn->prepare($sql);
    if ($target_file) {
      $stmt->bind_param("sssssi", $nama_toko, $deskripsi, $alamat, $kontak, $target_file, $id_toko);
    } else {
      $stmt->bind_param("ssssi", $nama_toko, $deskripsi, $alamat, $kontak, $id_toko);
    }

    if ($stmt->execute()) {
      return ['status' => 'success'];
    } else {
      return ['status' => 'error', 'message' => $conn->error];
    }
  }

  function deleteStore($conn, $id_toko) {
    $sql = "DELETE FROM toko WHERE id_toko = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_toko);

    if ($stmt->execute()) {
      return ['status' => 'success'];
    } else {
      return ['status' => 'error', 'message' => $conn->error];
    }
  }

  function addProduct($conn, $nama_produk, $deskripsi, $harga, $id_toko, $id_kategori, $gambar) {
    $nama_produk = $conn->real_escape_string($nama_produk);
    $deskripsi = $conn->real_escape_string($deskripsi);
    $harga = floatval($harga);
    $id_toko = intval($id_toko);
    $id_kategori = intval($id_kategori);

    $target_dir = "uploads/products/";
    $target_file = $target_dir . basename($gambar["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

    $check = getimagesize($gambar["tmp_name"]);
    if($check !== false) {
      $uploadOk = 1;
    } else {
      return false;
    }

    // cek ukuran , 500000 = 5mb
    if ($gambar["size"] > 500000) {
      return false;
    }

    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
      return false;
    }

    if (move_uploaded_file($gambar["tmp_name"], $target_file)) {
      $sql = "INSERT INTO produk (nama_produk, deskripsi, harga, id_toko, id_kategori, gambar)
              VALUES ('$nama_produk', '$deskripsi', $harga, $id_toko, $id_kategori, '$target_file')";
      if ($conn->query($sql) === TRUE) {
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }

  function editProduct($conn, $id_produk, $nama_produk, $deskripsi, $harga, $id_toko, $id_kategori, $gambar) {
    $id_produk = intval($id_produk);
    $nama_produk = $conn->real_escape_string($nama_produk);
    $deskripsi = $conn->real_escape_string($deskripsi);
    $harga = floatval($harga);
    $id_toko = intval($id_toko);
    $id_kategori = intval($id_kategori);

    $sql = "UPDATE produk SET
            nama_produk = '$nama_produk',
            deskripsi = '$deskripsi',
            harga = $harga,
            id_toko = $id_toko,
            id_kategori = $id_kategori";

    if (!empty($gambar['name'])) {
      $target_dir = "uploads/products/";
      $target_file = $target_dir . basename($gambar["name"]);
      $uploadOk = 1;
      $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

      $check = getimagesize($gambar["tmp_name"]);
      if($check !== false) {
        $uploadOk = 1;
      } else {
        return ['status' => 'error', 'message' => 'File yang dipilih bukan gambar!'];
      }

    // cek ukuran , 500000 = 5mb
    if ($gambar["size"] > 500000) {
      return ['status' => 'error', 'message' => 'File terlalu besar!'];
    }

    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
      return ['status' => 'error', 'message' => 'Maaf, hanya format JPG, JPEG, & PNG yang diperbolehkan.'];
    }

    if (move_uploaded_file($gambar["tmp_name"], $target_file)) {
      $sql .= ", gambar = '$target_file'";
      } else {
        return ['status' => 'error', 'message' => 'Gagal mengupload gambar!'];
      }
    }

    $sql .= " WHERE id_produk = $id_produk";

    if ($conn->query($sql) === TRUE) {
      return ['status' => 'success', 'message' => 'Product berhasil diperbarui!'];
    } else {
      return ['status' => 'error', 'message' => $conn->error];
    }
  }

  function getProduct($conn, $id_produk) {
    $id_produk = intval($id_produk);
    $sql = "SELECT * FROM produk WHERE id_produk = $id_produk";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
      return $result->fetch_assoc();
    } else {
      return null;
    }
  }

  function deleteProduct($conn, $id_produk) {
    $id_produk = intval($id_produk);

    $product = getProduct($conn, $id_produk);
    if ($product) {
      if (file_exists($product['gambar'])) {
        unlink($product['gambar']);
      }

      $sql = "DELETE FROM produk WHERE id_produk = $id_produk";
      if ($conn->query($sql) === TRUE) {
        return ['status' => 'success', 'message' => 'Product berhasil dihapus!'];
      } else {
        return ['status' => 'error', 'message' => $conn->error];
      }
    } else {
      return ['status' => 'error', 'message' => 'Product not found'];
    }
  }

  // untuk revuews.php
  function getAllReviews($conn) {
    $sql = "SELECT r.*, p.nama_produk, u.username
            FROM review r
            JOIN produk p ON r.id_produk = p.id_produk
            JOIN pengguna u ON r.id_pengguna = u.id_pengguna
            ORDER BY r.tanggal_review DESC";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  function getAllProducts($conn) {
    $sql = "SELECT id_produk, nama_produk FROM produk";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  function addReview($conn, $id_produk, $id_pengguna, $rating, $komentar) {
    $sql = "INSERT INTO review (id_produk, id_pengguna, rating, komentar) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiis", $id_produk, $id_pengguna, $rating, $komentar);
    return $stmt->execute();
  }

  function deleteReview($conn, $id_review) {
    $sql = "DELETE FROM review WHERE id_review = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_review);
    return $stmt->execute();
  }
?>