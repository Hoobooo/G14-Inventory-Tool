<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';
include_once '../../models/Inventory.php';
include_once '../../utils/AuthMiddleware.php';

AuthMiddleware::requireLogin();

$database = new Database();
$db = $database->connect();
$inventory = new Inventory($db);

$stmt = $inventory->getLowStock();
$low_stock_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($low_stock_items);