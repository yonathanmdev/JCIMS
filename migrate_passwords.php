<?php
// migrate_passwords.php
require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$db = \App\Config\Database::getConnection();

$stmt = $db->query("SELECT id, password FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

$update = $db->prepare("UPDATE users SET password = :password WHERE id = :id");

$migratedCount = 0;
$skippedCount = 0;

foreach ($users as $user) {
    $info = password_get_info($user['password']);

    if (!empty($info['algo'])) {
        $skippedCount++;
        continue;
    }

    $hashed = password_hash($user['password'], PASSWORD_BCRYPT);

    $update->execute([
        ':password' => $hashed,
        ':id'       => $user['id'],
    ]);

    $migratedCount++;
}

echo "Done. Migrated: $migratedCount, Skipped (already hashed): $skippedCount\n";