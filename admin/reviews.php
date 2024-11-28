<?php
  session_start();
  if (!isset($_SESSION['id_pengguna'])) {
    header("Location: login.php");
    exit();
  }

  require_once 'include/db_connection.php';
  require_once 'include/functions.php';

  $logged_in_username = $_SESSION['username'];
  $current_page = 'reviews';

  $message = '';
  $error = '';

  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_review'])) {
      $id_produk = $_POST['id_produk'];
      $komentar = $_POST['komentar'];
      $rating = $_POST['rating'];
      $id_pengguna = $_SESSION['id_pengguna'];

      $stmt = $conn->prepare("INSERT INTO ulasan (id_pengguna, id_produk, komentar, rating, created_at) VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)");
      $stmt->bind_param("iiss", $id_pengguna, $id_produk, $komentar, $rating);

      if ($stmt->execute()) {
        $_SESSION['message'] = 'Review berhasil ditambahkan!';
      } else {
        $_SESSION['error'] = 'Gagal menambahkan review: ' . $conn->error;
      }

      $stmt->close();
      header("Location: reviews.php");
      exit();
    } elseif (isset($_POST['edit_review'])) {
      $id_ulasan = $_POST['id_ulasan'];
      $komentar = $_POST['komentar'];
      $rating = $_POST['rating'];

      $stmt = $conn->prepare("UPDATE ulasan SET komentar = ?, rating = ? WHERE id_ulasan = ? AND id_pengguna = ?");
      $stmt->bind_param("ssii", $komentar, $rating, $id_ulasan, $_SESSION['id_pengguna']);

      if ($stmt->execute()) {
        $_SESSION['message'] = 'Review berhasil diperbarui!';
      } else {
        $_SESSION['error'] = 'Gagal memperbarui review: ' . $conn->error;
      }

      $stmt->close();
      header("Location: reviews.php");
      exit();
    } elseif (isset($_POST['delete_review'])) {
      $id_ulasan = $_POST['id_ulasan'];

      $stmt = $conn->prepare("DELETE FROM ulasan WHERE id_ulasan = ?");
      $stmt->bind_param("i", $id_ulasan);

      if ($stmt->execute()) {
        $_SESSION['message'] = 'Review berhasil dihapus!';
      } else {
        $_SESSION['error'] = 'Gagal menghapus review: ' . $conn->error;
      }
      $stmt->close();
      header("Location: reviews.php");
      exit();
    }
  }

  if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
  }
  if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
  }

  // Fetch all reviews
  $reviews_query = "SELECT u.id_ulasan, u.id_pengguna, u.id_produk, u.komentar, u.rating,
                          u.created_at, p.nama_produk, pg.username
                    FROM ulasan u
                    JOIN produk p ON u.id_produk = p.id_produk
                    JOIN pengguna pg ON u.id_pengguna = pg.id_pengguna
                    ORDER BY u.created_at DESC";
  $reviews_result = $conn->query($reviews_query);

  if (!$reviews_result) {
      die("Error in query: " . $conn->error);
  }

  $reviews = [];
  while ($row = $reviews_result->fetch_assoc()) {
      $reviews[] = $row;
  }

  // Fetch all products
  $products_query = "SELECT id_produk, nama_produk FROM produk";
  $products_result = $conn->query($products_query);

  if (!$products_result) {
      die("Error in query: " . $conn->error);
  }

  $products = [];
  while ($row = $products_result->fetch_assoc()) {
      $products[] = $row;
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Reviews</title>
  <link rel="stylesheet" href="style/reviews.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
  <body>
    <div class="container">
      <?php include 'include/sidebar.php'; ?>
      <div class="main-content">
        <h1>Manage Reviews</h1>
        <?php
          if ($message) echo "<p class='success'>$message</p>";
          if ($error) echo "<p class='error'>$error</p>";
        ?>
        <button id="addReviewBtn" class="btn-add">Tambah Ulasan</button>
        <div class="reviews-container">
          <?php foreach ($reviews as $review): ?>
          <div class="review-card">
            <h3><?php echo htmlspecialchars($review['nama_produk']); ?></h3>
            <hr/>
            <p><strong>User:</strong> <?php echo htmlspecialchars($review['username']); ?></p>
            <p><strong>Rating:</strong>
              <?php
              // Loop untuk total bintang review
                for ($i = 1; $i <= $review['rating']; $i++): ?>
                  <i class="fas fa-star"></i>
              <?php endfor; ?>

              <?php
                // Loop untuk bintang kosong
                for ($i = 1; $i <= (5 - $review['rating']); $i++): ?>
                  <i class="far fa-star"></i>
              <?php endfor; ?>
            </p>

            <p><strong>Komentar:</strong> <?php echo htmlspecialchars($review['komentar']); ?></p>
            <p><strong>Tanggal:</strong> <?php echo htmlspecialchars($review['created_at']); ?></p>

            <div class="action-buttons">
              <form method="post" onsubmit="return confirm('Apakah Anda yakin ingin menghapus review ini?');">
                <input type="hidden" name="id_ulasan" value="<?php echo $review['id_ulasan']; ?>">
                <button type="submit" name="delete_review" class="btn-delete"><i class="fas fa-trash-alt"></i> Hapus</button>
              </form>
              <?php if ($review['id_pengguna'] == $_SESSION['id_pengguna']): ?>
              <button class="btn-edit" onclick="showEditModal(<?php echo $review['id_ulasan']; ?>, <?php echo $review['id_produk']; ?>, '<?php echo addslashes($review['komentar']); ?>', <?php echo $review['rating']; ?>)">
                <i class="fas fa-edit"></i> Edit
              </button>
              <?php endif; ?>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <!-- Add Review Modal -->
    <div id="addReviewModal" class="modal">
      <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Tambah Review Baru</h2>
        <form id="addReviewForm" method="post">
          <select name="id_produk" required>
            <option value="">Pilih Produk</option>
            <?php foreach ($products as $product): ?>
            <option value="<?php echo $product['id_produk']; ?>"><?php echo htmlspecialchars($product['nama_produk']); ?></option>
            <?php endforeach; ?>
          </select>
          <textarea name="komentar" placeholder="Komentar" required></textarea>
          <select name="rating" required>
            <option value="">Pilih Rating</option>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
          </select>
          <button type="submit" name="add_review">Simpan</button>
        </form>
      </div>
    </div>

    <!-- Edit Review Modal -->
    <div id="editReviewModal" class="modal">
      <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Edit Review</h2>
        <form id="editReviewForm" method="post">
          <input type="hidden" id="edit_id_ulasan" name="id_ulasan">
            <select id="edit_id_produk" name="id_produk" required>
              <option value="">Pilih Produk</option>
              <?php foreach ($products as $product): ?>
              <option value="<?php echo $product['id_produk']; ?>"><?php echo htmlspecialchars($product['nama_produk']); ?></option>
              <?php endforeach; ?>
            </select>
            <textarea id="edit_komentar" name="komentar" placeholder="Komentar" required></textarea>
            <select id="edit_rating" name="rating" required>
              <option value="">Pilih Rating</option>
              <option value="1">1</option>
              <option value="2">2</option>
              <option value="3">3</option>
              <option value="4">4</option>
              <option value="5">5</option>
            </select>
            <button type="submit" name="edit_review">Simpan Perubahan</button>
        </form>
      </div>
    </div>

    <script>
    let addModal = document.getElementById('addReviewModal');
    let addBtn = document.getElementById('addReviewBtn');
    let editModal = document.getElementById('editReviewModal');
    let editSpan = editModal.getElementsByClassName('close')[0];
    let span = document.getElementsByClassName('close')[0];

    addBtn.onclick = function() {
      addModal.style.display = 'block';
    }

    span.onclick = function() {
      addModal.style.display = 'none';
    }

    window.onclick = function(event) {
      if (event.target == addModal) {
        addModal.style.display = 'none';
      }
    }

    function showEditModal(id, idProduk, komentar, rating) {
      document.getElementById('edit_id_ulasan').value = id;
      document.getElementById('edit_id_produk').value = idProduk;
      document.getElementById('edit_komentar').value = komentar;
      document.getElementById('edit_rating').value = rating;
      editModal.style.display = 'block';
    }

    editSpan.onclick = function() {
      editModal.style.display = 'none';
    }

    window.onclick = function(event) {
      if (event.target == editModal) {
        editModal.style.display = 'none';
      }
    }
    </script>
  </body>
</html>