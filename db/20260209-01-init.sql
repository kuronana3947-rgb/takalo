-- ===============================
-- DATABASE
-- ===============================
DROP DATABASE IF EXISTS takalo_db;
CREATE DATABASE takalo_db;
USE takalo_db;

-- ===============================
-- TABLE USERS
-- ===============================
CREATE TABLE users (
    idUser INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    mdp VARCHAR(255) NOT NULL,
    isAdmin BOOLEAN DEFAULT FALSE
);

INSERT INTO users (email, mdp, isAdmin) VALUES
('admin@troc.com', 'hashed_admin_pwd', TRUE),
('alice@mail.com', 'hashed_pwd_alice', FALSE),
('bob@mail.com', 'hashed_pwd_bob', FALSE),
('carol@mail.com', 'hashed_pwd_carol', FALSE);

-- ===============================
-- TABLE CATEGORIES
-- ===============================
CREATE TABLE categories (
    idCategorie INT AUTO_INCREMENT PRIMARY KEY,
    categorie VARCHAR(100) NOT NULL,
    img VARCHAR(255)
);

INSERT INTO categories (categorie, img) VALUES
('Électronique', 'electronique.png'),
('Vêtements', 'vetements.png'),
('Maison', 'maison.png'),
('Livres', 'livres.png');

-- ===============================
-- TABLE OBJETS
-- ===============================
CREATE TABLE objets (
    idObjet INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    descriptions TEXT,
    prix DECIMAL(10,2) NOT NULL,
    idCategorie INT,
    isValidate BOOLEAN DEFAULT FALSE,
    idProprietaire INT,

    FOREIGN KEY (idCategorie) REFERENCES categories(idCategorie),
    FOREIGN KEY (idProprietaire) REFERENCES users(idUser)
);

INSERT INTO objets (titre, descriptions, prix, idCategorie, isValidate, idProprietaire) VALUES
('iPhone 11', 'Bon état, 64GB', 500.00, 1, TRUE, 2),
('Veste en cuir', 'Taille M, peu portée', 120.00, 2, TRUE, 3),
('Table basse', 'Bois massif', 200.00, 3, FALSE, 4),
('Roman Harry Potter', 'Tome 1', 15.00, 4, TRUE, 2),
('Casque Bluetooth', 'Autonomie 20h', 80.00, 1, TRUE, 3);

-- ===============================
-- TABLE PHOTOS
-- ===============================
CREATE TABLE photos (
    idPhoto INT AUTO_INCREMENT PRIMARY KEY,
    idObjet INT,
    img VARCHAR(255) NOT NULL,

    FOREIGN KEY (idObjet) REFERENCES objets(idObjet) ON DELETE CASCADE
);

INSERT INTO photos (idObjet, img) VALUES
(1, 'iphone11_1.jpg'),
(1, 'iphone11_2.jpg'),
(2, 'veste_1.jpg'),
(3, 'table_1.jpg'),
(4, 'hp1.jpg'),
(5, 'casque.jpg');

-- ===============================
-- TABLE NOTIFICATIONS
-- ===============================
CREATE TABLE notifications (
    idNotification INT AUTO_INCREMENT PRIMARY KEY,
    idSender INT,
    idRecever INT,
    isRead BOOLEAN DEFAULT FALSE,

    FOREIGN KEY (idSender) REFERENCES users(idUser),
    FOREIGN KEY (idRecever) REFERENCES users(idUser)
);

INSERT INTO notifications (idSender, idRecever, isRead) VALUES
(2, 3, FALSE),
(3, 2, TRUE),
(4, 2, FALSE);

-- ===============================
-- TABLE ECHANGES
-- ===============================
CREATE TABLE echanges (
    idEchange INT AUTO_INCREMENT PRIMARY KEY,
    idSender INT,
    idRecever INT,
    idObjetSender INT,
    idObjetRecever INT,
    date DATETIME DEFAULT CURRENT_TIMESTAMP,
    isValidate BOOLEAN DEFAULT FALSE,

    FOREIGN KEY (idSender) REFERENCES users(idUser),
    FOREIGN KEY (idRecever) REFERENCES users(idUser),
    FOREIGN KEY (idObjetSender) REFERENCES objets(idObjet),
    FOREIGN KEY (idObjetRecever) REFERENCES objets(idObjet)
);

INSERT INTO echanges (idSender, idRecever, idObjetSender, idObjetRecever, isValidate) VALUES
(2, 3, 1, 2, FALSE),
(3, 2, 5, 4, TRUE);
