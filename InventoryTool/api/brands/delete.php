<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: DELETE");

include_once '../../config/database.php';
include_once '../../models/Brand.php';


$database = new Database();
$db = $database->connect();
$brand = new Brand($db);

$id = isset($_GET['id']) ? $_GET['id'] : die();

if($brand->delete($id)) {
    http_response_code(200);
    echo json_encode(["message" => "Brand deleted"]);
} else {
    http_response_code(500);
    echo json_encode(["message" => "Failed to delete brand"]);
}