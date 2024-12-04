<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';
include_once '../../models/Category.php';
include_once '../../utils/AuthMiddleware.php';

AuthMiddleware::requireLogin();

$database = new Database();
$db = $database->connect();
$category = new Category($db);

$stmt = $category->read();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($categories);