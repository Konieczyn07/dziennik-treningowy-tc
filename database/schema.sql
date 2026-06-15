CREATE TABLE IF NOT EXISTS workouts (
    id SERIAL PRIMARY KEY,
    exercise_name VARCHAR(100) NOT NULL,
    sets INTEGER NOT NULL CHECK (sets > 0),
    reps INTEGER NOT NULL CHECK (reps > 0),
    weight DECIMAL(5,2) DEFAULT 0.00,
    workout_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_workouts_date ON workouts(workout_date);

COMMENT ON TABLE workouts IS 'Tabela przechowująca historię treningów użytkownika';