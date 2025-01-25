let script = function(){

    this.orderItems = {};
    this.totalOrderAmount = 0.00;
    this.userChange = -1;
    this.tenderedAmt = 0;

    this.product = product

    this.showClock = function(){
        let dateObj = new Date;
        let months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    
    
        let year = dateObj.getFullYear();
        let monthNum = dateObj.getMonth();
        let dateCal = dateObj.getDate();
        let hour = dateObj.getHours();
        let min = dateObj.getMinutes();
    
        let timeFormatted = loadScript.toTwelveHourFormat(hour);
    
        document.getElementById('current-date').innerHTML =
        months[monthNum] + ' ' + dateCal + ' ' + year + ' ' + timeFormatted['time'] + ':' + min + '' + timeFormatted['am_pm'];
    
        setInterval(loadScript.showClock,60000);
    }
    
    this.toTwelveHourFormat = function(time){
        let am_pm = 'AM'
        if(time > 12 ){
             time = time -12;
             am_pm = 'PM'
        }
        return {
            time: time,
            am_pm: am_pm
        };
    }

    this.registerEvents = function(){
        document.addEventListener('click',function(e){
            let targetEl = e.target;
            let targetElClassList = targetEl.classList;
            let addtoOrderClasses = ['productImage', 'productName','productPrice','productVatStatus'];
            if(targetElClassList.contains('productImage') || 
               targetElClassList.contains('productName') || 
               targetElClassList.contains('productVatStatus') ||
               targetElClassList.contains('productPrice')){
            
            let productContainer = targetEl.closest('div.productContainer');
            let pid = productContainer.dataset.pid;
            let productInfo = loadScript.product[pid];

             let dialogForm = '\
                <h6 class="dialogProduct" style="font-size:18px; font-weight:bold;color: #3f3f3f;">' + productInfo['name'] + ' <span style="float:right">' + productInfo['price'] + '</span></h6>\
                <input type="number" id="orderQty" class="form-control" placeholder="Enter quantity..." min="1" value="1" />\
            ';

            BootstrapDialog.show({
                title: 'Add to Order',
                type: BootstrapDialog.TYPE_DEFAULT,
                message: dialogForm,
                closable: false, // Disable the close button (optional)
                buttons: [ // Define custom buttons
                    {
                        label: 'Cancel',
                        cssClass: 'btn-secondary',
                        action: function(dialogRef) {
                            dialogRef.close();
                        }
                    },
                    {
                        id: 'ok', // Assign an ID to the OK button
                        label: 'OK',
                        cssClass: 'btn-primary',
                        action: function(dialogRef) {
                            let orderQty = parseInt(document.getElementById('orderQty').value);
            
                            // Validate input for NaN, negative, and zero values
                            if (isNaN(orderQty) || orderQty <= 0) {
                                BootstrapDialog.alert({
                                    title: '<strong>Error</strong>',
                                    type: BootstrapDialog.TYPE_DANGER,
                                    message: 'Please enter a valid quantity greater than zero.',
                                    callback: function() {
                                        let inputBox = document.getElementById('orderQty');
                                        inputBox.focus();
                                        inputBox.select();
                                    }
                                });
                                return;
                            }
            
                            // Call the function to add the order
                            loadScript.addtoOrder(productInfo, pid, orderQty);
                            dialogRef.close();
                        }
                    }
                ],
                onshown: function(dialogRef) {
                    let inputBox = document.getElementById('orderQty');
                    inputBox.focus();
                    inputBox.select();
            
                    // Allow pressing Enter to trigger the OK button
                    inputBox.addEventListener('keydown', function(event) {
                        if (event.key === 'Enter') {
                            dialogRef.getButton('ok').click(); // Simulate OK button click
                        }
                    });
                }
            });
            
        
            }

            if (targetElClassList.contains('deleteOrderItem')) {
                let pid = targetEl.dataset.id;
                let productInfo = loadScript.orderItems[pid];

                BootstrapDialog.confirm({
                    title: '<strong>Delete Order Item',
                    type: BootstrapDialog.TYPE_DANGER,
                    message: 'Are you sure you want to delete <strong>' + productInfo['name'] +'</strong>?',
                    callback: function(toDelete){
                        if(toDelete){
                        delete loadScript.orderItems[pid];
                        loadScript.updateOrderItemTable();
                        }
                    }
                });

             
            }

            if(targetElClassList.contains('quantityUpdateBtn_minus')){
                let pid = targetEl.dataset.id;
                let productInfo = loadScript.orderItems[pid];

                loadScript.orderItems[pid]['orderQty']--;

                loadScript.orderItems[pid]['amount'] = loadScript.orderItems[pid]['orderQty'] * loadScript.orderItems[pid]['price'];
                
                if(loadScript.orderItems[pid]['orderQty'] === 0) delete loadScript.orderItems[pid];
                loadScript.updateOrderItemTable();
            }

            if(targetElClassList.contains('quantityUpdateBtn_plus')){
                let pid = targetEl.dataset.id;
                let productInfo = loadScript.orderItems[pid];

                loadScript.orderItems[pid]['orderQty']++;

                loadScript.orderItems[pid]['amount'] = loadScript.orderItems[pid]['orderQty'] * loadScript.orderItems[pid]['price'];
                loadScript.updateOrderItemTable();
            }
            if (targetElClassList.contains('checkOutBtn')) {
                if (Object.keys(loadScript.orderItems).length) {
                    let orderItemsHtml = '';
                    let counter = 1;
                    let totalAmt = 0.00;
                    const totalElement = document.querySelector('.item_total--total');
                    totalAmt = parseFloat(totalElement.textContent.replace('₱', '').replace(',', '').trim()) || 0.00;
                    for (const [pid, orderItem] of Object.entries(loadScript.orderItems)) {
                        orderItemsHtml += '\
                        <div class="row checkoutTblContentContainer">\
                            <div class="col-md-2 checkoutTblContent">' + counter + '</div>\
                            <div class="col-md-4 checkoutTblContent">' + orderItem['name'] + '</div>\
                            <div class="col-md-3 checkoutTblContent">' + loadScript.addCommas(orderItem['orderQty']) + '</div>\
                            <div class="col-md-3 checkoutTblContent">₱' + loadScript.addCommas(orderItem['amount'].toFixed(2)) + '</div>\
                        </div>';
                        counter++;
                    }
            
                    let content = '\
                            <div class="checkoutTotalAmountContainer" style="text-align:center;margin-top:10px">\
                                <span style="font-size: 36px;font-weight: bold;color: #f55;">₱' + loadScript.addCommas(totalAmt.toFixed(2)) + '</span><br>\
                                <span style="display: block;font-size: 17px;color: #444;">TOTAL AMOUNT</span><br>\
                                <hr/>\
                                <div class="summary-info" style="display:flex; justify-content: space-between; font-size: 18px; color: #444;">\
                                    <div>Total: ₱<span class="totalAmt">' + loadScript.addCommas(totalAmt.toFixed(2)) + '</span></div>\
                                    <div>Payments: ₱<span class="paymentsAmt">0.00</span></div>\
                                </div>\
                                <div class="summary-info" style="display:flex; justify-content: space-between; font-size: 18px; color: #444;">\
                                    <div>Balance: ₱<span class="balanceAmt">' + loadScript.addCommas(totalAmt.toFixed(2)) + '</span></div>\
                                    <div>Change: ₱<span class="changeAmt">0.00</span></div>\
                                </div>\
                            </div>\
                            <div style="text-align:center;">\
                                <table class="table table-bordered">\
                                    <thead>\
                                        <tr>\
                                            <th style="width: 50%;">Customer Details</th>\
                                            <th style="width: 40%;">Method</th>\
                                            <th style="width: 10%;">X</th>\
                                        </tr>\
                                    </thead>\
                                    <tbody id="payment-details-body">\
                                        <!-- Rows will be added here when "Cash" or "Cashless" is selected -->\
                                    </tbody>\
                                </table>\
                            </div>\
                            <div class="payment-methods" style="text-align:center; margin-top: 20px;">\
                                <input type="hidden" id="paymentMethod" value="cash">\
                                <button class="btn btn-success payment-btn" id="cash-btn">Cash</button>\
                                <button class="btn btn-info payment-btn" id="cashless-btn">Cashless</button>\
                            </div>\
                        </div>\
                    </div>';
         
                    // Total: ₱<span class="totalAmt">' + loadScript.addCommas(totalAmt.toFixed(2)) + '</span>
                    // Checkout confirmation dialog with onshown event to bind buttons
                    BootstrapDialog.show({
                        type: BootstrapDialog.TYPE_INFO,
                        title: '<center><B>CHECKOUT</B></center>',
                        cssClass: 'checkoutDialog',
                        message: content,
                        onshown: function(dialogRef) {
                            updateAmounts();
                            const tbody = document.getElementById('payment-details-body');
                            
                            // Initialize empty table
                            tbody.innerHTML = '';
                            
                            // Set initial payment method value if not set
                            const paymentMethodInput = document.getElementById('paymentMethod');
                            if (!paymentMethodInput.value) paymentMethodInput.value = ''; // Default to 'cash'
                            
                            // When "Cash" is clicked
                            document.getElementById('cash-btn').addEventListener('click', function() {
                                if (paymentMethodInput.value === 'cashless') {
                                    // If cashless was clicked first, add only Cash row
                                    const row = document.createElement('tr');
                                    row.classList.add('cash-row');
                                    row.innerHTML = `
                                        <td>Cash</td>
                                        <td>
                                            <div style="margin-top: 10px;">
                                                <label style="font-size: 16px;">Tendered:</label>
                                                <input class="form-control tendered-input" type="number" placeholder="0.00" style="text-align: right; width: 120px; display: inline-block; margin-left: 5px; border: 1px solid #ccc; padding: 5px; font-size: 14px;">
                                            </div>
                                        </td>
                                        <td style="text-align: center; vertical-align: middle;"><button class="btn btn-danger btn-sm">X</button></td>`;
                                    tbody.appendChild(row);
                                    row.querySelector('.tendered-input').addEventListener('input', updateAmounts);
                                    paymentMethodInput.value = 'cash';
                                } else {
                                    // Cash clicked first: show Name and Address fields
                                    tbody.innerHTML = `
                                        <tr class="cash-row">
                                            <td>
                                                Name: <input type="text" id="fName-cash" class="form-control" />
                                                Address: <input type="text" id="address-cash" class="form-control" />
                                            </td>
                                            <td>Cash
                                                <div style="margin-top: 10px;">
                                                    <label style="font-size: 16px;">Tendered:</label>
                                                    <input class="form-control tendered-input" type="number" placeholder="0.00" style="text-align: right; width: 120px; display: inline-block; margin-left: 5px; border: 1px solid #ccc; padding: 5px; font-size: 14px;">
                                                </div>
                                            </td>
                                            <td style="text-align: center; vertical-align: middle;"><button class="btn btn-danger btn-sm">X</button></td>
                                        </tr>`;
                                    paymentMethodInput.value = 'cash';
                                    tbody.querySelector('.tendered-input').addEventListener('input', updateAmounts);
                                }
                            });
                    
                            // When "Cashless" is clicked
                            document.getElementById('cashless-btn').addEventListener('click', function() {
                                if (paymentMethodInput.value === 'cash') {
                                    // If cash was clicked first, add Gcash and Reference fields
                                    const row = document.createElement('tr');
                                    row.classList.add('cashlessmix-row');
                                    row.innerHTML = `
                                        <td>
                                            Reference number: <input type="number" id="referencenumber-mix" class="form-control" />
                                        </td>
                                        <td>Cashless
                                            <div style="margin-top: 10px;">
                                                <label style="font-size: 16px;">Tendered:</label>
                                                <input class="form-control tendered-input" type="number" placeholder="0.00" style="text-align: right; width: 120px; display: inline-block; margin-left: 5px; border: 1px solid #ccc; padding: 5px; font-size: 14px;">
                                            </div>
                                        </td>
                                        <td style="text-align: center; vertical-align: middle;"><button class="btn btn-danger btn-sm">X</button></td>`;
                                    tbody.appendChild(row);
                                    row.querySelector('.tendered-input').addEventListener('input', updateAmounts);
                                } else {
                                    // Cashless clicked first: show all fields (Name, Address, Gcash number, Reference number)
                                    tbody.innerHTML = `
                                        <tr class="cashless-row">
                                            <td>
                                                Name: <input type="text" id="fName-cashless" class="form-control" />
                                                Address: <input type="text" id="address-cashless" class="form-control" />
                                                Reference number: <input type="number" id="referencenumber-cashless" class="form-control" />
                                            </td>
                                            <td>Cashless
                                                <div style="margin-top: 10px;">
                                                    <label style="font-size: 16px;">Tendered:</label>
                                                    <input class="form-control tendered-input" type="number" placeholder="0.00" style="text-align: right; width: 120px; display: inline-block; margin-left: 5px; border: 1px solid #ccc; padding: 5px; font-size: 14px;">
                                                </div>
                                            </td>
                                            <td style="text-align: center; vertical-align: middle;"><button class="btn btn-danger btn-sm">X</button></td>
                                        </tr>`;
                                }
                                paymentMethodInput.value = 'cashless';
                                tbody.querySelector('.tendered-input').addEventListener('input', updateAmounts);
                            });
                        },                                    
                        buttons: [
                            {
                                label: 'Payment',
                                cssClass: 'btn-primary',
                                action: function(dialogRef) {
                                    const tbody = document.getElementById('payment-details-body'); // The table body where payment methods are added
                                if (tbody && tbody.rows.length === 0) {
                                    // Show error if no payment method is selected
                                    BootstrapDialog.alert({
                                        type: BootstrapDialog.TYPE_DANGER,
                                        title: 'Payment Method Required',
                                        message: 'Please choose a payment method first.',
                                    });
                                    return; // Stop further execution if no payment method is selected
                                }
                                const vatText = document.querySelector('.item_total--vat').innerText;
                                const totalvatValue = parseFloat(
                                    vatText.replace(/[₱(),]/g, '').trim()
                                ) || 0;
                                const discountText = document.querySelector('.item_total--discount').innerText;
                                const discountType = document.querySelector('.toggleable-radio:checked')?.value || null;
                                const totalDiscountValue = parseFloat(
                                    discountText.replace(/[₱(),]/g, '').trim()
                                ) || 0;
                                    const tenderedInputs = document.querySelectorAll('.tendered-input');
                                    let tenderedAmt = 0.00;
                                // Combine methods into one string (e.g., 'cash/cashless')
                                    tenderedInputs.forEach(input => {
                                        tenderedAmt += parseFloat(input.value) || 0; // Sum up the tendered amounts
                                    });
                                    const totalAmtSpan = document.querySelector('.item_total--total');
                                    const change = document.querySelector('.changeAmt');
                                    const subtotalAmt = parseFloat(document.querySelector('.item_total--value').innerText.replace('₱', '').replace(',', ''));
                                    let paymentMethod = '';

                                    if (document.querySelector('.cash-row') && document.querySelector('.cashlessmix-row')) {
                                        paymentMethod = 'cash and cashless'; // Set to "cash and cashless" if both are present
                                    } else if (document.querySelector('.cash-row')) {
                                        paymentMethod = 'cash'; // Set to "cash" if only cash-row is present
                                    } else if (document.querySelector('.cashless-row')) {
                                        paymentMethod = 'cashless'; // Set to "cashless" if only cashless-row is present
                                    }

                                    // Log the selected payment method for debugging
                                    console.log("Selected payment method:", paymentMethod);

                                    const fullName = 
                                    paymentMethod === 'cash and cashless' || paymentMethod === 'cash'
                                        ? document.getElementById('fName-cash').value
                                        : paymentMethod === 'cashless'
                                        ? document.getElementById('fName-cashless').value
                                        : null;

                                    const address = 
                                        paymentMethod === 'cash and cashless' || paymentMethod === 'cash'
                                            ? document.getElementById('address-cash').value
                                            : paymentMethod === 'cashless'
                                            ? document.getElementById('address-cashless').value
                                            : null;

                                    const referenceNumber = 
                                        paymentMethod === 'cashless'
                                            ? document.getElementById('referencenumber-cashless').value
                                            : paymentMethod === 'cash and cashless'
                                            ? document.getElementById('referencenumber-mix').value
                                            : null;

                                
                                    if (paymentMethod === 'cashless' && !referenceNumber) {
                                        BootstrapDialog.alert({
                                            type: BootstrapDialog.TYPE_DANGER,
                                            title: 'Missing Reference Number',
                                            message: 'Please enter a reference number for Cashless payment.',
                                        });
                                        return;
                                    }

                                    if (paymentMethod === 'cashless' || paymentMethod === 'cash and cashless') {
                                        if (!referenceNumber) {
                                            BootstrapDialog.alert({
                                                type: BootstrapDialog.TYPE_DANGER,
                                                title: 'Missing Reference Number',
                                                message: 'Please enter a reference number for Cashless payment.',
                                            });
                                            return; // Stop further execution if reference number is missing
                                        }
                        
                                    }
                        
            
                                     // Here is the new validation for tenderedAmt being blank or less than totalAmt
                                    if (!tenderedAmt || tenderedAmt < parseFloat(totalAmtSpan.innerText.replace('₱', '').replace(',', ''))) {
                                        BootstrapDialog.alert({
                                            type: BootstrapDialog.TYPE_DANGER,
                                            title: 'Invalid Amount',
                                            message: 'Please enter a valid amount that is greater than or equal to the total amount.',
                                        });
                                        return;
                                    }
            
                                    // Proceed with the AJAX request
                                    $.post('action.php?action=checkout', {
                                        data: loadScript.orderItems,
                                        subtotalAmt: subtotalAmt,
                                        discountValue: totalDiscountValue,
                                        discountType: discountType,
                                        vatValue:totalvatValue,
                                        totalAmt: parseFloat(totalAmtSpan.innerText.replace('₱', '').replace(',', '')), // Extract the numeric value
                                        change: parseFloat(change.innerText.replace('₱', '').replace(',', '')),          // Send the calculated change
                                        tenderedAmt: tenderedAmt, // Send the tendered amount
                                        paymentMethods: paymentMethod,
                                        referenceNumber,
                                        customer: {
                                            fullName,
                                            address,
                                        }
                                    }, function (response) {
                                        console.log("Response received:", response); // Add this to inspect the response
                                        let type = response.success ? BootstrapDialog.TYPE_SUCCESS : BootstrapDialog.TYPE_DANGER;
                                        BootstrapDialog.alert({
                                            title: response.success ? 'Success' : 'Error',
                                            message: response.message,
                                            callback: function (isOk) {
                                                if (response.success) {
                                                    loadScript.resetData(response);
                                                    window.open('receipt.php?sale_id='+ response.id, '_blank')
                                                }
                                            }
                                        });
                                    }, 'json');
                                    dialogRef.close();
                                }
                            }
                        ]
                    });
                }
            }
            
            
            
            
            
            });

            $("body").on("click", function (e) {
                var targetEl = e.target; // Get the clicked element
            
                // Check if the clicked element has the 'voidBtn' class
                if (targetEl.classList.contains('voidBtn')) {
                    e.preventDefault(); // Prevent default action of the link
            
                    // Check if the table is empty
                    var tableBody = document.querySelector("#pos_items_tbl tbody");
                    if (!tableBody || tableBody.children.length === 0) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Table is Empty',
                            text: 'Please add a product first before voiding!',
                            confirmButtonColor: '#3085d6'
                        });
                        return; // Exit the function early if the table is empty
                    }
            
                    // Show a confirmation dialog using SweetAlert
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this action!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, void it!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        // If the user confirms, reset the data
                        if (result.isConfirmed) {
                            // Call the resetData function (remove product data here)
                            loadScript.resetData();
            
                            // Optionally provide feedback (success message, etc.)
                            Swal.fire({
                                icon: 'info',
                                title: 'Reset Successful',
                                text: 'The data has been reset successfully!'
                            });
                        } else {
                            // Optionally show a message if the user cancels
                            Swal.fire({
                                icon: 'error',
                                title: 'Cancelled',
                                text: 'The void action was cancelled.'
                            });
                        }
                    });
                }
            });
            
            
            function updateAmounts() {
                console.log("Updating amounts...");
            
                const totalAmount = parseFloat(document.querySelector('.item_total--total').textContent.replace('₱', '').replace(',', ''));
                console.log("Total Amount: ", totalAmount);
            
                let totalTendered = 0;
                document.querySelectorAll('.tendered-input').forEach(input => {
                    console.log("Tendered input value: ", input.value);
                    totalTendered += parseFloat(input.value) || 0;
                });
            
                // Update the Payments display without adding an additional ₱ symbol
                console.log("Total Tendered: ", totalTendered);
                document.querySelector('.paymentsAmt').textContent =  loadScript.addCommas(totalTendered.toFixed(2));
            
                if (totalTendered < totalAmount) {
                    document.querySelector('.balanceAmt').textContent = loadScript.addCommas((totalAmount - totalTendered).toFixed(2));
                    document.querySelector('.changeAmt').textContent = '0.00';
                } else {
                    document.querySelector('.balanceAmt').textContent = '0.00';
                    document.querySelector('.changeAmt').textContent = loadScript.addCommas((totalTendered - totalAmount).toFixed(2));
                }
            }

    // When the tendered input changes
    document.querySelectorAll('.tendered-input').forEach(input => {
        input.oninput = updateAmounts; // Update amounts live when input changes
    });

document.addEventListener('DOMContentLoaded', function() {
    // Function to attach event listener to each "Tendered" input field
    function attachTenderedInputEvent() {
        document.querySelectorAll('.tendered-input').forEach(input => {
            input.oninput = updateAmounts; // Update amounts live when input changes
        });
    }

    // Function to remove row
    function removeRow(button) {
        console.log("Removing row..."); // For debugging
        const row = button.closest('tr');
        row.remove(); // Remove the row

        // Re-attach the event listeners to all tendered-input fields
        attachTenderedInputEvent();

        // Update amounts after the row is removed
        updateAmounts();
    }

    // Attach event listener to the document to handle dynamically added rows
    document.body.addEventListener('click', function(event) {
        if (event.target && event.target.classList.contains('btn-danger')) {
            // Call removeRow when the delete button is clicked
            removeRow(event.target);
        }
    });

    // Call attachTenderedInputEvent initially to set up all inputs
    attachTenderedInputEvent();
})



        }


  // Function to reset the data
this.resetData = function() {
    // Reset order data
    loadScript.orderItems = {};  // Clear order items
    loadScript.totalOrderAmount = 0.00;
    loadScript.userChange = -1;
    loadScript.tenderedAmt = 0;

    // Update the order item table to reflect the empty order
    loadScript.updateOrderItemTable();

    // Reset discount input field to 0
    const discountInput = document.querySelector('.item_total--percentdiscount');
    if (discountInput) {
        discountInput.value = 0;
    }

    // Optionally re-trigger the total update after resetting the discount
    updateTotal(0);

    console.log("Data reset and product click listeners reinitialized"); // Debugging purpose
};


this.updateOrderItemTable = function () {
    loadScript.totalOrderAmount = 0.00;
    loadScript.totalVat = 0.00; // Initialize VAT total

    let ordersContainer = document.querySelector('.pos_items');
    let html = '<p style="text-align: center;font-size:20px;padding:55px;text-transform:uppercase" class="itemNoData">No data</p>';
    
    if (Object.keys(loadScript.orderItems).length > 0) {
        let tableHtml = ` 
            <table class="table datatable" id="pos_items_tbl">
            <thead>
                <tr>
                <th>#</th>
                <th>PRODUCT</th>
                <th>QTY</th>
                <th>PRICE</th>
                <th>AMOUNT</th>
                <th>X</th>
                </tr>
            </thead>
            <tbody>__ROWS__</tbody>
            </table>`;

        let rows = '';
        let rowNum = 1;

        // Loop through orderItems from session
        for (const [pid, orderItem] of Object.entries(loadScript.orderItems)) {
            let vatAmount = 0;

            // Check if item is vatable
            console.log(`Is Vatable for ${orderItem['name']}: ${orderItem['isVatable']}`);
            
            // Convert isVatable to a number before comparison
            if (Number(orderItem['isVatable']) === 1) {
                vatAmount = orderItem['amount'] * 0.05; // 5% VAT
                console.log(`VAT for ${orderItem['name']}: ₱${vatAmount}`);
            }

            console.log(orderItem); // Log the entire orderItem to check if 'isVatable' exists

            rows += ` 
                <tr>
                    <td>${rowNum}</td>
                    <td>${orderItem['name'].length > 10 ? orderItem['name'].substring(0, 10) + '...' : orderItem['name']}</td>
                    <td>${loadScript.addCommas(orderItem['orderQty'])}
                        <a href="javascript:void(0);" data-id="${pid}" class="quantityUpdateBtn quantityUpdateBtn_minus">
                            <i class="fa fa-minus quantityUpdateBtn quantityUpdateBtn_minus" data-id="${pid}" style="color:red;font-size:10px;text-decoration:none;"></i>
                        </a>
                        <a href="javascript:void(0);" data-id="${pid}" class="quantityUpdateBtn quantityUpdateBtn_plus">
                            <i class="fa fa-plus quantityUpdateBtn quantityUpdateBtn_plus" data-id="${pid}" style="color:green;font-size:10px;text-decoration:none;"></i>
                        </a>
                    </td>
                    <td>₱${loadScript.addCommas(orderItem['price'])}</td>
                    <td>₱${loadScript.addCommas(orderItem['amount'].toFixed(2))}</td>
                    <td>
                        <a href="javascript:void(0)" class="deleteOrderItem" data-id="${pid}">
                            <i class="fa fa-trash deleteOrderItem" data-id="${pid}"></i>
                        </a>
                    </td>
                </tr>`;

            rowNum++;
            loadScript.totalOrderAmount += orderItem['amount'];
            loadScript.totalVat += vatAmount; // Add to total VAT
        }

        html = tableHtml.replace('__ROWS__', rows);
    }

    ordersContainer.innerHTML = html;

    // Log total VAT for debugging
    console.log(`Total VAT: ₱${loadScript.totalVat}`);

    // Update the VAT display in the summary
    const vatDisplayElement = document.querySelector('.item_total--vat');
    if (vatDisplayElement) {
        vatDisplayElement.textContent = `₱${loadScript.totalVat.toFixed(2)}`;
    } else {
        console.log('VAT display element not found');
    }

    loadScript.updateTotalOrderAmount();

    // Listen for beforeunload to show the alert and allow the browser's native confirmation dialog
    window.addEventListener('beforeunload', function (e) {
        if (Object.keys(loadScript.orderItems).length > 0) {
            // Show a simple alert with the message
            alert("You have unsaved items in your cart! Are you sure you want to leave without saving?");

            // Standard for most browsers to prevent unloading
            e.preventDefault();
            e.returnValue = ''; // Required for most browsers to show the confirmation dialog
        }
    });
};

            function initializeProductClickListeners() {
                document.querySelectorAll('.product-item').forEach(item => {
                    item.addEventListener('click', function() {
                        let productId = item.getAttribute('data-id'); // Assuming each product has a data-id
                        // Your logic to add the product to the order
                        console.log("Product clicked: ", productId);
                        // For example:
                    });
                });
            }
        
// Function to update the total order amount based on loadScript data
this.updateTotalOrderAmount = function () {
    // Update subtotal from loadScript data
    const subtotal = loadScript.totalOrderAmount; // Total order amount before discounts
    console.log("Subtotal:", subtotal); // Debug log for subtotal
    document.querySelector('.item_total--value').innerHTML = '₱' + loadScript.addCommas(subtotal.toFixed(2));

    // Get discount percentage
    const discountInput = document.querySelector('.item_total--percentdiscount'); // Adjusted to match your input element
    const discountPercent = parseFloat(discountInput.value) || 0;
    console.log("Discount Percent:", discountPercent); // Debug log for discount percent

    // Calculate discount value
    const discountValue = (subtotal * discountPercent) / 100;
    console.log("Discount Value:", discountValue); // Debug log for discount value

    // Handle discount exceeding subtotal
    if (discountValue > subtotal) {
        alert("Discount cannot be greater than the subtotal!");
        discountInput.value = 0; // Reset discount input
        document.querySelector('.item_total--discount').textContent = `(₱0.00)`; // Reset discount display
        document.querySelector('.item_total--total').textContent = loadScript.addCommas(`₱${subtotal.toFixed(2)}`); // Reset total to subtotal
        return; // Exit function to avoid further updates
    }

    // Calculate the final total (after applying discount)
    const discountedSubtotal = subtotal - discountValue; // Subtotal after applying discount
    
    // Get the current VAT amount from the DOM (it should be in the .item_total--vat element)
    const vatElement = document.querySelector('.item_total--vat'); // Select the VAT element
    const currentVat = parseFloat(vatElement.textContent.replace('₱', '').trim()) || 0;
    console.log("Current VAT:", currentVat); // Debug log for current VAT

    // Calculate the final total by adding VAT to the discounted subtotal
    const finalTotal = discountedSubtotal + currentVat; // Add VAT to the discounted subtotal

    // Update the displays
    document.querySelector('.item_total--discount').textContent = loadScript.addCommas(`(₱${discountValue.toFixed(2)})`); // Display the discount value
    document.querySelector('.item_total--total').textContent = loadScript.addCommas(`₱${finalTotal.toFixed(2)}`); // Display the final total
};



            //format number
            this.formatNum = function(num){
                if(isNaN(num) || num === undefined) num = 0.00;
                return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }

            this.addCommas = function(nStr){
                nStr += '';
                var x = nStr.split('.');
                var x1 = x[0];
                var x2 = x.length > 1 ? '.' + x[1] : '';
                var rgx = /(\d+)(\d{3})/;
                while (rgx.test(x1)) {
                    x1 = x1.replace(rgx, '$1' + ',' + '$2');
                }
                return x1 + x2

            }


            this.addtoOrder = function(productInfo, pid, orderQty) {
                let curItemIds = Object.keys(loadScript.orderItems);
                let totalAmount = productInfo['price'] * orderQty;  // Calculate total amount without VAT
            
                // Check if the product is vatable
                let vatAmount = 0;
                if (productInfo['vat'] === 1) {  // Assuming productInfo['vat'] is 1 for vatable products
                    vatAmount = totalAmount * 0.05;  // 5% VAT calculation
                }
            
                // If the product is already in the order, update it
                if (curItemIds.indexOf(pid) > -1) {
                    loadScript.orderItems[pid]['amount'] += totalAmount + vatAmount;  // Add VAT to total amount
                    loadScript.orderItems[pid]['orderQty'] += orderQty;
                    loadScript.orderItems[pid]['vatAmount'] += vatAmount;  // Update VAT amount for the product
                }
                else {
                    // If it's a new product, add it to the order
                    loadScript.orderItems[pid] = {
                        name: productInfo['name'],
                        price: productInfo['price'],
                        orderQty: orderQty,
                        amount: totalAmount + vatAmount,  // Total amount including VAT
                        vatAmount: vatAmount,  // Store VAT amount
                        isVatable: productInfo['vat']  // Store VAT status (for future reference)
                    };
                }
            
                // Optionally, you can also update your total order amount and VAT total
                loadScript.totalOrderAmount += totalAmount;
                loadScript.totalVat += vatAmount;
            
                // Call to update the order table if necessary
                loadScript.updateOrderItemTable();
            };
            

        this.initialize = function(){
            // show clock
            this.showClock();
            //register all app events
            this.registerEvents()
        }

    };
    let loadScript = new script;
    loadScript.initialize();
    