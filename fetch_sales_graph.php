<?php

// Get the filter from the POST request
$filter = isset($_POST['filter']) ? $_POST['filter'] : 'today';

// Determine the time interval for the query based on the filter
switch ($filter) {
    case 'this_month':
        $interval = 'INTERVAL 1 MONTH';
        break;
    case 'this_year':
        $interval = 'INTERVAL 1 YEAR';
        break;
    case 'weekly':
        $interval = 'INTERVAL 7 DAY';
        break;
    case 'today':
    default:
        $interval = 'INTERVAL 1 DAY';
        break;
}

// SQL query to fetch sales data based on the selected time period
$sql = "SELECT DATE(date_created) AS date, SUM(total_amount) AS total_sales
        FROM sales
        WHERE date_created >= NOW() - $interval
        GROUP BY DATE(date_created)
        ORDER BY date_created";

$stmt = $pdo->query($sql);
$data = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $data[] = [
        'date' => $row['date'],
        'total_sales' => $row['total_sales']
    ];
}

echo json_encode($data);
?>
