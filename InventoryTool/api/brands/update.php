<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: PUT");

include_once '../../config/database.php';
include_once '../../models/Brand.php';


$database = new Database();
$db = $database->connect();
$brand = new Brand($db);

$id = isset($_GET['id']) ? $_GET['id'] : die();
$data = json_decode(file_get_contents("php://input"), true);

if($brand->update($id, $data)) {
    http_response_code(200);
    echo json_encode(["message" => "Brand updated"]);
} else {
    http_response_code(500);
    echo json_encode(["message" => "Failed to update brand"]);
}