<?php
require_once __DIR__ . '/../vendor/autoload.php';

// 1. .env ፋይሉን መጫን
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

// 2. ኮንዲሽኑን (Condition) እዚህ ጋር እንጠቀማለን
// 'SESSION_PATH' በ .env ውስጥ ተጽፎ ከሆነ እና ባዶ ካልሆነ
if (!empty($_ENV['SESSION_PATH'])) {
    
    // የፎልደሩን መንገድ ለ PHP ንገረው
    $path = $_ENV['SESSION_PATH'];

    // ፎልደሩ መኖሩን ቼክ አድርግ፣ ከሌለ ፍጠርለት
    if (!is_dir($path)) {
        mkdir($path, 0700, true);
    }

    // የሴሽን መቀመጫውን ቀይር
    session_save_path($path);
}
$isSecure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
session_name($isSecure ? '__Host-JCIMSESSION' : 'JCIMSESSION');
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'domain'   => '',
    'secure'   => $isSecure,
    'httponly' => true,
    'samesite' => 'Strict'
]);
session_start();
$GLOBALS['nonce'] = base64_encode(random_bytes(16));
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'nonce-{$GLOBALS['nonce']}' https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com https://code.ionicframework.com; font-src 'self' https://fonts.gstatic.com https://code.ionicframework.com; img-src 'self' data:; frame-ancestors 'none'");
if (($_ENV['APP_ENV'] ?? '') === 'local') {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}

$db = \App\Config\Database::getConnection();

/* ---------------- ROUTES ---------------- */
$baseRoutes = [
    'login' => ['AuthController', 'showLoginForm', false],
    'login_process' => ['AuthController', 'handleLogin', false],
    'dashboard' => ['DashboardController', 'index', true],
];

$teddyRoutes = require __DIR__ . '/../src/Routes/Teddyroutes.php';
$yoniRoutes  = require __DIR__ . '/../src/Routes/Yoniroutes.php';
$yibeRoutes  = require __DIR__ . '/../src/Routes/YibeRoutes.php';

$routes = array_merge($baseRoutes, $teddyRoutes, $yoniRoutes, $yibeRoutes);

/* ---------------- ROUTING FIX ---------------- */

// Get full action string
$rawAction = $_GET['action'] ?? 'login';

// Normalize
$rawAction = trim($rawAction, '/');

// Split into segments
$segments = explode('/', $rawAction);

// Route name
$action = $segments[0] ?? 'login';

/* ---------------- DEBUG (temporary if needed) */
// var_dump($segments); die();

/* ---------------- PARAMS ---------------- */
$params = [
    'uuid'      => isset($segments[1]) ? urldecode($segments[1]) : null,
    'record_id' => isset($segments[2]) ? urldecode($segments[2]) : null,
    'extra'     => array_map('urldecode', array_slice($segments, 3))
];

/* ---------------- ROUTE CHECK ---------------- */
if (!isset($routes[$action])) {
    header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/login");
    exit();
}

[$controllerName, $method, $requiresAuth] = $routes[$action];

/* ---------------- AUTH ---------------- */
if ($requiresAuth) {
    \App\Controllers\AuthController::checkAuth();
}

/* ---------------- CONTROLLER ---------------- */
$controllerClass = "\\App\\Controllers\\$controllerName";

if (!class_exists($controllerClass)) {
    die("Controller '$controllerClass' not found");
}

$controller = new $controllerClass($db);

/* ---------------- METHOD CALL ---------------- */
if (!method_exists($controller, $method)) {
    die("Method '$method' not found in $controllerClass");
}

$ref = new ReflectionMethod($controller, $method);

if ($ref->getNumberOfParameters() > 0) {
    $controller->$method($params);
} else {
    $controller->$method();
}
