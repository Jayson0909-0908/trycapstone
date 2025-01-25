<?php
// Include the connection file
include('connection.php'); 

// Define the SystemHistory class
class SystemHistory {
    private $pdo;

    public function __construct() {
        // Assuming you have a `database` class to handle connection
        $database = new database();  // Make sure `database` class exists and is correctly defined
        $this->pdo = $database->con;
    }

    public function getHistory() {
        // Ensure $pdo is valid before querying
        if ($this->pdo) {
            $sql = "SELECT user_id, action_type, action_description, created_at FROM system_history ORDER BY created_at DESC";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return false; // Return false if $pdo is null
    }
}

// Initialize the SystemHistory class with the PDO connection
$salesHistory = new SystemHistory();

// Get the system history
$history = $salesHistory->getHistory();

// Prepare response
$response = [];
if ($history) {
    foreach ($history as $entry) {
        $response[] = [
            'username' => $entry['username'],
            'action_type' => $entry['action_type'],
            'action_description' => $entry['action_description'],
            'created_at' => $entry['created_at']
        ];
    }
} else {
    $response['error'] = 'No history found.';
}

// Set the content type header to JSON
header('Content-Type: application/json');

// Return data as JSON
echo json_encode($response);
?>
