<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Takalo - Inscription</title>
    <link rel="stylesheet" href="/css/login.css">
    <link rel="stylesheet" href="/css/register.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h1 class="login-title">Takalo</h1>
            <p class="login-subtitle">Créez votre compte</p>
            
            <form id="registerForm" action="/register" method="post" novalidate>
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" placeholder="votre@email.com" required>
                    <span class="error-message" id="emailError"></span>
                </div>
                
                <div class="form-group">
                    <label for="password">Mot de passe *</label>
                    <input type="password" id="password" name="password" placeholder="Votre mot de passe" required>
                    <div class="password-strength">
                        <div class="strength-bar">
                            <div class="strength-fill"></div>
                        </div>
                        <span class="strength-text"></span>
                    </div>
                    <span class="error-message" id="passwordError"></span>
                </div>
                
                <div class="form-group">
                    <label for="confirmPassword">Confirmer le mot de passe *</label>
                    <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirmez votre mot de passe" required>
                    <span class="error-message" id="confirmPasswordError"></span>
                </div>
                
                <div class="form-options">
                    <label class="checkbox-container">
                        <input type="checkbox" id="terms" name="terms" required>
                        <span class="checkmark"></span>
                        J'accepte les <a href="/terms" target="_blank">conditions d'utilisation</a>
                    </label>
                </div>
                
                <div class="form-options">
                    <label class="checkbox-container">
                        <input type="checkbox" id="newsletter" name="newsletter">
                        <span class="checkmark"></span>
                        Je souhaite recevoir les newsletters
                    </label>
                </div>
                
                <button type="submit" class="login-btn">Créer mon compte</button>
                
                <div class="signup-link">
                    <p>Déjà un compte ? <a href="/login">Se connecter</a></p>
                </div>
            </form>
        </div>
    </div>
    
    <script src="/js/register.js"></script>
</body>
</html>