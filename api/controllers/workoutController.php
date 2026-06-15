<?php
class WorkoutController {
    private $workout;

    public function __construct($db) {
        $this->workout = new Workout($db);
    }

    public function getAll() {
        $stmt = $this->workout->getAll();
        $workouts = [];
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $row['id'] = (int)$row['id'];
            $row['sets'] = (int)$row['sets'];
            $row['reps'] = (int)$row['reps'];
            $row['weight'] = (float)$row['weight'];
            array_push($workouts, $row);
        }
        echo json_encode($workouts);
    }

    public function getSingle($id) {
        $this->workout->id = $id;
        $result = $this->workout->getSingle();
        if($result) {
            $result['id'] = (int)$result['id'];
            $result['sets'] = (int)$result['sets'];
            $result['reps'] = (int)$result['reps'];
            $result['weight'] = (float)$result['weight'];
            echo json_encode($result);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Nie znaleziono treningu o ID: " . $id]);
        }
    }

    public function create($data) {
        if(!isset($data['exercise_name']) || empty($data['exercise_name'])) {
            http_response_code(400);
            echo json_encode(["message" => "Brak nazwy ćwiczenia"]);
            return;
        }
        if(!isset($data['sets']) || $data['sets'] <= 0) {
            http_response_code(400);
            echo json_encode(["message" => "Liczba serii musi być większa od 0"]);
            return;
        }
        if(!isset($data['reps']) || $data['reps'] <= 0) {
            http_response_code(400);
            echo json_encode(["message" => "Liczba powtórzeń musi być większa od 0"]);
            return;
        }
        
        $this->workout->exercise_name = $data['exercise_name'];
        $this->workout->sets = $data['sets'];
        $this->workout->reps = $data['reps'];
        $this->workout->weight = $data['weight'] ?? 0;
        $this->workout->workout_date = $data['workout_date'];
        
        if($this->workout->create()) {
            http_response_code(201);
            echo json_encode([
                "message" => "Trening dodany pomyślnie",
                "id" => $this->workout->id
            ]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Błąd dodawania treningu"]);
        }
    }

    public function update($data) {
        if(!isset($data['id']) || empty($data['id'])) {
            http_response_code(400);
            echo json_encode(["message" => "Brak ID treningu"]);
            return;
        }
        
        $this->workout->id = $data['id'];
        $this->workout->exercise_name = $data['exercise_name'];
        $this->workout->sets = $data['sets'];
        $this->workout->reps = $data['reps'];
        $this->workout->weight = $data['weight'] ?? 0;
        $this->workout->workout_date = $data['workout_date'];
        
        if($this->workout->update()) {
            echo json_encode(["message" => "Trening zaktualizowany pomyślnie"]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Błąd aktualizacji treningu"]);
        }
    }

    public function delete($id) {
        if(empty($id)) {
            http_response_code(400);
            echo json_encode(["message" => "Brak ID do usunięcia"]);
            return;
        }
        
        $this->workout->id = $id;
        if($this->workout->delete()) {
            echo json_encode(["message" => "Trening usunięty pomyślnie"]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Błąd usuwania treningu"]);
        }
    }
    
    public function getStats() {
        $stats = $this->workout->getStats();
        $stats['total_workouts'] = (int)$stats['total_workouts'];
        $stats['total_exercises'] = (int)$stats['total_exercises'];
        $stats['total_reps'] = (int)$stats['total_reps'];
        $stats['avg_weight'] = (float)$stats['avg_weight'];
        echo json_encode($stats);
    }
}
?>