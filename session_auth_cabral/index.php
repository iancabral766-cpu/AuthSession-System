<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

if (is_logged_in()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token();

    $emailInput = $_POST['email'] ?? '';
    $passwordInput = $_POST['password'] ?? '';
    $email = is_string($emailInput) ? trim($emailInput) : '';
    $password = is_string($passwordInput) ? $passwordInput : '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
        $error = 'Enter a valid email address and password.';
    } else {
        $stmt = $conn->prepare(
            'SELECT id, password_hash, account_status FROM users WHERE email = ? LIMIT 1'
        );
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (
            $user
            && $user['account_status'] === 'Active'
            && password_verify($password, $user['password_hash'])
        ) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int) $user['id'];
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

            header('Location: dashboard.php');
            exit;
        }

        $error = 'Invalid email or password, or the account is inactive.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" 
    rel="stylesheet" 
    integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" 
    crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" 
    integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" 
    crossorigin="anonymous"></script>

</head>
<body>
<main class="card">
    <h1>Sign in</h1>
    <p class="muted">Use your account to continue.</p>
    <?php if (isset($_GET['registered'])): ?>
        <p class="success">Your account is ready. Please sign in.</p>
    <?php endif; ?>
    <?php if ($error !== ''): ?>
        <p class="error"><?= h($error) ?></p>
    <?php endif; ?>
    <form method="post" action="index.php">
        <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
        <label for="email">Email</label>
        <input id="email" name="email" type="email" autocomplete="email" required
               value="<?= h($_POST['email'] ?? '') ?>">
        <label for="password">Password</label>
        <input id="password" name="password" type="password" autocomplete="current-password" required>
        <button type="submit">Sign in</button>
    </form>
    <p class="muted">Wala ka pang Account? <a href="register.php">Create an account</a></p>
</main>
</body>
</html>
