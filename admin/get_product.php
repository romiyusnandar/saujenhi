<?php
require_once 'include/db_connection.php';
require_once 'include/functions.php';

if (isset($_GET['id'])) {
  $id_produk = intval($_GET['id']);
  $product = getProduct($conn, $id_produk);
  if ($product) {
    echo json_encode($product);
  } else {
    echo json_encode(['error' => 'Product tidak ditemukan']);
  }
} else {
  echo json_encode(['error' => 'ID tidak diberikan']);
}