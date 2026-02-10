<?php

$stats = $data['stats'] ?? [];
$recentEchanges = $data['recentEchanges'] ?? [];
$recentUsers = $data['recentUsers'] ?? [];
?>

<div class="page-header">
    <h1>Dashboard</h1>
    <p class="page-subtitle">Vue d'ensemble de la plateforme</p>
</div>


<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">👥</div>
        <div class="stat-info">
            <span class="stat-number"><?= $stats['totalUsers'] ?? 0 ?></span>
            <span class="stat-label">Utilisateurs</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">🔄</div>
        <div class="stat-info">
            <span class="stat-number"><?= $stats['totalEchanges'] ?? 0 ?></span>
            <span class="stat-label">Échanges</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">📦</div>
        <div class="stat-info">
            <span class="stat-number"><?= $stats['totalObjets'] ?? 0 ?></span>
            <span class="stat-label">Objets</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">📁</div>
        <div class="stat-info">
            <span class="stat-number"><?= $stats['totalCategories'] ?? 0 ?></span>
            <span class="stat-label">Catégories</span>
        </div>
    </div>
</div>


<div class="card">
    <h2 class="card-title">Derniers échanges</h2>
    <?php if (!empty($recentEchanges)): ?>
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Expéditeur</th>
                <th>Destinataire</th>
                <th>Objet proposé</th>
                <th>Objet demandé</th>
                <th>Date</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recentEchanges as $e): ?>
            <tr>
                <td><?= $e['idEchange'] ?></td>
                <td><?= htmlspecialchars($e['senderEmail'] ?? '') ?></td>
                <td><?= htmlspecialchars($e['receverEmail'] ?? '') ?></td>
                <td><?= htmlspecialchars($e['objetSenderTitre'] ?? '') ?></td>
                <td><?= htmlspecialchars($e['objetReceverTitre'] ?? '') ?></td>
                <td><?= $e['date'] ?? '' ?></td>
                <td>
                    <span class="badge-status <?= $e['isValidate'] ? 'validated' : 'pending' ?>">
                        <?= $e['isValidate'] ? 'Validé' : 'En attente' ?>
                    </span>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
        <p class="empty-state">Aucun échange pour le moment.</p>
    <?php endif; ?>
</div>
