<?php
declare(strict_types=1);

require_once __DIR__ . '/../Models/EventModel.php';

class EventController extends Controller
{
    private function requireAdminOrAgent(): UserModel
    {
        $user = AuthController::currentUser();
        if (!$user) {
            $this->redirect('login');
        }
        if (!in_array($user->getRoleNom(), ['admin', 'agent'], true)) {
            Controller::renderError(403);
        }
        return $user;
    }

    // ---------------- FRONT OFFICE (CITOYENS) ----------------

    public function frontIndex(): void
    {
        $userId = (int) ($_SESSION['user_id'] ?? 0);
        $stmt = $this->db->prepare(
            'SELECT e.*,
                    (SELECT COUNT(*) FROM event_registrations r WHERE r.event_id = e.id) AS nb_inscrits,
                    (SELECT COUNT(*) FROM event_registrations r2 WHERE r2.event_id = e.id AND r2.user_id = :uid) AS is_registered
               FROM events e
              WHERE e.statut = "actif"
              ORDER BY e.date_event ASC'
        );
        $stmt->execute(['uid' => $userId]);
        $events = array_map([EventModel::class, 'fromArray'], $stmt->fetchAll());

        $this->render('events', [
            'title'       => 'Événements municipaux — ' . APP_NAME,
            'currentPage' => 'events',
            'events'      => $events,
        ]);
    }

    public function toggleRegister(): void
    {
        $this->requirePost();
        $this->checkCsrfOrRedirect('events');

        $user = AuthController::currentUser();
        if (!$user) {
            $this->flash('info', 'Veuillez vous connecter pour vous inscrire à un événement.');
            $this->redirect('login');
        }

        $eventId = $this->intParam('id', $_POST);
        $check = $this->db->prepare('SELECT id FROM event_registrations WHERE event_id = :eid AND user_id = :uid');
        $check->execute(['eid' => $eventId, 'uid' => $user->getId()]);

        if ($check->fetch()) {
            $del = $this->db->prepare('DELETE FROM event_registrations WHERE event_id = :eid AND user_id = :uid');
            $del->execute(['eid' => $eventId, 'uid' => $user->getId()]);
            $this->flash('info', 'Votre inscription à l’événement a été annulée.');
        } else {
            $ins = $this->db->prepare('INSERT INTO event_registrations (event_id, user_id) VALUES (:eid, :uid)');
            $ins->execute(['eid' => $eventId, 'uid' => $user->getId()]);
            $this->flash('success', 'Inscription confirmée !');
        }
        $this->redirect('events');
    }

    // ---------------- BACK OFFICE (ADMIN / AGENT) ----------------

    public function index(): void
    {
        $this->requireAdminOrAgent();
        $stmt = $this->db->query(
            'SELECT e.*, (SELECT COUNT(*) FROM event_registrations r WHERE r.event_id = e.id) AS nb_inscrits
               FROM events e ORDER BY e.date_event DESC'
        );
        $events = array_map([EventModel::class, 'fromArray'], $stmt->fetchAll());

        $this->render('admin/events/index', [
            'title'       => 'Gestion des événements — ' . APP_NAME,
            'currentPage' => 'events',
            'events'      => $events,
        ], 'layout_admin');
    }

    public function create(): void
    {
        $this->requireAdminOrAgent();
        $this->render('admin/events/form', [
            'title'       => 'Nouvel événement — ' . APP_NAME,
            'currentPage' => 'events',
            'event'       => new EventModel(),
            'errors'      => [],
            'old'         => [],
            'isEdit'      => false,
        ], 'layout_admin');
    }

    public function store(): void
    {
        $user = $this->requireAdminOrAgent();
        $this->requirePost();
        $this->checkCsrfOrRedirect('admin_events');

        [$data, $errors] = $this->validateEvent($_POST);
        if (!empty($errors)) {
            $this->render('admin/events/form', [
                'title'       => 'Nouvel événement — ' . APP_NAME,
                'currentPage' => 'events',
                'event'       => new EventModel(),
                'errors'      => $errors,
                'old'         => $data,
                'isEdit'      => false,
            ], 'layout_admin');
            return;
        }

        $stmt = $this->db->prepare(
            'INSERT INTO events (titre, description, categorie, date_event, lieu, capacite_max, statut, created_by)
             VALUES (:titre, :description, :categorie, :date_event, :lieu, :capacite_max, :statut, :created_by)'
        );
        $stmt->execute($data + ['created_by' => $user->getId()]);

        $this->flash('success', 'Événement créé avec succès.');
        $this->redirect('admin_events');
    }

    public function edit(): void
    {
        $this->requireAdminOrAgent();
        $id = $this->intParam('id', $_GET);
        $stmt = $this->db->prepare('SELECT * FROM events WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        if (!$row) {
            Controller::renderError(404);
        }

        $this->render('admin/events/form', [
            'title'       => 'Modifier l’événement — ' . APP_NAME,
            'currentPage' => 'events',
            'event'       => EventModel::fromArray($row),
            'errors'      => [],
            'old'         => $row,
            'isEdit'      => true,
        ], 'layout_admin');
    }

    public function update(): void
    {
        $this->requireAdminOrAgent();
        $this->requirePost();
        $id = $this->intParam('id', $_POST);
        $this->checkCsrfOrRedirect('admin_event_edit', ['id' => $id]);

        [$data, $errors] = $this->validateEvent($_POST);
        if (!empty($errors)) {
            $this->render('admin/events/form', [
                'title'       => 'Modifier l’événement — ' . APP_NAME,
                'currentPage' => 'events',
                'event'       => EventModel::fromArray($data + ['id' => $id]),
                'errors'      => $errors,
                'old'         => $data,
                'isEdit'      => true,
            ], 'layout_admin');
            return;
        }

        $stmt = $this->db->prepare(
            'UPDATE events SET titre=:titre, description=:description, categorie=:categorie,
                    date_event=:date_event, lieu=:lieu, capacite_max=:capacite_max, statut=:statut
              WHERE id=:id'
        );
        $stmt->execute($data + ['id' => $id]);

        $this->flash('success', 'Événement mis à jour.');
        $this->redirect('admin_events');
    }

    public function delete(): void
    {
        $this->requireAdminOrAgent();
        $this->requirePost();
        $this->checkCsrfOrRedirect('admin_events');

        $id = $this->intParam('id', $_POST);
        $stmt = $this->db->prepare('DELETE FROM events WHERE id = :id');
        $stmt->execute(['id' => $id]);

        $this->flash('success', 'Événement supprimé.');
        $this->redirect('admin_events');
    }

    private function validateEvent(array $post): array
    {
        $data = [
            'titre'        => trim((string) ($post['titre'] ?? '')),
            'description'  => trim((string) ($post['description'] ?? '')),
            'categorie'    => trim((string) ($post['categorie'] ?? '')),
            'date_event'   => trim((string) ($post['date_event'] ?? '')),
            'lieu'         => trim((string) ($post['lieu'] ?? '')),
            'capacite_max' => (int) ($post['capacite_max'] ?? 50),
            'statut'       => in_array($post['statut'] ?? '', ['actif', 'inactif'], true) ? $post['statut'] : 'actif',
        ];

        $errors = [];
        if (mb_strlen($data['titre']) < 3) {
            $errors['titre'] = 'Le titre doit contenir au moins 3 caractères.';
        }
        if (mb_strlen($data['description']) < 10) {
            $errors['description'] = 'La description doit contenir au moins 10 caractères.';
        }
        if ($data['categorie'] === '') {
            $errors['categorie'] = 'La catégorie est obligatoire.';
        }
        if ($data['date_event'] === '') {
            $errors['date_event'] = 'La date et l’heure sont obligatoires.';
        }
        if ($data['lieu'] === '') {
            $errors['lieu'] = 'Le lieu est obligatoire.';
        }
        if ($data['capacite_max'] < 1) {
            $errors['capacite_max'] = 'La capacité doit être au moins de 1 place.';
        }

        return [$data, $errors];
    }
}
