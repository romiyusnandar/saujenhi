<?php
  session_start();
  if (!isset($_SESSION['id_pengguna'])) {
    header("Location: login.php");
    exit();
  }

  require_once 'include/db_connection.php';
  require_once 'include/functions.php';

  $logged_in_username = $_SESSION['username'];
  $current_page = 'stores';

  $message = '';
  $error = '';

  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
      $nama_toko = $_POST['nama_toko'];
      $deskripsi = $_POST['deskripsi'];
      $alamat = $_POST['alamat'];
      $kontak = $_POST['kontak'];
      $gambar = $_FILES['gambar'];
      $created_by = $_SESSION['id_pengguna'];

      if (addStore($conn, $nama_toko, $deskripsi, $alamat, $kontak, $gambar, $created_by)) {
        $message = 'Toko berhasil ditambahkan!';
      } else {
        $error = 'Gagal menambahkan toko: ' . $conn->error;
      }
    } else if (isset($_POST['edit'])) {
      $id_toko = $_POST['id_toko'];
      $nama_toko = $_POST['nama_toko'];
      $deskripsi = $_POST['deskripsi'];
      $alamat = $_POST['alamat'];
      $kontak = $_POST['kontak'];
      $gambar = $_FILES['gambar'];
      $result = editStore($conn, $id_toko, $nama_toko, $deskripsi, $alamat, $kontak, $gambar);
      if ($result['status'] === 'success') {
        $message = 'Toko berhasil diperbarui!';
      } else {
        $error = 'Gagal mengupdate toko: '. $result['message'];
      }
    } else if (isset($_POST['delete'])) {
      $id_toko = $_POST['id_toko'];
      $result = deleteStore($conn, $id_toko);
      if ($result['status'] === 'success') {
        $message = 'Toko berhasil dihapus!';
      } else {
        $error = 'Gagal menghapus toko: ' . $result['message'];
      }
    }
  }

  // dapatkan semua toko
  $result = $conn->query("SELECT * FROM toko");
  if (!$result) {
    die("Error in query: " . $conn->error);
  }

  $stores = [];
  while ($row = $result->fetch_assoc()) {
    $stores[] = $row;
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Toko</title>
  <link rel="stylesheet" href="style/stores.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
  <body>
    <div class="container">
      <?php include 'include/sidebar.php'; ?>
      <div class="main-content">
        <h1>Manage Toko</h1>
        <?php
          if ($message) echo "<p class='success'>$message</p>";
          if ($error) echo "<p class='error'>$error</p>";
        ?>
        <button id="addStoreBtn" class="btn-add">Tambah Toko</button>
        <table class="store-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Nama Toko</th>
              <th>Deskripsi</th>
              <th>Alamat</th>
              <th>Kontak</th>
              <th>Gambar</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($stores as $store): ?>
            <tr>
              <td><?php echo htmlspecialchars($store['id_toko']); ?></td>
              <td><?php echo htmlspecialchars($store['nama_toko']); ?></td>
              <td><?php echo htmlspecialchars($store['deskripsi']); ?></td>
              <td><?php echo htmlspecialchars($store['alamat']); ?></td>
              <td><?php echo htmlspecialchars($store['kontak']); ?></td>
              <td><img src="<?php echo htmlspecialchars($store['gambar']); ?>" alt="Gambar Toko" width="50"></td>
              <td>
                <button class="btn-edit" onclick="editStore(<?php echo $store['id_toko']; ?>)"><i class="fas fa-edit"></i> Edit</button>
                <button class="btn-delete" onclick="deleteStore(<?php echo $store['id_toko']; ?>)"><i class="fas fa-trash-alt"></i> Hapus</button>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

      <!-- Add Store Modal -->
      <div id="addStoreModal" class="modal">
        <div class="modal-content">
          <span class="close">&times;</span>
          <h2>Tambahkan Toko Baru</h2>
          <form id="addStoreForm" method="post" enctype="multipart/form-data">
            <input type="text" name="nama_toko" placeholder="Nama Toko" required>
            <textarea name="deskripsi" placeholder="Deskripsi"></textarea>
            <input type="text" name="alamat" placeholder="Alamat" required>
            <input type="text" name="kontak" placeholder="Kontak">
            <input type="file" name="gambar" required>
            <button type="submit" name="add">Simpan</button>
          </form>
        </div>
      </div>

      <!-- Edit Store Modal -->
      <div id="editStoreModal" class="modal">
        <div class="modal-content">
          <span class="close">&times;</span>
          <h2>Edit Toko</h2>
          <form id="editStoreForm" method="post" enctype="multipart/form-data">
            <input type="hidden" id="edit_id_toko" name="id_toko">
            <input type="text" id="edit_nama_toko" name="nama_toko" placeholder="Nama Toko" required>
            <textarea id="edit_deskripsi" name="deskripsi" placeholder="Deskripsi"></textarea>
            <input type="text" id="edit_alamat" name="alamat" placeholder="Alamat" required>
            <input type="text" id="edit_kontak" name="kontak" placeholder="Kontak">
            <input type="file" id="edit_gambar" name="gambar">
            <button type="submit" name="edit">Simpan</button>
          </form>
        </div>
      </div>

      <script>
      let addModal = document.getElementById('addStoreModal');
      let editModal = document.getElementById('editStoreModal');
      let addBtn = document.getElementById('addStoreBtn');
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

      function editStore(id) {
        fetch('get_store.php?id=' + id)
          .then(response => response.json())
          .then(data => {
            document.getElementById('edit_id_toko').value = data.id_toko;
            document.getElementById('edit_nama_toko').value = data.nama_toko;
            document.getElementById('edit_deskripsi').value = data.deskripsi;
            document.getElementById('edit_alamat').value = data.alamat;
            document.getElementById('edit_kontak').value = data.kontak;
            editModal.style.display = 'block';
          })
        .catch(error => console.error('Error:', error));
      }

      function deleteStore(id) {
        if (confirm('Yakin ingin menghapus toko tersebut?')) {
          let form = document.createElement('form');
          form.method = 'post';
          form.innerHTML = '<input type="hidden" name="id_toko" value="' + id + '"><input type="hidden" name="delete" value="1">';
          document.body.appendChild(form);
          form.submit();
        }
      }
      </script>
  </body>
</html>