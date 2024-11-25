<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../utils/Session.php';

if(Session::destroy()) {
    http_response_code(200);
    echo json_encode(["message" => "Logged out successfully"]);
} else {
    http_response_code(500);
    echo json_encode(["message" => "Failed to logout"]);
}