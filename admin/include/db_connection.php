<?php
  $server_name = "localhost";
  $username = "root";
  $password = "";
  $db_name = "saujenhi";

  // Buat koneksi
  $conn = new mysqli($server_name, $username, $password, $db_name);

  // Cek koneksi
  if ($conn->connect_error) {
    die("Koneksi gagal: ". $conn->connect_error);
  }
?>