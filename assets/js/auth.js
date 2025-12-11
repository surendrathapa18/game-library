// auth.js  
// Handles Register + Login form actions for the project

document.addEventListener('DOMContentLoaded', () => {

    const registerForm = document.getElementById('registerForm');
    const loginForm = document.getElementById('loginForm');

   
    // REGISTER FORM LOGIC (Used in register.html)
    
    if (registerForm) {
        registerForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            // Get values from register inputs
            const username = document.getElementById('regUsername').value.trim();
            const usernameConfirm = document.getElementById('regUsernameConfirm').value.trim();
            const password = document.getElementById('regPassword').value;
            const passwordConfirm = document.getElementById('regPasswordConfirm').value;

            // Basic client-side validation
            if (username.length < 4 || password.length < 6) {
                alert('Username must be at least 4 characters and password 6 characters.');
                return;
            }
            if (username !== usernameConfirm) {
                alert('Usernames do not match.');
                return;
            }
            if (password !== passwordConfirm) {
                alert('Passwords do not match.');
                return;
            }

            // Send registration data to auth.php
            const fd = new FormData();
            fd.append('action', 'register');
            fd.append('username', username);
            fd.append('username_confirm', usernameConfirm);
            fd.append('password', password);
            fd.append('password_confirm', passwordConfirm);

            const res = await fetch('auth.php', { method: 'POST', body: fd });
            const j = await res.json();

            // On success → send user to login page
            if (j.status === 'success') {
                alert(j.message || 'Account created! Please log in.');
                window.location.href = 'index.html';
            } else {
                alert(j.message || 'Registration failed.');
            }
        });
    }

    
    // LOGIN FORM LOGIC (Used in index.html)
 
    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            // Get login input values
            const username = document.getElementById('loginUser').value.trim();
            const password = document.getElementById('loginPass').value;

            // Send login request to auth.php
            const fd = new FormData();
            fd.append('action', 'login');
            fd.append('username', username);
            fd.append('password', password);

            const res = await fetch('auth.php', { method: 'POST', body: fd });
            const j = await res.json();

            // On success → open Dashboard
            if (j.status === 'success') {
                window.location.href = "dashboard.php";
            } else {
                alert(j.message || 'Invalid login.');
            }
        });
    }

});
