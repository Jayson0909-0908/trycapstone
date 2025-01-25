<?php
require_once 'connection.php';
$db = new database();
$sales_data = $db->getSale($_GET['sale_id']);
$customer_data = $sales_data['customer'];
$items = $sales_data['items'];
$sale = $sales_data['sales'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>K30 Cakes and Pastries && Bakery Supply Trading</title>
  <link href="assets/img/faviconn.png" rel="icon">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600|Nunito:300,400,600|Poppins:300,400,500,600" rel="stylesheet">

  <style>
    @page {
      margin: 0;
    }
    body {
      font-family: Arial, sans-serif;
      font-size: 12px;
      color: black;
      margin: 0;
      padding: 0;
      width: 58mm; /* Set to the exact width of your thermal paper */
    }
    h3, p {
      margin: 0;
      padding: 0;
    }
    .receipt-section {
        margin: 2px 0;
        padding: 0;
    }
    .bold {
      font-weight: bold;
    }
    .items-table {
      width: 100%;
      border-collapse: collapse;
    }
    .items-table th, .items-table td {
      border-top: 1px solid #333;
      padding: 2px 0;
    }
    .items-table th {
      font-weight: bold;
      text-align: left;
    }
    .items-table td:last-child {
      text-align: right;
    }
  </style>
</head>

<body>
  <div id="receiptContainer">
    <div class="receipt-section">
      <center>
      <h3 class="bold">K3O Cakes and Pastries & Bakery Supplies Trading</h3>
      <p>Unit 1-A Pontiac St.,Fairview Lane, Brgy. Fairview 1118 Novaliches, Quezon City</p>
      </center>
    </div>
    <br>

    <div class="receipt-section">
  <h3>Receipt Details</h3>
  <p>Receipt #: <b><?= $sale['id'] ?></b></p>
  <p>Date & Time: <?= date('M d, Y h:i:s A', strtotime($sale['date_created'])) ?></p>
  <p>Cashier: <?= $sales_data['user_name'] ?></p> <!-- Display the user name here -->
</div>
    <br>

    <div class="receipt-section">
      <h3>Items</h3>
      <table class="items-table">
        <thead>
          <tr>
            <th style="width: 55%;">Item</th>
            <th style="width: 15%;">Qty</th>
            <th style="width: 15%;">Price</th>
            <th style="width: 15%;">Subtotal</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($items as $item) { ?>
          <tr>
            <td><?= $item['product'] ?></td>
            <td><?= number_format($item['quantity']) ?></td>
            <td>₱<?= number_format($item['unit_price'], 2) ?></td>
            <td>₱<?= number_format($item['sub_total'], 2) ?></td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
    <br>

    <div class="receipt-section">
      <p>Total: <span class="right-align" style="float: right;">₱<?= number_format($sale['sub_total'], 2) ?></span></p>
      <p>Discount: <span class="right-align" style="float: right;">₱<?= number_format($sale['discount'], 2) ?></span></p>
      <p>Vat: <span class="right-align" style="float: right;">₱<?= number_format($sale['vat'], 2) ?></span></p>
      <p class="bold">NET Total: <span class="right-align" style="float: right;">₱<?= number_format($sale['total_amount'], 2) ?></span></p>
      <p><?= $sale['payment_method'] ?>Tendered: <span class="right-align" style="float: right;">₱<?= number_format($sale['amount_tendered'], 2) ?></span></p>
      <p>Change: <span class="right-align" style="float: right;">₱<?= number_format($sale['change_amt'], 2) ?></span></p>
    </div>
    <br>

    <div class="receipt-section">
      <h3>Customer Details:</h3>
      <p>Name: <?= $customer_data['full_name'] ?></p>
      <p>Address: <?= $customer_data['address'] ?></p>
    </div>
    <br>
    <center>
    <p>Thank you for your purchase!</p>
    </center>
  </div>

  <script>
    window.print();
  </script>
</body>
</html>
