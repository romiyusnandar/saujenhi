<?php
  session_start();
  // Periksa apakah user sudah login
  if (!isset($_SESSION['id_pengguna'])) {
      header("Location: welcome.php");
      exit();
  }

  require_once 'admin/include/db_connection.php';
  require_once 'admin/include/functions.php';

  $productsMakanan = getProductsMakanan($conn);
?>

<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="style/food.css">
  <title>Saujenhi-Food</title>
</head>
<body>
  <div class="container">
      <div class="category">
        <div class="category-item">
          <a href="food.php">
            <img src="assets/makanan.png" alt="Makanan">
          </a>
          <p>Makanan</p>
        </div>
      </div>
  </div>

  <div class="section">
    <div class="product-grid">
        <?php foreach ($productsMakanan as $product): ?>
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