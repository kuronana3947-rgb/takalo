# Système d'Authentification Takalo

Ce système d'authentification moderne inclut des formulaires de connexion et d'inscription avec validation JavaScript et sécurité renforcée.

## Fonctionnalités

### 🔐 Connexion
- Formulaire moderne avec validation en temps réel
- Validation côté client (JavaScript) et serveur (PHP)
- Support "Se souvenir de moi"
- Gestion des erreurs avec messages utilisateur
- Sécurité avec hashage des mots de passe
- Interface responsive

### 📝 Inscription
- Validation de mot de passe avec critères de sécurité
- Vérification de disponibilité d'email en temps réel
- Indicateur de force du mot de passe
- Validation des conditions d'utilisation
- Option newsletter

### 🎨 Interface
- Design moderne avec gradient et glassmorphism
- Animations fluides
- Responsive (mobile/desktop)
- Accessibilité améliorée

## Structure des fichiers

```
app/
├── views/
│   ├── login.php           # Page de connexion
│   └── register.php        # Page d'inscription
├── controllers/
│   └── Authentification.php # Contrôleur d'authentification
└── models/
    └── Users.php           # Modèle utilisateur

public/
├── css/
│   ├── login.css           # Styles pour les formulaires
│   └── register.css        # Styles spécifiques inscription
└── js/
    ├── login.js            # Validation connexion
    └── register.js         # Validation inscription

db/
├── 20260209-01-init.sql       # Base de données initiale
├── migration-password-hash.sql # Migration SQL
└── migrate-passwords.php      # Script de migration PHP
```

## Installation et Configuration

### 1. Base de données
Exécutez le script de base de données initial :
```bash
mysql -u root -p < db/20260209-01-init.sql
```

### 2. Migration des mots de passe
Pour sécuriser les mots de passe existants :
```bash
php db/migrate-passwords.php
```

### 3. Configuration des routes
Les routes sont déjà configurées dans `app/config/routes.php` :
- `GET /login` - Afficher formulaire de connexion
- `POST /login` - Traiter connexion
- `GET /register` - Afficher formulaire d'inscription
- `POST /register` - Traiter inscription
- `GET /logout` - Déconnexion
- `GET /api/check-email` - Vérifier disponibilité email

## Utilisation

### Connexion
URL: `/login`
- Email: admin@troc.com
- Mot de passe: admin123

Autres comptes de test :
- alice@mail.com / alice123
- bob@mail.com / bob123
- carol@mail.com / carol123

### Inscription
URL: `/register`
- Validation en temps réel
- Critères de mot de passe :
  - Minimum 8 caractères
  - Au moins une majuscule
  - Au moins une minuscule
  - Au moins un chiffre
  - Au moins un caractère spécial

## Validation JavaScript

### Côté Client
- Validation en temps réel des champs
- Messages d'erreur contextuels
- Indicateurs visuels (rouge/vert)
- Vérification de force du mot de passe
- Requêtes AJAX pour disponibilité email

### Côté Serveur
- Validation de sécurité complète
- Protection contre injections
- Hashage sécurisé des mots de passe
- Gestion des sessions
- Réponses JSON pour AJAX

## Sécurité

### Mots de passe
- Hashage avec `password_hash()` PHP
- Vérification avec `password_verify()`
- Critères de complexité obligatoires
- Migration automatique des anciens mots de passe

### Sessions
- Démarrage sécurisé des sessions
- Nettoyage complet à la déconnexion
- Gestion des cookies "Se souvenir de moi"

### Validation
- Protection XSS
- Validation stricte des emails
- Échappement des données utilisateur
- Requêtes préparées SQL

## API Endpoints

### POST /login
```json
{
  "email": "user@example.com",
  "password": "password123",
  "remember": true
}
```

Réponse succès :
```json
{
  "success": true,
  "message": "Connexion réussie",
  "redirect": "/dashboard"
}
```

### POST /register
```json
{
  "email": "newuser@example.com",
  "password": "SecurePassword123!",
  "newsletter": true
}
```

### GET /api/check-email?email=test@example.com
```json
{
  "available": true
}
```

## Personnalisation

### Styles CSS
Modifiez les fichiers CSS pour adapter l'apparence :
- Variables CSS pour couleurs
- Animations personnalisables
- Responsive breakpoints

### Messages d'erreur
Les messages sont configurables dans les contrôleurs :
- Français par défaut
- Localisation possible

### Validation
Ajustez les critères dans les fichiers JavaScript :
- Longueur minimale du mot de passe
- Critères de complexité
- Format email

## Dépannage

### Erreurs courantes
1. **"Email déjà utilisé"** - Vérifiez la base de données
2. **Erreur de connexion** - Vérifiez les mots de passe hashés
3. **CSS/JS non chargé** - Vérifiez les chemins des fichiers statiques

### Debug
Activez les logs d'erreur PHP pour diagnostiquer les problèmes serveur.

## Évolutions possibles

- [ ] Authentification à deux facteurs
- [ ] Réinitialisation par email
- [ ] Connexion OAuth (Google, Facebook)
- [ ] Limitation des tentatives de connexion
- [ ] Historique des connexions
- [ ] API REST complète