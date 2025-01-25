<?php
include('connection.php');
include('header.php');
include('sidebar.php');
include('footer.php');

$conn = new Database(); // Assuming 'Database' class initializes PDO

// Fetch sales summary
$startDate = $_POST['startDate'] ?? date('Y-m-d', strtotime('-7 days'));
$endDate = $_POST['endDate'] ?? date('Y-m-d');
$selectedReport = $_POST['reportType'] ?? 'summary';

    $itemSales = $conn->getItemSales($startDate, $endDate); // Assuming `getItemSales` fetches item sales data
    $summary = $conn->getSalesSummary($startDate, $endDate);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    <link href="assets/css/reports.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<main id="main" class="main">
    <div class="report-header d-flex justify-content-between align-items-center">
        <!-- Reports Title and Dropdown -->
        <div>
            <h4 style="margin-left: 120px;">Sales reports</h4>
        </div>
        <!-- Date Range Filter -->
        <div class="d-flex align-items-center">
            <label for="dateRange" class="me-2">Range:</label>
            <form method="POST" id="dateRangeForm" class="d-flex align-items-center">
                <input type="hidden" name="reportType" value="<?= $selectedReport; ?>">
                <input type="date" name="startDate" id="startDate" class="form-control" style="max-width: 150px;" value="<?= $startDate; ?>">
                <span class="mx-2">to</span>
                <input type="date" name="endDate" id="endDate" class="form-control" style="max-width: 150px;" value="<?= $endDate; ?>">
                <button type="submit" class="btn btn-primary ms-2 btn-sm">Filter</button>
            </form>
        </div>

        <!-- Print Button -->
        <div class="text-center">
            <button class="btn btn-sm btn-primary" style="padding: 5px 10px; font-size: 14px;" onclick="window.print()">Print</button>
        </div>
    </div>
        <!-- Report Content -->
        <div class="report-container">
               
                <div class="summary-table">
                    <h5 class="text-center">Item Sales</h5>
                    <p class="text-center">(<?= date('d/m/Y', strtotime($startDate)); ?> - <?= date('d/m/Y', strtotime($endDate)); ?>)</p>
                    <table>
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th># Sales</th>
                                <th>Total</th>
                                <th># Refunded</th>
                                <th>Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($itemSales as $item): ?>
                                <tr>
    <td><?= $item['name']; ?></td> <!-- Ensure 'name' matches the alias in your query -->
    <td><?= $item['sold']; ?></td>
    <td style="text-align: right;"><?= number_format($item['total'], 2); ?></td>
    <td><?= $item['refunded']; ?></td>
    <td style="text-align: right;"><?= number_format($item['balance'], 2); ?></td>
</tr>

                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div class="summary-table">
                    <h5 class="text-center">Summary</h5>
                    <p class="text-center">(<?= date('d/m/Y', strtotime($startDate)); ?> - <?= date('d/m/Y', strtotime($endDate)); ?>)</p>
                    <table>
                        <thead>
                            <tr>
                                <th></th>
                                <th># Sales</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Sales</td>
                                <td><?= $summary['total_sales']; ?></td>
                                <td><?= number_format($summary['total_revenue'], 2); ?></td>
                            </tr>
                            <tr>
                                <td>Voids</td>
                                <td><?= $summary['total_voids']; ?></td>
                                <td><?= number_format($summary['total_void_amount'], 2); ?></td>
                            </tr>
                            <tr>
                                <td>Revenue</td>
                                <td><?= $summary['total_sales']; ?></td>
                                <td><?= number_format($summary['total_revenue'] - $summary['total_void_amount'], 2); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
