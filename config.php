<?php
/**
 * Shared configuration for the Bare Skin Studio backend.
 * Pure PHP + MySQL (PDO) — no framework, no external libraries.
 */

// ---------------------------------------------------------------
// Database connection — update these for your environment.
// ---------------------------------------------------------------
define('DB_HOST', 'localhost');
define('DB_NAME', 'bare_skin_studio');
define('DB_USER', 'root');
define('DB_PASS', '');

// ---------------------------------------------------------------
// Studio details used in emails.
// ---------------------------------------------------------------
define('STUDIO_NAME', 'Bare Skin Studio');
define('STUDIO_EMAIL', 'book@bareskinstudio.com'); // "From" / "Reply-To" address for outgoing mail
define('STUDIO_ADDRESS', '118 Orchard St, New York, NY 10002');

// ---------------------------------------------------------------
// Admin login — password is "bareskin2026" for this demo.
// CHANGE THIS before going live: generate your own hash by running
//   php -r "echo password_hash('your-new-password', PASSWORD_DEFAULT);"
// and paste the result below.
// ---------------------------------------------------------------
define('ADMIN_PASSWORD_HASH', '$2b$12$reI8HcEAWgrfT22FbONjxeLIy5U87kWptW1ShaP61f7AL5oSQRHma');

// ---------------------------------------------------------------
// DB connection (PDO, lazily created + reused)
// ---------------------------------------------------------------
function getDb(){
    static $pdo = null;
    if ($pdo === null){
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
    return $pdo;
}

// ---------------------------------------------------------------
// Response / request helpers
// ---------------------------------------------------------------
function jsonResponse($data, $status = 200){
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function corsHeaders(){
    // Tighten Access-Control-Allow-Origin to your real domain in production.
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    header('Access-Control-Allow-Credentials: true');
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS'){
        http_response_code(204);
        exit;
    }
}

function readJsonBody(){
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function sanitize($str){
    return trim(strip_tags((string)$str));
}

// ---------------------------------------------------------------
// Admin auth guard — call at the top of any admin-only endpoint.
// Session-based: the browser holds a session cookie after login.php
// succeeds, so admin.html doesn't need to store the password anywhere.
// ---------------------------------------------------------------
function requireAdminAuth(){
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    if (empty($_SESSION['admin_authed'])){
        jsonResponse(['error' => 'Unauthorized'], 401);
    }
}
