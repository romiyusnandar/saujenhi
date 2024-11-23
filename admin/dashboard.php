<?php
  session_start();
  // Periksa apakah user sudah login
  if (!isset($_SESSION['id_pengguna'])) {
      header("Location: login.php");
      exit();
  }

  require_once 'include/db_connection.php';
  require_once 'include/functions.php';

  $stats = getDashboardStats($conn);

  // Mendapatkan data user yang sedang login
  $logged_in_username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Tidak diketahui';
  $current_page = 'dashboard';
?>

<!DOCTYPE html>
<html lang="en">
  <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Admin Dashboard</title>
      <link rel="stylesheet" href="style/dashboard.css">
  </head>
  <body>
      <div class="container">
        <?php include 'include/sidebar.php'; ?>
        <div class="main-content">
          <h1>Dashboard</h1>
          <hr/>
          <div class="stats-grid">
            <div class="stat-card">
              <h4>Total Pengguna</h4>
              <div class="stat-figure"><?php echo $stats['total_users']; ?></div>
            </div>
            <div class="stat-card">
              <h4>Total Toko</h4>
              <div class="stat-figure"><?php echo $stats['total_stores']; ?></div>
            </div>
            <div class="stat-card">
              <h4>Total Produk</h4>
              <div class="stat-figure"><?php echo $stats['total_products']; ?></div>
            </div>
            <div class="stat-card">
              <h4>Total Ulasan</h4>
              <div class="stat-figure"><?php echo $stats['total_reviews']; ?></div>
            </div>
          </div>
        </div>
      </div>
  </body>
</html>