<div class="cf-page-head">
    <div>
        <h1 class="cf-page-title"><?= $isEdit ? 'Modifier l’événement' : 'Nouvel événement' ?></h1>
    </div>
    <a href="<?= url('admin_events') ?>" class="cf-btn cf-btn--secondary">Retour à la liste</a>
</div>

<div class="cf-card" style="max-width:720px;">
    <form method="POST" action="<?= url($isEdit ? 'admin_event_update' : 'admin_event_store') ?>" class="cf-form" novalidate>
        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
        <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?= $event->getId() ?>">
        <?php endif; ?>

        <div class="cf-field">
            <label for="titre" class="cf-label">Titre de l'événement *</label>
            <input type="text" id="titre" name="titre" class="cf-input<?= invalid_class($errors, 'titre') ?>"
                   value="<?= e((string) ($old['titre'] ?? $event->getTitre())) ?>" required>
            <?= field_error($errors, 'titre', 'titre') ?>
        </div>

        <div class="cf-grid cf-grid--2">
            <div class="cf-field">
                <label for="categorie" class="cf-label">Catégorie *</label>
                <select id="categorie" name="categorie" class="cf-select<?= invalid_class($errors, 'categorie') ?>" required>
                    <?php $cat = (string) ($old['categorie'] ?? $event->getCategorie()); ?>
                    <option value="">-- Choisir --</option>
                    <option value="Culture" <?= $cat === 'Culture' ? 'selected' : '' ?>>Culture</option>
                    <option value="Sport" <?= $cat === 'Sport' ? 'selected' : '' ?>>Sport</option>
                    <option value="Réunion publique" <?= $cat === 'Réunion publique' ? 'selected' : '' ?>>Réunion publique</option>
                    <option value="Environnement" <?= $cat === 'Environnement' ? 'selected' : '' ?>>Environnement</option>
                </select>
                <?= field_error($errors, 'categorie', 'categorie') ?>
            </div>

            <div class="cf-field">
                <label for="date_event" class="cf-label">Date et heure *</label>
                <?php
                    $rawDate = (string) ($old['date_event'] ?? $event->getDateEvent());
                    $formattedDate = $rawDate !== '' ? date('Y-m-d\TH:i', strtotime($rawDate)) : '';
                ?>
                <input type="datetime-local" id="date_event" name="date_event" class="cf-input<?= invalid_class($errors, 'date_event') ?>"
                       value="<?= e($formattedDate) ?>" required>
                <?= field_error($errors, 'date_event', 'date_event') ?>
            </div>
        </div>

        <div class="cf-grid cf-grid--2">
            <div class="cf-field">
                <label for="lieu" class="cf-label">Lieu *</label>
                <input type="text" id="lieu" name="lieu" class="cf-input<?= invalid_class($errors, 'lieu') ?>"
                       value="<?= e((string) ($old['lieu'] ?? $event->getLieu())) ?>" required>
                <?= field_error($errors, 'lieu', 'lieu') ?>
            </div>

            <div class="cf-field">
                <label for="capacite_max" class="cf-label">Capacité maximale *</label>
                <input type="number" id="capacite_max" name="capacite_max" min="1" class="cf-input<?= invalid_class($errors, 'capacite_max') ?>"
                       value="<?= e((string) ($old['capacite_max'] ?? $event->getCapaciteMax())) ?>" required>
                <?= field_error($errors, 'capacite_max', 'capacite_max') ?>
            </div>
        </div>

        <div class="cf-field">
            <label for="statut" class="cf-label">Statut</label>
            <?php $st = (string) ($old['statut'] ?? $event->getStatut()); ?>
            <select id="statut" name="statut" class="cf-select">
                <option value="actif" <?= $st === 'actif' ? 'selected' : '' ?>>Actif (visible aux citoyens)</option>
                <option value="inactif" <?= $st === 'inactif' ? 'selected' : '' ?>>Inactif (masqué)</option>
            </select>
        </div>

        <div class="cf-field">
            <label for="description" class="cf-label">Description *</label>
            <textarea id="description" name="description" rows="4" class="cf-textarea<?= invalid_class($errors, 'description') ?>" required><?= e((string) ($old['description'] ?? $event->getDescription())) ?></textarea>
            <?= field_error($errors, 'description', 'description') ?>
        </div>

        <div style="display:flex; gap:0.75rem; margin-top:1rem;">
            <button type="submit" class="cf-btn cf-btn--primary"><?= $isEdit ? 'Enregistrer les modifications' : 'Publier l’événement' ?></button>
            <a href="<?= url('admin_events') ?>" class="cf-btn cf-btn--ghost">Annuler</a>
        </div>
    </form>
</div>
