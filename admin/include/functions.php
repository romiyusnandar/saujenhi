<?php
  // untuk users.php dan manage_admin.php

  function authUser($conn, $email, $password) {
    $stmt = $conn->prepare("SELECT id_pengguna, username, password, email, role FROM pengguna WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($id_pengguna, $username, $hashed_password, $email_db, $role);

    if ($stmt->fetch()) {
        if (password_verify($password, $hashed_password)) {
            return [
                'status' => 'success',
                'id_pengguna' => $id_pengguna,
                'username' => $username,
                'email' => $email_db,
                'role' => $role
            ];
        } else {
            return ['status' => 'wrong_password'];
        }
    } else {
        return ['status' => 'email_not_found'];
    }
}

function addUser($conn, $email, $username, $password, $role) {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO pengguna (email, username, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $email, $username, $hashed_password, $role);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

function editUser($conn, $id_pengguna, $email, $username, $password = null, $role = null) {
    // Check if email is already in use
    $stmt = $conn->prepare("SELECT id_pengguna FROM pengguna WHERE email = ? AND id_pengguna != ?");
    $stmt->bind_param("si", $email, $id_pengguna);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->close();
        return ['status' => 'error', 'message' => 'Email sudah dipakai!'];
    }
    $stmt->close();

    // Check if username is already in use
    $stmt = $conn->prepare("SELECT id_pengguna FROM pengguna WHERE username = ? AND id_pengguna != ?");
    $stmt->bind_param("si", $username, $id_pengguna);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->close();
        return ['status' => 'error', 'message' => 'Username sudah dipakai'];
    }
    $stmt->close();

    // Update user information
    if ($password && $role) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE pengguna SET email=?, username=?, password=?, role=? WHERE id_pengguna=?");
        $stmt->bind_param("ssssi", $email, $username, $hashed_password, $role, $id_pengguna);
    } elseif ($password) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE pengguna SET email=?, username=?, password=? WHERE id_pengguna=?");
        $stmt->bind_param("sssi", $email, $username, $hashed_password, $id_pengguna);
    } elseif ($role) {
        $stmt = $conn->prepare("UPDATE pengguna SET email=?, username=?, role=? WHERE id_pengguna=?");
        $stmt->bind_param("sssi", $email, $username, $role, $id_pengguna);
    } else {
        $stmt = $conn->prepare("UPDATE pengguna SET email=?, username=? WHERE id_pengguna=?");
        $stmt->bind_param("ssi", $email, $username, $id_pengguna);
    }

    $result = $stmt->execute();
    $stmt->close();

    if ($result) {
        return ['status' => 'success', 'message' => 'User berhasil diperbarui!'];
    } else {
        return ['status' => 'error', 'message' => 'Gagal memperbarui user: ' . $conn->error];
    }
}

function deleteUser($conn, $id_pengguna) {
    $stmt = $conn->prepare("DELETE FROM pengguna WHERE id_pengguna=?");
    $stmt->bind_param("i", $id_pengguna);
    $result = $stmt->execute();
    $stmt->close();

    if ($result) {
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
    $stmt->bind_result($total_users);
    if ($stmt->fetch()) {
        $stats['total_users'] = $total_users;
    }
    $stmt->close();

    // Total produk
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM produk");
    $stmt->execute();
    $stmt->bind_result($total_products);
    if ($stmt->fetch()) {
        $stats['total_products'] = $total_products;
    }
    $stmt->close();

    // Total toko
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM toko");
    $stmt->execute();
    $stmt->bind_result($total_stores);
    if ($stmt->fetch()) {
        $stats['total_stores'] = $total_stores;
    }
    $stmt->close();

    // Total ulasan
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM ulasan");
    $stmt->execute();
    $stmt->bind_result($total_reviews);
    if ($stmt->fetch()) {
        $stats['total_reviews'] = $total_reviews;
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
          $result = $stmt->execute();
          $stmt->close();
          return $result;
      }
      return false;
  }

  function getStoreById($conn, $id_toko) {
      $stmt = $conn->prepare("SELECT id_toko, nama_toko, deskripsi, alamat, kontak, gambar, created_by FROM toko WHERE id_toko = ?");
      $stmt->bind_param("i", $id_toko);
      $stmt->execute();
      $stmt->bind_result($id, $nama, $deskripsi, $alamat, $kontak, $gambar, $created_by);

      if ($stmt->fetch()) {
          $store = [
              'id_toko' => $id,
              'nama_toko' => $nama,
              'deskripsi' => $deskripsi,
              'alamat' => $alamat,
              'kontak' => $kontak,
              'gambar' => $gambar,
              'created_by' => $created_by
          ];
          $stmt->close();
          return $store;
      }

      $stmt->close();
      return null;
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

      $result = $stmt->execute();
      $stmt->close();

      if ($result) {
          return ['status' => 'success'];
      } else {
          return ['status' => 'error', 'message' => $conn->error];
      }
  }

  function deleteStore($conn, $id_toko) {
      $sql = "DELETE FROM toko WHERE id_toko = ?";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("i", $id_toko);

      $result = $stmt->execute();
      $stmt->close();

      if ($result) {
          return ['status' => 'success'];
      } else {
          return ['status' => 'error', 'message' => $conn->error];
      }
  }

  function addProduct($conn, $nama_produk, $deskripsi, $harga, $id_toko, $id_kategori, $gambar) {
      $target_dir = "uploads/products/";
      $file_extension = pathinfo($gambar["name"], PATHINFO_EXTENSION);
      $new_filename = uniqid() . '.' . $file_extension;
      $target_file = $target_dir . $new_filename;

      if (move_uploaded_file($gambar["tmp_name"], $target_file)) {
          $stmt = $conn->prepare("INSERT INTO produk (nama_produk, deskripsi, harga, id_toko, id_kategori, gambar) VALUES (?, ?, ?, ?, ?, ?)");
          $stmt->bind_param("ssdiis", $nama_produk, $deskripsi, $harga, $id_toko, $id_kategori, $target_file);
          $result = $stmt->execute();
          $stmt->close();
          return $result;
      }
      return false;
  }

  function editProduct($conn, $id_produk, $nama_produk, $deskripsi, $harga, $id_toko, $id_kategori, $gambar) {
      $target_file = "";
      if (!empty($gambar['name'])) {
          $target_dir = "uploads/products/";
          $file_extension = pathinfo($gambar["name"], PATHINFO_EXTENSION);
          $new_filename = uniqid() . '.' . $file_extension;
          $target_file = $target_dir . $new_filename;
          if (!move_uploaded_file($gambar["tmp_name"], $target_file)) {
              return ['status' => 'error', 'message' => 'Gagal mengupload gambar!'];
          }
      }

      $sql = "UPDATE produk SET nama_produk = ?, deskripsi = ?, harga = ?, id_toko = ?, id_kategori = ?";
      $sql .= $target_file ? ", gambar = ?" : "";
      $sql .= " WHERE id_produk = ?";

      $stmt = $conn->prepare($sql);
      if ($target_file) {
          $stmt->bind_param("ssdiisi", $nama_produk, $deskripsi, $harga, $id_toko, $id_kategori, $target_file, $id_produk);
      } else {
          $stmt->bind_param("ssdiii", $nama_produk, $deskripsi, $harga, $id_toko, $id_kategori, $id_produk);
      }

      $result = $stmt->execute();
      $stmt->close();

      if ($result) {
          return ['status' => 'success', 'message' => 'Product berhasil diperbarui!'];
      } else {
          return ['status' => 'error', 'message' => $conn->error];
      }
  }

  function getProduct($conn, $id_produk) {
      $stmt = $conn->prepare("SELECT id_produk, nama_produk, deskripsi, harga, id_toko, id_kategori, gambar FROM produk WHERE id_produk = ?");
      $stmt->bind_param("i", $id_produk);
      $stmt->execute();
      $stmt->bind_result($id, $nama, $deskripsi, $harga, $id_toko, $id_kategori, $gambar);

      if ($stmt->fetch()) {
          $product = [
              'id_produk' => $id,
              'nama_produk' => $nama,
              'deskripsi' => $deskripsi,
              'harga' => $harga,
              'id_toko' => $id_toko,
              'id_kategori' => $id_kategori,
              'gambar' => $gambar
          ];
          $stmt->close();
          return $product;
      }

      $stmt->close();
      return null;
  }

  function deleteProduct($conn, $id_produk) {
      $product = getProduct($conn, $id_produk);
      if ($product) {
          if (file_exists($product['gambar'])) {
              unlink($product['gambar']);
          }

          $stmt = $conn->prepare("DELETE FROM produk WHERE id_produk = ?");
          $stmt->bind_param("i", $id_produk);
          $result = $stmt->execute();
          $stmt->close();

          if ($result) {
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
      $sql = "SELECT r.id_review, r.rating, r.komentar, r.tanggal_review, p.nama_produk, u.username
              FROM review r
              JOIN produk p ON r.id_produk = p.id_produk
              JOIN pengguna u ON r.id_pengguna = u.id_pengguna
              ORDER BY r.tanggal_review DESC";
      $stmt = $conn->prepare($sql);
      $stmt->execute();
      $stmt->bind_result($id_review, $rating, $komentar, $tanggal_review, $nama_produk, $username);

      $reviews = [];
      while ($stmt->fetch()) {
          $reviews[] = [
              'id_review' => $id_review,
              'rating' => $rating,
              'komentar' => $komentar,
              'tanggal_review' => $tanggal_review,
              'nama_produk' => $nama_produk,
              'username' => $username
          ];
      }
      $stmt->close();
      return $reviews;
  }

  function getAllProducts($conn) {
      $sql = "SELECT id_produk, nama_produk FROM produk";
      $stmt = $conn->prepare($sql);
      $stmt->execute();
      $stmt->bind_result($id_produk, $nama_produk);

      $products = [];
      while ($stmt->fetch()) {
          $products[] = [
              'id_produk' => $id_produk,
              'nama_produk' => $nama_produk
          ];
      }
      $stmt->close();
      return $products;
  }

  function getProducts($conn, $limit = null) {
      $sql = "SELECT p.id_produk, p.nama_produk, p.deskripsi, p.harga, p.gambar, t.nama_toko, k.nama_kategori, p.updated_at
              FROM produk p
              JOIN toko t ON p.id_toko = t.id_toko
              JOIN kategori k ON p.id_kategori = k.id_kategori
              ORDER BY p.updated_at DESC";

      if ($limit !== null) {
          $sql .= " LIMIT ?";
      }

      $stmt = $conn->prepare($sql);

      if ($limit !== null) {
          $stmt->bind_param("i", $limit);
      }

      $stmt->execute();
      $stmt->bind_result($id_produk, $nama_produk, $deskripsi, $harga, $gambar, $nama_toko, $nama_kategori, $updated_at);

      $products = [];

      while ($stmt->fetch()) {
          $products[] = [
              'id_produk' => $id_produk,
              'nama_produk' => $nama_produk,
              'deskripsi' => $deskripsi,
              'harga' => $harga,
              'gambar' => $gambar,
              'nama_toko' => $nama_toko,
              'nama_kategori' => $nama_kategori,
              'updated_at' => $updated_at
          ];
      }

      $stmt->close();
      return $products;
  }

  function getProductsMakanan($conn, $limit = null) {
      $sql = "SELECT p.id_produk, p.nama_produk, p.deskripsi, p.harga, p.gambar, t.nama_toko, k.nama_kategori, p.updated_at
              FROM produk p
              JOIN toko t ON p.id_toko = t.id_toko
              JOIN kategori k ON p.id_kategori = k.id_kategori
              WHERE k.nama_kategori = 'Makanan'
              ORDER BY p.updated_at DESC";

      if ($limit !== null) {
          $sql .= " LIMIT ?";
      }

      $stmt = $conn->prepare($sql);

      if ($limit !== null) {
          $stmt->bind_param("i", $limit);
      }

      $stmt->execute();
      $stmt->bind_result($id_produk, $nama_produk, $deskripsi, $harga, $gambar, $nama_toko, $nama_kategori, $updated_at);

      $products = [];

      while ($stmt->fetch()) {
          $products[] = [
              'id_produk' => $id_produk,
              'nama_produk' => $nama_produk,
              'deskripsi' => $deskripsi,
              'harga' => $harga,
              'gambar' => $gambar,
              'nama_toko' => $nama_toko,
              'nama_kategori' => $nama_kategori,
              'updated_at' => $updated_at
          ];
      }

      $stmt->close();
      return $products;
  }

  function getProductsSnack($conn, $limit = null) {
    $sql = "SELECT p.id_produk, p.nama_produk, p.deskripsi, p.harga, p.gambar, t.nama_toko, k.nama_kategori, p.updated_at
            FROM produk p
            JOIN toko t ON p.id_toko = t.id_toko
            JOIN kategori k ON p.id_kategori = k.id_kategori
            WHERE k.nama_kategori = 'Snack'
            ORDER BY p.updated_at DESC";

    if ($limit !== null) {
        $sql .= " LIMIT ?";
    }

    $stmt = $conn->prepare($sql);

    if ($limit !== null) {
        $stmt->bind_param("i", $limit);
    }

    $stmt->execute();
    $stmt->bind_result($id_produk, $nama_produk, $deskripsi, $harga, $gambar, $nama_toko, $nama_kategori, $updated_at);

    $products = [];

    while ($stmt->fetch()) {
        $products[] = [
            'id_produk' => $id_produk,
            'nama_produk' => $nama_produk,
            'deskripsi' => $deskripsi,
            'harga' => $harga,
            'gambar' => $gambar,
            'nama_toko' => $nama_toko,
            'nama_kategori' => $nama_kategori,
            'updated_at' => $updated_at
        ];
    }

    $stmt->close();
    return $products;
}

function getProductsDrink($conn, $limit = null) {
  $sql = "SELECT p.id_produk, p.nama_produk, p.deskripsi, p.harga, p.gambar, t.nama_toko, k.nama_kategori, p.updated_at
          FROM produk p
          JOIN toko t ON p.id_toko = t.id_toko
          JOIN kategori k ON p.id_kategori = k.id_kategori
          WHERE k.nama_kategori = 'Minuman'
          ORDER BY p.updated_at DESC";

  if ($limit !== null) {
      $sql .= " LIMIT ?";
  }

  $stmt = $conn->prepare($sql);

  if ($limit !== null) {
      $stmt->bind_param("i", $limit);
  }

  $stmt->execute();
  $stmt->bind_result($id_produk, $nama_produk, $deskripsi, $harga, $gambar, $nama_toko, $nama_kategori, $updated_at);

  $products = [];

  while ($stmt->fetch()) {
      $products[] = [
          'id_produk' => $id_produk,
          'nama_produk' => $nama_produk,
          'deskripsi' => $deskripsi,
          'harga' => $harga,
          'gambar' => $gambar,
          'nama_toko' => $nama_toko,
          'nama_kategori' => $nama_kategori,
          'updated_at' => $updated_at
      ];
  }

  $stmt->close();
  return $products;
}

  function addReview($conn, $id_produk, $id_pengguna, $rating, $komentar) {
      $sql = "INSERT INTO review (id_produk, id_pengguna, rating, komentar) VALUES (?, ?, ?, ?)";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("iiis", $id_produk, $id_pengguna, $rating, $komentar);
      $result = $stmt->execute();
      $stmt->close();
      return $result;
  }

  function deleteReview($conn, $id_review) {
      $sql = "DELETE FROM review WHERE id_review = ?";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("i", $id_review);
      $result = $stmt->execute();
      $stmt->close();
      return $result;
  }
?>