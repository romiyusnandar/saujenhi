<?php
  session_start();

  require_once 'include/db_connection.php';
  require_once 'include/functions.php';

  $error = '';

  // debug hasing password
  // $password_asli = "admin123"; // Password asli
  // $hashed_password = password_hash($password_asli, PASSWORD_DEFAULT); // Hash password
  // echo "Password asli: $password_asli <br>";
  // echo "Hash password: $hashed_password <br>";
  // $inputPassword = "admin123";
  // $hashedPassword = "$2y$10$4KQ.UwUm.yS.LZVbChNBAeGBEM76uUxUAxPZyFWWLehSEaRy2xqfa";

  // if (password_verify($inputPassword, $hashedPassword)) {
  //     echo "Login berhasil!";
  // } else {
  //     echo "Password salah!";
  // }

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

      if ($authResult['role'] === 'admin') {
        // Redirect ke dashboard admin
        header("Location: dashboard.php");
        exit();
      } else {
        $error = "Anda bukan admin. Akses ditolak.";
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
      <h2>Login untuk mengakses <br>Admin Dashboard</h2>
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