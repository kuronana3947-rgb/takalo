<?php

$echanges = $data ?? [];
?>

<div class="page-header">
    <h1>Mes échanges</h1>
    <p class="page-subtitle">Propositions d'échange en cours</p>
</div>

<?php if (!empty($echanges)): ?>
<div class="echanges-list">
    <?php foreach ($echanges as $e): ?>
    <div class="echange-card <?= $e['isValidate'] ? 'validated' : 'pending' ?>">
        <div class="echange-header">
            <span class="badge-status <?= $e['isValidate'] ? 'validated' : 'pending' ?>">
                <?= $e['isValidate'] ? '✅ Validé' : '⏳ En attente' ?>
            </span>
            <span class="echange-date"><?= $e['date'] ?? '' ?></span>
        </div>
        <div class="echange-body">
            <div class="echange-side">
                <span class="echange-label">Proposé</span>
                <strong><?= htmlspecialchars($e['objetSenderTitre'] ?? '') ?></strong>
                <small>par <?= htmlspecialchars($e['senderEmail'] ?? '') ?></small>
            </div>
            <div class="echange-arrow">⇄</div>
            <div class="echange-side">
                <span class="echange-label">Demandé</span>
                <strong><?= htmlspecialchars($e['objetReceverTitre'] ?? '') ?></strong>
                <small>par <?= htmlspecialchars($e['receverEmail'] ?? '') ?></small>
            </div>
        </div>
        <?php if (!$e['isValidate'] && isset($user) && $e['idRecever'] == $user['idUser']): ?>
        <div class="echange-actions">
            <form action="/echanges/accept" method="post" style="display:inline">
                <input type="hidden" name="idEchange" value="<?= $e['idEchange'] ?>">
                <button type="submit" class="btn btn-success btn-sm">Accepter</button>
            </form>
            <form action="/echanges/reject" method="post" style="display:inline">
                <input type="hidden" name="idEchange" value="<?= $e['idEchange'] ?>">
                <button type="submit" class="btn btn-danger btn-sm">Refuser</button>
            </form>
        </div>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
</div>
<?php else: ?>
    <div class="empty-state-box">
        <span class="empty-icon">🔄</span>
        <p>Aucun échange en cours.</p>
    </div>
<?php endif; ?>
