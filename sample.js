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
            let addtoOrderClasses = ['productImage', 'productName','productPrice'];
            if(targetElClassList.contains('productImage') || 
               targetElClassList.contains('productName') || 
               targetElClassList.contains('productPrice')){
            
            let productContainer = targetEl.closest('div.productContainer');
            let pid = productContainer.dataset.pid;
            let productInfo = loadScript.product[pid];

            let dialogForm = '\
 		 <h6 class="dialogProduct" style="font-size:18px; font-weight:bold;color: #3f3f3f; ">'+productInfo['name'] +' <span style="float:right"> '+ productInfo['price'] +'</span></h6>\
         <input type="number" id="orderQty" class="form-control" placeholder="Enter quantity..." min="1"/>\
         ';
            BootstrapDialog.confirm({
                title: 'Add to Order',
                type: BootstrapDialog.TYPE_DEFAULT,
                message: dialogForm,
                callback: function(addOrder){
                    if(addOrder){
                        let orderQty = parseInt(document.getElementById('orderQty').value);
                        if(isNaN(orderQty)){
                            BootstrapDialog.alert({
                                title: '<strong>Error</strong>',
                                type: BootstrapDialog.TYPE_DANGER,
                                message: 'Please type order quantity.'
                            });
                            return a;
                        }
                        

                        loadScript.addtoOrder(productInfo,pid,orderQty);
                    }
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
                    for (const [pid, orderItem] of Object.entries(loadScript.orderItems)) {
                        orderItemsHtml += '\
                        <div class="row checkoutTblContentContainer">\
                            <div class="col-md-2 checkoutTblContent">' + counter + '</div>\
                            <div class="col-md-4 checkoutTblContent">' + orderItem['name'] + '</div>\
                            <div class="col-md-3 checkoutTblContent">' + loadScript.addCommas(orderItem['orderQty']) + '</div>\
                            <div class="col-md-3 checkoutTblContent">₱' + loadScript.addCommas(orderItem['amount'].toFixed(2)) + '</div>\
                        </div>';
                        totalAmt += orderItem['amount'];
                        counter++;
                    }
            
                    let content = '\
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
                                <span style="font-size: 36px;font-weight: bold;color: #f55;">₱' + loadScript.addCommas(totalAmt.toFixed(2)) + '</span><br>\
                                <span style="display: block;font-size: 17px;color: #444;">TOTAL AMOUNT</span><br>\
                                <hr/>\
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
            
                    // Checkout confirmation dialog with onshown event to bind buttons
                    BootstrapDialog.show({
                        type: BootstrapDialog.TYPE_INFO,
                        title: '<center><B>CHECKOUT</B></center>',
                        cssClass: 'checkoutDialog',
                        message: content,
                        onshown: function(dialogRef) {
                            // Event listener for payment button clicks after dialog is shown
                            dialogRef.getModalBody().find('.payment-btn').on('click', function(event) {
                                const target = event.target;
                                const tbody = document.getElementById('payment-details-body');
                                tbody.innerHTML = ''; // Clear existing rows
            
                                // --- CHANGED CODE: Hide the clicked button ---
                                target.style.display = 'none';  // This line hides the clicked button
                                // -------------------------------
            
                                if (target.id === 'cash-btn') {
                                    // Show customer details row for cash payment
                                    tbody.innerHTML = '\
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
                                        </td>\
                                        <td style="text-align: center; vertical-align: middle;"><button class="btn btn-danger btn-sm" onclick="removeRow(this)">X</button></td>\
                                    </tr>';
                                    document.getElementById('paymentMethod').value = 'cash';
                                } else if (target.id === 'cashless-btn') {
                                    // Show customer details row for cashless payment
                                    tbody.innerHTML = '\
                                    <tr>\
                                        <td>\
                                            Name: <input type="text" id="fName_cashless" class="form-control" />\
                                            Address: <input type="text" id="address_cashless" class="form-control" />\
                                            Gcash number: <input type="number" id="contact" class="form-control" />\
                                            Reference number: <input type="number" id="referencenumber" class="form-control" />\
                                        </td>\
                                        <td>Cashless\
                                            <div style="margin-top: 10px;">\
        <label for="userAmt" style="font-size: 16px;">Tendered:</label>\
        <input class="form-control" id="userAmt" type="number" placeholder="0.00" style="text-align: right; width: 60px; display: inline-block; margin-left: 5px; border: 1px solid #ccc; padding: 5px; font-size: 14px;">\
    </div>\
    <div style="margin-top: 10px;">\
        <label for="changeAmt" style="font-size: 16px;">Change:</label>\
        <input id="changeAmt" class="changeAmt" type="text" value="0.00" readonly\
            style="text-align: right; width: 60px; display: inline-block; margin-left: 20px; border: 1px solid #ccc; padding: 5px; font-size: 14px; background-color: #f9f9f9;">\
    </div>\</td>\
                                        <td style="text-align: center; vertical-align: middle;"><button class="btn btn-danger btn-sm" onclick="removeRow(this)">X</button></td>\
                                    </tr>';
                                    document.getElementById('paymentMethod').value = 'cashless';
                                }
                            });
                        },
                        buttons: [
                            {
                                label: 'CheckOut',
                                cssClass: 'btn-primary',
                                action: function(dialogRef) {
                                    const paymentMethod = document.getElementById('paymentMethod').value;
                                    const tenderedAmt = parseFloat(document.getElementById('userAmt').value) || 0;
                                    const fullName = paymentMethod === 'cash'
                                        ? document.getElementById('fName').value
                                        : document.getElementById('fName_cashless').value;
                                    const address = paymentMethod === 'cash'
                                        ? document.getElementById('address').value
                                        : document.getElementById('address_cashless').value;
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
                                    dialogRef.close();
                                }
                            }
                        ]
                    });
                }
            }
            
            });


            document.addEventListener('click', function(e) {
                let target = e.target;
            
                if (target.id === 'cash-btn') {
                    // Show customer details when cash is selected
                    document.getElementById('customer-details-cash').style.display = 'block';
                    document.getElementById('customer-details-cashless').style.display = 'none';
                    document.getElementById('paymentMethod').value = 'cash';
                } else if (target.id === 'cashless-btn') {
                    // Hide customer details when cashless is selected
                    document.getElementById('customer-details-cashless').style.display = 'block';
                    document.getElementById('customer-details-cash').style.display = 'none';
                    document.getElementById('paymentMethod').value = 'cashless';
                }
            });
            document.addEventListener('keyup', function(e){
                let targetEl = e.target;
                let targetElClassList = targetEl.classList;

                if(targetEl.id === 'userAmt'){
                    let userAmt = targetEl.value == '' ? 0: parseFloat(targetEl.value);
                    loadScript.tenderedAmt = userAmt;
                    let change = userAmt - loadScript.totalOrderAmount;
                    loadScript.userChange = change;
                    let displayedChange = change < 0 ? 0 : change;

                    let changeEl = document.getElementById('changeAmt');
                    changeEl.value = loadScript.addCommas(displayedChange.toFixed(2));
            
                    // Optional: Add or remove the 'text-danger' class based on the change value
                    if (change < 0) {
                        changeEl.classList.add('text-danger');
                    } else {
                        changeEl.classList.remove('text-danger');
                    }
            
                    console.log("Change:", change);
                }
            });
        }


        this.resetData = function(response) {
            let productJson = response.product;
            loadScript.product = {};
        
            productJson.forEach(($row) => {
                loadScript.product[$row.productID] = {
                    name: $row.brandID,
                    price: $row.unitPrice
                };
            });
        
            // Reset order data
            loadScript.orderItems = {};
            loadScript.totalOrderAmount = 0.00;
            loadScript.userChange = -1;
            loadScript.tenderedAmt = 0;
        
            // Update the order item table
            loadScript.updateOrderItemTable();
        
            // Reinitialize product click listeners to make products clickable again
            initializeProductClickListeners();
        
            console.log("Data reset and product click listeners reinitialized"); // Debugging purpose
        };
        

            

            this.updateOrderItemTable = function(){

                loadScript.totalOrderAmount = 0.00;
                
                let ordersContainer = document.querySelector('.pos_items');
                let html = '<p style="text-align: center;font-size:20px;padding:55px;text-transform:uppercase" class="itemNoData">No data</p>';
                if(Object.keys(loadScript.orderItems)){
                    let tableHtml =`
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
                    for(const [pid, orderItem] of Object.entries(loadScript.orderItems)){
                        rows +=  `
                            <tr>
                                <td>${rowNum}</td>
                            <td>${orderItem['name'].length > 10 ? orderItem['name'].substring(0, 10) + '...' : orderItem['name']}</td>
                                <td>${loadScript.addCommas(orderItem['orderQty']) }
                                    <a href="javascript:void(0);" data-id="${pid}" class="quantityUpdateBtn quantityUpdateBtn_minus">
                                        <i class="fa fa-minus quantityUpdateBtn quantityUpdateBtn_minus" data-id="${pid}" style="color:red;font-size:10px;text-decoration:none;"></i>
                                    </a>
                                    <a href="javascript:void(0);"  data-id="${pid}" class="quantityUpdateBtn quantityUpdateBtn_plus">
                                        <i class="fa fa-plus quantityUpdateBtn quantityUpdateBtn_plus" data-id="${pid}"  style="color:green;font-size:10px;text-decoration:none;"></i>
                                    </a>
                                    
                                </td>
                                                            <td>₱${loadScript.addCommas(orderItem['price']) }</td>

                                <td>₱ ${loadScript.addCommas(orderItem['amount'].toFixed(2))}</td>
                                <td>
                                    <a href="javascript:void(0)" class="deleteOrderItem" data-id="${pid}">
                                    <i class="fa fa-trash deleteOrderItem" data-id="${pid}"></i>
                                    </a>
                                </td>
                            </tr>
                    `;
                        rowNum++;

                        loadScript.totalOrderAmount += orderItem['amount'];
                    }
                    html = tableHtml.replace('__ROWS__', rows);
                }
                
                    ordersContainer.innerHTML = html; 
                    loadScript.updateTotalOrderAmount();
                    
                
            }

            function initializeProductClickListeners() {
                document.querySelectorAll('.product-item').forEach(item => {
                    item.addEventListener('click', function() {
                        let productId = item.getAttribute('data-id'); // Assuming each product has a data-id
                        // Your logic to add the product to the order
                        console.log("Product clicked: ", productId);
                        // For example:
                        loadScript.addToOrder(productId);
                    });
                });
            }
            

            this.updateTotalOrderAmount = function(){
                // update total amount
                document.querySelector('.item_total--value').innerHTML = '₱' + loadScript.addCommas(loadScript.totalOrderAmount.toFixed(2));
            }

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


            this.addtoOrder = function(productInfo,pid,orderQty){
                let curItemIds = Object.keys(loadScript.orderItems);
                let totalAmount = productInfo['price'] * orderQty

                if(curItemIds.indexOf(pid) > -1){
                    loadScript.orderItems[pid]['amount'] += totalAmount;
                    loadScript.orderItems[pid]['orderQty'] += orderQty;

                }
                else{
                
                    loadScript.orderItems[pid] = {
                        name: productInfo['name'],
                        price: productInfo['price'],
                        orderQty: orderQty,
                        amount: totalAmount

                    };
                
                }
                

                this.updateOrderItemTable();
                


                
            }

        this.initialize = function(){
            // show clock
            this.showClock();
            //register all app events
            this.registerEvents()
        }

    };
    let loadScript = new script;
    loadScript.initialize();





2nd case





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
            let addtoOrderClasses = ['productImage', 'productName','productPrice'];
            if(targetElClassList.contains('productImage') || 
               targetElClassList.contains('productName') || 
               targetElClassList.contains('productPrice')){
            
            let productContainer = targetEl.closest('div.productContainer');
            let pid = productContainer.dataset.pid;
            let productInfo = loadScript.product[pid];

            let dialogForm = '\
 		 <h6 class="dialogProduct" style="font-size:18px; font-weight:bold;color: #3f3f3f; ">'+productInfo['name'] +' <span style="float:right"> '+ productInfo['price'] +'</span></h6>\
         <input type="number" id="orderQty" class="form-control" placeholder="Enter quantity..." min="1"/>\
         ';
            BootstrapDialog.confirm({
                title: 'Add to Order',
                type: BootstrapDialog.TYPE_DEFAULT,
                message: dialogForm,
                callback: function(addOrder){
                    if(addOrder){
                        let orderQty = parseInt(document.getElementById('orderQty').value);
                        if(isNaN(orderQty)){
                            BootstrapDialog.alert({
                                title: '<strong>Error</strong>',
                                type: BootstrapDialog.TYPE_DANGER,
                                message: 'Please type order quantity.'
                            });
                            return a;
                        }
                        

                        loadScript.addtoOrder(productInfo,pid,orderQty);
                    }
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
                    for (const [pid, orderItem] of Object.entries(loadScript.orderItems)) {
                        orderItemsHtml += '\
                        <div class="row checkoutTblContentContainer">\
                            <div class="col-md-2 checkoutTblContent">' + counter + '</div>\
                            <div class="col-md-4 checkoutTblContent">' + orderItem['name'] + '</div>\
                            <div class="col-md-3 checkoutTblContent">' + loadScript.addCommas(orderItem['orderQty']) + '</div>\
                            <div class="col-md-3 checkoutTblContent">₱' + loadScript.addCommas(orderItem['amount'].toFixed(2)) + '</div>\
                        </div>';
                        totalAmt += orderItem['amount'];
                        counter++;
                    }
            
                    let content = '\
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
                                <span style="font-size: 36px;font-weight: bold;color: #f55;">₱' + loadScript.addCommas(totalAmt.toFixed(2)) + '</span><br>\
                                <span style="display: block;font-size: 17px;color: #444;">TOTAL AMOUNT</span><br>\
                                <hr/>\
                            </div>\
                                                        <div class="checkoutUserChangeContainer" style="margin-top:32px;text-align:right;">\
                            <p class="checkoutUserChange" style="color:#06ab17;font-size:22px"><small>Change: </small><span class="changeAmt" style="color:b0b0b0;">₱ 0.00</span></p>\
                            </div>\
                            <div style="text-align:center;">\
                                <table class="table table-bordered">\
                                    <thead>\
                                        <tr>\
                                            <th style="width: 50%;">Method</th>\
                                            <th style="width: 40%;">Amount</th>\
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
            
                    // Checkout confirmation dialog with onshown event to bind buttons
                    BootstrapDialog.show({
                        type: BootstrapDialog.TYPE_INFO,
                        title: '<center><B>CHECKOUT</B></center>',
                        cssClass: 'checkoutDialog',
                        message: content,
                        onshown: function(dialogRef) {
                            // Event listener for payment button clicks after dialog is shown
                            dialogRef.getModalBody().find('.payment-btn').on('click', function(event) {
                                const target = event.target;
                                const tbody = document.getElementById('payment-details-body');
            
                                // Append a new row based on the selected payment method
                                if (target.id === 'cash-btn') {
                                    // Add row for cash payment
                                    tbody.innerHTML += '\
                                    <tr>\
                                        <td>Cash</td>\
                                        <td>\
                                            <div style="margin-top: 10px;">\
                                                <label for="userAmt" style="font-size: 16px;">Tendered:</label>\
                                                <input class="form-control userAmt" type="number" placeholder="0.00" style="text-align: right; width: 60px; display: inline-block; margin-left: 5px; border: 1px solid #ccc; padding: 5px; font-size: 14px;">\
                                            </div>\
                                            <div style="margin-top: 10px;">\
                                                <label for="changeAmt" style="font-size: 16px;">Change:</label>\
                                                <input class="changeAmt" type="text" value="0.00" readonly\
                                                    style="text-align: right; width: 60px; display: inline-block; margin-left: 20px; border: 1px solid #ccc; padding: 5px; font-size: 14px; background-color: #f9f9f9;">\
                                            </div>\
                                        </td>\
                                        <td style="text-align: center; vertical-align: middle;"><button class="btn btn-danger btn-sm" onclick="removeRow(this)">X</button></td>\
                                    </tr>';
                                    document.getElementById('paymentMethod').value = 'cash';
                                } else if (target.id === 'cashless-btn') {
                                    // Add row for cashless payment
                                    tbody.innerHTML += '\
                                    <tr>\
                                        <td>Cashless<br>\
                                            Gcash number: <input type="number" class="form-control contact" />\
                                            Reference number: <input type="number" class="form-control referencenumber" />\
                                        </td>\
                                        <td>\
                                            <div style="margin-top: 10px;">\
                                                <label for="userAmt" style="font-size: 16px;">Tendered:</label>\
                                                <input class="form-control userAmt" type="number" placeholder="0.00" style="text-align: right; width: 60px; display: inline-block; margin-left: 5px; border: 1px solid #ccc; padding: 5px; font-size: 14px;">\
                                            </div>\
                                            <div style="margin-top: 10px;">\
                                                <label for="changeAmt" style="font-size: 16px;">Change:</label>\
                                                <input class="changeAmt" type="text" value="0.00" readonly\
                                                    style="text-align: right; width: 60px; display: inline-block; margin-left: 20px; border: 1px solid #ccc; padding: 5px; font-size: 14px; background-color: #f9f9f9;">\
                                            </div>\
                                        </td>\
                                        <td style="text-align: center; vertical-align: middle;"><button class="btn btn-danger btn-sm" onclick="removeRow(this)">X</button></td>\
                                    </tr>';
                                    document.getElementById('paymentMethod').value = 'cashless';
                                }
                            });
                        },
                        buttons: [
                            {
                                label: 'CheckOut',
                                cssClass: 'btn-primary',
                                action: function(dialogRef) {
                                    const paymentMethod = document.getElementById('paymentMethod').value;
                                    const tenderedAmt = parseFloat(document.getElementById('userAmt').value) || 0;
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
                                        tenderedAmt: loadScript.tenderedAmt,
                                        paymentMethod,
                                        referenceNumber,
                                        customer: {
                                            contact
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
                    });
                }
            }
            
            
            });


            document.addEventListener('click', function(e) {
                let target = e.target;
            
                if (target.id === 'cash-btn') {
                    // Show customer details when cash is selected
                    document.getElementById('customer-details-cash').style.display = 'block';
                    document.getElementById('customer-details-cashless').style.display = 'none';
                    document.getElementById('paymentMethod').value = 'cash';
                } else if (target.id === 'cashless-btn') {
                    // Hide customer details when cashless is selected
                    document.getElementById('customer-details-cashless').style.display = 'block';
                    document.getElementById('customer-details-cash').style.display = 'none';
                    document.getElementById('paymentMethod').value = 'cashless';
                }
            });
            document.addEventListener('keyup', function(e){
                let targetEl = e.target;
                let targetElClassList = targetEl.classList;

                if(targetEl.id === 'userAmt'){
                    let userAmt = targetEl.value == '' ? 0: parseFloat(targetEl.value);
                    loadScript.tenderedAmt = userAmt;
                    let change = userAmt - loadScript.totalOrderAmount;
                    loadScript.userChange = change;
                    let displayedChange = change < 0 ? 0 : change;

                    let changeEl = document.getElementById('changeAmt');
                    changeEl.value = loadScript.addCommas(displayedChange.toFixed(2));
            
                    // Optional: Add or remove the 'text-danger' class based on the change value
                    if (change < 0) {
                        changeEl.classList.add('text-danger');
                    } else {
                        changeEl.classList.remove('text-danger');
                    }
            
                    console.log("Change:", change);
                }
            });
        }


        this.resetData = function(response) {
            let productJson = response.product;
            loadScript.product = {};
        
            productJson.forEach(($row) => {
                loadScript.product[$row.productID] = {
                    name: $row.brandID,
                    price: $row.unitPrice
                };
            });
        
            // Reset order data
            loadScript.orderItems = {};
            loadScript.totalOrderAmount = 0.00;
            loadScript.userChange = -1;
            loadScript.tenderedAmt = 0;
        
            // Update the order item table
            loadScript.updateOrderItemTable();
        
            // Reinitialize product click listeners to make products clickable again
            initializeProductClickListeners();
        
            console.log("Data reset and product click listeners reinitialized"); // Debugging purpose
        };
        

            

            this.updateOrderItemTable = function(){

                loadScript.totalOrderAmount = 0.00;
                
                let ordersContainer = document.querySelector('.pos_items');
                let html = '<p style="text-align: center;font-size:20px;padding:55px;text-transform:uppercase" class="itemNoData">No data</p>';
                if(Object.keys(loadScript.orderItems)){
                    let tableHtml =`
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
                    for(const [pid, orderItem] of Object.entries(loadScript.orderItems)){
                        rows +=  `
                            <tr>
                                <td>${rowNum}</td>
                            <td>${orderItem['name'].length > 10 ? orderItem['name'].substring(0, 10) + '...' : orderItem['name']}</td>
                                <td>${loadScript.addCommas(orderItem['orderQty']) }
                                    <a href="javascript:void(0);" data-id="${pid}" class="quantityUpdateBtn quantityUpdateBtn_minus">
                                        <i class="fa fa-minus quantityUpdateBtn quantityUpdateBtn_minus" data-id="${pid}" style="color:red;font-size:10px;text-decoration:none;"></i>
                                    </a>
                                    <a href="javascript:void(0);"  data-id="${pid}" class="quantityUpdateBtn quantityUpdateBtn_plus">
                                        <i class="fa fa-plus quantityUpdateBtn quantityUpdateBtn_plus" data-id="${pid}"  style="color:green;font-size:10px;text-decoration:none;"></i>
                                    </a>
                                    
                                </td>
                                                            <td>₱${loadScript.addCommas(orderItem['price']) }</td>

                                <td>₱ ${loadScript.addCommas(orderItem['amount'].toFixed(2))}</td>
                                <td>
                                    <a href="javascript:void(0)" class="deleteOrderItem" data-id="${pid}">
                                    <i class="fa fa-trash deleteOrderItem" data-id="${pid}"></i>
                                    </a>
                                </td>
                            </tr>
                    `;
                        rowNum++;

                        loadScript.totalOrderAmount += orderItem['amount'];
                    }
                    html = tableHtml.replace('__ROWS__', rows);
                }
                
                    ordersContainer.innerHTML = html; 
                    loadScript.updateTotalOrderAmount();
                    
                
            }

            function initializeProductClickListeners() {
                document.querySelectorAll('.product-item').forEach(item => {
                    item.addEventListener('click', function() {
                        let productId = item.getAttribute('data-id'); // Assuming each product has a data-id
                        // Your logic to add the product to the order
                        console.log("Product clicked: ", productId);
                        // For example:
                        loadScript.addToOrder(productId);
                    });
                });
            }
            

            this.updateTotalOrderAmount = function(){
                // update total amount
                document.querySelector('.item_total--value').innerHTML = '₱' + loadScript.addCommas(loadScript.totalOrderAmount.toFixed(2));
            }

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


            this.addtoOrder = function(productInfo,pid,orderQty){
                let curItemIds = Object.keys(loadScript.orderItems);
                let totalAmount = productInfo['price'] * orderQty

                if(curItemIds.indexOf(pid) > -1){
                    loadScript.orderItems[pid]['amount'] += totalAmount;
                    loadScript.orderItems[pid]['orderQty'] += orderQty;

                }
                else{
                
                    loadScript.orderItems[pid] = {
                        name: productInfo['name'],
                        price: productInfo['price'],
                        orderQty: orderQty,
                        amount: totalAmount

                    };
                
                }
                

                this.updateOrderItemTable();
                


                
            }

        this.initialize = function(){
            // show clock
            this.showClock();
            //register all app events
            this.registerEvents()
        }

    };
    let loadScript = new script;
    loadScript.initialize();





