<?php

class WorkoutController{
	private $workout;
	
	public function __construct($db){
		$this->workout = new Workout($db);
	}

	private function getUserId() {
		return $_SESSION['user_id'];
	}
	
	public function getAll(){
		$this->workout->user_id = $this->getUserId();
		$return = $this->workout->getAll();
		$workouts = [];
	
		while($row = $return->fetch(PDO::FETCH_ASSOC)){
			array_push($workouts, $row);
		}
		echo json_encode($workouts);
	}
	
    public function getSingle($id) {
        $this->workout->id = $id;
        $this->workout->user_id = $this->getUserId();
        $result = $this->workout->getSingle();
        if($result) {
            echo json_encode($result);
        } else {
            echo json_encode(["message" => "Nie znaleziono treningu o ID: " . $id]);
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
        $this->workout->exercise_name = $data['exercise_name'];
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
        $this->workout->id = $data['id'];
        $this->workout->exercise_name = $data['exercise_name'];
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
        $this->workout->id = $id;
        if($this->workout->delete()) {
            echo json_encode(["message" => "Trening usunięty pomyślnie"]);
        } else {
            echo json_encode(["message" => "Błąd usuwania treningu"]);
        }
	}
}

?>
