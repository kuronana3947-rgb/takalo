<?php
$objets = $data ?? [];
?>

<div class="page-header">
    <h1>Mes objets</h1>
    <p class="page-subtitle">Gérez vos objets à échanger</p>
</div>

<div class="objets-grid">
    <?php if (!empty($objets)): ?>
        <?php foreach ($objets as $obj): ?>
        <div class="objet-card">
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
            <div class="objet-actions">
                <a href="/objets/<?= $obj['idObjet'] ?>" class="btn btn-sm btn-outline">Voir</a>
                <form action="/objets/delete" method="post" style="display:inline" onsubmit="return confirm('Supprimer cet objet ?')">
                    <input type="hidden" name="idObjet" value="<?= $obj['idObjet'] ?>">
                    <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="empty-state-box">
            <span class="empty-icon">📦</span>
            <p>Vous n'avez pas encore d'objet.</p>
            <button class="btn btn-primary" onclick="document.getElementById('btnAddObjet').click()">Ajouter un objet</button>
        </div>
    <?php endif; ?>
</div>
