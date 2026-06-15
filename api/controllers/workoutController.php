<?php

class WorkoutController{
	private $workout;
	
	public function __construct($db){
		$this->workout = new Workout($db);
	}

	private function getUserId() {
		return $_SESSION['user_id'] ?? null;
	}
	
	public function getAll(){
		$this->workout->user_id = $this->getUserId();
		$return = $this->workout->getAll();
		$workouts = [];
	
        if($result !== false){
            while($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)){
                array_push($workouts, $row);
            }
        }
        echo json_encode($workouts);
	}
	
    public function getSingle($id) {
        $this->workout->id = $id;
        $this->workout->user_id = $this->getUserId();
        $result = $this->workout->getSingle();
        if($result !== false) {
            $workout = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
            if($workout){
                echo json_encode($workout);
            } else {
                echo json_encode(["message" => "Nie znaleziono treningu o id: " . $id]);
            }
        } else {
            echo json_encode(["message" => "Błąd podczas pobierania treningu"]);
        }
    }
	
	public function create($data){
		if(!isset($data['exercise_name']) || empty($data['exercise_name'])) {
            echo json_encode(["message" => "Brak nazwy ćwiczenia"]);
            return;
        }
        if(!isset($data['sets']) || empty($data['sets'])) {
            echo json_encode(["message" => "Brak liczby serii"]);
            return;
        }
        if(!isset($data['reps']) || empty($data['reps'])) {
            echo json_encode(["message" => "Brak liczby powtórzeń"]);
            return;
        }
        if(!isset($data['workout_date']) || empty($data['workout_date'])) {
            echo json_encode(["message" => "Brak daty treningu"]);
            return;
        }
        
        $this->workout->user_id = $this->getUserId();
        if(!$this->workout->user_id){
            echo echo json_encode(["message" => "Brak zalogowanego użytkownika"]);
            return;
        }
        $this->workout->exercise_name = htmlspecialchars(strip_tags($data['exercise_name']));
        $this->workout->sets = $data['sets'];
        $this->workout->reps = $data['reps'];
        $this->workout->weight = $data['weight'] ?? 0;
        $this->workout->workout_date = $data['workout_date'];
        
        if($this->workout->create()) {
            echo json_encode(["message" => "Trening dodany pomyślnie"]);
        } else {
            echo json_encode(["message" => "Błąd dodawania treningu"]);
        }
	}
	
 public function update($data) {
		if(!isset($data['id']) || empty($data['id'])) {
            echo json_encode(["message" => "Brak ID treningu"]);
            return;
        }
        if(!isset($data['exercise_name']) || empty($data['exercise_name'])) {
            echo json_encode(["message" => "Brak nazwy ćwiczenia"]);
            return;
        }
        if(!isset($data['sets']) || empty($data['sets'])) {
            echo json_encode(["message" => "Brak liczby serii"]);
            return;
        }
        if(!isset($data['reps']) || empty($data['reps'])) {
            echo json_encode(["message" => "Brak liczby powtórzeń"]);
            return;
        }
        if(!isset($data['workout_date']) || empty($data['workout_date'])) {
            echo json_encode(["message" => "Brak daty treningu"]);
            return;
        }
        
        $this->workout->user_id = $this->getUserId();
        if(!$this->workout->user_id){
            echo echo json_encode(["message" => "Brak zalogowanego użytkownika"]);
            return;
        }
        $this->workout->id = $data['id'];
        $this->workout->exercise_name = htmlspecialchars(strip_tags($data['exercise_name']));
        $this->workout->sets = $data['sets'];
        $this->workout->reps = $data['reps'];
        $this->workout->weight = $data['weight'] ?? 0;
        $this->workout->workout_date = $data['workout_date'];
        
        if($this->workout->update()) {
            echo json_encode(["message" => "Trening zaktualizowany pomyślnie"]);
        } else {
            echo json_encode(["message" => "Błąd aktualizacji treningu"]);
        }
    }

	
	public function delete($id){
		if(empty($id)) {
            echo json_encode(["message" => "Brak ID do usunięcia"]);
            return;
        }
        
        $this->workout->user_id = $this->getUserId();
        if(!$userId) {
            echo json_encode(["message" => "Brak zalogowanego użytkownika"]);
            return;
        }
        $this->workout->id = $id;
        if($this->workout->delete()) {
            echo json_encode(["message" => "Trening usunięty pomyślnie"]);
        } else {
            echo json_encode(["message" => "Błąd usuwania treningu"]);
        }
	}
}

?>
