<?php
$isHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
session_set_cookie_params([
    'httponly' => true,
    'secure' => $isHttps,
    'samesite' => 'Lax',
]);
session_start();

function h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf_token(): void
{
    $submittedToken = $_POST['csrf_token'] ?? '';
    if (!is_string($submittedToken) || !hash_equals(csrf_token(), $submittedToken)) {
        http_response_code(400);
        exit('Invalid form token. Please reload the page and try again.');
    }
}

function is_logged_in(): bool
{
    return isset($_SESSION['user_id']);
}

function require_auth(mysqli $conn): array
{
    if (!is_logged_in()) {
        header('Location: index.php');
        exit;
    }

    $userId = (int) $_SESSION['user_id'];
    $stmt = $conn->prepare(
        'SELECT id, name, email, account_status, created_at FROM users WHERE id = ? LIMIT 1'
    );
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$user || $user['account_status'] !== 'Active') {
        $_SESSION = [];
        session_regenerate_id(true);
        header('Location: index.php');
        exit;
    }

    return $user;
}
