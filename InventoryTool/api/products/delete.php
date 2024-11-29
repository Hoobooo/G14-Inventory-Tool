<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: DELETE");

include_once '../../config/database.php';
include_once '../../models/Product.php';
include_once '../../utils/AuthMiddleware.php';

AuthMiddleware::requireLogin();

$database = new Database();
$db = $database->connect();
$product = new Product($db);

$id = isset($_GET['id']) ? $_GET['id'] : die();

if($product->delete($id)) {
    http_response_code(200);
    echo json_encode(["message" => "Product deleted"]);
} else {
    http_response_code(500);
    echo json_encode(["message" => "Failed to delete product"]);
}