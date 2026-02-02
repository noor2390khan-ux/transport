document.addEventListener('DOMContentLoaded', function() {
    // Form Validation Logic
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            const username = loginForm.username.value.trim();
            const password = loginForm.password.value;
            
            if (!username || !password) {
                e.preventDefault();
                alert('All fields are required.');
            }
        });
    }

    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            const username = registerForm.username.value.trim();
            const email = registerForm.email.value.trim();
            const password = registerForm.password.value;
            
            if (!username || !email || !password) {
                e.preventDefault();
                alert('All fields are required.');
                return;
            }

            if (!validateEmail(email)) {
                e.preventDefault();
                alert('Please enter a valid email address.');
            }
            
            if (password.length < 6) {
                e.preventDefault();
                alert('Password must be at least 6 characters long.');
            }
        });
    }

    const postLoadForm = document.getElementById('postLoadForm');
    if (postLoadForm) {
        postLoadForm.addEventListener('submit', function(e) {
            const weight = parseFloat(postLoadForm.weight.value);
            if (weight <= 0) {
                e.preventDefault();
                alert('Weight must be a positive number.');
            }
        });
    }

    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    // Dynamic Micro-animations (subtle hover effects handled by CSS, 
    // but can add JS enhancements here)
});
