<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Forecast and Current Sales</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Chart.js Library -->
</head>
<body>

<canvas id="salesComparisonChart" width="400" height="200"></canvas>

<?php
class Database {
    private $uname = "root";
    private $pw = "";
    private $dsn = "mysql:host=localhost;dbname=possystem";
    public $con;

    public function __construct() {
        try {
            $this->con = new PDO($this->dsn, $this->uname, $this->pw);
            $this->con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Error : " . $e->getMessage();
        }
    }
}

class SalesForecast {
    private $db;

    public function __construct() {
        $this->db = new Database();  // Initialize database connection
    }

    // Fetch current sales data for today
    public function getCurrentDailySales() {
        $sql = "SELECT SUM(quantity * unit_price) as total_sales
                FROM sales_item
                WHERE DATE(date_created) = CURDATE()";

        $stmt = $this->db->con->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC)['total_sales'];
        
        // If no sales for today, return 0 instead of NULL
        return $result !== null ? $result : 0;
    }

    // Fetch current sales data for this month
    public function getCurrentMonthlySales() {
        $sql = "SELECT SUM(quantity * unit_price) as total_sales
                FROM sales_item
                WHERE MONTH(date_created) = MONTH(CURDATE())
                AND YEAR(date_created) = YEAR(CURDATE())";

        $stmt = $this->db->con->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC)['total_sales'];
        
        // Return 0 if no sales for this month
        return $result !== null ? $result : 0;
    }

    // Fetch current sales data for this year
    public function getCurrentYearlySales() {
        $sql = "SELECT SUM(quantity * unit_price) as total_sales
                FROM sales_item
                WHERE YEAR(date_created) = YEAR(CURDATE())";

        $stmt = $this->db->con->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC)['total_sales'];
        
        // Return 0 if no sales for this year
        return $result !== null ? $result : 0;
    }

    // Forecast sales for the next day, next month, and next year based on average sales
    public function forecastNextSales() {
        // Forecast based on average daily sales of the last 7 days
        $sql = "SELECT AVG(quantity * unit_price) as daily_avg_sales
                FROM sales_item
                WHERE DATE(date_created) >= CURDATE() - INTERVAL 7 DAY";
        $stmt = $this->db->con->prepare($sql);
        $stmt->execute();
        $daily_avg_sales = $stmt->fetch(PDO::FETCH_ASSOC)['daily_avg_sales'];

        // Forecast based on average monthly sales of the last 6 months
        $sql = "SELECT AVG(total_sales) as monthly_avg_sales
                FROM (SELECT SUM(quantity * unit_price) as total_sales
                      FROM sales_item
                      WHERE date_created >= CURDATE() - INTERVAL 6 MONTH
                      GROUP BY YEAR(date_created), MONTH(date_created)) AS monthly_sales";
        $stmt = $this->db->con->prepare($sql);
        $stmt->execute();
        $monthly_avg_sales = $stmt->fetch(PDO::FETCH_ASSOC)['monthly_avg_sales'];

        // Forecast based on average yearly sales of the last 2 years
        $sql = "SELECT AVG(total_sales) as yearly_avg_sales
                FROM (SELECT SUM(quantity * unit_price) as total_sales
                      FROM sales_item
                      WHERE date_created >= CURDATE() - INTERVAL 2 YEAR
                      GROUP BY YEAR(date_created)) AS yearly_sales";
        $stmt = $this->db->con->prepare($sql);
        $stmt->execute();
        $yearly_avg_sales = $stmt->fetch(PDO::FETCH_ASSOC)['yearly_avg_sales'];

        return [
            'next_day_sales' => $daily_avg_sales,
            'next_month_sales' => $monthly_avg_sales,
            'next_year_sales' => $yearly_avg_sales,
        ];
    }
}

// Usage
$salesForecast = new SalesForecast();

// Get current sales
$current_daily_sales = $salesForecast->getCurrentDailySales();
$current_monthly_sales = $salesForecast->getCurrentMonthlySales();
$current_yearly_sales = $salesForecast->getCurrentYearlySales();

// Get sales forecast for next day, month, and year
$next_sales_forecast = $salesForecast->forecastNextSales();

// Display the sales data
echo "<h2>Current Sales and Forecasts</h2>";
echo "<p>Today's Sales: $" . number_format($current_daily_sales, 2) . "</p>";
echo "<p>This Month's Sales: $" . number_format($current_monthly_sales, 2) . "</p>";
echo "<p>This Year's Sales: $" . number_format($current_yearly_sales, 2) . "</p>";
echo "<br>";
echo "<h2>Sales Forecasts</h2>";
echo "<p>Forecast for Next Day: $" . number_format($next_sales_forecast['next_day_sales'], 2) . "</p>";
echo "<p>Forecast for Next Month: $" . number_format($next_sales_forecast['next_month_sales'], 2) . "</p>";
echo "<p>Forecast for Next Year: $" . number_format($next_sales_forecast['next_year_sales'], 2) . "</p>";

// Pass the data to JavaScript for chart visualization
?>

<script>
// Prepare the data for the sales comparison chart
const labels = ['Daily', 'Monthly', 'Yearly'];
const currentSales = [
    <?php echo json_encode($current_daily_sales); ?>,
    <?php echo json_encode($current_monthly_sales); ?>,
    <?php echo json_encode($current_yearly_sales); ?>
];
const forecastedSales = [
    <?php echo json_encode($next_sales_forecast['next_day_sales']); ?>,
    <?php echo json_encode($next_sales_forecast['next_month_sales']); ?>,
    <?php echo json_encode($next_sales_forecast['next_year_sales']); ?>
];

// Render the sales comparison chart
const ctx = document.getElementById('salesComparisonChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [
            {
                label: 'Current Sales',
                data: currentSales,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            },
            {
                label: 'Forecasted Sales',
                data: forecastedSales,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }
        ]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>

</body>
</html>


let content ='\
                    <div class="row">\
                        <div class="col-md-6">\
                          <p style="font-size:16px;color:#b0b0b0;font-weight:bold;">Items</p>\
                            <div class="row checkoutTblHeaderContainer_title">\
                                <div class="col-md-2 checkoutTblHeader" style="font-size:16px;color:#b0b0b0;font-weight:bold;text-align:center;">#</div>\
                                <div class="col-md-4 checkoutTblHeader" style="font-size:16px;color:#b0b0b0;font-weight:bold;text-align:center;">Product Name</div>\
                                <div class="col-md-3 checkoutTblHeader" style="font-size:16px;color:#b0b0b0;font-weight:bold;text-align:center;">Order Qty</div>\
                                <div class="col-md-3 checkoutTblHeader" style="font-size:16px;color:#b0b0b0;font-weight:bold;text-align:center;">Amount</div>\
                            </div>' + orderItemsHtml + '\
                        </div>\
                        <div class="col-md-6">\
                            <div class="checkoutTotalAmountContainer" style="text-align:center;margin-top:10px">\
                                <span style="font-size: 36px;font-weight: bold;color: #f55;">₱'+ loadScript.addCommas(totalAmt.toFixed(2)) +'</span><br>\
                                <span style="display: block;font-size: 17px;color: #444;;">TOTAL AMOUNT</span><br>\
                            <hr/>\
                                </div>\
            <div class="payment-methods" style="text-align:center;">\
                <input type="hidden" id="paymentMethod" value="cash">\
                <button class="btn btn-success payment-btn" id="cash-btn">Cash</button>\
                <button class="btn btn-info payment-btn" id="cashless-btn">Cashless</button>\
            </div>\
            <div id="customer-details-cash" style="display:none; margin-top: 10px;">\
             <div class="checkoutUserAmt">\
                            <input class="form-control" id="userAmt"type="text" placeholder="Enter Amount." style="text-align:right;" />\
                            </div>\
                            <div class="checkoutUserChangeContainer" style="margin-top:32px;text-align:right;">\
                            <p class="checkoutUserChange" style="color:#06ab17;font-size:22px"><small>Change: </small><span class="changeAmt" style="color:b0b0b0;">₱ 0.00</span></p>\
                            </div>\
                <h5>Customer Details</h5>\
                <div class="form-group">\
                    <label for="fName">Name</label>\
                    <input type="text" id="fName" class="form-control"/>\
                </div>\
                <div class="form-group">\
                    <label for="address">Address</label>\
                    <input type="text" id="address" class="form-control" />\
                </div>\
            </div>\
            <div id="customer-details-cashless" style="display:none; margin-top: 10px;">\
             <div class="checkoutUserAmt">\
                            <input class="form-control" id="userAmt"type="text" placeholder="Enter Amount." style="text-align:right;" />\
                            </div>\
                            <div class="checkoutUserChangeContainer" style="margin-top:32px;text-align:right;">\
                            <p class="checkoutUserChange" style="color:#06ab17;font-size:22px"><small>Change: </small><span class="changeAmt" style="color:b0b0b0;">₱ 0.00</span></p>\
                            </div>\
                <h5>Customer Details</h5>\
    <div class="form-group">\
        <label for="fName_cashless">Name</label>\
        <input type="text" id="fName_cashless" class="form-control"/>\
    </div>\
    <div class="form-group">\
        <label for="address_cashless">Address</label>\
        <input type="text" id="address_cashless" class="form-control" />\
    </div>\
                <div class="form-group">\
                    <label for="contact">Gcash number</label>\
                    <input type="number" id="contact" class="form-control" />\
                </div>\
                <div class="form-group">\
                    <label for="referencenumber">Reference number</label>\
                    <input type="number" id="referencenumber" class="form-control" />\
                </div>\
            </div>\
            </div>\
        </div>\
    </div>'


    let content ='\
                    <div class="row">\
                        <div class="col-md-6">\
                          <p style="font-size:16px;color:#b0b0b0;font-weight:bold;">Items</p>\
                            <div class="row checkoutTblHeaderContainer_title">\
                                <div class="col-md-2 checkoutTblHeader" style="font-size:16px;color:#b0b0b0;font-weight:bold;text-align:center;">#</div>\
                                <div class="col-md-4 checkoutTblHeader" style="font-size:16px;color:#b0b0b0;font-weight:bold;text-align:center;">Product Name</div>\
                                <div class="col-md-3 checkoutTblHeader" style="font-size:16px;color:#b0b0b0;font-weight:bold;text-align:center;">Order Qty</div>\
                                <div class="col-md-3 checkoutTblHeader" style="font-size:16px;color:#b0b0b0;font-weight:bold;text-align:center;">Amount</div>\
                            </div>' + orderItemsHtml + '\
                        </div>\
                        <div class="col-md-6">\
                            <div class="checkoutTotalAmountContainer" style="text-align:center;margin-top:10px">\
                                <span style="font-size: 36px;font-weight: bold;color: #f55;">₱'+ loadScript.addCommas(totalAmt.toFixed(2)) +'</span><br>\
                                <span style="display: block;font-size: 17px;color: #444;;">TOTAL AMOUNT</span><br>\
                            <hr/>\
                                </div>\
            <div class="payment-methods" style="text-align:center;">\
                <input type="hidden" id="paymentMethod" value="cash">\
                <button class="btn btn-success payment-btn" id="cash-btn">Cash</button>\
                <button class="btn btn-info payment-btn" id="cashless-btn">Cashless</button>\
            </div>\
            <div id="customer-details-cash" style="display:none; margin-top: 10px;">\
                                      <div class="checkoutUserChangeContainer" style="margin-top:32px;text-align:right;">\
                            <p class="checkoutUserChange" style="color:#06ab17;font-size:22px"><small>Change: </small><span class="changeAmt" style="color:b0b0b0;">₱ 0.00</span></p>\
                            </div>\
            <table class="payment-table">\
            <thead>\
                <tr>\
                    <th>Customer Details</th>\
                    <th>Method</th>\
                    <th>Amount</th>\
                </tr>\
            </thead>\
            <tbody>\
                <tr>\
                    <td>Name:<input type="text" id="fName">\
                    Address:<input type="text" id="address" class="form-control" /></td>\
                    <td></td>\
                    <td>Amount:<input class="form-control" id="userAmt"type="text" placeholder="Enter Amount." style="text-align:right;" /></td>\
                </tr>\
            </tbody>\
            </table>\
            <div id="customer-details-cashless" style="display:none; margin-top: 10px;">\
             <div class="checkoutUserAmt">\
                            <input class="form-control" id="userAmt"type="text" placeholder="Enter Amount." style="text-align:right;" />\
                            </div>\
                            <div class="checkoutUserChangeContainer" style="margin-top:32px;text-align:right;">\
                            <p class="checkoutUserChange" style="color:#06ab17;font-size:22px"><small>Change: </small><span class="changeAmt" style="color:b0b0b0;">₱ 0.00</span></p>\
                            </div>\
                <h5>Customer Details</h5>\
    <div class="form-group">\
        <label for="fName_cashless">Name</label>\
        <input type="text" id="fName_cashless" class="form-control"/>\
    </div>\
    <div class="form-group">\
        <label for="address_cashless">Address</label>\
        <input type="text" id="address_cashless" class="form-control" />\
    </div>\
                <div class="form-group">\
                    <label for="contact">Gcash number</label>\
                    <input type="number" id="contact" class="form-control" />\
                </div>\
                <div class="form-group">\
                    <label for="referencenumber">Reference number</label>\
                    <input type="number" id="referencenumber" class="form-control" />\
                </div>\
            </div>\
            </div>\
        </div>\
    </div>'


    <div id="customer-details-cash" style="display:none; margin-top: 10px;">\
                            <table class="table table-bordered">\
                                <thead>\
                                    <tr>\
                                        <th>Customer Details</th>\
                                        <th>Method</th>\
                                        <th>Amount</th>\
                                        <th>Remove</th>\
                                    </tr>\
                                </thead>\
                                <tbody>\
                                    <tr>\
                                        <td>\
                                            Name: <input type="text" id="fName" class="form-control" />\
                                            Address: <input type="text" id="address" class="form-control" />\
                                        </td>\
                                        <td>Cash</td>\
                                        <td><input class="form-control" id="userAmt" type="text" placeholder="Enter Amount." style="text-align:right;" /></td>\
                                        <td><button class="btn btn-danger btn-sm" onclick="removeRow(this)">X</button></td>\
                                    </tr>\
                                </tbody>\
                            </table>\
                        </div>\
                        <div id="customer-details-cashless" style="display:none; margin-top: 10px;">\
                            <table class="table table-bordered">\
                                <thead>\
                                    <tr>\
                                        <th>Customer Details</th>\
                                        <th>Method</th>\
                                        <th>Amount</th>\
                                        <th>Remove</th>\
                                    </tr>\
                                </thead>\
                                <tbody>\
                                    <tr>\
                                        <td>\
                                            Name: <input type="text" id="fName_cashless" class="form-control" />\
                                            Address: <input type="text" id="address_cashless" class="form-control" />\
                                        </td>\
                                        <td>Cashless</td>\
                                        <td><input class="form-control" id="userAmt_cashless" type="text" placeholder="Enter Amount." style="text-align:right;" /></td>\
                                        <td><button class="btn btn-danger btn-sm" onclick="removeRow(this)">X</button></td>\
                                    </tr>\
                                </tbody>\
                            </table>\
                        </div>\
                    </div>\
                </div>';





                new 

                <script>
    var cashSelected = false; // Track if Cash was selected first

    function showDetails(method) {
        // Remove the empty row if it exists
        var emptyRow = document.getElementById("empty-row");
        if (emptyRow) {
            emptyRow.remove();
        }

        var detailsBody = document.getElementById("details-body");

        // Create a new row for the method selected
        var newRow = document.createElement("tr");

        if (method === "Cash") {
            cashSelected = true;  // Mark Cash as selected first
            newRow.innerHTML = `
                <td>
                    Name: <input type="text" id="fName" class="form-control" /><br>
                    Address: <input type="text" id="address" class="form-control" />
                </td>
                <td>Cash
                    <div style="margin-top: 10px;">
                        <label for="userAmt" style="font-size: 16px;">Tendered:</label>
                        <input class="form-control" id="userAmt" type="number" placeholder="0.00"
                               style="text-align: right; width: 60px; display: inline-block; margin-left: 5px; border: 1px solid #ccc; padding: 5px; font-size: 14px;">
                    </div>
                    <div style="margin-top: 10px;">
                        <label for="changeAmt" style="font-size: 16px;">Change:</label>
                        <input id="changeAmt" class="changeAmt" type="text" value="0.00" readonly
                               style="text-align: right; width: 60px; display: inline-block; margin-left: 20px; border: 1px solid #ccc; padding: 5px; font-size: 14px; background-color: #f9f9f9;">
                    </div>
                </td>
                <td style="text-align: center; vertical-align: middle;">
                    <button class="btn btn-danger btn-sm" onclick="removeRow(this)">X</button>
                </td>`;
        } else if (method === "Cashless") {
            // If Cash was selected first, display Gcash and Reference fields along with Tendered and Change
            if (cashSelected) {
                newRow.innerHTML = `
                    <td>
                        Gcash number: <input type="number" id="contact" class="form-control" /><br>
                        Reference number: <input type="number" id="referencenumber" class="form-control" />
                    </td>
                    <td>Cashless
                        <div style="margin-top: 10px;">
                            <label for="userAmtCashless" style="font-size: 16px;">Tendered:</label>
                            <input class="form-control" id="userAmtCashless" type="number" placeholder="0.00"
                                   style="text-align: right; width: 60px; display: inline-block; margin-left: 5px; border: 1px solid #ccc; padding: 5px; font-size: 14px;">
                        </div>
                        <div style="margin-top: 10px;">
                            <label for="changeAmtCashless" style="font-size: 16px;">Change:</label>
                            <input id="changeAmtCashless" class="changeAmt" type="text" value="0.00" readonly
                                   style="text-align: right; width: 60px; display: inline-block; margin-left: 20px; border: 1px solid #ccc; padding: 5px; font-size: 14px; background-color: #f9f9f9;">
                        </div>
                    </td>
                    <td style="text-align: center; vertical-align: middle;">
                        <button class="btn btn-danger btn-sm" onclick="removeRow(this)">X</button>
                    </td>`;
            } else {
                // If Cashless was selected first, display all fields (Name, Address, Gcash, Reference)
                newRow.innerHTML = `
                    <td>
                        Name: <input type="text" id="fName_cashless" class="form-control" /><br>
                        Address: <input type="text" id="address_cashless" class="form-control" /><br>
                        Gcash number: <input type="number" id="contact" class="form-control" /><br>
                        Reference number: <input type="number" id="referencenumber" class="form-control" />
                    </td>
                    <td>Cashless</td>
                    <td style="text-align: center; vertical-align: middle;">
                        <button class="btn btn-danger btn-sm" onclick="removeRow(this)">X</button>
                    </td>`;
            }
        }

        detailsBody.appendChild(newRow);
    }

    function removeRow(button) {
        // Remove the row where the remove button was clicked
        button.closest("tr").remove();

        // Restore the empty row if there are no rows left
        var detailsBody = document.getElementById("details-body");
        if (detailsBody.childElementCount === 0) {
            var emptyRow = document.createElement("tr");
            emptyRow.id = "empty-row";
            emptyRow.innerHTML = `<td colspan="3" style="text-align: center;">Select a payment method to enter details.</td>`;
            detailsBody.appendChild(emptyRow);
            cashSelected = false;  // Reset cashSelected if all rows are removed
        }
    }
</script>




<div id="customer-details-cashless" style="display:none; margin-top: 10px;">\
                                            <table class="table table-bordered">\
                                    <thead>\
                                        <tr>\
                                            <th style="width: 50%;">Customer Details</th>\
                                            <th style="width: 40%;">Method</th>\
                                            <th style="width: 10%;">X</th>\
                                        </tr>\
                                    </thead>\
                                    <tbody>\
                                        <tr>\
                                            <td>\
                                                Name: <input type="text" id="fName" class="form-control" />\
                                                Address: <input type="text" id="address" class="form-control" />\
                                                Gcash number: <input type="number" id="contact" class="form-control" />\
                                                Reference number: <input type="number" id="referencenumber" class="form-control" />\
                                            </td>\
                                            <td>Cash\
                                            <div style="margin-top: 10px;">\
        <label for="userAmt" style="font-size: 16px;">Tendered:</label>\
        <input class="form-control" id="userAmt" type="number" placeholder="0.00" style="text-align: right; width: 60px; display: inline-block; margin-left: 5px; border: 1px solid #ccc; padding: 5px; font-size: 14px;">\
    </div>\
    <div style="margin-top: 10px;">\
        <label for="changeAmt" style="font-size: 16px;">Change:</label>\
        <input id="changeAmt" class="changeAmt" type="text" value="0.00" readonly\
            style="text-align: right; width: 60px; display: inline-block; margin-left: 20px; border: 1px solid #ccc; padding: 5px; font-size: 14px; background-color: #f9f9f9;">\
    </div>\
                                            <td style="text-align: center; vertical-align: middle;"><button class="btn btn-danger btn-sm" onclick="removeRow(this)">X</button></td>\
                                        </tr>\
                                    </tbody>\
                                </table>\
            </div>\









            
            if(targetElClassList.contains('checkOutBtn')){
                    if(Object.keys(loadScript.orderItems).length){

                    let orderItemsHtml = '';
                    let counter = 1;
                    let totalAmt = 0.00;
                    for (const [pid, orderItem] of Object.entries(loadScript.orderItems)){
                        orderItemsHtml +='\
                        <div class="row checkoutTblContentContainer">\
                        <div class="col-md-2 checkoutTblContent">'+ counter +'</div>\
                        <div class="col-md-4 checkoutTblContent">'+ orderItem['name'] +'</div>\
                        <div class="col-md-3 checkoutTblContent">'+ loadScript.addCommas(orderItem['orderQty']) +'</div>\
                        <div class="col-md-3 checkoutTblContent">₱'+ loadScript.addCommas(orderItem['amount'].toFixed(2)) +'</div>\
                </div>';
                        totalAmt += orderItem['amount'];
                        counter++;
                    }

                    let content ='\
                    <div class="row">\
                        <div class="col-md-6">\
                        <p style="font-size:16px;color:#b0b0b0;font-weight:bold;">Items</p>\
                            <div class="row checkoutTblHeaderContainer_title">\
                                <div class="col-md-2 checkoutTblHeader" style="font-size:16px;color:#b0b0b0;font-weight:bold;text-align:center;">#</div>\
                                <div class="col-md-4 checkoutTblHeader" style="font-size:16px;color:#b0b0b0;font-weight:bold;text-align:center;">Product Name</div>\
                                <div class="col-md-3 checkoutTblHeader" style="font-size:16px;color:#b0b0b0;font-weight:bold;text-align:center;">Order Qty</div>\
                                <div class="col-md-3 checkoutTblHeader" style="font-size:16px;color:#b0b0b0;font-weight:bold;text-align:center;">Amount</div>\
                            </div>' + orderItemsHtml + '\
                        </div>\
                        <div class="col-md-6">\
                            <div class="checkoutTotalAmountContainer" style="text-align:center;margin-top:10px">\
                                <span style="font-size: 36px;font-weight: bold;color: #f55;">₱'+ loadScript.addCommas(totalAmt.toFixed(2)) +'</span><br>\
                                <span style="display: block;font-size: 17px;color: #444;;">TOTAL AMOUNT</span><br>\
                            <hr/>\
                                </div>\
            <div class="payment-methods" style="text-align:center;">\
                <input type="hidden" id="paymentMethod" value="cash">\
                <button class="btn btn-success payment-btn" id="cash-btn">Cash</button>\
                <button class="btn btn-info payment-btn" id="cashless-btn">Cashless</button>\
            </div>\
            <div id="customer-details-cash" style="display:none; margin-top: 10px;">\
                                <table class="table table-bordered">\
                                    <thead>\
                                        <tr>\
                                            <th style="width: 50%;">Customer Details</th>\
                                            <th style="width: 40%;">Method</th>\
                                            <th style="width: 10%;">X</th>\
                                        </tr>\
                                    </thead>\
                                    <tbody>\
                                        <tr>\
                                            <td>\
                                                Name: <input type="text" id="fName" class="form-control" />\
                                                Address: <input type="text" id="address" class="form-control" />\
                                            </td>\
                                            <td>Cash\
                                            <div style="margin-top: 10px;">\
        <label for="userAmt" style="font-size: 16px;">Tendered:</label>\
        <input class="form-control" id="userAmt" type="number" placeholder="0.00" style="text-align: right; width: 60px; display: inline-block; margin-left: 5px; border: 1px solid #ccc; padding: 5px; font-size: 14px;">\
    </div>\
    <div style="margin-top: 10px;">\
        <label for="changeAmt" style="font-size: 16px;">Change:</label>\
        <input id="changeAmt" class="changeAmt" type="text" value="0.00" readonly\
            style="text-align: right; width: 60px; display: inline-block; margin-left: 20px; border: 1px solid #ccc; padding: 5px; font-size: 14px; background-color: #f9f9f9;">\
    </div>\
                                            <td style="text-align: center; vertical-align: middle;"><button class="btn btn-danger btn-sm" onclick="removeRow(this)">X</button></td>\
                                        </tr>\
                                    </tbody>\
                                </table>\
                            </div>\
            <div id="customer-details-cashless" style="display:none; margin-top: 10px;">\
                                            <table class="table table-bordered">\
                                    <thead>\
                                        <tr>\
                                            <th style="width: 50%;">Customer Details</th>\
                                            <th style="width: 40%;">Method</th>\
                                            <th style="width: 10%;">X</th>\
                                        </tr>\
                                    </thead>\
                                    <tbody>\
                                        <tr>\
                                            <td>\
                                                Name: <input type="text" id="fName_cashless" class="form-control" />\
                                                Address: <input type="text" id="address_cashless" class="form-control" />\
                                                Gcash number: <input type="number" id="contact" class="form-control" />\
                                                Reference number: <input type="number" id="referencenumber" class="form-control" />\
                                            </td>\
                                            <td>Cash\
                                            <div style="margin-top: 10px;">\
        <label for="userAmt" style="font-size: 16px;">Tendered:</label>\
        <input class="form-control" id="userAmt" type="number" placeholder="0.00" style="text-align: right; width: 60px; display: inline-block; margin-left: 5px; border: 1px solid #ccc; padding: 5px; font-size: 14px;">\
    </div>\
    <div style="margin-top: 10px;">\
        <label for="changeAmt" style="font-size: 16px;">Change:</label>\
        <input id="changeAmt" class="changeAmt" type="text" value="0.00" readonly\
            style="text-align: right; width: 60px; display: inline-block; margin-left: 20px; border: 1px solid #ccc; padding: 5px; font-size: 14px; background-color: #f9f9f9;">\
    </div>\
                                            <td style="text-align: center; vertical-align: middle;"><button class="btn btn-danger btn-sm" onclick="removeRow(this)">X</button></td>\
                                        </tr>\
                                    </tbody>\
                                </table>\
            </div>\
            </div>\
        </div>\
    </div>'

    // Event listener for payment button clicks
    document.querySelectorAll('.payment-btn').forEach(button => {
        button.addEventListener('click', function (event) {
            const target = event.target;

            if (target.id === 'cash-btn') {
                // Show customer details for cash payment
                document.getElementById('customer-details-cash').style.display = 'block';
                document.getElementById('customer-details-cashless').style.display = 'none';
                document.getElementById('paymentMethod').value = 'cash';
            } else if (target.id === 'cashless-btn') {
                // Show customer details for cashless payment
                document.getElementById('customer-details-cashless').style.display = 'block';
                document.getElementById('customer-details-cash').style.display = 'none';
                document.getElementById('paymentMethod').value = 'cashless';
            }
        });
    });

    // Checkout confirmation dialog
    BootstrapDialog.confirm({
        type: BootstrapDialog.TYPE_INFO,
        title: '<center><B>CHECKOUT</B></center>',
        cssClass: 'checkoutDialog',
        message: content,
        btnOkLabel: 'CheckOut',
        callback: function (checkout) {
            if (checkout) {
                // Log the customer details and other data
                const paymentMethod = document.getElementById('paymentMethod').value;
                const fullName = paymentMethod === 'cash'
                    ? document.getElementById('fName').value
                    : document.getElementById('fName_cashless').value; // Adjust ID if changed
                const address = paymentMethod === 'cash'
                    ? document.getElementById('address').value
                    : document.getElementById('address_cashless').value; // Adjust ID if changed
                const contact = document.getElementById('contact').value;
                const referenceNumber = document.getElementById('referencenumber').value;

                console.log({
                    data: loadScript.orderItems,
                    totalAmt: loadScript.totalOrderAmount,
                    change: loadScript.userChange,
                    tenderedAmt: loadScript.tenderedAmt,
                    paymentMethod,
                    referenceNumber,
                    customer: {
                        fullName,
                        contact,
                        address,
                    }
                });

                // Proceed with the AJAX request
                $.post('action.php?action=checkout', {
                    data: loadScript.orderItems,
                    totalAmt: loadScript.totalOrderAmount,
                    change: loadScript.userChange,
                    tenderedAmt: loadScript.tenderedAmt,
                    paymentMethod,
                    referenceNumber,
                    customer: {
                        fullName,
                        contact,
                        address,
                    }
                }, function (response) {
                    let type = response.success ? BootstrapDialog.TYPE_SUCCESS : BootstrapDialog.TYPE_DANGER;
                    BootstrapDialog.alert({
                        title: response.success ? 'Success' : 'Error',
                        message: response.message,
                        callback: function (isOk) {
                            if (response.success) {
                                loadScript.resetData(response);
                            }
                        }
                    });
                }, 'json');
            }
        }
    });

                    }



                    buttons: [
    {
        label: 'CheckOut',
        cssClass: 'btn-primary',
        action: function(dialogRef) {
            const paymentMethod = document.getElementById('paymentMethod').value;

            // Sum all tendered amounts
            const tenderedInputs = document.querySelectorAll('.tendered-input');
            let tenderedAmt = 0;
            tenderedInputs.forEach(input => {
                tenderedAmt += parseFloat(input.value) || 0;
            });

            const fullName = paymentMethod === 'cash'
                ? document.getElementById('fName').value
                : null;
            const address = paymentMethod === 'cash'
                ? document.getElementById('address').value
                : null;
            const contact = paymentMethod === 'cashless' ? document.getElementById('contact').value : null;
            const referenceNumber = paymentMethod === 'cashless' ? document.getElementById('referencenumber').value : null;

            if (paymentMethod === 'cashless' && !referenceNumber) {
                BootstrapDialog.alert({
                    type: BootstrapDialog.TYPE_DANGER,
                    title: 'Missing Reference Number',
                    message: 'Please enter a reference number for Cashless payment.',
                });
                return;
            }

            if (tenderedAmt < totalAmt) {
                BootstrapDialog.alert({
                    type: BootstrapDialog.TYPE_DANGER,
                    title: 'Insufficient Amount',
                    message: 'Please enter an amount greater than or equal to the total amount.',
                });
                return;
            }

            // Proceed with the AJAX request
            $.post('action.php?action=checkout', {
                data: loadScript.orderItems,
                totalAmt: loadScript.totalOrderAmount,
                change: loadScript.userChange,
                tenderedAmt: tenderedAmt, // Use the total tendered amount here
                paymentMethod,
                referenceNumber,
                customer: {
                    fullName,
                    contact,
                    address,
                }
            }, function (response) {
                let type = response.success ? BootstrapDialog.TYPE_SUCCESS : BootstrapDialog.TYPE_DANGER;
                BootstrapDialog.alert({
                    title: response.success ? 'Success' : 'Error',
                    message: response.message,
                    callback: function (isOk) {
                        if (response.success) {
                            loadScript.resetData(response);
                        }
                    }
                });
            }, 'json');
            dialogRef.close();
        }
    }
]

