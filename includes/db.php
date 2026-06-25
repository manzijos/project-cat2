<?php
session_start();

if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(__DIR__));
}

if (!defined('DB_PATH')) {
    define('DB_PATH', APP_ROOT . '/data/shop.sqlite');
}

if (!defined('UPLOAD_DIR')) {
    define('UPLOAD_DIR', APP_ROOT . '/uploads/');
}

if (!is_dir(APP_ROOT . '/data')) {
    mkdir(APP_ROOT . '/data', 0777, true);
}

if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0777, true);
}

function get_db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $pdo = new PDO('sqlite:' . DB_PATH);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
}

function init_db(): void
{
    $db = get_db();

    $db->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT UNIQUE NOT NULL,
            password_hash TEXT NOT NULL,
            role TEXT NOT NULL,
            full_name TEXT NOT NULL,
            location TEXT,
            created_at TEXT DEFAULT CURRENT_TIMESTAMP
        )");

    $db->exec("
        CREATE TABLE IF NOT EXISTS products (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            seller_id INTEGER NOT NULL,
            name TEXT NOT NULL,
            description TEXT NOT NULL,
            price REAL NOT NULL,
            stock INTEGER NOT NULL DEFAULT 1,
            image_path TEXT,
            document_path TEXT,
            location TEXT,
            created_at TEXT DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY(seller_id) REFERENCES users(id)
        )");

    $db->exec("
        CREATE TABLE IF NOT EXISTS orders (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            customer_id INTEGER NOT NULL,
            product_id INTEGER NOT NULL,
            quantity INTEGER NOT NULL,
            total REAL NOT NULL,
            status TEXT NOT NULL DEFAULT 'Placed',
            created_at TEXT DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY(customer_id) REFERENCES users(id),
            FOREIGN KEY(product_id) REFERENCES products(id)
        )");

    $db->exec("
        CREATE TABLE IF NOT EXISTS comments (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            customer_id INTEGER NOT NULL,
            product_id INTEGER NOT NULL,
            rating INTEGER NOT NULL,
            message TEXT NOT NULL,
            created_at TEXT DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY(customer_id) REFERENCES users(id),
            FOREIGN KEY(product_id) REFERENCES products(id)
        )");

    $stmt = $db->prepare('SELECT id FROM users WHERE username = :username');
    $stmt->execute([':username' => 'admin']);

    if (!$stmt->fetch()) {
        $db->prepare('INSERT INTO users (username, password_hash, role, full_name, location) VALUES (:username, :password_hash, :role, :full_name, :location)')
            ->execute([
                ':username' => 'admin',
                ':password_hash' => password_hash('admin123', PASSWORD_DEFAULT),
                ':role' => 'admin',
                ':full_name' => 'System Administrator',
                ':location' => 'Nairobi'
            ]);
    }
}

init_db();

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function is_logged_in(): bool
{
    return !empty($_SESSION['user']);
}

function require_login(array $allowedRoles = []): array
{
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }

    $user = current_user();
    if ($allowedRoles && !in_array($user['role'], $allowedRoles, true)) {
        header('Location: dashboard.php');
        exit;
    }

    return $user;
}

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function upload_file(string $fieldName, string $targetDir): ?string
{
    if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $tmpName = $_FILES[$fieldName]['tmp_name'];
    $originalName = basename($_FILES[$fieldName]['name']);
    $extension = pathinfo($originalName, PATHINFO_EXTENSION);
    $safeName = uniqid('file_', true) . '.' . $extension;
    $destination = rtrim($targetDir, '/') . '/' . $safeName;

    if (!move_uploaded_file($tmpName, $destination)) {
        return null;
    }

    return $safeName;
}
