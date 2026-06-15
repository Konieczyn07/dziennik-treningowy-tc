<?php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Credentials: true");

require_once 'config/database.php';
require_once 'models/user.php';
require_once 'controllers/userController.php';

$database = new Database();
$db = $database->getConn();
$controller = new UserController($db);

$controller->register();
?>