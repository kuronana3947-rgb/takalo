<?php
/**
 * categories.php — Liste des catégories avec objets
 * Variables : $data (tableau de catégories)
 */
$categories = $data ?? [];
?>

<div class="page-header">
    <h1>Catégories</h1>
    <p class="page-subtitle">Parcourez les objets par catégorie</p>
</div>

<div class="categories-grid">
    <?php if (!empty($categories)): ?>
        <?php foreach ($categories as $cat): ?>
        <a href="/categories/<?= $cat['idCategorie'] ?>" class="category-card">
            <div class="category-img">
                <?php if (!empty($cat['img'])): ?>
                    <img src="/images/categories/<?= htmlspecialchars($cat['img']) ?>" alt="<?= htmlspecialchars($cat['categorie']) ?>">
                <?php else: ?>
                    <span class="category-placeholder">📁</span>
                <?php endif; ?>
            </div>
            <h3><?= htmlspecialchars($cat['categorie']) ?></h3>
        </a>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="empty-state">Aucune catégorie disponible.</p>
    <?php endif; ?>
</div>
