<?php
require_once 'include/db_connection.php';
require_once 'include/functions.php';

if (isset($_GET['id'])) {
  $id_toko = $_GET['id'];
  $store = getStoreById($conn, $id_toko);
  echo json_encode($store);
} else {
  echo json_encode(['error' => 'ID toko tidak diberikan']);
}