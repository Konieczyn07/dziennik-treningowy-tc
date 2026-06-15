CREATE TABLE users (
    id INT IDENTITY(1,1) PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
);

CREATE TABLE workouts (
    id INT IDENTITY(1,1) PRIMARY KEY,
    user_id INT NOT NULL,
    exercise_name VARCHAR(100) NOT NULL,
    sets INT NOT NULL,
    reps INT NOT NULL,
    weight DECIMAL(5,2),
    workout_date DATE NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);