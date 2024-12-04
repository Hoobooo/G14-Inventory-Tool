<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT");

include_once '../../config/database.php';
include_once '../../models/Category.php';
include_once '../../utils/AuthMiddleware.php';

AuthMiddleware::requireLogin();

$database = new Database();
$db = $database->connect();
$category = new Category($db);

$id = isset($_GET['id']) ? $_GET['id'] : die();
$data = json_decode(file_get_contents("php://input"), true);

if($category->update($id, $data)) {
    http_response_code(200);
    echo json_encode(["message" => "Category updated"]);
} else {
    http_response_code(500);
    echo json_encode(["message" => "Failed to update category"]);
}