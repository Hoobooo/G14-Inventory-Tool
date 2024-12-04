<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");

include_once '../../config/database.php';
include_once '../../models/Brand.php';



$database = new Database();
$db = $database->connect();
$brand = new Brand($db);

$data = json_decode(file_get_contents("php://input"), true);

if($brand->create($data)) {
    http_response_code(201);
    echo json_encode(["message" => "Brand created"]);
} else {
    http_response_code(500);
    echo json_encode(["message" => "Failed to create brand"]);
}