<?php
include('connection.php');
include('header.php');
include('sidebar.php');
include('footer.php');
date_default_timezone_set('Asia/Manila');

class SalesForecast {
    private $pdo;

    public function __construct() {
        $database = new database();
        $this->pdo = $database->con;
    }

    public function getSalesData() {
        // Generate an array of the last 10 days (including today)
        $dates = [];
        for ($i = 6; $i >= 0; $i--) {
            $dates[date('Y-m-d', strtotime("-$i day"))] = 0; // Default sales to 0
        }
    
        // Fetch actual sales data
        $sql = "SELECT DATE(date_created) AS date, SUM(total_amount) AS total_sales 
                FROM sales 
                WHERE date_created >= NOW() - INTERVAL 6 DAY 
                GROUP BY DATE(date_created) 
                ORDER BY date_created";
        $stmt = $this->pdo->query($sql);
    
        // Populate sales data into the dates array
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (!empty($row['date'])) {
                $dates[$row['date']] = $row['total_sales'];
            }
        }
    
        // Convert the dates array into the desired format
        $data = [];
        foreach ($dates as $date => $sales) {
            $data[] = [
                'x' => strtotime($date),
                'y' => $sales
            ];
        }
    
        return $data;
    }
    

    // Calculate regression coefficients
    public function calculateLinearRegression($data) {
        if (empty($data)) {
            throw new Exception('Input data is empty.');
        }

        $x = array_column($data, 'x');
        $y = array_column($data, 'y');
        $n = count($x);

        $sumX = array_sum($x);
        $sumY = array_sum($y);
        $sumXY = $sumX2 = 0;

        for ($i = 0; $i < $n; $i++) {
            $sumXY += $x[$i] * $y[$i];
            $sumX2 += $x[$i] ** 2;
        }

        $denominator = ($n * $sumX2 - $sumX ** 2);
        if ($denominator == 0) {
            throw new Exception('Cannot calculate regression: Division by zero.');
        }

        $m = ($n * $sumXY - $sumX * $sumY) / $denominator;
        $c = ($sumY - $m * $sumX) / $n;

        return [$m, $c];
    }

    // Predict future sales based on the linear regression formula
    public function predictSales($m, $c, $futureDate) {
        $futureX = strtotime($futureDate); // Convert future date to a timestamp
        return $m * $futureX + $c; // Linear formula
    }

    // Predict actual sales (use for forecasting future actual sales)
    public function predictActualSales($m, $c, $dates) {
        $predictedSales = [];
        foreach ($dates as $date) {
            $predictedSales[] = $this->predictSales($m, $c, $date);
        }
        return $predictedSales;
    }

//     public function getSalesDatamonth() {
//         $sql = "SELECT DATE_FORMAT(date_created, '%Y-%m') AS month, SUM(total_amount) AS total_sales 
//                 FROM sales 
//                 WHERE date_created >= NOW() - INTERVAL 12 MONTH 
//                 GROUP BY DATE_FORMAT(date_created, '%Y-%m') 
//                 ORDER BY date_created";
//         $stmt = $this->pdo->query($sql);
//         $data = [];
//         while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
//             $data[] = [
//                 'x' => strtotime($row['month'] . '-01'), // First day of the month for timestamp
//                 'y' => $row['total_sales'],
//                 'month_name' => date('F', strtotime($row['month'] . '-01')) // Full month name
//             ];
//         }
//         return $data;
//     }

//   // Function to calculate sales forecast for future months based on the previous 6 months
// public function predictSalesForNextMonths($salesDatamonth, $monthsToPredict = 7) {
//     $forecastSalesData = [];
//     $forecastMonthNames = [];

//     // Ensure we have enough data for a 6-month rolling window
//     if (count($salesDatamonth) < 6) {
//         throw new Exception("Insufficient data: At least 6 months of data is required for forecasting.");
//     }

//     // Loop to predict future months
//     for ($i = 0; $i < $monthsToPredict; $i++) {
//         // Extract the last 6 months of data
//         $recentData = array_slice($salesDatamonth, -6);

//         // Add current month as the 6th data point for prediction
//         $currentMonthIndex = count($salesDatamonth);
//         $recentData[] = [
//             'x' => $currentMonthIndex,
//             'y' => 0 // Placeholder for future sales
//         ];

//         // Perform linear regression
//         list($m, $c) = $this->calculateLinearRegressionmonth($recentData);

//         // Predict the next month's sales
//         $predictedSales = $m * $currentMonthIndex + $c;

//         // Avoid negative sales
//         $predictedSales = max(0, $predictedSales);

//         // Add prediction to the forecast data
//         $forecastSalesData[] = $predictedSales;

//         // Generate the forecasted month's name
//         $currentTimestamp = strtotime("first day of +$i month");
//         $forecastMonthNames[] = date('F', $currentTimestamp);

//         // Append the predicted month to the data for subsequent rolling windows
//         $salesDatamonth[] = [
//             'x' => $currentMonthIndex,
//             'y' => $predictedSales
//         ];
//     }

//     return [$forecastMonthNames, $forecastSalesData];
// }

public function getSalesDatamonth() {
    // Generate an array of the last 12 months
    $months = [];
    for ($i = 11; $i >= 0; $i--) {
        $monthKey = date('Y-m', strtotime("-$i month"));
        $months[$monthKey] = 0; // Default sales to 0
    }

    // Fetch actual sales data
    $sql = "SELECT DATE_FORMAT(date_created, '%Y-%m') AS month, SUM(total_amount) AS total_sales 
            FROM sales 
            WHERE date_created >= NOW() - INTERVAL 12 MONTH 
            GROUP BY DATE_FORMAT(date_created, '%Y-%m') 
            ORDER BY date_created";
    $stmt = $this->pdo->query($sql);

    // Populate sales data into the months array
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if (!empty($row['month'])) {
            $months[$row['month']] = $row['total_sales'];
        }
    }

    // Convert the months array into the desired format
    $data = [];
    foreach ($months as $month => $sales) {
        $data[] = [
            'x' => strtotime($month . '-01'), // First day of the month for timestamp
            'y' => $sales,
            'month_name' => date('F', strtotime($month . '-01')) // Full month name
        ];
    }

    return $data;
}


// Perform linear regression
public function calculateLinearRegressionmonth($data) {
    $x = array_column($data, 'x');
    $y = array_column($data, 'y');
    $n = count($x);

    $sumX = array_sum($x);
    $sumY = array_sum($y);
    $sumXY = $sumX2 = 0;

    for ($i = 0; $i < $n; $i++) {
        $sumXY += $x[$i] * $y[$i];
        $sumX2 += $x[$i] ** 2;
    }

    $m = ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - $sumX ** 2);
    $c = ($sumY - $m * $sumX) / $n;

    return [$m, $c];
}

// Predict future sales based on the last 6 months
public function predictSalesForNextMonths($salesDatamonth, $monthsToPredict = 7) {
    $forecastSalesData = [];
    $forecastMonthNames = [];

    // Ensure we have enough data for a 6-month rolling window
    if (count($salesDatamonth) < 6) {
        throw new Exception("Insufficient data: At least 6 months of data is required for forecasting.");
    }

    // Loop to predict future months
    for ($i = 0; $i < $monthsToPredict; $i++) {
        // Extract the last 6 months of data
        $recentData = array_slice($salesDatamonth, -6);

        // Add current month as the 6th data point for prediction
        $currentMonthIndex = count($salesDatamonth);
        $recentData[] = [
            'x' => $currentMonthIndex,
            'y' => 0 // Placeholder for future sales
        ];

        // Perform linear regression
        list($m, $c) = $this->calculateLinearRegressionmonth($recentData);

        // Predict the next month's sales
        $predictedSales = $m * $currentMonthIndex + $c;

        // Avoid negative sales
        $predictedSales = max(0, $predictedSales);

        // Add prediction to the forecast data
        $forecastSalesData[] = $predictedSales;

        // Generate the forecasted month's name
        $currentTimestamp = strtotime("first day of +$i month");
        $forecastMonthNames[] = date('F', $currentTimestamp);

        // Append the predicted month to the data for subsequent rolling windows
        $salesDatamonth[] = [
            'x' => $currentMonthIndex,
            'y' => $predictedSales
        ];
    }

    return [$forecastMonthNames, $forecastSalesData];
}

}

// Main script
try {
// Assuming $pdo is your PDO instance
$salesForecast = new SalesForecast();

// Retrieve actual sales data
$salesDatamonth = $salesForecast->getSalesDatamonth();

// Prepare actual sales data for charting
$monthNames = array_column($salesDatamonth, 'month_name');
$actualSalesDatamonth = array_column($salesDatamonth, 'y');

// Predict future sales for the next 7 months
list($forecastMonthNames, $forecastSalesData) = $salesForecast->predictSalesForNextMonths($salesDatamonth, 7);

// Encode data for JavaScript
$monthNamesString = json_encode($monthNames);
$actualSalesDataString = json_encode($actualSalesDatamonth);
$forecastMonthNamesString = json_encode($forecastMonthNames);
$forecastSalesDataString = json_encode($forecastSalesData);

} catch (Exception $e) {
echo "Error: " . $e->getMessage();

    
    
}

try {
    $salesForecast = new SalesForecast();
    $salesData = $salesForecast->getSalesData();

    if (empty($salesData)) {
        throw new Exception('No sales data available for the given period.');
    }

    // Calculate regression coefficients for actual sales
    list($m, $c) = $salesForecast->calculateLinearRegression($salesData);

    // Display results
    echo "Slope (m): $m, Intercept (c): $c";


// Prepare data for Chart.js
$dates = [];
$actualSales = [];
$predictedActualSales = [];
$forecastDates = [];
$forecastSales = [];

// Get actual sales data
foreach ($salesData as $data) {
    $dates[] = date('Y-m-d', $data['x']);
    $actualSales[] = $data['y'];
}

// Predict actual sales using linear regression (for last 7 days)
$predictedActualSales = $salesForecast->predictActualSales($m, $c, $dates);

// Generate forecast for the next 7 days
$futureDate = strtotime('today');
for ($i = 0; $i < 7; $i++) {
    $forecastDate = date('Y-m-d', strtotime("+$i day", $futureDate));
    $predictedFutureSales = $salesForecast->predictSales($m, $c, $forecastDate);
    $forecastDates[] = $forecastDate;
    $forecastSales[] = $predictedFutureSales;

}

// Pass data to the front-end for use in charts
$datesString = json_encode($dates);
$actualSalesString = json_encode($actualSales);
$predictedActualSalesString = json_encode($predictedActualSales);
$forecastDatesString = json_encode($forecastDates);
$forecastSalesString = json_encode($forecastSales);

// Prepare data for monthly forecast
// Fetch sales data for the past 12 months (grouped by month)
// $monthNames = [];
// $actualSalesDatamonth = [];
// $predictedSalesDatamonth = [];
// $forecastMonthNames = [];
// $forecastSalesData = [];

// // Retrieve actual monthly sales data
// foreach ($salesDatamonth as $data) {
//     $monthNames[] = $data['month_name']; // Full month name (e.g., January)
//     $actualSalesDatamonth[] = $data['y']; // Actual sales amount
// }


// // Generate future month names and timestamps for predictions
// for ($i = 0; $i <= 7; $i++) {
//     $currentTimestamp = strtotime("first day of +$i month");
//     $forecastMonthNames[] = date('F', $currentTimestamp);
//     $predictedSalesDatamonth = $salesForecast->predictSales($a, $b, $currentTimestamp);
//     $forecastSalesData[] = max(0, $predictedSalesDatamonth); // Avoid negative sales
// }

// // Encode data for JavaScript
// $monthNamesString = json_encode($monthNames);
// $actualSalesDataString = json_encode($actualSalesDatamonth);
// $forecastMonthNamesString = json_encode($forecastMonthNames);
// $forecastSalesDataString = json_encode($forecastSalesData);
} catch (Exception $e) {
    // Handle exceptions and display the error message
    echo "Error: " . $e->getMessage();
}

?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Dashboard</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
        <div class="container-fluid">
            <div class="row g-4">
                <!-- Sales Cards -->
                <div class="col-xxl-4 col-md-4">
                    <div class="card info-card sales-card h-100">
                        <div class="filter">
                            <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                <li class="dropdown-header text-start">
                                    <h6>Filter</h6>
                                </li>
                                <li><a class="dropdown-item filter-option" href="#" data-filter="today">Today</a></li>
                                <li><a class="dropdown-item filter-option" href="#" data-filter="this_month">This Month</a></li>
                                <li><a class="dropdown-item filter-option" href="#" data-filter="this_year">This Year</a></li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Sales <span id="filter-title">| Today</span></h5>
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-cart"></i>
                                </div>
                                <div class="ps-3">
                                    <h6 id="sales-count">0</h6>
                                    <span>Total Sales</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-4 col-md-4">
                    <div class="card info-card revenue-card h-100">
                        <div class="filter">
                            <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                <li class="dropdown-header text-start">
                                    <h6>Filter</h6>
                                </li>
                                <li><a class="dropdown-item filter-option" href="#" data-filter="today">Today</a></li>
                                <li><a class="dropdown-item filter-option" href="#" data-filter="this_month">This Month</a></li>
                                <li><a class="dropdown-item filter-option" href="#" data-filter="this_year">This Year</a></li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Revenue <span id="revenue-filter-title">| This Month</span></h5>
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <span>₱</span>
                                </div>
                                <div class="ps-3">
                                    <h6 id="revenue-count">₱0</h6>
                                    <span>Total Revenue</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-4 col-md-4">
                    <div class="card info-card customers-card h-100">
                        <div class="filter">
                            <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                <li class="dropdown-header text-start">
                                    <h6>Filter</h6>
                                </li>
                                <li><a class="dropdown-item" href="#">Today</a></li>
                                <li><a class="dropdown-item" href="#">This Month</a></li>
                                <li><a class="dropdown-item" href="#">This Year</a></li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Customers <span id="customer-filter-title">| Today</span></h5>
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-people"></i>
                                </div>
                                <div class="ps-3">
                                    <h6 id="customers-count">0</h6>
                                    <span>Number of Customers</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- End Sales Cards -->

            <div class="row g-4 mt-4">
                <!-- Actual Sales Chart (Weekly) -->
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">Actual Sales (Weekly)</h5>
                            <canvas id="actualSalesChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Forecasted Sales Chart (Next 7 Days) -->
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">Forecasted Sales (Next 7 Days)</h5>
                            <canvas id="forecastSalesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div><!-- End Weekly Charts -->

            <div class="row g-4 mt-4">
                <!-- Actual Sales Chart (Monthly) -->
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">Actual Sales (Monthly)</h5>
                            <canvas id="actualSalesChartmonth"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Forecasted Sales Chart (Next Month) -->
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">Forecasted Sales (Next Month)</h5>
                            <canvas id="forecastSalesChartmonths"></canvas>
                        </div>
                    </div>
                </div>
            </div><!-- End Monthly Charts -->
        </div>
    </section>
</main>


<!-- Include necessary JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const dates = <?php echo $datesString; ?>;
const actualSales = <?php echo $actualSalesString; ?>;
const predictedActualSales = <?php echo $predictedActualSalesString; ?>;
const forecastDates = <?php echo $forecastDatesString; ?>;
const forecastSales = <?php echo $forecastSalesString; ?>;

// Actual Sales Chart
const ctx1 = document.getElementById('actualSalesChart').getContext('2d');
new Chart(ctx1, {
    type: 'line',
    data: {
        labels: dates,
        datasets: [
            {
                label: 'Actual Sales',
                data: actualSales,
                borderColor: 'rgba(75, 192, 192, 1)',
                fill: false
            },
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: true },
            title: { display: true, text: 'Actual Sales (Last 7 Days)' }
        }
    }
});

// Forecasted Sales Chart
const ctx2 = document.getElementById('forecastSalesChart').getContext('2d');
new Chart(ctx2, {
    type: 'line',
    data: {
        labels: forecastDates,
        datasets: [
            {
                label: 'Forecasted Sales',
                data: forecastSales,
                borderColor: 'rgba(255, 99, 132, 1)',
                borderDash: [5, 5],
                borderWidth: 2,
                fill: false
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: true },
            title: { display: true, text: 'Forecasted Sales (Next 7 Days)' }
        }
    }
});

</script>

<script>
const datesMonth = <?php echo $monthNamesString; ?>; // Actual months
const actualSalesMonth = <?php echo $actualSalesDataString; ?>; // Actual sales
const forecastMonthNames = <?php echo $forecastMonthNamesString; ?>; // Future months
const forecastSalesData = <?php echo $forecastSalesDataString; ?>; // Forecasted sales

// Monthly Actual Sales Chart
const ctx3 = document.getElementById('actualSalesChartmonth').getContext('2d');
new Chart(ctx3, {
    type: 'line',
    data: {
        labels: datesMonth,
        datasets: [
            {
                label: 'Actual Sales',
                data: actualSalesMonth,
                borderColor: 'rgba(75, 192, 192, 1)',
                fill: false
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: true },
            title: { display: true, text: 'Actual Sales (Last 12 Months)' }
        }
    }
});

// Monthly Forecasted Sales Chart
const ctx4 = document.getElementById('forecastSalesChartmonths').getContext('2d');
new Chart(ctx4, {
    type: 'line',
    data: {
        labels: forecastMonthNames,
        datasets: [
            {
                label: 'Forecasted Sales',
                data: forecastSalesData,
                borderColor: 'rgba(255, 99, 132, 1)',
                borderDash: [5, 5],
                borderWidth: 2,
                fill: false
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: true },
            title: { display: true, text: 'Forecasted Sales (Next 7 Months)' }
        }
    }
});


console.log(datesMonth);
console.log(actualSalesMonth);
console.log(forecastSalesMonth);

</script>


<script>
function fetchSalesData(filter) {
    console.log('Sending filter:', filter); // Debugging: Log the filter being sent

    // Get the current month and year for dynamic titles
    const currentDate = new Date();
    const currentMonth = currentDate.toLocaleString('default', { month: 'long' }); // Full month name
    const currentYear = currentDate.getFullYear();

    // Set the filter title based on the selected filter
    let filterTitle = '';
    if (filter === 'today') {
        filterTitle = 'Today';
    } else if (filter === 'this_month') {
        filterTitle = currentMonth; // Display the current month name
    } else if (filter === 'this_year') {
        filterTitle = currentYear; // Display the current year
    }

    fetch('fetch_sales.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ filter: filter })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Received data:', data); // Debugging: Log the response from PHP

        // Update UI only if no error
        if (!data.error) {
            const salesCountElem = document.querySelector('#sales-count');
            const revenueCountElem = document.querySelector('#revenue-count');
            const customersCountElem = document.querySelector('#customers-count');
            
            if (salesCountElem) {
                salesCountElem.textContent = data.total_sales;
            }

            if (revenueCountElem) {
                const revenue = isNaN(data.total_revenue) ? 0 : data.total_revenue; // Default to 0 if it's not a number
                revenueCountElem.textContent = '₱' + revenue.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }

            if (customersCountElem) {
                customersCountElem.textContent = data.total_customers || 0;
            }

            // Update filter titles dynamically
            document.querySelector('#filter-title').textContent = `| ${filterTitle}`;
            document.querySelector('#revenue-filter-title').textContent = `| ${filterTitle}`;
            document.querySelector('#customer-filter-title').textContent = `| ${filterTitle}`;
        } else {
            console.error('PHP Error:', data.error);
        }
    })
    .catch(error => {
        console.error('Error fetching sales data:', error);
    });
}

// Event listener for filter change
document.querySelectorAll('.filter-option').forEach(item => {
    item.addEventListener('click', function(e) {
        const filter = e.target.getAttribute('data-filter');
        fetchSalesData(filter);
    });
});

// Initialize sales, revenue, and customer data for the default filter ('today')
fetchSalesData('today');


</script>
