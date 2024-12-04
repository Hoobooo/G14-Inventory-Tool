<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../../config/database.php';
include_once '../../models/Category.php';
include_once '../../utils/AuthMiddleware.php';

AuthMiddleware::requireLogin();

$database = new Database();
$db = $database->connect();
$category = new Category($db);

$data = json_decode(file_get_contents("php://input"), true);

if($category->create($data)) {
    http_response_code(201);
    echo json_encode(["message" => "Category created"]);
} else {
    http_response_code(500);
    echo json_encode(["message" => "Failed to create category"]);
}