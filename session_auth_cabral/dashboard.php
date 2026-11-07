<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

$user = require_auth($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
          rel="stylesheet"
          integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65"
          crossorigin="anonymous">
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-primary shadow-sm">
    <div class="container">
        <span class="navbar-brand mb-0 h1">SessionAuthSystem</span>
        <form method="post" action="logout.php" class="m-0">
            <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
            <button class="btn btn-outline-light btn-sm" type="submit">Log out</button>
        </form>
    </div>
</nav>

<main class="container py-5">
    <div class="mb-4">
        <h1 class="text-secondary mb-0">Welcome, <?= h($user['name']) ?>.</h1>
        <p>This page is protected and only visible to logged-in User. </p>
    </div>
    <div class="row g-4">
        <section class="col-12 col-lg-6" aria-labelledby="account-heading">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-white">
                    <h2 class="h5 mb-0" id="account-heading">Account status</h2>
                </div>
                <div class="card-body">
                    <p class="mb-0">
                        <span class="badge text-bg-success"><?= h($user['account_status']) ?></span>
                    </p>
                        <div class="alert" role="status">
                            <strong>Your session is active and protected against Session.</strong>
                            Fixation using
                            <code>session_regenerate_id(true)</code>
                        </div>
                </div>
            </div>
        </section>

        <section class="col-12 col-lg-6" aria-labelledby="user-heading">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-white">
                    <h2 class="h5 mb-0" id="user-heading">User details</h2>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">User ID:</dt>
                        <dd class="col-sm-8"><?= h((string) $user['id']) ?></dd>
                        <dt class="col-sm-4">Username:</dt>
                        <dd class="col-sm-8"><?= h($user['name']) ?></dd>
                    </dl>
                </div>
            </div>
        </section>

        <section class="col-12" aria-labelledby="session-heading">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h2 class="h5 mb-0" id="session-heading">Active session ID</h2>
                </div>
                <div class="card-body">
                    <dt class="col-sm-4">Current Session ID:</dt>
                    <code class="d-block text-break"><?= h(session_id()) ?></code>
                    <p class="text-secondary small mt-3 mb-0">
                        Sign out and sign back in to see a newly generated session ID.
                    </p>
                </div>
            </div>
        </section>
    </div>

    <p class="text-secondary small mt-4 mb-0">
        This is a demo dashboard. Log out and back in to see session regeneration in action.
    </p>
</main>
</body>
</html>
