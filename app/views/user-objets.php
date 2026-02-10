<?php
/**
 * user-objets.php — Objets d'un autre utilisateur
 * Variables : $data['owner'], $data['objets']
 */
$owner  = $data['owner'] ?? [];
$objets = $data['objets'] ?? [];
?>

<div class="page-header">
    <h1>Objets de <?= htmlspecialchars($owner['email'] ?? 'Utilisateur') ?></h1>
    <p class="page-subtitle"><?= count($objets) ?> objet<?= count($objets) > 1 ? 's' : '' ?> publié<?= count($objets) > 1 ? 's' : '' ?></p>
</div>

<div class="objets-grid">
    <?php if (!empty($objets)): ?>
        <?php foreach ($objets as $obj): ?>
        <a href="/objets/<?= $obj['idObjet'] ?>" class="objet-card objet-card-link">
            <div class="objet-img">
                <?php if (!empty($obj['photos']) && count($obj['photos']) > 0): ?>
                    <img src="/images/objets/<?= htmlspecialchars($obj['photos'][0]['img']) ?>" alt="<?= htmlspecialchars($obj['titre']) ?>">
                    <?php if (count($obj['photos']) > 1): ?>
                        <span class="photo-count">+<?= count($obj['photos']) - 1 ?></span>
                    <?php endif; ?>
                <?php else: ?>
                    <span class="objet-placeholder">📷</span>
                <?php endif; ?>
            </div>
            <div class="objet-info">
                <h3><?= htmlspecialchars($obj['titre']) ?></h3>
                <p class="objet-desc"><?= htmlspecialchars($obj['descriptions'] ?? '') ?></p>
                <div class="objet-meta">
                    <span class="objet-prix"><?= number_format($obj['prix'], 0, ',', ' ') ?> Ar</span>
                    <span class="badge-status <?= $obj['isValidate'] ? 'validated' : 'pending' ?>">
                        <?= $obj['isValidate'] ? 'Validé' : 'En attente' ?>
                    </span>
                </div>
            </div>
        </a>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="empty-state-box">
            <span class="empty-icon">📦</span>
            <p>Cet utilisateur n'a pas encore d'objet.</p>
        </div>
    <?php endif; ?>
</div>
