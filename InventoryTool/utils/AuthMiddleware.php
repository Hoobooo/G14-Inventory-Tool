<?php
require_once 'Session.php';

class AuthMiddleware {
    public static function requireLogin() {
        if (!Session::isLoggedIn()) {
            http_response_code(401);
            echo json_encode(["message" => "Please login to access this resource"]);
            exit();
        }
    }

    public static function requireAdmin() {
        if (!Session::isLoggedIn() || Session::get('role_id') != 1) {
            http_response_code(403);
            echo json_encode(["message" => "Admin access required"]);
            exit();
        }
    }
}