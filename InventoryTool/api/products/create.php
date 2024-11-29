<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

include_once '../../config/database.php';
include_once '../../models/Product.php';
include_once '../../utils/AuthMiddleware.php';

AuthMiddleware::requireLogin();

$database = new Database();
$db = $database->connect();
$product = new Product($db);

$data = json_decode(file_get_contents("php://input"), true);

if($data && $product->create($data)) {
    http_response_code(201);
    echo json_encode(["message" => "Product created"]);
} else {
    http_response_code(500);
    echo json_encode(["message" => "Failed to create product"]);
}