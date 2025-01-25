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

    public function getSalesData($filter = 'weekly') {
        $dateCondition = '';
        $forecastDays = 7; // Default forecast for the next 7 days
    
        switch($filter) {
            case 'weekly':
                $dateCondition = "WHERE date_created >= NOW() - INTERVAL 7 DAY";
                break;
            case 'monthly':
                $dateCondition = "WHERE date_created >= NOW() - INTERVAL 6 MONTH";
                break;
            case 'yearly':
                $dateCondition = "WHERE date_created >= NOW() - INTERVAL 5 YEAR";
                break;
        }
    
        // Fetch actual sales data based on the filter
        $sql = "SELECT DATE(date_created) AS date, SUM(total_amount) AS total_sales 
                FROM sales 
                $dateCondition
                GROUP BY DATE(date_created) 
                ORDER BY date_created";
        $stmt = $this->pdo->query($sql);
        $data = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $data[] = [
                'x' => strtotime($row['date']),
                'y' => $row['total_sales']
            ];
        }
    
        return $data;
    }
    
    public function getForecastData($filter = 'weekly') {
        // Get the sales data to calculate forecast
        $salesData = $this->getSalesData($filter);
    
        // Calculate the regression for forecasting
        list($m, $c) = $this->calculateLinearRegression($salesData);
    
        // Prepare forecast data
        $forecastDates = [];
        $forecastSales = [];
        $futureDate = strtotime('today');
    
        // Generate forecast for the next 7 days
        for ($i = 0; $i < 7; $i++) {
            $forecastDate = date('Y-m-d', strtotime("+$i day", $futureDate));
            $predictedSales = $this->predictSales($m, $c, $forecastDate);
            $forecastDates[] = $forecastDate;
            $forecastSales[] = $predictedSales;
        }
    
        return ['forecastDates' => $forecastDates, 'forecastSales' => $forecastSales];
    }
    

    // Calculate regression coefficients (m and c for the line)
    public function calculateLinearRegression($data) {
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
}

$salesForecast = new SalesForecast();
$salesData = $salesForecast->getSalesData();

// Calculate regression coefficients for actual sales
list($m, $c) = $salesForecast->calculateLinearRegression($salesData);

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
        <div class="col-lg-12">
            <div class="row">
                <!-- Sales Cards -->
            <!-- Sales Cards -->
<div class="col-xxl-4 col-md-6">
    <div class="card info-card sales-card">
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
                    <h6 id="sales-count">0</h6> <!-- This will be dynamically updated -->
                    <span>Total Sales</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-xxl-4 col-md-6">
    <div class="card info-card revenue-card">
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
                    <h6 id="revenue-count">₱0</h6> <!-- This will be dynamically updated -->
                    <span>Total Revenue</span>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-xxl-4 col-xl-12">
    <div class="card info-card customers-card">
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
            <h5 class="card-title">Customers <span>| Today</span></h5>

            <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-people"></i>
                </div>
                <div class="ps-3">
                    <h6 id="customers-count">0</h6> <!-- Add the ID for customer count -->
                    <span>Number of Customer</span>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="row">
    <!-- Actual Sales Chart -->
    <div class="col-xxl-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Actual Sales</h5>

                <!-- Filter for day, month, and year -->
                <div class="filter">
                    <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                        <li class="dropdown-header text-start">
                            <h6>Filter</h6>
                        </li>
                        <li><a class="dropdown-item" href="#" data-filter="weekly">Weekly</a></li>
                        <li><a class="dropdown-item" href="#" data-filter="monthly">Monthly</a></li>
                        <li><a class="dropdown-item" href="#" data-filter="yearly">Yearly</a></li>
                    </ul>
                </div>

                <canvas id="actualSalesChart"></canvas>
            </div>
        </div>
    </div><!-- End Actual Sales Chart -->

    <!-- Forecasted Sales Chart -->
    <div class="col-xxl-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Forecasted Sales (Next 7 Days)</h5>

                <div class="filter">
                    <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                        <li class="dropdown-header text-start">
                            <h6>Filter</h6>
                        </li>
                        <li><a class="dropdown-item" href="#" data-filter="weekly">Weekly</a></li>
                        <li><a class="dropdown-item" href="#" data-filter="monthly">Monthly</a></li>
                        <li><a class="dropdown-item" href="#" data-filter="yearly">Yearly</a></li>
                    </ul>
                </div>
                <canvas id="forecastSalesChart"></canvas>
            </div>
        </div>
    </div><!-- End Forecasted Sales Chart -->
</div>


                <!-- System History Section -->
<div class="col-xxl-12 col-md-12">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">System History</h5>
            <table class="table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Action</th>
                        <th>Description</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody id="systemHistoryTable">
                    <!-- System history data will be inserted here by JavaScript -->
                </tbody>
            </table>
        </div>
    </div>
</div>

            </div>
            </div>
        </div>
    </section>
</main>

<!-- Include necessary JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Fetch data when a filter is selected (Weekly, Monthly, Yearly)
document.querySelectorAll('.dropdown-item').forEach(item => {
    item.addEventListener('click', function() {
        const filter = this.textContent.toLowerCase();
        fetchSalesGraph(filter)
            .then(data => {
                if (data && data.actualSales && data.forecastSales) {
                    // Update both actual sales and forecasted sales charts
                    updateCharts(data.actualSales, data.forecastSales);
                } else {
                    console.error('Invalid data structure:', data);
                }
            })
            .catch(error => {
                console.error('Error fetching data:', error);
            });
    });
});

function fetchSalesGraph(filter) {
    return fetch(`fetch_sales_graph.php?filter=${filter}`)
        .then(response => {
            console.log('Response:', response);  // Log the response to see what's returned
            return response.json();  // Parse the JSON response
        })
        .then(data => {
            console.log('Data:', data);  // Log the parsed data
            return data;
        })
        .catch(error => {
            console.error('Error fetching data:', error);
        });
}

// Unified chart update function
function updateCharts(actualSales, forecastSales) {
    // Update Actual Sales Chart
    const ctx1 = document.getElementById('actualSalesChart').getContext('2d');
    new Chart(ctx1, {
        type: 'line',
        data: {
            labels: actualSales.dates,
            datasets: [
                {
                    label: 'Actual Sales',
                    data: actualSales.sales,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    fill: false
                },
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: true },
                title: { display: true, text: 'Actual vs Predicted Sales' }
            }
        }
    });

    // Update Forecasted Sales Chart
    const ctx2 = document.getElementById('forecastSalesChart').getContext('2d');
    new Chart(ctx2, {
        type: 'line',
        data: {
            labels: forecastSales.forecastDates,
            datasets: [
                {
                    label: 'Forecasted Sales',
                    data: forecastSales.forecastSales,
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
}

// Initial load (optional, you can set the default filter to 'weekly' or other)
updateCharts({dates: [], sales: []}, {forecastDates: [], forecastSales: []});
</script>

<script>
function fetchSalesData(filter) {
    console.log('Sending filter:', filter); // Debugging: Log the filter being sent

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

            // Update filter titles
            document.querySelector('#filter-title').textContent = `| ${filter.charAt(0).toUpperCase() + filter.slice(1)}`;
            document.querySelector('#revenue-filter-title').textContent = `| ${filter.charAt(0).toUpperCase() + filter.slice(1)}`;
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


// Function to fetch system history and update the table
function fetchSystemHistory() {
    fetch('systemlog.php')
        .then(response => response.json())  // Parse the response directly as JSON
        .then(data => {
            console.log(data); // Log the parsed JSON to check if it's valid

            const tableBody = document.getElementById('systemHistoryTable');
            tableBody.innerHTML = ''; // Clear the table before inserting new rows

            if (data.error) {
                console.error(data.error);
                return;
            }

            // Insert each history record into the table
            data.forEach(entry => {
                const row = document.createElement('tr');
                const userCell = document.createElement('td');
                userCell.textContent = entry.username;
                row.appendChild(userCell);

                const actionCell = document.createElement('td');
                actionCell.textContent = entry.action_type;
                row.appendChild(actionCell);

                const descriptionCell = document.createElement('td');
                descriptionCell.textContent = entry.action_description;
                row.appendChild(descriptionCell);

                const dateCell = document.createElement('td');
                dateCell.textContent = entry.created_at;
                row.appendChild(dateCell);

                tableBody.appendChild(row);
            });
        })
        .catch(error => {
            console.error('Error fetching system history:', error);
        });
}

// Call the function to fetch system history on page load
document.addEventListener('DOMContentLoaded', fetchSystemHistory);

// Refresh system history every 1 minute (60000 ms)
setInterval(fetchSystemHistory, 60000);


</script>
