<?php

// Basic error reporting (for development)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Adjust the path to Database.php according to your project structure
require_once __DIR__ . '/../app/core/Database.php';

$db = null;
try {
    $dbInstance = Database::getInstance();
    $db = $dbInstance->getConnection();
    echo "Database connection established successfully.\n";
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage() . "\n");
}

// Create migrations table if it doesn't exist
$migrationsTableSql = "
CREATE TABLE IF NOT EXISTS migrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    migration_name VARCHAR(255) NOT NULL UNIQUE,
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

try {
    $db->exec($migrationsTableSql);
    echo "Checked/created 'migrations' table successfully.\n";
} catch (PDOException $e) {
    die("Failed to create migrations table: " . $e->getMessage() . "\n");
}

// Get all migration files
$migrationFilesPath = __DIR__ . '/migrations/';
$allFiles = scandir($migrationFilesPath);
$migrationFiles = array_filter($allFiles, function($file) {
    return pathinfo($file, PATHINFO_EXTENSION) === 'sql';
});
sort($migrationFiles); // Ensure they are processed in order

if (empty($migrationFiles)) {
    echo "No migration files found.\n";
    exit;
}

echo "Found migration files: " . implode(', ', $migrationFiles) . "\n";

// Get applied migrations
$stmt = $db->query("SELECT migration_name FROM migrations");
$appliedMigrations = $stmt->fetchAll(PDO::FETCH_COLUMN);
echo "Applied migrations: " . implode(', ', $appliedMigrations) . "\n";

foreach ($migrationFiles as $file) {
    if (in_array($file, $appliedMigrations)) {
        echo "Migration '{$file}' already applied. Skipping.\n";
        continue;
    }

    echo "Applying migration '{$file}'...\n";
    $sql = file_get_contents($migrationFilesPath . $file);

    if (empty(trim($sql))) {
        echo "Migration file '{$file}' is empty. Skipping.\n";
        continue;
    }

    try {
        $db->beginTransaction();
        $db->exec($sql);
        
        // Record the migration
        $insertStmt = $db->prepare("INSERT INTO migrations (migration_name) VALUES (?)");
        $insertStmt->execute([$file]);
        
        $db->commit();
        echo "Migration '{$file}' applied successfully.\n";
    } catch (PDOException $e) {
        $db->rollBack();
        die("Failed to apply migration '{$file}': " . $e->getMessage() . "\nCheck 'migrations' table for consistency.\n");
    }
}

echo "All new migrations applied successfully.\n";

?>
