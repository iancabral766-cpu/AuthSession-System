<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli('localhost', 'root', '', 'auth_demo');
    $conn->set_charset('utf8mb4');
    $conn->query(
        "CREATE TABLE IF NOT EXISTS users (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(254) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            account_status VARCHAR(20) NOT NULL DEFAULT 'Active',
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );
} catch (mysqli_sql_exception $exception) {
    error_log('Authentication database error: ' . $exception->getMessage());
    http_response_code(500);
    exit('Unable to connect to the authentication database. Check the MySQL service and auth_demo database.');
}
?>