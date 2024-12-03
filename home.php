<?php
  session_start();
  // Periksa apakah user sudah login
  if (!isset($_SESSION['id_pengguna'])) {
      header("Location: welcome.php");
      exit();
  }

  require_once 'admin/include/db_connection.php';
  require_once 'admin/include/functions.php';

  $products = getProducts($conn, 10);
?>

<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="style/home.css">
  <title>Saujenhi-Home</title>
</head>
<body>
  <div class="container">
    <div class="inner-container">
      <div class="logo">
        <img src="assets/logo-saujenhi.png" alt="Saujenhi Logo">
      </div>
      <div class="mid-container">
        <h1>Saujenhi Rekomendasi Korean food</h1>
        <div class="search-bar">
          <label for="search-bar"><img src="assets/ic_search.png" /></label>
          <input type="text"  placeholder="Cari makanan..." >
        </div>
      </div>
      <div class="user-icon">
        <img src="assets/user_profile.svg" alt="User  Icon">
        <a href="admin/logout.php">Logout</a>
      </div>
    </div>
  </div>
  <div class="category">
    <div class="category-item">
      <a href="food.php">
        <img src="assets/makanan.png" alt="Makanan">
      </a>
      <p>Makanan</p>
    </div>
    <div class="category-item">
      <a href="snack.php">
        <img src="assets/snack.png" alt="Snack">
      </a>
      <p>Snack</p>
    </div>
    <div class="category-item">
      <a href="drink.php">
        <img src="assets/minuman.png" alt="Minuman">
      </a>
      <p>Minuman</p>
    </div>
  </div>
  <div class="section">
  <h2 class="section-title">Recommended Dishes</h2>
  <div class="product-grid">
      <?php foreach ($products as $product): ?>
          <div class="product-card">
              <img src="<?php echo htmlspecialchars('admin/' . $product['gambar']); ?>" alt="<?php echo htmlspecialchars($product['nama_produk']); ?>" class="product-image">
              <div class="product-info">
                  <h3 class="product-title"><?php echo htmlspecialchars($product['nama_produk']); ?></h3>
                  <p class="product-price">Rp <?php echo number_format($product['harga'], 0, ',', '.'); ?></p>
              </div>
          </div>
      <?php endforeach; ?>
  </div>
</div>

</body>
</html>