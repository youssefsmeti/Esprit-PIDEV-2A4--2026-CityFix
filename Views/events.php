<section class="cf-section">
    <div class="cf-container">
        <div class="cf-page-head">
            <div>
                <h1 class="cf-page-title">Événements municipaux</h1>
                <p class="cf-page-subtitle">Découvrez les activités, réunions publiques et manifestations de votre commune.</p>
            </div>
        </div>

        <?php if (empty($events)): ?>
            <div class="cf-card">
                <div class="cf-empty">
                    <p class="cf-empty__title">Aucun événement prévu pour le moment</p>
                    <p class="cf-empty__text">Revenez prochainement pour consulter les annonces de la municipalité.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="cf-grid cf-grid--3">
                <?php foreach ($events as $ev): ?>
                    <article class="cf-card" style="display:flex; flex-direction:column; justify-content:space-between;">
                        <div>
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.75rem;">
                                <span class="cf-badge cf-badge--info"><?= e($ev->getCategorie()) ?></span>
                                <span class="cf-muted" style="font-size:0.85rem;">
                                    <?= $ev->getNbInscrits() ?> / <?= $ev->getCapaciteMax() ?> places
                                </span>
                            </div>
                            <h3 style="margin-bottom:0.5rem;"><?= e($ev->getTitre()) ?></h3>
                            <p class="cf-muted" style="font-size:0.9rem; margin-bottom:0.75rem;">
                                <strong>Date :</strong> <?= e(date('d/m/Y H:i', strtotime($ev->getDateEvent()))) ?><br>
                                <strong>Lieu :</strong> <?= e($ev->getLieu()) ?>
                            </p>
                            <p style="margin-bottom:1.25rem;"><?= nl2br(e($ev->getDescription())) ?></p>
                        </div>

                        <form method="POST" action="<?= url('event_register') ?>">
                            <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                            <input type="hidden" name="id" value="<?= $ev->getId() ?>">
                            <?php if ($ev->isRegistered()): ?>
                                <button type="submit" class="cf-btn cf-btn--secondary" style="width:100%;">
                                    Annuler mon inscription
                                </button>
                            <?php elseif ($ev->getNbInscrits() >= $ev->getCapaciteMax()): ?>
                                <button type="button" class="cf-btn cf-btn--ghost" style="width:100%;" disabled>
                                    Complet
                                </button>
                            <?php else: ?>
                                <button type="submit" class="cf-btn cf-btn--primary" style="width:100%;">
                                    S'inscrire à l'événement
                                </button>
                            <?php endif; ?>
                        </form>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
