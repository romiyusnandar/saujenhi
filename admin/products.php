<?php
  session_start();
  if (!isset($_SESSION['id_pengguna'])) {
    header("Location: login.php");
    exit();
  }

  require_once 'include/db_connection.php';
  require_once 'include/functions.php';

  $logged_in_username = $_SESSION['username'];
  $current_page = 'products';

  $message = '';
  $error = '';

  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
      $nama_produk = $_POST['nama_produk'];
      $deskripsi = $_POST['deskripsi'];
      $harga = $_POST['harga'];
      $id_toko = $_POST['id_toko'];
      $id_kategori = $_POST['id_kategori'];
      $gambar = $_FILES['gambar'];

      if (addProduct($conn, $nama_produk, $deskripsi, $harga, $id_toko, $id_kategori, $gambar)) {
        $message = 'Produk berhasil ditambahkan!';
      } else {
        $error = 'Gagal menambahkan produk: ' . $conn->error;
      }
    } else if (isset($_POST['edit'])) {
      $id_produk = $_POST['id_produk'];
      $nama_produk = $_POST['nama_produk'];
      $deskripsi = $_POST['deskripsi'];
      $harga = $_POST['harga'];
      $id_toko = $_POST['id_toko'];
      $id_kategori = $_POST['id_kategori'];
      $gambar = $_FILES['gambar'];

      $result = editProduct($conn, $id_produk, $nama_produk, $deskripsi, $harga, $id_toko, $id_kategori, $gambar);
      if ($result['status'] === 'success') {
        $message = 'Produk berhasil diperbarui!';
      } else {
        $error = 'Gagal mengupdate produk: '. $result['message'];
      }
    } else if (isset($_POST['delete'])) {
      $id_produk = $_POST['id_produk'];
      $result = deleteProduct($conn, $id_produk);
      if ($result['status'] === 'success') {
        $message = 'Produk berhasil dihapus!';
      } else {
        $error = 'Gagal menghapus produk: ' . $result['message'];
      }
    }
  }

  // Get all products
  $result = $conn->query("SELECT p.*, t.nama_toko, k.nama_kategori FROM produk p
                          JOIN toko t ON p.id_toko = t.id_toko
                          JOIN kategori k ON p.id_kategori = k.id_kategori");
  $products = $result->fetch_all(MYSQLI_ASSOC);

  // Get all stores
  $stores = $conn->query("SELECT id_toko, nama_toko FROM toko")->fetch_all(MYSQLI_ASSOC);

  // Get all categories
  $categories = $conn->query("SELECT id_kategori, nama_kategori FROM kategori")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Produk</title>
  <link rel="stylesheet" href="style/products.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
  <body>
    <div class="container">
      <?php include 'include/sidebar.php'; ?>
      <div class="main-content">
        <h1>Manage Produk</h1>
        <?php
          if ($message) echo "<p class='success'>$message</p>";
          if ($error) echo "<p class='error'>$error</p>";
        ?>
        <button id="addProductBtn" class="btn-add">Tambah Produk</button>
          <table class="product-table">
            <thead>
              <tr>
                  <th>ID</th>
                  <th>Nama Produk</th>
                  <th>Deskripsi</th>
                  <th>Harga</th>
                  <th>Toko</th>
                  <th>Kategori</th>
                  <th>Gambar</th>
                  <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
              <?php foreach ($products as $product): ?>
              <tr>
                <td><?php echo htmlspecialchars($product['id_produk']); ?></td>
                <td><?php echo htmlspecialchars($product['nama_produk']); ?></td>
                <td><?php echo htmlspecialchars($product['deskripsi']); ?></td>
                <td><?php echo htmlspecialchars($product['harga']); ?></td>
                <td><?php echo htmlspecialchars($product['nama_toko']); ?></td>
                <td><?php echo htmlspecialchars($product['nama_kategori']); ?></td>
                <td><img src="<?php echo htmlspecialchars($product['gambar']); ?>" alt="Gambar Produk" width="50"></td>
                <td>
                  <button class="btn-edit" onclick="editProduct(<?php echo $product['id_produk']; ?>)"><i class="fas fa-edit"></i> Edit</button>
                  <button class="btn-delete" onclick="deleteProduct(<?php echo $product['id_produk']; ?>)"><i class="fas fa-trash-alt"></i> Hapus</button>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
      </div>
    </div>

    <!-- Add Product Modal -->
    <div id="addProductModal" class="modal">
      <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Tambahkan Produk Baru</h2>
        <form id="addProductForm" method="post" enctype="multipart/form-data">
          <input type="text" name="nama_produk" placeholder="Nama Produk" required>
          <textarea name="deskripsi" placeholder="Deskripsi"></textarea>
          <input type="number" name="harga" placeholder="Harga" step="100" required>
          <select name="id_toko" required>
            <option value="">Pilih Toko</option>
            <?php foreach ($stores as $store): ?>
            <option value="<?php echo $store['id_toko']; ?>"><?php echo htmlspecialchars($store['nama_toko']); ?></option>
            <?php endforeach; ?>
          </select>
          <select name="id_kategori" required>
            <option value="">Pilih Kategori</option>
            <?php foreach ($categories as $category): ?>
            <option value="<?php echo $category['id_kategori']; ?>"><?php echo htmlspecialchars($category['nama_kategori']); ?></option>
            <?php endforeach; ?>
          </select>
          <input type="file" name="gambar" required>
          <button type="submit" name="add">Simpan</button>
        </form>
      </div>
    </div>

      <!-- Edit Product Modal -->
    <div id="editProductModal" class="modal">
      <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Edit Produk</h2>
        <form id="editProductForm" method="post" enctype="multipart/form-data">
          <input type="hidden" id="edit_id_produk" name="id_produk">
          <input type="text" id="edit_nama_produk" name="nama_produk" placeholder="Nama Produk" required>
          <textarea id="edit_deskripsi" name="deskripsi" placeholder="Deskripsi"></textarea>
          <input type="number" id="edit_harga" name="harga" placeholder="Harga" step="0.01" required>
          <select id="edit_id_toko" name="id_toko" required>
            <?php foreach ($stores as $store): ?>
            <option value="<?php echo $store['id_toko']; ?>"><?php echo htmlspecialchars($store['nama_toko']); ?></option>
            <?php endforeach; ?>
          </select>
          <select id="edit_id_kategori" name="id_kategori" required>
            <?php foreach ($categories as $category): ?>
            <option value="<?php echo $category['id_kategori']; ?>"><?php echo htmlspecialchars($category['nama_kategori']); ?></option>
            <?php endforeach; ?>
          </select>
          <input type="file" id="edit_gambar" name="gambar">
          <button type="submit" name="edit">Simpan</button>
        </form>
      </div>
    </div>

    <script>
    let addModal = document.getElementById('addProductModal');
    let editModal = document.getElementById('editProductModal');
    let addBtn = document.getElementById('addProductBtn');
    let spans = document.getElementsByClassName('close');

    addBtn.onclick = function() {
      addModal.style.display = 'block';
    }

    for (let span of spans) {
      span.onclick = function() {
        addModal.style.display = 'none';
        editModal.style.display = 'none';
      }
    }

    window.onclick = function(event) {
      if (event.target == addModal) {
        addModal.style.display = 'none';
      }
      if (event.target == editModal) {
        editModal.style.display = 'none';
      }
    }

    function editProduct(id) {
      fetch('get_product.php?id=' + id)
        .then(response => response.json())
        .then(data => {
          document.getElementById('edit_id_produk').value = data.id_produk;
          document.getElementById('edit_nama_produk').value = data.nama_produk;
          document.getElementById('edit_deskripsi').value = data.deskripsi;
          document.getElementById('edit_harga').value = data.harga;
          document.getElementById('edit_id_toko').value = data.id_toko;
          document.getElementById('edit_id_kategori').value = data.id_kategori;
          editModal.style.display = 'block';
        })
      .catch(error => console.error('Error:', error));
    }

    function deleteProduct(id) {
      if (confirm('Yakin ingin menghapus produk tersebut?')) {
        let form = document.createElement('form');
        form.method = 'post';
        form.innerHTML = '<input type="hidden" name="id_produk" value="' + id + '"><input type="hidden" name="delete" value="1">';
        document.body.appendChild(form);
        form.submit();
      }
    }
    </script>
  </body>
</html>