// Validation du formulaire de connexion
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('loginForm');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const emailError = document.getElementById('emailError');
    const passwordError = document.getElementById('passwordError');
    const submitBtn = form.querySelector('.login-btn');

    // Expressions régulières pour la validation
    const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    const passwordMinLength = 6;

    // Fonction pour afficher les erreurs
    function showError(input, errorElement, message) {
        input.classList.remove('success');
        input.classList.add('error');
        errorElement.textContent = message;
        errorElement.classList.add('show');
    }

    // Fonction pour afficher le succès
    function showSuccess(input, errorElement) {
        input.classList.remove('error');
        input.classList.add('success');
        errorElement.textContent = '';
        errorElement.classList.remove('show');
    }

    // Validation de l'email
    function validateEmail(email) {
        if (!email) {
            return 'L\'email est requis';
        }
        if (!emailRegex.test(email)) {
            return 'Veuillez entrer un email valide';
        }
        return null;
    }

    // Validation du mot de passe
    function validatePassword(password) {
        if (!password) {
            return 'Le mot de passe est requis';
        }
        if (password.length < passwordMinLength) {
            return `Le mot de passe doit contenir au moins ${passwordMinLength} caractères`;
        }
        return null;
    }

    // Validation en temps réel pour l'email
    emailInput.addEventListener('blur', function() {
        const error = validateEmail(this.value.trim());
        if (error) {
            showError(this, emailError, error);
        } else {
            showSuccess(this, emailError);
        }
    });

    // Validation en temps réel pour le mot de passe
    passwordInput.addEventListener('blur', function() {
        const error = validatePassword(this.value);
        if (error) {
            showError(this, passwordError, error);
        } else {
            showSuccess(this, passwordError);
        }
    });

    // Réinitialiser les styles quand l'utilisateur commence à taper
    emailInput.addEventListener('input', function() {
        if (this.classList.contains('error')) {
            this.classList.remove('error');
            emailError.classList.remove('show');
        }
    });

    passwordInput.addEventListener('input', function() {
        if (this.classList.contains('error')) {
            this.classList.remove('error');
            passwordError.classList.remove('show');
        }
    });

    // Validation complète du formulaire
    function validateForm() {
        let isValid = true;
        
        // Valider l'email
        const emailErr = validateEmail(emailInput.value.trim());
        if (emailErr) {
            showError(emailInput, emailError, emailErr);
            isValid = false;
        } else {
            showSuccess(emailInput, emailError);
        }

        // Valider le mot de passe
        const passwordErr = validatePassword(passwordInput.value);
        if (passwordErr) {
            showError(passwordInput, passwordError, passwordErr);
            isValid = false;
        } else {
            showSuccess(passwordInput, passwordError);
        }

        return isValid;
    }

    // Gestion de la soumission du formulaire
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Valider le formulaire
        if (!validateForm()) {
            return false;
        }

        // Animation de chargement
        submitBtn.classList.add('loading');
        submitBtn.disabled = true;
        submitBtn.textContent = '';

        // Préparer les données
        const formData = new FormData();
        formData.append('email', emailInput.value.trim());
        formData.append('password', passwordInput.value);
        
        if (document.getElementById('remember').checked) {
            formData.append('remember', '1');
        }

        // Envoyer les données
        fetch('/log', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erreur réseau');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Succès - rediriger
                showAlert('Connexion réussie ! Redirection...', 'success');
                setTimeout(() => {
                    window.location.href = data.redirect || '/dashboard';
                }, 1500);
            } else {
                // Erreur de connexion
                showAlert(data.message || 'Email ou mot de passe incorrect', 'error');
                resetSubmitButton();
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showAlert('Une erreur est survenue. Veuillez réessayer.', 'error');
            resetSubmitButton();
        });
    });

    // Fonction pour réinitialiser le bouton de soumission
    function resetSubmitButton() {
        submitBtn.classList.remove('loading');
        submitBtn.disabled = false;
        submitBtn.textContent = 'Se connecter';
    }

    // Fonction pour afficher les alertes
    function showAlert(message, type) {
        // Supprimer l'ancienne alerte si elle existe
        const existingAlert = document.querySelector('.alert');
        if (existingAlert) {
            existingAlert.remove();
        }

        // Créer la nouvelle alerte
        const alert = document.createElement('div');
        alert.className = `alert alert-${type}`;
        alert.textContent = message;

        // Insérer l'alerte au début du formulaire
        form.insertBefore(alert, form.firstChild);

        // Supprimer l'alerte après 5 secondes
        setTimeout(() => {
            if (alert.parentNode) {
                alert.remove();
            }
        }, 5000);
    }

    // Gestion de la touche Entrée
    [emailInput, passwordInput].forEach(input => {
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                form.dispatchEvent(new Event('submit'));
            }
        });
    });

    // Animation au focus des inputs
    [emailInput, passwordInput].forEach(input => {
        input.addEventListener('focus', function() {
            this.parentNode.style.transform = 'scale(1.02)';
        });

        input.addEventListener('blur', function() {
            this.parentNode.style.transform = 'scale(1)';
        });
    });

    // Effet de parallaxe léger sur le fond
    document.addEventListener('mousemove', function(e) {
        const loginBox = document.querySelector('.login-box');
        const x = (e.clientX / window.innerWidth) * 10;
        const y = (e.clientY / window.innerHeight) * 10;
        
        loginBox.style.transform = `translateX(${x}px) translateY(${y}px)`;
    });

    // Vérification de la force du mot de passe (optionnel)
    passwordInput.addEventListener('input', function() {
        const password = this.value;
        if (password.length >= passwordMinLength) {
            let strength = 0;
            
            // Critères de force
            if (password.length >= 8) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/\d/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;

            // Afficher un indicateur de force (optionnel)
            let strengthText = '';
            let strengthClass = '';
            
            if (strength <= 2) {
                strengthText = 'Faible';
                strengthClass = 'weak';
            } else if (strength <= 3) {
                strengthText = 'Moyen';
                strengthClass = 'medium';
            } else {
                strengthText = 'Fort';
                strengthClass = 'strong';
            }
        }
    });
});

// Fonction utilitaire pour gérer les cookies (si "Se souvenir de moi" est coché)
function setCookie(name, value, days) {
    const expires = new Date();
    expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000));
    document.cookie = `${name}=${value};expires=${expires.toUTCString()};path=/;SameSite=Strict`;
}

function getCookie(name) {
    const nameEQ = name + "=";
    const ca = document.cookie.split(';');
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) === ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}

// Pré-remplir l'email si "Se souvenir de moi" était coché
document.addEventListener('DOMContentLoaded', function() {
    const rememberedEmail = getCookie('remembered_email');
    if (rememberedEmail) {
        document.getElementById('email').value = rememberedEmail;
        document.getElementById('remember').checked = true;
    }
});