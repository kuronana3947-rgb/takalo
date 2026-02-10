<?php
/**
 * model.php — Layout principal
 * Variables attendues : $namePage, $data, $user
 */
$namePage = $namePage ?? 'home';
$data     = $data ?? [];
$user     = $user ?? null;
$isAdmin  = !empty($user['isAdmin']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Takalo — <?= ucfirst($namePage) ?></title>
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>

<!-- ===== HEADER ===== -->
<header class="header">
    <div class="header-left">
        <button class="menu-toggle" id="menuToggle">&#9776;</button>
        <a href="/home" class="logo">Takalo</a>
    </div>

    <div class="header-right">
        <!-- Bouton ajouter un objet -->
        <button class="btn btn-primary" id="btnAddObjet">
            <span class="icon">+</span> Ajouter un objet
        </button>

        <!-- Notifications -->
        <div class="notif-wrapper" id="notifWrapper">
            <button class="btn-icon" id="btnNotif">
                🔔
                <?php if (!empty($notifications) && count(array_filter($notifications, fn($n) => !$n['isRead'])) > 0): ?>
                    <span class="badge"><?= count(array_filter($notifications, fn($n) => !$n['isRead'])) ?></span>
                <?php endif; ?>
            </button>
            <div class="notif-dropdown" id="notifDropdown">
                <h4>Notifications</h4>
                <?php if (!empty($notifications)): ?>
                    <?php foreach ($notifications as $n): ?>
                        <div class="notif-item <?= $n['isRead'] ? '' : 'unread' ?>">
                            <span>Échange proposé par <?= htmlspecialchars($n['senderEmail'] ?? 'utilisateur #'.$n['idSender']) ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="notif-empty">Aucune notification</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Profil -->
        <div class="profile-wrapper" id="profileWrapper">
            <button class="btn-icon" id="btnProfile">👤</button>
            <div class="profile-dropdown" id="profileDropdown">
                <p class="profile-email"><?= htmlspecialchars($user['email'] ?? '') ?></p>
                <a href="/profil">Mon profil</a>
                <a href="/logout" class="logout">Déconnexion</a>
            </div>
        </div>
    </div>
</header>

<!-- ===== SIDEBAR ===== -->
<aside class="sidebar" id="sidebar">
    <nav class="sidebar-nav">
        <?php if ($isAdmin): ?>
            <a href="/home" class="nav-item <?= $namePage === 'home' ? 'active' : '' ?>">
                <span class="nav-icon">📊</span> Dashboard
            </a>
        <?php endif; ?>
        <a href="/categories" class="nav-item <?= $namePage === 'categories' ? 'active' : '' ?>">
            <span class="nav-icon">📁</span> Catégories
        </a>
        <a href="/echanges" class="nav-item <?= $namePage === 'echanges' ? 'active' : '' ?>">
            <span class="nav-icon">🔄</span> Échanges
        </a>
        <a href="/profil" class="nav-item <?= $namePage === 'profil' ? 'active' : '' ?>">
            <span class="nav-icon">📦</span> Mes objets
        </a>
        <a href="/historique" class="nav-item <?= $namePage === 'historique' ? 'active' : '' ?>">
            <span class="nav-icon">📜</span> Historique
        </a>
    </nav>
</aside>

<!-- ===== CONTENU PRINCIPAL ===== -->
<main class="main-content">
    <?php
        $viewFile = __DIR__ . '/' . $namePage . '.php';
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            echo '<p>Page introuvable.</p>';
        }
    ?>
</main>

<!-- ===== MODAL AJOUT OBJET ===== -->
<div class="modal-overlay" id="modalObjet">
    <div class="modal">
        <div class="modal-header">
            <h2>Ajouter un objet</h2>
            <button class="modal-close" id="modalClose">&times;</button>
        </div>
        <form id="formAddObjet" action="/objets/add" method="post" enctype="multipart/form-data">
            <div class="modal-body">
                <div class="form-group">
                    <label for="titre">Titre *</label>
                    <input type="text" id="titre" name="titre" required placeholder="Ex : iPhone 11">
                </div>
                <div class="form-group">
                    <label for="descriptions">Description</label>
                    <textarea id="descriptions" name="descriptions" rows="3" placeholder="Décrivez votre objet..."></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="prix">Prix estimé (Ar) *</label>
                        <input type="number" id="prix" name="prix" step="0.01" min="0" required placeholder="0.00">
                    </div>
                    <div class="form-group">
                        <label for="idCategorie">Catégorie *</label>
                        <select id="idCategorie" name="idCategorie" required>
                            <option value="">-- Choisir --</option>
                            <?php if (!empty($allCategories)): ?>
                                <?php foreach ($allCategories as $cat): ?>
                                    <option value="<?= $cat['idCategorie'] ?>"><?= htmlspecialchars($cat['categorie']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                <!-- Upload photos multiples -->
                <div class="form-group">
                    <label>Photos (plusieurs possibles)</label>
                    <div class="photo-upload" id="photoUpload">
                        <input type="file" id="photoInput" name="photos[]" multiple accept="image/*" hidden>
                        <button type="button" class="btn btn-outline" id="btnSelectPhotos">📷 Choisir des photos</button>
                        <div class="photo-preview" id="photoPreview"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="modalCancel">Annuler</button>
                <button type="submit" class="btn btn-primary">Publier</button>
            </div>
        </form>
    </div>
</div>

<script src="/js/app.js"></script>
</body>
</html>
