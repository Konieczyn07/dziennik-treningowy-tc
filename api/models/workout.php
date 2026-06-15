<?php
class Workout {
    private $conn;
    private $table = "workouts";

    public $id;
    public $exercise_name;
    public $sets;
    public $reps;
    public $weight;
    public $workout_date;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT id, exercise_name, sets, reps, weight, workout_date, created_at 
                  FROM " . $this->table . " 
                  ORDER BY workout_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getSingle() {
        if(empty($this->id)) {
            return false;
        }
        
        $query = "SELECT id, exercise_name, sets, reps, weight, workout_date, created_at 
                  FROM " . $this->table . " 
                  WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create() {
        if(empty($this->exercise_name) || empty($this->sets) || empty($this->reps) || empty($this->workout_date)) {
            return false;
        }
        
        $query = "INSERT INTO " . $this->table . " 
                  (exercise_name, sets, reps, weight, workout_date) 
                  VALUES 
                  (:exercise_name, :sets, :reps, :weight, :workout_date)
                  RETURNING id";
        
        $stmt = $this->conn->prepare($query);
        
        $this->exercise_name = htmlspecialchars(strip_tags($this->exercise_name));
        $this->sets = (int)$this->sets;
        $this->reps = (int)$this->reps;
        $this->weight = (float)($this->weight ?? 0);
        $this->workout_date = htmlspecialchars(strip_tags($this->workout_date));
        
        $stmt->bindParam(":exercise_name", $this->exercise_name);
        $stmt->bindParam(":sets", $this->sets, PDO::PARAM_INT);
        $stmt->bindParam(":reps", $this->reps, PDO::PARAM_INT);
        $stmt->bindParam(":weight", $this->weight);
        $stmt->bindParam(":workout_date", $this->workout_date);
        
        if($stmt->execute()) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $result['id'];
            return true;
        }
        return false;
    }

    public function update() {
        if(empty($this->id)) {
            return false;
        }
        
        $query = "UPDATE " . $this->table . " 
                  SET exercise_name = :exercise_name,
                      sets = :sets,
                      reps = :reps,
                      weight = :weight,
                      workout_date = :workout_date 
                  WHERE id = :id";
        
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
        
        return $stmt->execute();
    }

    public function delete() {
        if(empty($this->id)) {
            return false;
        }
        
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $this->id = (int)$this->id;
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    public function getStats() {
        $query = "SELECT 
                    COUNT(*) as total_workouts,
                    COUNT(DISTINCT exercise_name) as total_exercises,
                    SUM(sets * reps) as total_reps,
                    AVG(weight) as avg_weight
                  FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>