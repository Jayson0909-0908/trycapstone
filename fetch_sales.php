<?php
include('connection.php');

class SalesFilter {
    private $pdo;

    public function __construct() {
        $database = new Database();
        $this->pdo = $database->con;
    }

    public function getSales($filter) {
        $salesQuery = "";
        $customerQuery = "";

        if ($filter === "today") {
            $salesQuery = "SELECT COUNT(*) AS total_sales, SUM(total_amount) AS total_revenue FROM sales WHERE DATE(date_created) = CURDATE()";
            $customerQuery = "SELECT COUNT(DISTINCT customer_id) AS total_customers FROM sales WHERE DATE(date_created) = CURDATE()";
        } elseif ($filter === "this_month") {
            $salesQuery = "SELECT COUNT(*) AS total_sales, SUM(total_amount) AS total_revenue FROM sales WHERE MONTH(date_created) = MONTH(CURDATE()) AND YEAR(date_created) = YEAR(CURDATE())";
            $customerQuery = "SELECT COUNT(DISTINCT customer_id) AS total_customers FROM sales WHERE MONTH(date_created) = MONTH(CURDATE()) AND YEAR(date_created) = YEAR(CURDATE())";
        } elseif ($filter === "this_year") {
            $salesQuery = "SELECT COUNT(*) AS total_sales, SUM(total_amount) AS total_revenue FROM sales WHERE YEAR(date_created) = YEAR(CURDATE())";
            $customerQuery = "SELECT COUNT(DISTINCT customer_id) AS total_customers FROM sales WHERE YEAR(date_created) = YEAR(CURDATE())";
        }

        // Execute sales query
        $stmt = $this->pdo->query($salesQuery);
        $salesResult = $stmt->fetch(PDO::FETCH_ASSOC);

        // Execute customer query
        $stmt = $this->pdo->query($customerQuery);
        $customerResult = $stmt->fetch(PDO::FETCH_ASSOC);

        return [
            'total_sales' => $salesResult['total_sales'] ?? 0,
            'total_revenue' => (float)($salesResult['total_revenue'] ?? 0),
            'total_customers' => $customerResult['total_customers'] ?? 0
        ];
    }
}

header('Content-Type: application/json');

try {
    $input = file_get_contents('php://input'); // Read JSON input
    $data = json_decode($input, true); // Decode JSON

    if (!$data) {
        throw new Exception('Invalid JSON input.');
    }

    $filter = $data['filter'] ?? 'today'; // Use 'today' as default if filter is missing
    error_log("Filter received: $filter"); // Log the received filter for debugging

    $salesFilter = new SalesFilter();
    $salesData = $salesFilter->getSales($filter);

    echo json_encode($salesData);
} catch (Exception $e) {
    // Handle any errors
    error_log("Error: " . $e->getMessage());
    echo json_encode(['error' => $e->getMessage()]);
}

?>
