<?php
include('header.php');
include('sidebar.php');
include('footer.php');
require_once 'connection.php';

?>

<body>
  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Sales</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item">Tables</li>
          <li class="breadcrumb-item active">Data</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body" id="showSales" name="showSales">
              <h5 class="card-title">Sales Table</h5>
            </div>
          </div>

        </div>
      </div>
    </section>

  </main><!-- End #main -->

  <!-- Modal for Sale Edit -->
  <div class="modal fade" id="editcategory" tabindex="-1" aria-labelledby="editcategoryLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content border-light">
        <!-- Modal Header -->
        <div class="modal-header bg-light">
          <h5 class="modal-title" id="editcategoryLabel">Transaction Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <!-- Modal Body -->
        <div class="modal-body">
          <form action="#" method="post" id="edit-form-data">
            <!-- First Row: Status and Process DT -->
            <div class="row">
              <div class="col-md-5">
              <strong>Status:</strong>
                 <span class="badge bg-success ms-2" id="transactionStatus">Closed</span>
              </div>
              <div class="col-md-5">
                <strong>Process DT:</strong> <span id="date_created"></span>
              </div>
            </div>

            <!-- Second Row: ID and User -->
            <div class="row">
              <div class="col-md-5">
                <strong>ID:</strong> <span id="receipt_id"></span>
              </div>
              <div class="col-md-5">
                <strong>User:</strong> <span id="user_id"></span>
              </div>
            </div>

            <!-- Third Row: Ref and Device -->
            <div class="row mb-2">
              <div class="col-md-6">
                <strong>Ref:</strong> <span id="reference_number"></span>
              </div>
            </div>

            <!-- Fourth Row: Trans DT and Location -->
            <div class="row mb-3">
              <div class="col-md-6">
                <strong>Trans DT:</strong> <span id="date_updated"></span>
              </div>
            </div>

            <!-- Tabs -->
            <ul class="nav nav-tabs mb-3" id="transactionTabs" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link active" id="details-tab" data-bs-toggle="tab" data-bs-target="#details" type="button" role="tab" aria-controls="details" aria-selected="true">Details</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="items-tab" data-bs-toggle="tab" data-bs-target="#items" type="button" role="tab" aria-controls="items" aria-selected="false">Items</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="payments-tab" data-bs-toggle="tab" data-bs-target="#payments" type="button" role="tab" aria-controls="payments" aria-selected="false">Payments</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="options-tab" data-bs-toggle="tab" data-bs-target="#options" type="button" role="tab" aria-controls="options" aria-selected="false">Options</button>
              </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content">
              <div class="tab-pane fade show active" id="details" role="tabpanel" aria-labelledby="details-tab">
                <div class="p-3" id="saleDetails">
                  <div class="row">
                    <div class="col-5">
                  <h6><strong>Sale Totals:</strong></h6>
                  </div>
                  <div class="col-5">
                  <h6><strong>Customer Details:</strong></h6>
                  </div>
                  </div>
                  <div class="row">
                  <div class="col-5">
                  <p>Subtotal: <span id="sub_total"></span></p>
                  <p>Discount: <span id="discount"></span></p>
                  <p>VAT: <span id="vat"></span></p>
                  <p>Total: <span id="total_amount"></span></p>
                  </div>
                  <div class="col-5">
                  <p>Name: <span id="customer_name"></span></p>
                  <p>Address: <span id="customer_address"></span></p>
                </div>
                </div>
                </div>
              </div>
              <div class="tab-pane fade" id="items" role="tabpanel" aria-labelledby="items-tab">
             <div class="p-3">
             <h5>Items:</h5>
             <table id="simple_items_table" class="table table-bordered">
      <thead>
        <tr>
          <th>Name</th>
          <th>Qty</th>
          <th>Unit</th>
          <th>Price</th>
        </tr>
      </thead>
      <tbody id="items_table_body">
        <!-- Dynamic rows will be inserted here -->
      </tbody>
    </table>
  </div>
</div>

              <div class="tab-pane fade" id="payments" role="tabpanel" aria-labelledby="payments-tab">
              <div class="p-3">
             <h5>Items:</h5>
             <table id="simple_payments_table" class="table table-bordered">
      <thead>
        <tr>
          <th>Method</th>
          <th>Amount</th>
          <th>Date</th>
        </tr>
      </thead>
      <tbody>
      <tr>
         <td id="payment_method"></td>
         <td class="total_amount"></td>
         <td class="date_created"></td>
         </tr>
         </tbody>
    </table>
              </div>
              </div>
              <div class="tab-pane fade" id="options" role="tabpanel" aria-labelledby="options-tab">
  <div class="p-3">
    <button type="button" class="btn btn-primary" id="printButton">
      <i class="bi bi-printer"></i> Print Receipt
    </button>
  </div>
</div>

            </div>
          </form>
        </div>

        <!-- Modal Footer -->
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x"></i> Close</button>
        </div>
      </div>
    </div>
  </div>
<style>
/* General table styles */
 table {
  font-size: 14px;
  width: 100%;
  border-collapse: collapse; /* Ensure borders are merged between cells */
  margin: 0; /* Remove outer margins for full-width use */
 }

 table.dataTable {
  /* Apply styles only for DataTables */
  font-size: 14px;
  width: 100%;
  border-collapse: collapse;
 }

/* Prevent DataTables styles on #simple_items_table */
 #simple_items_table {
  font-size: initial;
  width: 100%; /* Ensure full width */
  margin: 0; /* Remove outer margins */
  border-collapse: collapse;
 }

 #simple_items_table th, #simple_items_table td {
  border: 1px solid #dee2e6; /* Light borders for cells */
  padding: 6px 10px; /* Slightly reduced padding for maximizing space */
  text-align: left;
  font-family: Arial, sans-serif;
 }

 #simple_items_table th {
  background-color: #f8f9fa; /* Light background for header */
  color: #333;
  font-weight: bold;
  text-transform: uppercase; /* Make header labels stand out */
 }

 #simple_items_table td {
  background-color: #fff;
  color: #555;
 }

 #simple_items_table tr:nth-child(even) {
  background-color: #f9f9f9; /* Lighter alternating row color */
 }

 #simple_items_table tr:hover {
  background-color: #e9ecef; /* Slight hover effect */
 }

/* Remove padding for better space utilization */
 #simple_items_table td, #simple_items_table th {
  padding: 4px 8px; /* Reduce padding for compactness */
 }

/* Responsive design: optimize for small screens */
 @media (max-width: 768px) {
  table, #simple_items_table {
    width: 100%;
    display: block;
    overflow-x: auto;
    margin: 0; /* Remove margin for maximum width */
  }
  
  #simple_items_table th, #simple_items_table td {
    padding: 4px 6px; /* Further reduce padding for small screens */
  }
 }

    </style>

  <script type="text/javascript">
    $(document).ready(function() {

      // Load sales data
      ShowAllSales();
      function ShowAllSales() {
        $.ajax({
          url: "action.php",
          type: "POST",
          data: { action: "viewsales" },
          success: function(response) {
            $('#showSales').html(response);
            $("table").DataTable({
              order: [0, 'desc']
            });
          }
        });
      }

      // Edit sale button
      $("body").on("click", ".editBtn", function(e) {
    e.preventDefault();
    edit_id = $(this).attr('id');
    
    // Send AJAX request to fetch sale data
    $.ajax({
        url: "action.php",
        type: "POST",
        data: { action: "getSaleData", sale_id: edit_id },
        success: function(response) {
            // Parse the response
            const data = JSON.parse(response);

            // Populate the modal with the data
            // Sale data
            $("#receipt_id").text(data.sales.id);
            $("#total_amount").text(data.sales.total_amount);
            $("#amount_tendered").text(data.sales.amount_tendered);
            $("#sub_total").text(data.sales.sub_total);
            $("#discount").text(data.sales.discount);
            $("#vat").text(data.sales.vat);
            $("#change_amount").text(data.sales.change_amt);
            $("#date_created").text(data.sales.date_created);
            $("#user_id").text(data.sales.user_id);
            $("#payment_method").text(data.sales.payment_method);
            $("#reference_number").text(data.sales.reference_number);
            $("#date_updated").text(data.sales.date_updated);
            const status = data.sales.status; // Assuming status is "Complete" or "Void"
        const statusBadge = $("#transactionStatus");
        
        if (status === "Complete") {
            statusBadge.text("Complete");
            statusBadge.removeClass("bg-danger").addClass("bg-success");
        } else if (status === "Void") {
            statusBadge.text("Void");
            statusBadge.removeClass("bg-success").addClass("bg-danger");
        }
            let subtotal = parseFloat(data.sales.total_amount);
            let vat = parseFloat(data.sales.vat || 0); // if VAT is not provided, use 0

            $(".total_amount").each(function(index) {
  let subtotall = parseFloat(data.sales.total_amount);
  $(this).text(subtotall.toFixed(2));  // Format total_amount to 2 decimal places
});
$(".date_created").each(function(index) {
  $(this).text(data.sales.date_created);  // Populate date created for each row
});

// Calculate total

// Update the fields
            $("#subtotal_amount").text("₱" + subtotal.toFixed(2));
            $("#vat_amount").text("₱" + vat.toFixed(2));  // Display VAT
            $("#total_amount").text("₱" + total.toFixed(2));  // Display total (subtotal + VAT)

            
            // Customer data
            $("#customer_name").text(data.customer.full_name);
            $("#customer_address").text(data.customer.address);

            // Items data
            let itemsHTML = "";
            $.each(data.items, function(index, item) {
                itemsHTML += `
                    <tr>
                        <td>${item.product}</td>
                        <td>${item.quantity}</td>
                        <td>₱${item.unit_price}</td>
                        <td>₱${item.sub_total}</td>
                    </tr>
                `;
            });
            $("#items_table_body").html(itemsHTML);
        }
    });
});


$(document).ready(function () {
  // Print button functionality
  $("#printButton").on("click", function () {
    const saleId = $("#receipt_id").text(); // Get the sale ID dynamically from the modal

    if (saleId) {
      // Open the receipt in a new tab
      window.open(`receipt.php?sale_id=${saleId}`, '_blank');
    } else {
      alert("No Sale ID available for printing the receipt.");
    }
  });
});

$(document).ready(function () {
  // Print button functionality
  $("#printButton").on("click", function () {
    const saleId = $("#receipt_id").text(); // Get the sale ID dynamically from the modal

    if (saleId) {
      // Open the receipt in a new tab
      const url = `receipt.php?sale_id=${saleId}`;
      window.open(url, '_blank');
    } else {
      alert("No Sale ID available for printing the receipt.");
    }
  });
});

    });

    // Disable automatic DataTables initialization globally
$.fn.dataTable.ext.errMode = 'none'; // Avoid errors
$.fn.dataTableSettings = []; // Clear all DataTables instances
$(document).ready(function () {
  // Initialize DataTables only for specific tables
  $('#specific_table_id').DataTable({
    paging: true,
    searching: true,
    info: true,
  });

  // Ensure your modal table is not initialized by DataTables
  if ($.fn.DataTable.isDataTable('#simple_items_table')) {
    $('#simple_items_table').DataTable().destroy();
  }
});
$('#editcategory').on('shown.bs.modal', function () {
  // Remove unwanted DataTables elements
  $('#simple_items_table_wrapper').find('.dataTables_length, .dataTables_filter, .dataTables_info, .dataTables_paginate').remove();

  // Ensure the table itself doesn't inherit DataTables classes or markup
  $('#simple_items_table').removeClass('dataTable');
});
$('#editcategory').on('shown.bs.modal', function () {
  // Remove all DataTables wrappers and restore the table
  $('#simple_items_table_wrapper').replaceWith($('#simple_items_table'));
});

$('#editcategory').on('shown.bs.modal', function () {
  // If DataTables is initialized on the table, destroy it
  if ($.fn.DataTable.isDataTable('#simple_payments_table')) {
    $('#simple_payments_table').DataTable().destroy();
  }

  // Remove any DataTables specific classes and wrapper elements
  $('#simple_payments_table_wrapper').find('.dataTables_length, .dataTables_filter, .dataTables_info, .dataTables_paginate').remove();

  // Ensure the table doesn't have any DataTables classes or extra markup
  $('#simple_payments_table').removeClass('dataTable');
});

  </script>

</body>

</html>
