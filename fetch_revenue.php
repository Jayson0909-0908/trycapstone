<?php
// Assuming a POST request with a JSON body
$data = json_decode(file_get_contents("php://input"));

$filter = $data->filter;
$revenue = 0;

switch ($filter) {
    case 'Today':
        $sql = "SELECT SUM(total_amount) as revenue_today FROM sales WHERE DATE(date_created) = CURDATE()";
        break;
    case 'This Month':
        $sql = "SELECT SUM(total_amount) as revenue_month FROM sales WHERE YEAR(date_created) = YEAR(CURDATE()) AND MONTH(date_created) = MONTH(CURDATE())";
        break;
    case 'This Year':
        $sql = "SELECT SUM(total_amount) as revenue_year FROM sales WHERE YEAR(date_created) = YEAR(CURDATE())";
        break;
}

$result = $db->query($sql);
$row = $result->fetch_assoc();
$revenue = $row['revenue_today'] ?? 0;

echo json_encode(['revenue' => $revenue]);

?>
