<?php

$historique = $data ?? [];
?>

<div class="page-header">
    <h1>Historique</h1>
    <p class="page-subtitle">Vos échanges passés</p>
</div>

<?php if (!empty($historique)): ?>
<table class="table">
    <thead>
        <tr>
            <th>#</th>
            <th>Objet donné</th>
            <th>Objet reçu</th>
            <th>Avec</th>
            <th>Date</th>
            <th>Statut</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($historique as $h): ?>
        <tr>
            <td><?= $h['idEchange'] ?></td>
            <td><?= htmlspecialchars($h['objetSenderTitre'] ?? '') ?></td>
            <td><?= htmlspecialchars($h['objetReceverTitre'] ?? '') ?></td>
            <td>
                <?php
                    if (isset($user) && $h['idSender'] == $user['idUser']) {
                        echo htmlspecialchars($h['receverEmail'] ?? '');
                    } else {
                        echo htmlspecialchars($h['senderEmail'] ?? '');
                    }
                ?>
            </td>
            <td><?= $h['date'] ?? '' ?></td>
            <td>
                <span class="badge-status validated">✅ Validé</span>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
    <div class="empty-state-box">
        <span class="empty-icon">📜</span>
        <p>Aucun échange dans l'historique.</p>
    </div>
<?php endif; ?>
