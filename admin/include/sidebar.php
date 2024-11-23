<?php
if (!isset($username)) {
  $logged_in_username = $_SESSION['username'] ?? 'Tidak diketahui';
}
?>
  <link rel="stylesheet" href="style/sidebar.css">
  <div class="sidebar">
    <div class="logo">
      <img src="assets/logo-saujenhi.png" alt="Logo">
      <h3>Saujenhi Admin</h3>
      <p>Login sebagai: <?php echo htmlspecialchars($logged_in_username); ?></p>
    </div>
    <ul>
      <li><a href="dashboard.php" <?php echo ($current_page == 'dashboard') ? 'class="active"' : ''; ?>><img src="assets/ic_overview.png"/>Overview</a></li>
      <li><a href="stores.php" <?php echo ($current_page == 'stores') ? 'class="active"' : ''; ?>><img src="assets/ic_store.png"/>Toko</a></li>
      <li><a href="products.php" <?php echo ($current_page == 'products') ? 'class="active"' : ''; ?>><img src="assets/ic_product.png"/>Produk</a></li>
      <li><a href="users.php" <?php echo ($current_page == 'users') ? 'class="active"' : ''; ?>><img src="assets/ic_user.png"/>Pengguna</a></li>
      <li><a href="reviews.php" <?php echo ($current_page == 'reviews') ? 'class="active"' : ''; ?>><img src="assets/ic_review.png"/>Ulasan</a></li>
      <li><a href="manage_admin.php" <?php echo ($current_page == 'manage_admin') ? 'class="active"' : ''; ?>><img src="assets/ic_admin.png"/>Admin</a></li>
      <li><a href="logout.php"><img src="assets/ic_logout.png"/>Logout</a></li>
    </ul>
  </div>