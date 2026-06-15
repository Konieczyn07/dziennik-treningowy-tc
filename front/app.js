const root = document.location.origin;
const AUTH_URL = `${root}/api`;
const API_URL = `${root}/api/index.php`;

const fetchOptions = { credentials: 'include' };

let isEditing = false;
let currentUser = null;


async function login(username, password) {
    const response = await fetch(`${AUTH_URL}/login.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
        body: JSON.stringify({ username, password })
    });

    const data = await response.json();

    if (data.success) {
        currentUser = data.user;
        showAuthMessage(data.message, 'success');
        showApp();
        fetchWorkouts();
    } else {
        showAuthMessage(data.message, 'error');
    }
}

async function register(username, email, password) {
    try{
        const response = await fetch(`${AUTH_URL}/register.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            credentials: 'include',
            body: JSON.stringify({ username, email, password })
        });

        const data = await response.json();

        if (data.success) {
            showAuthMessage(data.message, 'success');
            switchAuthTab('login');
            document.getElementById('login-username').value = username;
        } else {
            showAuthMessage(data.message, 'error');
        }
    }catch (error){
        console.error('Błąd rejestracji: ', error);
    }
}

async function logout() {
    try {
        await fetch(`${AUTH_URL}/logout.php`, {
            method: 'POST',
            credentials: 'include'
        });
    } catch (error) {
        console.error('Błąd wylogowania:', error);
    }

    currentUser = null;
    showAuth();
}

function showApp() {
    document.getElementById('auth-section').classList.add('hidden');
    document.getElementById('app-section').classList.remove('hidden');
    document.getElementById('user-greeting').textContent = `Witaj, ${currentUser.username}!`;
}

function showAuth() {
    document.getElementById('auth-section').classList.remove('hidden');
    document.getElementById('app-section').classList.add('hidden');
    document.getElementById('workouts-container').innerHTML = '';
    resetForm();
}

function showAuthMessage(message, type) {
    const el = document.getElementById('auth-message');
    el.textContent = message;
    el.className = `auth-message ${type}`;
}

function switchAuthTab(tab) {
    document.querySelectorAll('.auth-tab').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.tab === tab);
    });
    document.getElementById('login-panel').classList.toggle('hidden', tab !== 'login');
    document.getElementById('register-panel').classList.toggle('hidden', tab !== 'register');
    document.getElementById('auth-message').textContent = '';
    document.getElementById('auth-message').className = 'auth-message';
}

// --- Workouts ---

async function fetchWorkouts() {
    try {
        const response = await fetch(API_URL, fetchOptions);

        if (response.status === 401) {
            showAuth();
            showAuthMessage('Sesja wygasła. Zaloguj się ponownie.', 'error');
            return;
        }

        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('API nie zwróciło JSON');
        }

        const workouts = await response.json();

        if (Array.isArray(workouts)) {
            displayWorkouts(workouts);
        } else if (workouts.error) {
            document.getElementById('workouts-container').innerHTML =
                `<div class="empty-message">${escapeHtml(workouts.message)}</div>`;
        } else {
            document.getElementById('workouts-container').innerHTML =
                '<div class="empty-message">Błąd: API nie zwróciło danych</div>';
        }
    } catch (error) {
        console.error('Błąd pobierania:', error);
        document.getElementById('workouts-container').innerHTML =
            '<div class="empty-message">Błąd połączenia z API.</div>';
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
    try {
        const response = await fetch(API_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            credentials: 'include',
            body: JSON.stringify(workoutData)
        });

        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            const text = await response.text();
            throw new Error('API zwróciło błąd serwera: ' + text.substring(0, 200));
        }

        const result = await response.json();

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
    try {
        const response = await fetch(API_URL, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            credentials: 'include',
            body: JSON.stringify({ id, ...workoutData })
        });

        const result = await response.json();

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

    try {
        const response = await fetch(`${API_URL}?id=${id}`, {
            method: 'DELETE',
            credentials: 'include'
        });

        const result = await response.json();

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
    try {
        const response = await fetch(`${API_URL}?id=${id}`, fetchOptions);
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
    document.getElementById('cancel-btn').style.display = 'none';
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

// --- Event listeners ---

document.querySelectorAll('.auth-tab').forEach(btn => {
    btn.addEventListener('click', () => switchAuthTab(btn.dataset.tab));
});

document.getElementById('login-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const username = document.getElementById('login-username').value.trim();
    const password = document.getElementById('login-password').value;
    await login(username, password);
});

document.getElementById('register-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const username = document.getElementById('register-username').value.trim();
    const email = document.getElementById('register-email').value.trim();
    const password = document.getElementById('register-password').value;
    await register(username, email, password);
});

document.getElementById('logout-btn').addEventListener('click', logout);

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

document.addEventListener('DOMContentLoaded', async () => {
    const loggedIn = await checkAuth();
    if (loggedIn) {
        fetchWorkouts();
    }
});
