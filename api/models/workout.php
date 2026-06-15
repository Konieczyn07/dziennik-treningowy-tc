<?php

class Workout {
    private $conn;
    private $table = "workouts";
    
    public $id;
    public $user_id;
    public $exercise_name;
    public $sets;
    public $reps;
    public $weight;
    public $workout_date;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function getAll() {
        // Sprawdź połączenie
        if(!$this->conn) {
            error_log("Database connection is null in getAll()");
            return false;
        }
        
        $query = "SELECT id, user_id, exercise_name, sets, reps, weight, 
                         CAST(workout_date AS date) as workout_date
                  FROM " . $this->table . " 
                  WHERE user_id = ? 
                  ORDER BY workout_date DESC";
        
        try {
            $params = array($this->user_id);
            $stmt = sqlsrv_query($this->conn, $query, $params);
            
            if($stmt === false) {
                $errors = sqlsrv_errors();
                error_log("SQL Error in getAll: " . print_r($errors, true));
                return false;
            }
            
            return $stmt;
        } catch (Exception $e) {
            error_log("Exception in getAll: " . $e->getMessage());
            return false;
        }
    }
    
    public function getSingle() {
        if(!$this->conn) {
            error_log("Database connection is null in getSingle()");
            return false;
        }
        
        $query = "SELECT TOP 1 id, user_id, exercise_name, sets, reps, weight, 
                         CAST(workout_date AS date) as workout_date
                  FROM " . $this->table . " 
                  WHERE id = ? AND user_id = ?";
        
        try {
            $params = array($this->id, $this->user_id);
            $stmt = sqlsrv_query($this->conn, $query, $params);
            
            if($stmt === false) {
                $errors = sqlsrv_errors();
                error_log("SQL Error in getSingle: " . print_r($errors, true));
                return false;
            }
            
            return $stmt;
        } catch (Exception $e) {
            error_log("Exception in getSingle: " . $e->getMessage());
            return false;
        }
    }
    
    public function create() {
        if(!$this->conn) {
            error_log("Database connection is null in create()");
            return false;
        }
        
        $query = "INSERT INTO " . $this->table . " 
                  (user_id, exercise_name, sets, reps, weight, workout_date) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        
        try {
            $params = array(
                $this->user_id,
                $this->exercise_name,
                $this->sets,
                $this->reps,
                $this->weight,
                $this->workout_date
            );
            
            $stmt = sqlsrv_query($this->conn, $query, $params);
            
            if($stmt === false) {
                $errors = sqlsrv_errors();
                error_log("SQL Error in create: " . print_r($errors, true));
                return false;
            }
            
            return true;
        } catch (Exception $e) {
            error_log("Exception in create: " . $e->getMessage());
            return false;
        }
    }
    
    public function update() {
        if(!$this->conn) {
            error_log("Database connection is null in update()");
            return false;
        }
        
        $query = "UPDATE " . $this->table . " 
                  SET exercise_name = ?, 
                      sets = ?, 
                      reps = ?, 
                      weight = ?, 
                      workout_date = ? 
                  WHERE id = ? AND user_id = ?";
        
        try {
            $params = array(
                $this->exercise_name,
                $this->sets,
                $this->reps,
                $this->weight,
                $this->workout_date,
                $this->id,
                $this->user_id
            );
            
            $stmt = sqlsrv_query($this->conn, $query, $params);
            
            if($stmt === false) {
                $errors = sqlsrv_errors();
                error_log("SQL Error in update: " . print_r($errors, true));
                return false;
            }
            
            return true;
        } catch (Exception $e) {
            error_log("Exception in update: " . $e->getMessage());
            return false;
        }
    }
    
    public function delete() {
        if(!$this->conn) {
            error_log("Database connection is null in delete()");
            return false;
        }
        
        $query = "DELETE FROM " . $this->table . " 
                  WHERE id = ? AND user_id = ?";
        
        try {
            $params = array($this->id, $this->user_id);
            $stmt = sqlsrv_query($this->conn, $query, $params);
            
            if($stmt === false) {
                $errors = sqlsrv_errors();
                error_log("SQL Error in delete: " . print_r($errors, true));
                return false;
            }
            
            return true;
        } catch (Exception $e) {
            error_log("Exception in delete: " . $e->getMessage());
            return false;
        }
    }
}
?>