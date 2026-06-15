const API_URL = window.API_BASE_URL || '/api/index.php';

let isEditing = false;

async function fetchWorkouts() {
    console.log('Pobieram dane z:', API_URL);
    
    try {
        const response = await fetch(API_URL);
        
        console.log('Status odpowiedzi:', response.status);
        console.log('Content-Type:', response.headers.get('content-type'));
        
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            const text = await response.text();
            console.error('Odpowiedź nie jest JSON. Pierwsze 100 znaków:', text.substring(0, 100));
            throw new Error('API nie zwróciło JSON. Sprawdź ścieżkę!');
        }
        
        const workouts = await response.json();
        console.log('Otrzymane dane:', workouts);
        
        if (Array.isArray(workouts)) {
            displayWorkouts(workouts);
        } else {
            console.error('API nie zwróciło tablicy:', workouts);
            document.getElementById('workouts-container').innerHTML = 
                '<div class="empty-message">Błąd: API nie zwróciło danych</div>';
        }
    } catch (error) {
        console.error('Błąd pobierania:', error);
        document.getElementById('workouts-container').innerHTML = 
            '<div class="empty-message">Błąd połączenia z API.<br>' + 
            'Sprawdź czy ścieżka jest poprawna:<br>' +
            '<code>' + API_URL + '</code></div>';
    }
}

function displayWorkouts(workouts) {
    const container = document.getElementById('workouts-container');
    
    if (!workouts || workouts.length === 0) {
        container.innerHTML = '<div class="empty-message">Brak treningów. Dodaj pierwszy!</div>';
        return;
    }
    
    container.innerHTML = workouts.map(workout => `
        <div class="workout-card" data-id="${workout.id}">
            <h3>${escapeHtml(workout.exercise_name)}</h3>
            <div class="workout-details">
                <span>Serie: ${workout.sets}</span>
                <span>Powtórzenia: ${workout.reps}</span>
                <span>Ciężar: ${workout.weight} kg</span>
                <span>Data: ${workout.workout_date}</span>
            </div>
            <div class="workout-actions">
                <button class="edit-btn" onclick="editWorkout(${workout.id})">Edytuj</button>
                <button class="delete-btn" onclick="deleteWorkout(${workout.id})">Usuń</button>
            </div>
        </div>
    `).join('');
}

async function addWorkout(workoutData) {
    console.log('Dodaję trening:', workoutData);
    
    try {
        const response = await fetch(API_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(workoutData)
        });
        
        const result = await response.json();
        console.log('Odpowiedź API:', result);
        
        if (result.message) {
            alert(result.message);
            fetchWorkouts();
            resetForm();
        } else {
            alert('Błąd: ' + JSON.stringify(result));
        }
    } catch (error) {
        console.error('Błąd dodawania:', error);
        alert('Błąd połączenia: ' + error.message);
    }
}

async function updateWorkout(id, workoutData) {
    console.log('Aktualizuję trening ID:', id, workoutData);
    
    try {
        const response = await fetch(API_URL, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id, ...workoutData })
        });
        
        const result = await response.json();
        console.log('Odpowiedź API:', result);
        
        if (result.message) {
            alert(result.message);
            fetchWorkouts();
            resetForm();
        } else {
            alert('Błąd: ' + JSON.stringify(result));
        }
    } catch (error) {
        console.error('Błąd aktualizacji:', error);
        alert('Błąd połączenia: ' + error.message);
    }
}

async function deleteWorkout(id) {
    if (!confirm('Czy na pewno chcesz usunąć ten trening?')) return;
    
    console.log('Usuwam trening ID:', id);
    
    try {
        const response = await fetch(`${API_URL}?id=${id}`, {
            method: 'DELETE'
        });
        
        const result = await response.json();
        console.log('Odpowiedź API:', result);
        
        if (result.message) {
            alert(result.message);
            fetchWorkouts();
        } else {
            alert('Błąd: ' + JSON.stringify(result));
        }
    } catch (error) {
        console.error('Błąd usuwania:', error);
        alert('Błąd połączenia: ' + error.message);
    }
}

async function editWorkout(id) {
    console.log('Edycja treningu ID:', id);
    
    try {
        const response = await fetch(`${API_URL}?id=${id}`);
        const workout = await response.json();
        
        if (!workout || !workout.id) {
            alert('Nie znaleziono treningu');
            return;
        }
        
        document.getElementById('workout-id').value = workout.id;
        document.getElementById('exercise-name').value = workout.exercise_name;
        document.getElementById('sets').value = workout.sets;
        document.getElementById('reps').value = workout.reps;
        document.getElementById('weight').value = workout.weight;
        document.getElementById('workout-date').value = workout.workout_date;
        
        document.getElementById('form-title').textContent = 'Edytuj trening';
        document.getElementById('submit-btn').textContent = 'Zapisz zmiany';
        document.getElementById('cancel-btn').style.display = 'inline-block';
        
        isEditing = true;
    } catch (error) {
        console.error('Błąd pobierania treningu do edycji:', error);
        alert('Błąd: ' + error.message);
    }
}

function resetForm() {
    document.getElementById('workout-form').reset();
    document.getElementById('workout-id').value = '';
    document.getElementById('form-title').textContent = 'Dodaj trening';
    document.getElementById('submit-btn').textContent = 'Dodaj';
    isEditing = false;
}

function escapeHtml(str) {
    if (!str) return '';
    return str
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

document.getElementById('workout-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const workoutData = {
        exercise_name: document.getElementById('exercise-name').value,
        sets: parseInt(document.getElementById('sets').value),
        reps: parseInt(document.getElementById('reps').value),
        weight: parseFloat(document.getElementById('weight').value) || 0,
        workout_date: document.getElementById('workout-date').value
    };
    
    if (!workoutData.exercise_name || !workoutData.sets || !workoutData.reps || !workoutData.workout_date) {
        alert('Wypełnij wszystkie wymagane pola!');
        return;
    }
    
    const id = document.getElementById('workout-id').value;
    
    if (isEditing && id) {
        await updateWorkout(id, workoutData);
    } else {
        await addWorkout(workoutData);
    }
});

document.getElementById('cancel-btn').addEventListener('click', resetForm);

document.addEventListener('DOMContentLoaded', () => {
    console.log('Strona załadowana, pobieram dane...');
    console.log('API_URL =', API_URL);
    fetchWorkouts();
});