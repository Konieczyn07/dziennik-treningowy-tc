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
        $query = "SELECT * FROM workouts WHERE user_id = ? ORDER BY workout_date DESC";
        $this->user_id = (int)$this->user_id;
        $params = array($this->user_id);
        $stmt = sqlsrv_query($this->conn, $query, $params);
        return $stmt;
    }
	
	public function getSingle(){
		if(empty($this->id)){
			return false;
		}
		
		$query = "SELECT * FROM workouts WHERE id = ? AND user_id = ? LIMIT 1";
		$this->user_id = (int)$this->user_id;	
        $params = array($this->id, $this->user_id);
        $stmt = sqlsrv_query($this->conn, $query, $params);
        return $stmt;
	}
	
	public function create() {
        if(empty($this->exercise_name) || empty($this->sets) || empty($this->reps) || empty($this->workout_date) || empty($this->user_id)) {
            return false;
        }
        
		$query = "INSERT INTO workouts 
                  (user_id, exercise_name, sets, reps, weight, workout_date) 
                  VALUES 
                  (?, ?, ?, ?, ?, ?)";
        
        $this->user_id = (int)$this->user_id;
        $this->exercise_name = htmlspecialchars(strip_tags($this->exercise_name));
        $this->sets = (int)$this->sets;
        $this->reps = (int)$this->reps;
        $this->weight = (float)($this->weight ?? 0);
        $this->workout_date = htmlspecialchars(strip_tags($this->workout_date));
        
        $params = array($this->user_id, $this->exercise_name, $this->sets, $this->reps, $this->weight, $this->workout_date);
        $stmt = sqlsrv_query($this->conn, $query, $params);
        if($stmt) {
            return true;
        }
        return false;
    }
	
	public function update(){
		if(empty($this->id)) {
        error_log("UPDATE: Brak ID");
        return false;
    }
    
    $query = 'UPDATE workouts 
              SET exercise_name = ?,
                  sets = ?,
                  reps = ?,
                  weight = ?,
                  workout_date = ? 
              WHERE id = ? AND user_id = ?';

    $this->exercise_name = htmlspecialchars(strip_tags($this->exercise_name));
    $this->sets = (int)$this->sets;
    $this->reps = (int)$this->reps;
    $this->weight = (float)($this->weight ?? 0);
    $this->workout_date = htmlspecialchars(strip_tags($this->workout_date));
    $this->id = (int)$this->id;
    
    $params = array($this->exercise_name, $this->sets, $this->reps, $this->weight, $this->workout_date, $this->id, $this->user_id);
    $stmt = sqlsrv_query($this->conn, $query, $params);
    if($stmt) {
        return true;
    }
    
    error_log("UPDATE BŁĄD: " . print_r($stmt->error, true));
    return false;
	}
	
	public function delete(){
		$query = "DELETE FROM workouts WHERE id = ? AND user_id = ?";

        $this->id = (int)$this->id;
		$params = array($this->id, $this->user_id);
        $stmt = sqlsrv_query($this->conn, $query, $params);
        
		if($stmt){
			return true;
		}
		return false;
	}
}

?>