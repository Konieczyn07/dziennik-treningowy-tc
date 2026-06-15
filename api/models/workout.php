<?php

class Workout{
	private $conn;
	private $table = "workouts";
	
	public $id;
	public $user_id;
	public $exercise_name;
	public $sets;
	public $reps;
	public $weight;
	public $workout_date;
	
	public function __construct($db){
		$this->conn = $db;
	}
	
	public function getAll() {
        $query = "SELECT * FROM " . $this->table . " WHERE user_id = :user_id ORDER BY workout_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $this->user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }
	
	public function getSingle(){
		if(empty($this->id)){
			return false;
		}
		
		$query = "SELECT * FROM " . $this->table . " WHERE id = ? AND user_id = ? LIMIT 1";
		
		$return = $this->conn->prepare($query);
		$return->bindParam(1, $this->id);
		$return->bindParam(2, $this->user_id);
		$return->execute();
		return $return->fetch(PDO::FETCH_ASSOC);
	}
	
	public function create() {
        if(empty($this->exercise_name) || empty($this->sets) || empty($this->reps) || empty($this->workout_date) || empty($this->user_id)) {
            return false;
        }
        
		$query = "INSERT INTO " . $this->table . " 
                  (user_id, exercise_name, sets, reps, weight, workout_date) 
                  VALUES 
                  (:user_id, :exercise_name, :sets, :reps, :weight, :workout_date)";
        
        $stmt = $this->conn->prepare($query);

        $this->exercise_name = htmlspecialchars(strip_tags($this->exercise_name));
        $this->sets = (int)$this->sets;
        $this->reps = (int)$this->reps;
        $this->weight = (float)($this->weight ?? 0);
        $this->workout_date = htmlspecialchars(strip_tags($this->workout_date));
        $this->user_id = (int)$this->user_id;
        
        $stmt->bindParam(":user_id", $this->user_id, PDO::PARAM_INT);
        $stmt->bindParam(":exercise_name", $this->exercise_name);
        $stmt->bindParam(":sets", $this->sets, PDO::PARAM_INT);
        $stmt->bindParam(":reps", $this->reps, PDO::PARAM_INT);
        $stmt->bindParam(":weight", $this->weight);
        $stmt->bindParam(":workout_date", $this->workout_date);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
	
	public function update(){
		if(empty($this->id)) {
        error_log("UPDATE: Brak ID");
        return false;
    }
    
    $query = "UPDATE " . $this->table . " 
              SET exercise_name = :exercise_name,
                  sets = :sets,
                  reps = :reps,
                  weight = :weight,
                  workout_date = :workout_date 
              WHERE id = :id AND user_id = :user_id";
    
    $stmt = $this->conn->prepare($query);

    $this->exercise_name = htmlspecialchars(strip_tags($this->exercise_name));
    $this->sets = (int)$this->sets;
    $this->reps = (int)$this->reps;
    $this->weight = (float)($this->weight ?? 0);
    $this->workout_date = htmlspecialchars(strip_tags($this->workout_date));
    $this->id = (int)$this->id;
    
    $stmt->bindParam(":exercise_name", $this->exercise_name);
    $stmt->bindParam(":sets", $this->sets, PDO::PARAM_INT);
    $stmt->bindParam(":reps", $this->reps, PDO::PARAM_INT);
    $stmt->bindParam(":weight", $this->weight);
    $stmt->bindParam(":workout_date", $this->workout_date);
    $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
    $stmt->bindParam(":user_id", $this->user_id, PDO::PARAM_INT);
    
    if($stmt->execute()) {
        return true;
    }
    
    error_log("UPDATE BŁĄD: " . print_r($stmt->errorInfo(), true));
    return false;
	}
	
	public function delete(){
		$query = "DELETE FROM " . $this->table . " WHERE id = ? AND user_id = ?";
		
		$return = $this->conn->prepare($query);
		$this->id = htmlspecialchars(strip_tags($this->id));
		$return->bindParam(1, $this->id);
		$return->bindParam(2, $this->user_id);
		if($return->execute()){
			return true;
		}
		return false;
	}
}

?>