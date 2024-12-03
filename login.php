<?php
  session_start();

  require_once 'admin/include/db_connection.php';
  require_once 'admin/include/functions.php';

  $error = '';

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $authResult = authUser($conn, $email, $password);

    // Periksa hasil autentikasi
    if ($authResult['status'] === 'success') {
      // Simpan data user ke session
      $_SESSION['id_pengguna'] = $authResult['id_pengguna'];
      $_SESSION['username'] = $authResult['username'];
      $_SESSION['user_email'] = $authResult['email'];
      $_SESSION['role'] = $authResult['role'];

      if ($_SESSION['role'] === 'admin') {
        header("Location: admin/index.php");
      } else {
        header("Location: home.php");
      }

    } elseif ($authResult['status'] === 'wrong_password') {
      $error = "Password salah.";
    } elseif ($authResult['status'] === 'email_not_found') {
      $error = "Email tidak ditemukan.";
    } else {
      $error = "Terjadi kesalahan pada autentikasi.";
    }
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Saujenhi</title>
  <link rel="stylesheet" href="style/login.css">
</head>
  <body>
    <div class="login-container">
      <div class="login-box">
        <div class="logo">
          <img src="assets/logo-saujenhi.png" alt="Logo">
        </div>
      </div>
      <h2>Silahkan Login <br>Terlebih Dahulu</h2>
      <?php if ($error): ?>
      <p class="error"><?php echo $error; ?></p>
      <?php endif; ?>
      <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div class="input-group">
          <label for="email"><img src="assets/ic_email.png" alt="email-icon" /></label>
          <input type="email" name="email" placeholder="Email" required>
        </div>
        <div class="input-group">
          <label for="password"><img src="assets/ic_password.png" alt="password-icon" /></label>
          <input type="password" name="password" placeholder="Password" required>
        </div>
        <button type="submit" class="btn-login">Login</button>
      </form>
      </div>
  </body>
</html>