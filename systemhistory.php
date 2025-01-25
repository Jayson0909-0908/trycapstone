<?php
class SystemHistory {
    private $pdo;


    public function __construct() {
        $database = new database();
        $this->pdo = $database->con;
    }

    public function getHistory() {
        // Ensure $pdo is valid before querying
        if ($this->pdo) {
            $sql = "SELECT username, action_type, action_description, created_at FROM system_history ORDER BY created_at DESC";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return false; // Return false if $pdo is null
    }
}
?>
