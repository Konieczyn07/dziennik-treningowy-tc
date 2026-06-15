<?php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");

require_once 'models/user.php';
require_once 'controllers/userController.php';

$controller = new UserController(null);
$controller->check();
?>