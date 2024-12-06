<?php
session_start();
require_once 'admin/include/db_connection.php';
require_once 'admin/include/functions.php';

// Periksa apakah user sudah login
if (!isset($_SESSION['id_pengguna'])) {
    header("Location: welcome.php");
    exit();
}

// Ambil ID produk dari parameter URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_review'])) {
        $id_produk = $product_id;
        $komentar = $_POST['komentar'];
        $rating = intval($_POST['rating']);
        $id_pengguna = $_SESSION['id_pengguna'];

        $stmt = $conn->prepare("INSERT INTO ulasan (id_pengguna, id_produk, komentar, rating, created_at) VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)");
        $stmt->bind_param("iisi", $id_pengguna, $id_produk, $komentar, $rating);

        if ($stmt->execute()) {
            $_SESSION['message'] = 'Review berhasil ditambahkan!';
        } else {
            $_SESSION['error'] = 'Gagal menambahkan review: ' . $conn->error;
        }

        $stmt->close();
        header("Location: product_detail.php?id=$id_produk");
        exit();
    } elseif (isset($_POST['edit_review'])) {
      $id_ulasan = intval($_POST['id_ulasan']);
      $komentar = trim($_POST['komentar']);
      $rating = intval($_POST['rating']);
      $id_pengguna = intval($_SESSION['id_pengguna']);

      $stmt = $conn->prepare("UPDATE ulasan SET komentar = ?, rating = ? WHERE id_ulasan = ? AND id_pengguna = ?");
      $stmt->bind_param("siii", $komentar, $rating, $id_ulasan, $id_pengguna);

      if ($stmt->execute()) {
          $_SESSION['message'] = 'Review berhasil diperbarui!';
      } else {
          $_SESSION['error'] = 'Gagal memperbarui review: ' . $conn->error;
      }

      $stmt->close();
      header("Location: product_detail.php?id=" . $product_id);
      exit();
  }
}

// Ambil detail produk dan ulasan
$product = getProductById($conn, $product_id);
if (!$product) {
    header("Location: home.php");
    exit();
}
$reviews = getReviewsByProductId($conn, $product_id);

// Ambil ulasan user jika ada
$user_review = null;
$stmt = $conn->prepare("SELECT id_ulasan, komentar, rating FROM ulasan WHERE id_produk = ? AND id_pengguna = ?");
$stmt->bind_param("ii", $product_id, $_SESSION['id_pengguna']);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($id_ulasan, $komentar, $rating);
    $stmt->fetch();
    $user_review = [
        'id_ulasan' => $id_ulasan,
        'komentar' => $komentar,
        'rating' => $rating
    ];
}
$stmt->close();
?>


<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($product['nama_produk']); ?> - Saujenhi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            font-family: Poppins, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f8f8;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .product-image {
            width: 100%;
            border-radius: 10px;
        }
        .product-price {
            font-size: 24px;
            color: #e74c3c;
            margin-top: 10px;
        }
        .product-description {
            margin-top: 15px;
            line-height: 1.6;
        }

        .btn-home {
          display: flex;
          flex-direction: row;
          justify-content: space-between;
        }
        .btn-home .back-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .btn-home .back-button:hover {
            background-color: #2980b9;
        }
        .btn-home .see-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background-color: #27ae60;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .btn-home .see-button:hover {
            background-color: #278060;
        }
        .reviews {
            margin-top: 30px;
        }
        .review-item {
            border-bottom: 1px solid #ddd;
            padding: 10px 0;
        }
        .review-item:last-child {
            border-bottom: none;
        }
        .review-author {
            font-weight: bold;
            color: #2c3e50;
        }
        .review-rating {
            color: #f1c40f;
        }
        .review-comment {
            margin-top: 5px;
            line-height: 1.4;
        }
        .add-review-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background-color: #27ae60;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .add-review-button:hover {
            background-color: #2ecc71;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }

        .modal-content {
          background-color: #ffffff;
          border-radius: 10px;
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
          padding: 20px;
          margin: auto;
          width: 90%;
          max-width: 400px;
          animation: fadeIn 0.3s ease-in-out;
        }

        textarea, select, input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        textarea {
            resize: vertical;
            height: auto;
            box-sizing: border-box;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10%);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        button[type="submit"] {
            display: block;
            width: 100%;
            background-color: #3498db;
            color: #fff;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            margin-top: 10px;
        }

        button[type="submit"]:hover {
            background-color: #2980b9;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .edit-delete-buttons {
            margin-top: 10px;
        }

        .edit-button, .delete-button {
            padding: 5px 10px;
            margin-right: 10px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        .delete-button {
            background-color: #e74c3c;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><?php echo htmlspecialchars($product['nama_produk']); ?></h1>
        <img src="<?php echo htmlspecialchars('admin/' . $product['gambar']); ?>" alt="<?php echo htmlspecialchars($product['nama_produk']); ?>" class="product-image">
        <p class="product-price">Rp <?php echo number_format($product['harga'], 0, ',', '.'); ?></p>
        <p class="product-description"><?php echo nl2br(htmlspecialchars($product['deskripsi'])); ?></p>

        <div class="btn-home">
            <a href="home.php" class="back-button">Kembali ke Home</a>
            <?php if (!empty($product['kontak'])): ?>
                <a href="https://<?php echo htmlspecialchars($product['kontak']); ?>" class="see-button" target="_blank">Kunjungi Toko</a>
            <?php else: ?>
                <span class="see-button disabled">Kontak Tidak Tersedia</span>
            <?php endif; ?>
        </div>



        <!-- Bagian Ulasan -->
        <div class="reviews">
            <h2>Ulasan</h2>
            <?php if ($reviews && count($reviews) > 0): ?>
                <?php foreach ($reviews as $review): ?>
                    <div class="review-item">
                        <span class="review-author"><?php echo htmlspecialchars($review['username']); ?></span>
                        <span class="review-rating">
                            <!-- <?php for ($i = 0; $i < $review['rating']; $i++): ?>★<?php endfor; ?> -->

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
                        </span>
                        <p class="review-comment"><?php echo nl2br(htmlspecialchars($review['komentar'])); ?></p>
                        <?php if ($review['username'] == $_SESSION['id_pengguna']): ?>
                            <div class="edit-delete-buttons">
                                <button class="edit-button" onclick="showEditModal(<?php echo $review['id_ulasan']; ?>, '<?php echo htmlspecialchars($review['komentar']); ?>', <?php echo $review['rating']; ?>)">Edit</button>
                                <form method="post" style="display: inline;">
                                    <input type="hidden" name="id_ulasan" value="<?php echo $review['id_ulasan']; ?>">
                                    <button type="submit" name="delete_review" class="delete-button" onclick="return confirm('Apakah Anda yakin ingin menghapus ulasan ini?')">Hapus</button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Belum ada ulasan untuk produk ini.</p>
            <?php endif; ?>

            <?php if ($user_review): ?>
                <button id="editReviewBtn" class="add-review-button">Edit Ulasan</button>
            <?php else: ?>
                <button id="addReviewBtn" class="add-review-button">Tambah Ulasan</button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Add Review Modal -->
    <div id="addReviewModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Tambah Ulasan Baru</h2>
            <form id="addReviewForm" method="post">
                <input type="hidden" name="id_produk" value="<?php echo $product_id; ?>">
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
        <h2>Edit Ulasan</h2>
        <form method="post" action="">
            <input type="hidden" id="edit_id_ulasan" name="id_ulasan">
            <label for="edit_komentar">Komentar:</label>
            <textarea id="edit_komentar" name="komentar" required></textarea>
            <label for="edit_rating">Rating:</label>
            <select id="edit_rating" name="rating" required>
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
    </div>
    <script>
      let addModal = document.getElementById('addReviewModal');
    let addBtn = document.getElementById('addReviewBtn');
    let editModal = document.getElementById('editReviewModal');
    let editBtn = document.getElementById('editReviewBtn');
    let spans = document.getElementsByClassName('close');

    if (addBtn) {
        addBtn.onclick = function() {
            addModal.style.display = 'block';
        }
    }

    if (editBtn) {
        editBtn.onclick = function() {
            <?php if ($user_review): ?>
                document.getElementById('edit_id_ulasan').value = <?php echo $user_review['id_ulasan']; ?>;
                document.getElementById('edit_komentar').value = <?php echo json_encode($user_review['komentar']); ?>;
                document.getElementById('edit_rating').value = <?php echo $user_review['rating']; ?>;
                editModal.style.display = 'block';
            <?php else: ?>
                alert('Anda belum memberikan ulasan untuk produk ini.');
            <?php endif; ?>
        }
    }

    function showEditModal(id, komentar, rating) {
        document.getElementById('edit_id_ulasan').value = id;
        document.getElementById('edit_komentar').value = komentar;
        document.getElementById('edit_rating').value = rating;
        editModal.style.display = 'block';
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

    function showEditModal(id, komentar, rating) {
        document.getElementById('edit_id_ulasan').value = id;
        document.getElementById('edit_komentar').value = komentar;
        document.getElementById('edit_rating').value = rating;
        editModal.style.display = 'block';
    }

    </script>
</body>
</html>
