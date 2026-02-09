// Validation du formulaire d'inscription
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registerForm');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirmPassword');
    const termsCheckbox = document.getElementById('terms');
    const newsletterCheckbox = document.getElementById('newsletter');
    
    const emailError = document.getElementById('emailError');
    const passwordError = document.getElementById('passwordError');
    const confirmPasswordError = document.getElementById('confirmPasswordError');
    
    const submitBtn = form.querySelector('.login-btn');

    // Expressions régulières pour la validation
    const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

    // Critères de mot de passe
    const passwordCriteria = {
        length: { regex: /.{8,}/, message: 'Au moins 8 caractères' },
        uppercase: { regex: /[A-Z]/, message: 'Une majuscule' },
        lowercase: { regex: /[a-z]/, message: 'Une minuscule' },
        number: { regex: /\d/, message: 'Un chiffre' },
        special: { regex: /[^A-Za-z0-9]/, message: 'Un caractère spécial' }
    };

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
        
        const errors = [];
        Object.keys(passwordCriteria).forEach(key => {
            if (!passwordCriteria[key].regex.test(password)) {
                errors.push(passwordCriteria[key].message);
            }
        });
        
        if (errors.length > 0) {
            return 'Le mot de passe doit contenir : ' + errors.join(', ');
        }
        
        return null;
    }

    // Validation de la confirmation du mot de passe
    function validateConfirmPassword(password, confirmPassword) {
        if (!confirmPassword) {
            return 'La confirmation du mot de passe est requise';
        }
        if (password !== confirmPassword) {
            return 'Les mots de passe ne correspondent pas';
        }
        return null;
    }

    // Calculer la force du mot de passe
    function calculatePasswordStrength(password) {
        let strength = 0;
        let level = '';
        
        Object.keys(passwordCriteria).forEach(key => {
            if (passwordCriteria[key].regex.test(password)) {
                strength++;
            }
        });

        if (strength <= 1) {
            level = 'weak';
        } else if (strength <= 2) {
            level = 'medium';
        } else if (strength <= 3) {
            level = 'good';
        } else {
            level = 'strong';
        }

        return { strength, level };
    }

    // Mettre à jour l'indicateur de force du mot de passe
    function updatePasswordStrength(password) {
        const strengthBar = document.querySelector('.strength-fill');
        const strengthText = document.querySelector('.strength-text');
        
        if (!password) {
            strengthBar.className = 'strength-fill';
            strengthText.textContent = '';
            strengthText.className = 'strength-text';
            return;
        }

        const { strength, level } = calculatePasswordStrength(password);
        
        strengthBar.className = `strength-fill ${level}`;
        strengthText.className = `strength-text ${level}`;
        
        const strengthTexts = {
            weak: 'Très faible',
            medium: 'Faible',
            good: 'Moyen',
            strong: 'Fort'
        };
        
        strengthText.textContent = strengthTexts[level] || '';
    }

    // Vérifier la disponibilité de l'email (simulation)
    function checkEmailAvailability(email) {
        // Simulation d'une vérification d'email
        return new Promise((resolve) => {
            setTimeout(() => {
                // Emails déjà pris pour la démo
                const takenEmails = ['admin@troc.com', 'alice@mail.com', 'bob@mail.com'];
                resolve(!takenEmails.includes(email.toLowerCase()));
            }, 500);
        });
    }

    // Validation en temps réel pour l'email
    let emailTimeout;
    emailInput.addEventListener('input', function() {
        const email = this.value.trim();
        
        // Reset styles
        if (this.classList.contains('error')) {
            this.classList.remove('error');
            emailError.classList.remove('show');
        }
        
        // Vérification différée de l'email
        clearTimeout(emailTimeout);
        emailTimeout = setTimeout(async () => {
            if (email) {
                const error = validateEmail(email);
                if (error) {
                    showError(this, emailError, error);
                } else {
                    // Vérifier la disponibilité
                    emailError.textContent = 'Vérification...';
                    emailError.classList.add('show');
                    
                    const isAvailable = await checkEmailAvailability(email);
                    if (!isAvailable) {
                        showError(this, emailError, 'Cet email est déjà utilisé');
                    } else {
                        showSuccess(this, emailError);
                    }
                }
            }
        }, 800);
    });

    // Validation en temps réel pour le mot de passe
    passwordInput.addEventListener('input', function() {
        const password = this.value;
        
        // Mettre à jour l'indicateur de force
        updatePasswordStrength(password);
        
        // Reset styles
        if (this.classList.contains('error')) {
            this.classList.remove('error');
            passwordError.classList.remove('show');
        }
        
        // Valider si l'utilisateur a déjà interagi
        if (password.length > 0) {
            const error = validatePassword(password);
            if (error) {
                if (password.length >= 8) { // Ne montrer l'erreur que si assez long
                    showError(this, passwordError, error);
                }
            } else {
                showSuccess(this, passwordError);
            }
        }
        
        // Revalider la confirmation si elle existe
        if (confirmPasswordInput.value) {
            validateConfirmPasswordField();
        }
    });

    // Validation de la confirmation du mot de passe
    function validateConfirmPasswordField() {
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        
        const error = validateConfirmPassword(password, confirmPassword);
        if (error) {
            showError(confirmPasswordInput, confirmPasswordError, error);
            return false;
        } else {
            showSuccess(confirmPasswordInput, confirmPasswordError);
            return true;
        }
    }

    confirmPasswordInput.addEventListener('input', function() {
        // Reset styles
        if (this.classList.contains('error')) {
            this.classList.remove('error');
            confirmPasswordError.classList.remove('show');
        }
        
        // Valider en temps réel
        if (this.value.length > 0) {
            validateConfirmPasswordField();
        }
    });

    // Validation complète du formulaire
    async function validateForm() {
        let isValid = true;
        
        // Valider l'email
        const emailErr = validateEmail(emailInput.value.trim());
        if (emailErr) {
            showError(emailInput, emailError, emailErr);
            isValid = false;
        } else {
            // Vérifier la disponibilité
            const isAvailable = await checkEmailAvailability(emailInput.value.trim());
            if (!isAvailable) {
                showError(emailInput, emailError, 'Cet email est déjà utilisé');
                isValid = false;
            } else {
                showSuccess(emailInput, emailError);
            }
        }

        // Valider le mot de passe
        const passwordErr = validatePassword(passwordInput.value);
        if (passwordErr) {
            showError(passwordInput, passwordError, passwordErr);
            isValid = false;
        } else {
            showSuccess(passwordInput, passwordError);
        }

        // Valider la confirmation
        if (!validateConfirmPasswordField()) {
            isValid = false;
        }

        // Vérifier l'acceptation des conditions
        if (!termsCheckbox.checked) {
            showAlert('Vous devez accepter les conditions d\'utilisation', 'error');
            isValid = false;
        }

        return isValid;
    }

    // Gestion de la soumission du formulaire
    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        // Valider le formulaire
        const isValid = await validateForm();
        if (!isValid) {
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
        formData.append('newsletter', newsletterCheckbox.checked ? '1' : '0');

        // Envoyer les données
        try {
            const response = await fetch('/register', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error('Erreur réseau');
            }

            const data = await response.json();

            if (data.success) {
                // Succès
                showAlert('Compte créé avec succès ! Redirection...', 'success');
                setTimeout(() => {
                    window.location.href = data.redirect || '/login';
                }, 2000);
            } else {
                // Erreur
                showAlert(data.message || 'Une erreur est survenue lors de la création du compte', 'error');
                resetSubmitButton();
            }
        } catch (error) {
            console.error('Erreur:', error);
            showAlert('Une erreur est survenue. Veuillez réessayer.', 'error');
            resetSubmitButton();
        }
    });

    // Fonction pour réinitialiser le bouton de soumission
    function resetSubmitButton() {
        submitBtn.classList.remove('loading');
        submitBtn.disabled = false;
        submitBtn.textContent = 'Créer mon compte';
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
    [emailInput, passwordInput, confirmPasswordInput].forEach(input => {
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                form.dispatchEvent(new Event('submit'));
            }
        });
    });

    // Animation au focus des inputs
    [emailInput, passwordInput, confirmPasswordInput].forEach(input => {
        input.addEventListener('focus', function() {
            this.parentNode.style.transform = 'scale(1.02)';
        });

        input.addEventListener('blur', function() {
            this.parentNode.style.transform = 'scale(1)';
        });
    });

    // Validation des conditions d'utilisation
    termsCheckbox.addEventListener('change', function() {
        if (this.checked) {
            this.parentNode.style.color = '#27ae60';
        } else {
            this.parentNode.style.color = '';
        }
    });
});