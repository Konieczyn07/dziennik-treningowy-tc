<?php
$allowedOrigin = getenv('CORS_ALLOWED_ORIGIN') ?: '*';
header("Access-Control-Allow-Origin: " . $allowedOrigin);
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include_once 'config/database.php';
include_once 'models/workout.php';
include_once 'controllers/workoutController.php';

$database = new Database();
$db = $database->getConnection();
$controller = new WorkoutController($db);

$method = $_SERVER['REQUEST_METHOD'];
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);
$segments = explode('/', trim($path, '/'));

$inputData = [];
if ($method === 'POST' || $method === 'PUT') {
    $inputData = json_decode(file_get_contents("php://input"), true);
}

switch($method){
	case 'GET':
		if(isset($_GET['id'])){
			$controller->getSingle($_GET['id']);
		}else{
			$controller->getAll();
		}
		break;
	case 'POST':
        $controller->create($inputData);
        break;
	case 'PUT':
        if(isset($inputData['id']) && !empty($inputData['id'])) {
            $controller->update($inputData);
        } else {
            echo json_encode(["message" => "Brak ID do aktualizacji"]);
        }
        break;
	case 'DELETE':
		if(isset($_GET['id'])){
			$controller->delete($_GET['id']);
		}
		break;
	default:
		echo json_encode(["message" => "Metoda nieobsługiwana"]);
}
?>