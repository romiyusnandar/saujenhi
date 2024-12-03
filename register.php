<?php
session_start();
require_once 'admin/include/db_connection.php';
require_once 'admin/include/functions.php';

$error = '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = 'user';

    if ($password !== $confirm_password) {
        $error = "Passwords tidak sama!";
    } else {
        if (addUser($conn, $email, $username, $password, $role)) {
            $message = 'User berhasil ditambahkan!';
            header("Location: home.php");
            exit();
        } else {
            $error = 'Gagal menambahkan user: ' . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register - Saujenhi</title>
  <link rel="stylesheet" href="style/register.css">
</head>
<body>
  <div class="login-container">
    <div class="login-box">
      <div class="logo">
        <img src="assets/logo-saujenhi.png" alt="Logo">
      </div>
    </div>
    <h2>Buat Akun Barumu!</h2>
    <?php if ($error): ?>
    <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>
    <?php if ($message): ?>
    <p class="success"><?php echo $message; ?></p>
    <?php endif; ?>
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
      <div class="form-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" placeholder="Enter your username" required>
      </div>
      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="Enter your email" required>
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Enter your password" required>
      </div>
      <div class="form-group">
        <label for="confirm_password">Konfirmasi Password</label>
        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>
      </div>
      <input type="hidden" name="role" value="user">
      <button type="submit">Register</button>
    </form>
  </div>
</body>
</html>