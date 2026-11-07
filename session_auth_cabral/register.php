<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

if (is_logged_in()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$name = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token();

    $nameInput = $_POST['name'] ?? '';
    $emailInput = $_POST['email'] ?? '';
    $passwordInput = $_POST['password'] ?? '';
    $name = is_string($nameInput) ? trim($nameInput) : '';
    $email = is_string($emailInput) ? trim($emailInput) : '';
    $password = is_string($passwordInput) ? $passwordInput : '';

    if ($name === '' || strlen($name) > 100) {
        $error = 'Enter a name of 1 to 100 characters.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 254) {
        $error = 'Enter a valid email address.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters long.';
    } else {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare(
            'INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)'
        );
        $stmt->bind_param('sss', $name, $email, $passwordHash);

        try {
            $stmt->execute();
            $stmt->close();
            header('Location: index.php?registered=1');
            exit;
        } catch (mysqli_sql_exception $exception) {
            $stmt->close();
            if ((int) $exception->getCode() === 1062) {
                $error = 'An account with that email already exists.';
            } else {
                throw $exception;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create account</title>
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
    <h1>Create account</h1>
    <?php if ($error !== ''): ?>
        <p class="error"><?= h($error) ?></p>
    <?php endif; ?>
    <form method="post" action="register.php">
        <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
        <label for="name">Name</label>
        <input id="name" name="name" type="text" maxlength="100" autocomplete="name" required
               value="<?= h($name) ?>">
        <label for="email">Email</label>
        <input id="email" name="email" type="email" maxlength="254" autocomplete="email" required
               value="<?= h($email) ?>">
        <label for="password">Password (at least 8 characters)</label>
        <input id="password" name="password" type="password" minlength="8"
               autocomplete="new-password" required>
        <button type="submit">Register</button>
    </form>
    <p class="muted">Already registered? <a href="index.php">Sign in</a></p>
</main>
</body>
</html>
