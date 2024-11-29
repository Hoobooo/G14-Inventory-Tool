<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../../config/database.php';
include_once '../../models/Inventory.php';
include_once '../../utils/AuthMiddleware.php';
include_once '../../utils/Session.php';

AuthMiddleware::requireLogin();

$database = new Database();
$db = $database->connect();
$inventory = new Inventory($db);

$data = json_decode(file_get_contents("php://input"), true);
$userId = Session::get('user_id');

if($inventory->adjustStock($data['product_id'], $data['quantity'], $userId, $data['type'])) {
    http_response_code(200);
    echo json_encode(["message" => "Stock adjusted successfully"]);
} else {
    http_response_code(500);
    echo json_encode(["message" => "Failed to adjust stock"]);
}