<?php

$objet     = $data['objet'] ?? [];
$photos    = $data['photos'] ?? [];
$owner     = $data['owner'] ?? [];
$mesObjets = $data['mesObjets'] ?? [];
$isOwner   = isset($user) && isset($objet['idProprietaire']) && $user['idUser'] == $objet['idProprietaire'];
?>

<div class="page-header">
    <h1><?= htmlspecialchars($objet['titre'] ?? 'Objet introuvable') ?></h1>
    <p class="page-subtitle">
        <?php if (!empty($objet)): ?>
            Publié par 
            <a href="/user/<?= $objet['idProprietaire'] ?>" class="link-primary">
                <?= htmlspecialchars($owner['email'] ?? 'Utilisateur inconnu') ?>
            </a>
        <?php endif; ?>
    </p>
</div>

<?php if (!empty($objet)): ?>
<div class="objet-detail">
    <div class="objet-gallery">
        <div class="gallery-main" id="galleryMain">
            <?php if (!empty($photos)): ?>
                <img src="/images/objets/<?= htmlspecialchars($photos[0]['img']) ?>" alt="<?= htmlspecialchars($objet['titre']) ?>" id="mainPhoto">
            <?php else: ?>
                <div class="gallery-placeholder">📷 Aucune photo</div>
            <?php endif; ?>
        </div>
        <?php if (count($photos) > 1): ?>
        <div class="gallery-thumbs">
            <?php foreach ($photos as $i => $photo): ?>
            <div class="gallery-thumb <?= $i === 0 ? 'active' : '' ?>" data-src="/images/objets/<?= htmlspecialchars($photo['img']) ?>">
                <img src="/images/objets/<?= htmlspecialchars($photo['img']) ?>" alt="Photo <?= $i + 1 ?>">
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <div class="objet-detail-info">
        <div class="detail-card">
            <div class="detail-prix"><?= number_format($objet['prix'] ?? 0, 0, ',', ' ') ?> Ar</div>
            
            <div class="detail-status">
                <span class="badge-status <?= $objet['isValidate'] ? 'validated' : 'pending' ?>">
                    <?= $objet['isValidate'] ? '✅ Validé' : '⏳ En attente de validation' ?>
                </span>
            </div>

            <div class="detail-section">
                <h3>Description</h3>
                <p><?= nl2br(htmlspecialchars($objet['descriptions'] ?? 'Aucune description.')) ?></p>
            </div>

            <div class="detail-section">
                <h3>Catégorie</h3>
                <p><a href="/categories/<?= $objet['idCategorie'] ?>" class="link-primary"><?= htmlspecialchars($objet['categorieName'] ?? 'Non classé') ?></a></p>
            </div>

            <div class="detail-section">
                <h3>Propriétaire</h3>
                <a href="/user/<?= $objet['idProprietaire'] ?>" class="owner-link">
                    <span class="owner-avatar">👤</span>
                    <span><?= htmlspecialchars($owner['email'] ?? '') ?></span>
                </a>
            </div>

            <div class="detail-actions">
                <?php if (!$isOwner && !empty($mesObjets)): ?>

                    <button class="btn btn-primary btn-lg" id="btnProposeEchange">
                        🔄 Proposer un échange
                    </button>
                <?php elseif (!$isOwner && empty($mesObjets)): ?>
                    <p class="info-text">Ajoutez d'abord un objet pour pouvoir proposer un échange.</p>
                    <button class="btn btn-primary" onclick="document.getElementById('btnAddObjet').click()">
                        + Ajouter un objet
                    </button>
                <?php elseif ($isOwner): ?>
                    <form action="/objets/delete" method="post" onsubmit="return confirm('Supprimer cet objet ?')">
                        <input type="hidden" name="idObjet" value="<?= $objet['idObjet'] ?>">
                        <button type="submit" class="btn btn-danger">🗑 Supprimer cet objet</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- ===== MODAL PROPOSER ÉCHANGE ===== -->
<?php if (!$isOwner && !empty($mesObjets)): ?>
<div class="modal-overlay" id="modalEchange">
    <div class="modal">
        <div class="modal-header">
            <h2>Proposer un échange</h2>
            <button class="modal-close" id="modalEchangeClose">&times;</button>
        </div>
        <form action="/echanges/propose" method="post" id="formProposeEchange">
            <input type="hidden" name="idObjetRecever" value="<?= $objet['idObjet'] ?>">
            <input type="hidden" name="idRecever" value="<?= $objet['idProprietaire'] ?>">
            <div class="modal-body">
                <p class="exchange-info">
                    Vous proposez d'échanger un de vos objets contre 
                    <strong><?= htmlspecialchars($objet['titre']) ?></strong>
                </p>
                
                <div class="form-group">
                    <label>Choisissez votre objet à proposer :</label>
                    <div class="exchange-objets-list">
                        <?php foreach ($mesObjets as $mo): ?>
                        <label class="exchange-objet-option">
                            <input type="radio" name="idObjetSender" value="<?= $mo['idObjet'] ?>" required>
                            <div class="exchange-objet-card">
                                <div class="exchange-objet-img">
                                    <?php if (!empty($mo['photos']) && count($mo['photos']) > 0): ?>
                                        <img src="/images/objets/<?= htmlspecialchars($mo['photos'][0]['img']) ?>" alt="">
                                    <?php else: ?>
                                        <span>📷</span>
                                    <?php endif; ?>
                                </div>
                                <div class="exchange-objet-info">
                                    <strong><?= htmlspecialchars($mo['titre']) ?></strong>
                                    <span class="exchange-objet-prix"><?= number_format($mo['prix'], 0, ',', ' ') ?> Ar</span>
                                </div>
                            </div>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="modalEchangeCancel">Annuler</button>
                <button type="submit" class="btn btn-primary">Envoyer la proposition</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<?php else: ?>
<div class="empty-state-box">
    <span class="empty-icon">❌</span>
    <p>Cet objet n'existe pas ou a été supprimé.</p>
    <a href="/categories" class="btn btn-primary">Parcourir les catégories</a>
</div>
<?php endif; ?>
