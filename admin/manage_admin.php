<?php
  session_start();
  if (!isset($_SESSION['id_pengguna'])) {
    header("Location: login.php");
    exit();
  }

  require_once 'include/db_connection.php';
  require_once 'include/functions.php';

  $logged_in_username = $_SESSION['username'];
  $current_page = 'manage_admin';

  $message = '';
  $error = '';

  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
      $email = $_POST['email'];
      $username = $_POST['username'];
      $password = $_POST['password'];
      $role = $_POST['role'];
      if (addUser($conn, $email, $username, $password, $role)) {
        $message = 'Admin berhasil ditambahkan!';
      } else {
        $error = 'Gagal menambahkan admin: ' . $conn->error;
      }
    } else if (isset($_POST['edit'])) {
      $id_pengguna = $_POST['id_pengguna'];
      $email = $_POST['email'];
      $username = $_POST['username'];
      $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;
      $role = $_POST['role'];
      $result = editUser($conn, $id_pengguna, $email, $username, $password, $role);
      if ($result['status'] === 'success') {
        $message = 'Admin berhasil diperbarui!';
      } else {
        $error = 'Gagal mengupdate admin: '. $result['message'];
      }
    } else if (isset($_POST['delete'])) {
      $id_pengguna = $_POST['id_pengguna'];
      $result = deleteUser($conn, $id_pengguna);
      if ($result['status'] === 'success') {
        if ($id_pengguna == $_SESSION['id_pengguna']) {
          session_destroy();
          header("Location: login.php");
          exit();
        } else {
          $message = 'Admin berhasil dihapus!';
        }
      } else {
        $error = 'Gagal menghapus admin: ' . $result['message'];
      }
    }
  }

  // dapatkan semua user dengan role admin
  $result = $conn->query("SELECT * FROM pengguna WHERE role = 'admin'");
  if (!$result) {
    die("Error in query: " . $conn->error);
  }

  $users = [];
  while ($row = $result->fetch_assoc()) {
    $users[] = $row;
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Admins</title>
  <link rel="stylesheet" href="style/manage_admin.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
  <body>
    <div class="container">
      <?php include 'include/sidebar.php'; ?>
      <div class="main-content">
        <h1>Manage Admins</h1>
        <?php
          if ($message) echo "<p class='success'>$message</p>";
          if ($error) echo "<p class='error'>$error</p>";
        ?>
        <button id="addAdminBtn" class="btn-add">Tambah Admin</button>
        <table class="admin-table">
          <thead>
            <tr>
                <th>ID</th>
                <th>Email</th>
                <th>Username</th>
                <th>Role</th>
                <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
              <?php foreach ($users as $user): ?>
              <tr>
                <td><?php echo htmlspecialchars($user['id_pengguna']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><?php echo htmlspecialchars($user['username']); ?></td>
                <td><?php echo htmlspecialchars($user['role']); ?></td>
                <td>
                    <button class="btn-edit" onclick="editUser(<?php echo $user['id_pengguna']; ?>, '<?php echo htmlspecialchars($user['email']); ?>', '<?php echo htmlspecialchars($user['username']); ?>')"><i class="fas fa-edit"></i> Edit</button>
                    <button class="btn-delete" onclick="deleteUser(<?php echo $user['id_pengguna']; ?>)"><i class="fas fa-trash-alt"></i> Hapus</button>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
        </table>
      </div>
    </div>

    <!-- Add Admin Modal -->
    <div id="addAdminModal" class="modal">
      <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Tambahkan Admin Baru</h2>
        <form id="addAdminForm" method="post">
          <input type="email" name="email" placeholder="Email" required>
          <input type="text" name="username" placeholder="Username" required>
          <input type="password" name="password" placeholder="Password" required>
          <input type="hidden" name="role" value="admin">
          <button type="submit" name="add">Simpan</button>
        </form>
      </div>
    </div>

    <!-- Edit Admin Modal -->
    <div id="editAdminModal" class="modal">
      <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Edit Admin</h2>
        <form id="editAdminForm" method="post">
          <input type="hidden" id="edit_id_pengguna" name="id_pengguna">
          <input type="email" id="edit_email" name="email" placeholder="Email" required>
          <input type="text" id="edit_username" name="username" placeholder="Username" required>
          <input type="password" id="edit_password" name="password" placeholder="Password (kosongkan jika tidak ingin diganti)">
          <input type="hidden" name="role" value="admin">
          <button type="submit" name="edit">Simpan</button>
        </form>
      </div>
    </div>

    <script>
    let addModal = document.getElementById('addAdminModal');
    let editModal = document.getElementById('editAdminModal');
    let addBtn = document.getElementById('addAdminBtn');
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

    function editUser(id, email, username) {
      document.getElementById('edit_id_pengguna').value = id;
      document.getElementById('edit_email').value = email;
      document.getElementById('edit_username').value = username;
      editModal.style.display = 'block';
    }

    function deleteUser(id) {
      if (confirm('Jika akun dihapus akan diarahkan ke login page, yakin ingin menghapus admin tersebut?')) {
        let form = document.createElement('form');
        form.method = 'post';
        form.innerHTML = '<input type="hidden" name="id_pengguna" value="' + id + '"><input type="hidden" name="delete" value="1">';
        document.body.appendChild(form);
        form.submit();
      }
    }
    </script>
  </body>
</html>