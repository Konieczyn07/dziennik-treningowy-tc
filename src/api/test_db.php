<?php
header('Content-Type: text/plain; charset=utf-8');

echo "=== KOMPLETNY TEST APLIKACJI ===\n\n";

require_once 'config/database.php';
require_once 'models/Workout.php';
require_once 'controllers/WorkoutController.php';

$database = new Database();
$db = $database->getConn();
$controller = new WorkoutController($db);

echo "1. TEST DODAWANIA (CREATE)\n";

$testData = [
    'exercise_name' => 'Testowe ćwiczenie',
    'sets' => 3,
    'reps' => 10,
    'weight' => 50,
    'workout_date' => date('Y-m-d')
];

echo "   Dodawanie: " . json_encode($testData) . "\n";

$workout = new Workout($db);
$workout->exercise_name = $testData['exercise_name'];
$workout->sets = $testData['sets'];
$workout->reps = $testData['reps'];
$workout->weight = $testData['weight'];
$workout->workout_date = $testData['workout_date'];

if($workout->create()) {
    $lastId = $db->lastInsertId();
    echo "Dodano, ID: $lastId\n";
} else {
    echo "Błąd dodawania\n";
}

echo "\n2. TEST ODCZYTU (READ)\n";
$stmt = $workout->getAll();
$count = $stmt->rowCount();
echo "   Liczba treningów w bazie: $count\n";

echo "\n3. TEST AKTUALIZACJI (UPDATE)\n";
if(isset($lastId)) {
    $workout->id = $lastId;
    $workout->exercise_name = 'Zaktualizowane ćwiczenie';
    $workout->sets = 5;
    if($workout->update()) {
        echo "Zaktualizowano ID: $lastId\n";
    } else {
        echo "Błąd aktualizacji\n";
    }
}

echo "\n4. TEST ODCZYTU POJEDYNCZEGO (GET ONE)\n";
if(isset($lastId)) {
    $workout->id = $lastId;
    $single = $workout->getSingle();
    if($single) {
        echo "Odczytano: " . $single['exercise_name'] . "\n";
    }
}

echo "\n5. TEST USUWANIA (DELETE)\n";
if(isset($lastId)) {
    if($workout->delete()) {
        echo "Usunięto ID: $lastId\n";
    } else {
        echo "Błąd usuwania\n";
    }
}

echo "\n6. WERYFIKACJA PO USUNIĘCIU\n";
$workout->id = $lastId ?? 0;
$deleted = $workout->getSingle();
if(!$deleted) {
    echo "Rekord został usunięty (brak w bazie)\n";
} else {
    echo "Rekord nadal istnieje!\n";
}

echo "\n=== TEST ZAKOŃCZONY ===\n";
?>