<?php
/**
 * index.php — Point d'entrée unique de CITYFIX (workshop « Sessions et Sécurité », Partie 3.3).
 * Chaque requête passe par ce fichier : index.php?action=xxx
 *   1. configuration sécurisée de la session ;
 *   2. en-têtes HTTP de sécurité ;
 *   3. expiration de la session après inactivité ;
 *   4. routage vers le contrôleur correspondant.
 */

declare(strict_types=1);

// ---------------------------------------------------------------------
// 1. SESSION SÉCURISÉE — les ini_set() doivent précéder session_start()
// ---------------------------------------------------------------------
ini_set('session.use_strict_mode', '1');     // refuse les identifiants de session inventés
ini_set('session.use_only_cookies', '1');    // identifiant jamais transmis dans l'URL
ini_set('session.cookie_httponly', '1');     // cookie inaccessible au JavaScript (XSS)
ini_set('session.cookie_samesite', 'Lax');   // cookie non envoyé depuis un site tiers (CSRF)
session_start();

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/Models/UserModel.php';
require_once __DIR__ . '/Models/RoleModel.php';
require_once __DIR__ . '/Models/PermissionModel.php';
require_once __DIR__ . '/Controllers/Controller.php';
require_once __DIR__ . '/Controllers/AuthController.php';
require_once __DIR__ . '/Controllers/HomeController.php';
require_once __DIR__ . '/Controllers/UserController.php';
require_once __DIR__ . '/Controllers/RoleController.php';
require_once __DIR__ . '/Controllers/DashboardController.php';
require_once __DIR__ . '/Models/EventModel.php';
require_once __DIR__ . '/Controllers/EventController.php';

// ---------------------------------------------------------------------
// 2. EN-TÊTES HTTP DE SÉCURITÉ (workshop, Partie 7.5)
// ---------------------------------------------------------------------
header('X-Frame-Options: DENY');                                  // anti clickjacking
header('X-Content-Type-Options: nosniff');                        // pas de « MIME sniffing »
header('Referrer-Policy: strict-origin-when-cross-origin');
header("Content-Security-Policy: default-src 'self'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; form-action 'self'; frame-ancestors 'none'; base-uri 'self'");

// ---------------------------------------------------------------------
// 3. EXPIRATION PAR INACTIVITÉ (30 min, ou 10 s si SESSION_TEST_MODE = true)
// ---------------------------------------------------------------------
if (isset($_SESSION['user_id'], $_SESSION['last_activity'])
    && (time() - (int) $_SESSION['last_activity'] > SESSION_LIFETIME)) {
    AuthController::destroySession();
    session_start();
    $_SESSION['flash'][] = ['type' => 'info', 'message' => 'Votre session a expiré après une période d’inactivité. Veuillez vous reconnecter.'];
}
$_SESSION['last_activity'] = time();

// ---------------------------------------------------------------------
// 4. ROUTAGE
// ---------------------------------------------------------------------
$action = (string) ($_GET['action'] ?? 'home');

switch ($action) {
    // ---------------- Pages publiques (Front Office) ----------------
    case 'home':
        (new HomeController())->home();
        break;
    case 'about':
        (new HomeController())->about();
        break;

    // ---------------- Authentification ----------------
    case 'register':
        (new AuthController())->register();
        break;
    case 'login':
        (new AuthController())->login();
        break;
    case 'logout':
        (new AuthController())->logout();
        break;
    case 'forgot_password':
        (new AuthController())->forgotPassword();
        break;
    case 'reset_password':
        (new AuthController())->resetPassword();
        break;

    // ---------------- Espace citoyen (profil) ----------------
    case 'profile':
        (new UserController())->profile();
        break;
    case 'profile_update':
        (new UserController())->updateProfile();
        break;
    case 'password_update':
        (new UserController())->updatePassword();
        break;

    // ---------------- Back Office ----------------
    case 'dashboard':
        (new DashboardController())->index();
        break;
    case 'users':
        (new UserController())->index();
        break;
    case 'user_show':
        (new UserController())->show();
        break;
    case 'user_create':
        (new UserController())->create();
        break;
    case 'user_store':
        (new UserController())->store();
        break;
    case 'user_edit':
        (new UserController())->edit();
        break;
    case 'user_update':
        (new UserController())->update();
        break;
    case 'user_toggle':
        (new UserController())->toggle();
        break;
    case 'user_delete':
        (new UserController())->delete();
        break;
    case 'roles':
        (new RoleController())->index();
        break;
    case 'roles_update':
        (new RoleController())->update();
        break;
    // ---------------- Événements (Module 6) ----------------
    case 'events':
        (new EventController())->frontIndex();
        break;
    case 'event_register':
        (new EventController())->toggleRegister();
        break;
    case 'admin_events':
        (new EventController())->index();
        break;
    case 'admin_event_create':
        (new EventController())->create();
        break;
    case 'admin_event_store':
        (new EventController())->store();
        break;
    case 'admin_event_edit':
        (new EventController())->edit();
        break;
    case 'admin_event_update':
        (new EventController())->update();
        break;
    case 'admin_event_delete':
        (new EventController())->delete();
        break;

    // ---------------- Page introuvable ----------------
    default:
        Controller::renderError(404);
}
