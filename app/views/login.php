<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Takalo - Connexion</title>
    <link rel="stylesheet" href="/css/login.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h1 class="login-title">Takalo</h1>
            <p class="login-subtitle">Connectez-vous à votre compte</p>
            
            <form id="loginForm" action="/log" method="post" novalidate>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" value="admin@troc.com" id="email" name="email" placeholder="votre@email.com" required>
                    <span class="error-message" id="emailError"></span>
                </div>
                
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" value="hashed_admin_pwd" id="password" name="password" placeholder="Votre mot de passe" required>
                    <span class="error-message" id="passwordError"></span>
                </div>
                
                <div class="form-options">
                    <label class="checkbox-container">
                        <input type="checkbox" id="remember" name="remember">
                        <span class="checkmark"></span>
                        Se souvenir de moi
                    </label>
                    <a href="#" class="forgot-password">Mot de passe oublié ?</a>
                </div>
                
                <button type="submit" class="login-btn">Se connecter</button>
                
                <div class="signup-link">
                    <p>Pas encore de compte ? <a href="/register">Créer un compte</a></p>
                </div>
            </form>
        </div>
    </div>
    
    <script src="/js/login.js"></script>
</body>
</html>