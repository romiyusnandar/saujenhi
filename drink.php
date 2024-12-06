<?php
  session_start();
  // Periksa apakah user sudah login
  if (!isset($_SESSION['id_pengguna'])) {
      header("Location: welcome.php");
      exit();
  }

  require_once 'admin/include/db_connection.php';
  require_once 'admin/include/functions.php';

  $productsMakanan = getProductsDrink ($conn);
?>

<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="style/food.css">
  <title>Drink</title>
</head>
<body>
  <div class="container">
      <div class="category">
        <div class="category-item">
          <a href="food.php">
            <img src="assets/snack.png" alt="Makanan">
          </a>
          <p>Snack</p>
        </div>
      </div>
  </div>

  <div class="section">
    <div class="product-grid">
        <?php foreach ($productsMakanan as $product): ?>
            <a href="product_detail.php?id=<?php echo htmlspecialchars($product['id_produk'] ?? ''); ?>">
              <div class="product-card">
                  <img src="<?php echo htmlspecialchars('admin/' . $product['gambar']); ?>" alt="<?php echo htmlspecialchars($product['nama_produk']); ?>" class="product-image">
                  <div class="product-info">
                      <h3 class="product-title"><?php echo htmlspecialchars($product['nama_produk']); ?></h3>
                      <p class="product-price">Rp <?php echo number_format($product['harga'], 0, ',', '.'); ?></p>
                  </div>
              </div>
            </a>
        <?php endforeach; ?>
    </div>
  </div>

</body>
</html>