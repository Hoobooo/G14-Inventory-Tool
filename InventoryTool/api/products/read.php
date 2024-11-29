<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';
include_once '../../models/Product.php';
include_once '../../utils/AuthMiddleware.php';

AuthMiddleware::requireLogin();

$database = new Database();
$db = $database->connect();
$product = new Product($db);

$stmt = $product->read();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($products);