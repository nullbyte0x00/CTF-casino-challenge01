<?php
session_start();

$host = 'mysql';
$dbname = 'casino';
$user = 'user';  // change if needed
$pass = 'secret';      // change if needed
$charset = 'utf8mb4';

global $pdo;

try {
    $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("DB Connection failed: " . $e->getMessage());
}



class PDOModel {
    private $pdo;
    private $table;
    private $columns = [];

    public function __construct($table) {
        global $pdo; // use global PDO connection
        $this->pdo = $pdo;
        $this->table = $table;

        $this->loadTableColumns();
    }

    // Load column names from the table
    private function loadTableColumns() {
        $stmt = $this->pdo->prepare("DESCRIBE `$this->table`");
        $stmt->execute();
        $this->columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // Insert data from array (e.g., $_POST)
    public function insert(array $data): bool {
        // Filter data to only include valid table columns
        $filtered = array_intersect_key($data, array_flip($this->columns));

        // Optionally remove auto-increment column
        unset($filtered['id']); // adjust if needed

        if (empty($filtered)) {
            return false; // nothing to insert
        }

        $columns = array_keys($filtered);
        $placeholders = array_map(fn($col) => ':' . $col, $columns);

        $sql = "INSERT INTO `$this->table` (" . implode(', ', array_map(fn($col) => "`$col`", $columns)) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";
        $stmt = $this->pdo->prepare($sql);

        foreach ($filtered as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }

        return $stmt->execute();
    }
}
?>
