<div class="cf-page-head">
    <div>
        <h1 class="cf-page-title">Gestion des événements</h1>
        <p class="cf-page-subtitle">Publiez et gérez les événements municipaux accessibles aux citoyens.</p>
    </div>
    <a href="<?= url('admin_event_create') ?>" class="cf-btn cf-btn--primary">
        <?= icon('plus', 'cf-icon cf-icon--sm') ?> Nouvel événement
    </a>
</div>

<div class="cf-card">
    <div class="cf-table-wrap">
        <table class="cf-table">
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Catégorie</th>
                    <th>Date & Heure</th>
                    <th>Lieu</th>
                    <th>Inscrits</th>
                    <th>Statut</th>
                    <th class="cf-table__actions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($events)): ?>
                    <tr><td colspan="7" class="cf-muted" style="text-align:center; padding:2rem;">Aucun événement enregistré.</td></tr>
                <?php else: ?>
                    <?php foreach ($events as $ev): ?>
                        <tr>
                            <td><strong><?= e($ev->getTitre()) ?></strong></td>
                            <td><span class="cf-badge cf-badge--info"><?= e($ev->getCategorie()) ?></span></td>
                            <td><?= e(date('d/m/Y H:i', strtotime($ev->getDateEvent()))) ?></td>
                            <td><?= e($ev->getLieu()) ?></td>
                            <td><?= $ev->getNbInscrits() ?> / <?= $ev->getCapaciteMax() ?></td>
                            <td>
                                <span class="cf-badge <?= $ev->isActif() ? 'cf-badge--success' : 'cf-badge--warning' ?>">
                                    <?= e(ucfirst($ev->getStatut())) ?>
                                </span>
                            </td>
                            <td class="cf-table__actions">
                                <a href="<?= url('admin_event_edit', ['id' => $ev->getId()]) ?>" class="cf-btn cf-btn--ghost cf-btn--sm" title="Modifier">
                                    <?= icon('edit', 'cf-icon cf-icon--sm') ?>
                                </a>
                                <form method="POST" action="<?= url('admin_event_delete') ?>" style="display:inline;" onsubmit="return confirm('Supprimer cet événement ?');">
                                    <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                                    <input type="hidden" name="id" value="<?= $ev->getId() ?>">
                                    <button type="submit" class="cf-btn cf-btn--danger cf-btn--sm" title="Supprimer">
                                        <?= icon('trash', 'cf-icon cf-icon--sm') ?>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
