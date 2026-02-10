# Takalo

Plateforme web d'échange d'objets entre utilisateurs.

## Technologies

- PHP 8 / FlightPHP
- MySQL / PDO
- JavaScript (Vanilla)
- CSS3

## Entités

### Users
| Colonne  | Type         | Contrainte       |
|----------|--------------|------------------|
| idUser   | INT          | PRIMARY KEY, AUTO_INCREMENT |
| email    | VARCHAR(255) | NOT NULL, UNIQUE |
| mdp      | VARCHAR(255) | NOT NULL         |
| isAdmin  | BOOLEAN      | DEFAULT FALSE    |

### Categories
| Colonne     | Type         | Contrainte       |
|-------------|--------------|------------------|
| idCategorie | INT          | PRIMARY KEY, AUTO_INCREMENT |
| categorie   | VARCHAR(100) | NOT NULL         |
| img         | VARCHAR(255) |                  |

### Objets
| Colonne        | Type          | Contrainte       |
|----------------|---------------|------------------|
| idObjet        | INT           | PRIMARY KEY, AUTO_INCREMENT |
| titre          | VARCHAR(255)  | NOT NULL         |
| descriptions   | TEXT          |                  |
| prix           | DECIMAL(10,2) | NOT NULL         |
| idCategorie    | INT           | FK → categories  |
| isValidate     | BOOLEAN       | DEFAULT FALSE    |
| idProprietaire | INT           | FK → users       |

### Photos
| Colonne  | Type         | Contrainte                  |
|----------|--------------|-----------------------------|
| idPhoto  | INT          | PRIMARY KEY, AUTO_INCREMENT |
| idObjet  | INT          | FK → objets (ON DELETE CASCADE) |
| img      | VARCHAR(255) | NOT NULL                    |

### Notifications
| Colonne        | Type    | Contrainte       |
|----------------|---------|------------------|
| idNotification | INT     | PRIMARY KEY, AUTO_INCREMENT |
| idSender       | INT     | FK → users       |
| idRecever      | INT     | FK → users       |
| isRead         | BOOLEAN | DEFAULT FALSE    |

### Echanges
| Colonne        | Type     | Contrainte       |
|----------------|----------|------------------|
| idEchange      | INT      | PRIMARY KEY, AUTO_INCREMENT |
| idSender       | INT      | FK → users       |
| idRecever      | INT      | FK → users       |
| idObjetSender  | INT      | FK → objets      |
| idObjetRecever | INT      | FK → objets      |
| date           | DATETIME | DEFAULT CURRENT_TIMESTAMP |
| isValidate     | BOOLEAN  | DEFAULT FALSE    |

## Fonctionnalités

### Authentification
- Inscription avec email et mot de passe
- Connexion / Déconnexion
- Vérification de disponibilité d'email en temps réel
- Hashage des mots de passe (bcrypt)
- Protection des routes par session

### Gestion des objets
- Ajout d'un objet avec titre, description, prix estimé et catégorie
- Upload de plusieurs photos par objet
- Affichage détaillé d'un objet avec galerie photos
- Suppression d'un objet (propriétaire uniquement)

### Catégories
- Liste de toutes les catégories
- Affichage des objets par catégorie

### Échanges
- Proposition d'échange depuis la page détail d'un objet
- Sélection d'un de ses propres objets à proposer
- Acceptation ou refus d'une proposition reçue
- Historique des échanges validés

### Notifications
- Notification à la réception d'une proposition d'échange
- Notification lors de l'acceptation ou du refus
- Badge de compteur sur l'icône de notification

### Profil
- Affichage de ses propres objets
- Consultation des objets d'un autre utilisateur

### Dashboard (Admin)
- Statistiques : nombre d'utilisateurs, objets, échanges, catégories
- Liste des échanges récents

## Structure du projet

```
app/
├── config/
│   ├── bootstrap.php
│   ├── config.php
│   ├── routes.php
│   └── services.php
├── controllers/
│   └── Authentification.php
├── middlewares/
│   └── SecurityHeadersMiddleware.php
├── models/
│   ├── Users.php
│   ├── Categories.php
│   ├── Objets.php
│   ├── Photos.php
│   ├── Echanges.php
│   └── Notifications.php
└── views/
    ├── model.php
    ├── login.php
    ├── register.php
    ├── home.php
    ├── categories.php
    ├── categorie-objets.php
    ├── echanges.php
    ├── historique.php
    ├── profil.php
    ├── objet.php
    └── user-objets.php
db/
└── 20260209-01-init.sql
public/
├── index.php
├── css/
│   └── app.css
├── js/
│   └── app.js
└── images/
    └── objets/
```

## Installation

1. Cloner le projet
```bash
git clone https://github.com/votre-repo/takalo-1.git
cd takalo-1
```

2. Installer les dépendances
```bash
composer install
```

3. Créer la base de données
```bash
mysql -u root -p < db/20260209-01-init.sql
```

4. Configurer la connexion
```bash
cp app/config/config_sample.php app/config/config.php
```

5. Lancer le serveur
```bash
php -S localhost:8000 -t public
```
