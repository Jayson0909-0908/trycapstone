<?php
class database{
private $uname="root";
private $pw="";
private $dsn="mysql:host=localhost;dbname=possystem";
public $con;
public function __construct()
{
    

try {
   $this->con= new PDO($this->dsn,$this->uname,$this->pw);
} catch (PDOException $e) {
    echo "Error : ".$e->getMessage();
}
}

//category
public function insert($categName,$isactive,$isdeleted){
    $sql = "INSERT INTO category (categname,isActive,isDeleted) VALUES(:categoryName,:isactivee,:isdeletedd)";
    $stmt = $this->con->prepare($sql);
    $stmt->execute(['categoryName'=>$categName,'isactivee'=>$isactive,'isdeletedd'=>$isdeleted]);
    return true;
}

public function read(){
    $data = array();
    $sql = "SELECT * FROM category";
    $stmt = $this->con->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($result as $row){
        $data[] = $row;
    }
    return $data;
}

public function totalRowCountcategory(){
    $sql = "SELECT * FROM category";
    $stmt = $this->con->prepare($sql);
    $stmt->execute();
    $t_rows = $stmt->rowCount();
    return $t_rows;
}

public function getUserById($id){
    try {
    $sql = "SELECT * FROM category where categID=:id";
    $stmt = $this->con->prepare($sql);
    $stmt->execute(['id'=>$id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result;
}
    catch (PDOException $th) {
        echo "Error".$th->getMessage();
    }
}

public function update($id,$categName,$isactive,$isdeleted){
    $sql = "UPDATE category SET categname=:categName, isActive=:isactive, isDeleted=:isdeleted WHERE categID=:id";
    $stmt = $this->con->prepare($sql);
    $stmt->execute(['categName'=>$categName,'isactive'=>$isactive,'isdeleted'=>$isdeleted,'id'=>$id]);
    return true;
}

public function updateStatus($id, $status) {
    $sql = "UPDATE sales SET status = :status WHERE id = :id";
    $stmt = $this->con->prepare($sql);
    $stmt->execute(['status' => $status, 'id' => $id]);
    return true; // Indicate success
}

public function delete($id){
    $sql = "DELETE FROM category WHERE categID=:id";
    $stmt = $this->con->prepare($sql);
    $stmt->execute(['id'=>$id]);
    return true;
}

public function categoryselection(){
    $sql = "SELECT * FROM category WHERE isActive=1";
    $stmt = $this->con->prepare($sql);
    $stmt->execute();
    return $stmt;
}

//sales

public function readsales() {
    $data = array();
    $sql = "
        SELECT 
            s.*, 
            c.full_name AS customer_name, 
            u.name AS user_name
        FROM 
            sales s
        LEFT JOIN 
            customer c ON s.customer_id = c.id
        LEFT JOIN 
            user u ON s.user_id = u.userID
    ";
    $stmt = $this->con->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($result as $row) {
        $data[] = $row;
    }
    return $data;
}


public function totalnumberofsales(){
    $sql = "SELECT * FROM sales";
    $stmt = $this->con->prepare($sql);
    $stmt->execute();
    $t_rows = $stmt->rowCount();
    return $t_rows;
}

public function readcustomer() {
    $sql = "
        SELECT 
            c.full_name,
            COUNT(c.full_name) AS transaction_count
        FROM 
            customer c
        WHERE
            TRIM(c.full_name) != ''  -- Exclude rows with empty or blank names
        GROUP BY 
            c.full_name
        ORDER BY 
            transaction_count DESC
    ";
    $stmt = $this->con->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}





public function totalcustomer(){
    $sql = "SELECT * FROM customer";
    $stmt = $this->con->prepare($sql);
    $stmt->execute();
    $t_rows = $stmt->rowCount();
    return $t_rows;
}


//products code

public function getproductById($id){
    $sql = "SELECT * FROM product where productID=:id";
    $stmt = $this->con->prepare($sql);
    $stmt->execute(['id'=>$id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result;
}

public function updateproduct($id,$brandID,$desc,$catID,$unitID,$unitPrice,$isvatable,$isactive,$isdeleted){
    $sql = "UPDATE product SET brandID=?, productdesc=?, catID=?, unitID=?, unitPrice=?, isVatable=?, isActive=?, isDeleted=? WHERE productID=?";
    $data = array($brandID,$desc,$catID,$unitID,$unitPrice,$isvatable,$isactive,$isdeleted,$id);
    $stmt = $this->con->prepare($sql);
    $stmt->execute($data);
    return true;
}

// public function readproduct(){
//     $data = array();
//     $sql = "SELECT * FROM product";
//     $stmt = $this->con->prepare($sql);
//     $stmt->execute();
//     $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
//     foreach ($result as $row){
//         $data[] = $row;
//     }
//     return $data;
// }

public function readProduct() {
    $query = "SELECT * FROM product";
    $stmt = $this->con->query($query);

    // Check if the statement is correctly prepared
    if (!$stmt) {
        print_r($this->con->errorInfo());
        return false;
    }

    // Fetch all products
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function product(){
    $sql = "SELECT * FROM product";
    $stmt = $this->con->prepare($sql);
    $stmt->execute();
    $t_rows = $stmt->rowCount();
    return $t_rows;
}

public function insertproduct($brandID,$desc,$catID,$unitID,$unitPrice,$isvatable,$isactive,$isdeleted){

    $sql = "INSERT INTO product(brandID,productdesc,catID,unitID,unitPrice,isVatable,isActive,isDeleted)VALUES(?,?,?,?,?,?,?,?)";
    $data=array($brandID,$desc,$catID,$unitID,$unitPrice,$isvatable,$isactive,$isdeleted);
    $stmt=$this->con->prepare($sql);
    $stmt->execute($data);
    return true;
} 

public function deleteproduct($id){
    $sql = "DELETE FROM product WHERE productID=?";
    $data = array($id);
    $stmt = $this->con->prepare($sql);
    $stmt->execute($data);
    return true;
}



// unit

public function unitselection(){
    $sql = "SELECT unitID, unitname FROM unit";
    $stmt = $this->con->prepare($sql);
    $stmt->execute();
    return $stmt;


}

public function insertunit($unitName,$isactive,$isdeleted){
    $sql = "INSERT INTO unit (unitname,isActive,isDeleted) VALUES(:unitName,:isactivee,:isdeletedd)";
    $stmt = $this->con->prepare($sql);
    $stmt->execute(['unitName'=>$unitName,'isactivee'=>$isactive,'isdeletedd'=>$isdeleted]);
    return true;
}

public function readunit(){
    $data = array();
    $sql = "SELECT * FROM unit";
    $stmt = $this->con->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($result as $row){
        $data[] = $row;
    }
    return $data;
}

public function totalRowCountunit(){
    $sql = "SELECT * FROM unit";
    $stmt = $this->con->prepare($sql);
    $stmt->execute();
    $t_rows = $stmt->rowCount();
    return $t_rows;
}

public function getUnitById($id){
    $sql = "SELECT * FROM unit where unitID=:id";
    $stmt = $this->con->prepare($sql);
    $stmt->execute(['id'=>$id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result;
}

//sales

public function deleteSales($id){
    $sql = "DELETE FROM sales WHERE id=:id";
    $stmt = $this->con->prepare($sql);
    $stmt->execute(['id'=>$id]);
    return true;
}

//customer

public function deleteCustomer($id){
    $sql = "DELETE FROM customer WHERE unitID=:id";
    $stmt = $this->con->prepare($sql);
    $stmt->execute(['id'=>$id]);
    return true;
}

public function updateunit($id,$unitName,$isactive){
    $sql = "UPDATE unit SET unitname=:unitName, isActive=:isactive WHERE unitID=:id";
    $stmt = $this->con->prepare($sql);
    $stmt->execute(['unitName'=>$unitName,'isactive'=>$isactive,'id'=>$id]);
    return true;
}

public function deleteunit($id){
    $sql = "DELETE FROM unit WHERE unitID=:id";
    $stmt = $this->con->prepare($sql);
    $stmt->execute(['id'=>$id]);
    return true;
}

//userrrss

public function insertusers($username,$password,$isdeleted,$isactive,$contact,$position,$name){
    $sql = "INSERT INTO user(name,username,pw,isDeleted,isActive,contact,position) VALUES(:name,:username,:pw,:isactivee,:isdeletedd,:contact,:position)";
    $stmt = $this->con->prepare($sql);
    $stmt->execute(['name'=>$name,'username'=>$username,'pw'=>$password,'isactivee'=>$isactive,'isdeletedd'=>$isdeleted,'contact'=>$contact,'position'=>$position]);
    return true;
}

public function readusers(){
    $data = array();
    $sql = "SELECT * FROM user";
    $stmt = $this->con->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($result as $row){
        $data[] = $row;
    }
    return $data;
}

public function totalRowCountUsers(){
    $sql = "SELECT * FROM user";
    $stmt = $this->con->prepare($sql);
    $stmt->execute();
    $t_rows = $stmt->rowCount();
    return $t_rows;
}

public function getUsersById($id){
    $sql = "SELECT * FROM user where userID=:id";
    $stmt = $this->con->prepare($sql);
    $stmt->execute(['id'=>$id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result;
}

public function updateusers($id,$username,$password,$isactive,$isdeleted,$contact,$position,$name){
    $sql = "UPDATE user SET name=:Name,username=:Username,pw=:Pw,contact=:Contact,position=:Position,isActive=:isactive, isDeleted=:isdeleted WHERE userID=:id";
    $stmt = $this->con->prepare($sql);
    $stmt->execute(['Name'=>$name,'Username'=>$username,'Pw'=>$password,'Contact'=>$contact,'Position'=>$position,'isactive'=>$isactive,'isdeleted'=>$isdeleted,'id'=>$id]);
    return true;
}

public function deleteusers($id){
    $sql = "DELETE FROM user WHERE userID=:id";
    $stmt = $this->con->prepare($sql);
    $stmt->execute(['id'=>$id]);
    return true;
}

public function loginuser($uname,$password){
    try {
        $sql = "SELECT * FROM user WHERE username=? AND pw=? AND isActive=1";
    $data = array($uname,$password);
    $stmt = $this->con->prepare($sql);
    $stmt->execute($data);
    return $stmt;
    } catch (PDOException $th) {
        echo "Error".$th->getMessage();
    }
    
    
}

//// transactions
public function getAllActiveCategory(){
        $sql = "SELECT categname FROM category WHERE isActive=1";
        $stmt = $this->con->prepare($sql);
        $stmt->execute();
        $t_rows = $stmt->rowCount();
        return $t_rows;
    }
public function readAllActiveCategory(){
        $data = array();
        $sql = "SELECT * FROM category WHERE isActive=1";
        $stmt = $this->con->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($result as $row){
            $data[] = $row;
        }
        return $data;
    }

    public function readAllActiveproduct(){
        $data = array();
        $sql = "SELECT * FROM product WHERE isActive=1";
        $stmt = $this->con->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($result as $row){
            $data[] = $row;
        }
        return $data;
    }

    public function getproductActive($category_click){
        // Use placeholders to prevent SQL injection
        $sql = "SELECT categname FROM category WHERE categID = :category_click";    
        $stmt = $this->con->prepare($sql);
        
        // Bind the category ID parameter
        $stmt->bindParam(':category_click', $category_click, PDO::PARAM_INT);
        
        // Execute the query
        $stmt->execute();
        
        // Fetch the result (fetch returns false if no result)
        $t_rows = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Check if result is not empty
        if ($t_rows) {
            return $t_rows;
        } else {
            return null; // Return null if no category found
        }
    }
    

public function getProductsByCategory($cid){
    $data = array();
    $sql = "SELECT * FROM product WHERE catID='$cid' and isActive=1";
    $stmt = $this->con->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($result as $row){
        $data[] = $row;
    }
    return $data;
    }

public function getProductsActive(){
        $data = array();
        $sql = "SELECT * FROM product WHERE isActive=1";
        $stmt = $this->con->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($result as $row){
            $data[] = $row;
        }
        return $data;
        }
        public function totalRowCountActiveproduct(){
            $sql = "SELECT * FROM product";
            $stmt = $this->con->prepare($sql);
            $stmt->execute();
            $t_rows = $stmt->rowCount();
            return $t_rows;
        }

// public function getProductsByCategory($categoryID) {
//     // Prepare the SQL query
//     $query = "SELECT * FROM product WHERE catID = :categoryID";
//     $stmt = $this->con->prepare($query);

//     // Check if the statement is correctly prepared
//     if (!$stmt) {
//         print_r($this->con->errorInfo());
//         return false;
//     }

//     // Bind the category ID to the query and execute
//     if ($stmt->execute(['categoryID' => $categoryID])) {
//         return $stmt->fetchAll(PDO::FETCH_ASSOC);
//     } else {
//         // Return false if there was an error in execution
//         return false;
//     }
// }
    

public function readproductcategory($product){
    $data = array();
    $sql = "SELECT * FROM product WHERE catID='$product' AND isActive=1";
    $stmt = $this->con->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($result as $row){
        $data[] = $row;
    }
    return $data;
}


//brand

public function insertbrand($brand, $isactive, $isdeleted) {
    try {
        // Step 1: Check if the brand already exists
        if ($this->checkBrandExists($brand)) {
            return "Brand already exists.";
        }

        // Step 2: Insert the brand into the database
        $sql = "INSERT INTO brand (brandname, isActive, isDeleted) VALUES (:brandname, :isactive, :isdeleted)";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([
            'brandname' => $brand,
            'isactive' => $isactive,
            'isdeleted' => $isdeleted
        ]);

        // Step 3: Log the action in the system_history table
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
            $username = $_SESSION['username'];
            $this->logAction($username, 'Add Brand', "Added brand: $brand");
        }
else {
            return "User not logged in. Unable to log action.";
        }

        return "Brand inserted successfully.";
    } catch (PDOException $e) {
        // Log the error and return failure message
        error_log("Error inserting brand: " . $e->getMessage());
        return "Failed to insert brand: " . $e->getMessage();
    }
}

public function logAction($username, $actionType, $description) {
    try {
        $sql = "INSERT INTO system_history (username, action_type, action_description, created_at) 
                VALUES (:username, :action_type, :description, NOW())";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([
            'username' => $username,
            'action_type' => $actionType,
            'description' => $description
        ]);
        return true;
    } catch (PDOException $e) {
        error_log("Error logging action: " . $e->getMessage());
        return false;
    }
}


public function checkBrandExists($brand) {
    $sql = "SELECT * FROM brand WHERE brandname = :brandname AND isDeleted = 0";
    $stmt = $this->con->prepare($sql);
    $stmt->execute(['brandname' => $brand]);
    return $stmt->fetch(PDO::FETCH_ASSOC); // Return row if exists, false otherwise
}

public function checkUserExists($username) {
    $sql = "SELECT * FROM user WHERE username = :username AND isDeleted = 0";
    $stmt = $this->con->prepare($sql);
    $stmt->execute(['username' => $username]);
    return $stmt->fetch(PDO::FETCH_ASSOC); // Return row if exists, false otherwise
}

public function checkUserExistsForEdit($username, $userID) {
    $sql = "SELECT * FROM user WHERE username = :username AND id != :id AND isDeleted = 0";
    $stmt = $this->con->prepare($sql);
    $stmt->execute(['username' => $username, 'id' => $userID]);
    return $stmt->fetch(PDO::FETCH_ASSOC); // Return row if exists, false otherwise
}


public function totalRowCountbrand(){
    $sql = "SELECT * FROM brand";
    $stmt = $this->con->prepare($sql);
    $stmt->execute();
    $t_rows = $stmt->rowCount();
    return $t_rows;
}

public function getBrandById($id){
    try {
    $sql = "SELECT * FROM brand where brandID=:id";
    $stmt = $this->con->prepare($sql);
    $stmt->execute(['id'=>$id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result;
}
    catch (PDOException $th) {
        echo "Error".$th->getMessage();
    }
}

public function updateBrand($id,$brandName,$isactive){
    $sql = "UPDATE brand SET brandname=:brandName, isActive=:isactive WHERE brandID=:id";
    $stmt = $this->con->prepare($sql);
    $stmt->execute(['brandName'=>$brandName,'isactive'=>$isactive,'id'=>$id]);
    return true;
}

public function deleteBrand($id){
    $sql = "DELETE FROM brand WHERE brandID=:id";
    $stmt = $this->con->prepare($sql);
    $stmt->execute(['id'=>$id]);
    return true;
}

//brand
public function readbrand(){
    $data = array();
    $sql = "SELECT * FROM brand";
    $stmt = $this->con->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($result as $row){
        $data[] = $row;
    }
    return $data;
}

public function brandselection() {
    // Step 1: Fetch all active brands
    $sqlAllBrands = "SELECT brandID, brandname FROM brand WHERE isActive = 1";
    $stmtAllBrands = $this->con->prepare($sqlAllBrands);
    $stmtAllBrands->execute();
    $allBrands = $stmtAllBrands->fetchAll(PDO::FETCH_ASSOC);

    // Step 2: Fetch all brand IDs used in the product table
    $sqlProductBrands = "SELECT DISTINCT brandID FROM product";
    $stmtProductBrands = $this->con->prepare($sqlProductBrands);
    $stmtProductBrands->execute();
    $productBrands = $stmtProductBrands->fetchAll(PDO::FETCH_COLUMN);

    // Debug: Log fetched data for verification
    // Uncomment to verify in the console or a log
    // var_dump($allBrands, $productBrands);

    // Step 3: Filter out brands that exist in the product table
    $filteredBrands = array_filter($allBrands, function($brand) use ($productBrands) {
        return !in_array($brand['brandID'], $productBrands); // Exclude matching brandIDs
    });

    return $filteredBrands;
}




//uploading transactions
public function getProducts(){
    $stmt = $this->con->prepare("SELECT * FROM product");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $rows;
}


public function saveProducts() {
    try {
        // Set timezone for PHP to Philippines
        date_default_timezone_set('Asia/Manila');

        $data = $_POST['data'];
        $customer = $_POST['customer'];

        // Insert customer details with NOW() for date fields
        $sql = "INSERT INTO customer(full_name, address, date_created, date_updated)
                VALUES (:full_name, :address, NOW(), NOW())";
        $db_arr = [
            'full_name' => $customer['fullName'],
            'address' => $customer['address']
        ];
        $stmt = $this->con->prepare($sql);
        $stmt->execute($db_arr);
        $customer_id = $this->con->lastInsertId();

        // Log customer_id for debugging
        error_log("Inserted Customer ID: $customer_id");

        // Insert sales details with NOW() for date fields
        $sql = "INSERT INTO sales(customer_id, user_id, sub_total, discounttype, discount, vat, total_amount, amount_tendered, change_amt, payment_method, reference_number, date_created, date_updated, status)
        VALUES (:customer_id, :user_id, :sub_total, :discounttype, :discount, :vat, :total_amount, :amount_tendered, :change_amt, :payment_method, :reference_number, NOW(), NOW(), :status)";

        // Validate and log the input data
        $total_amount = $_POST['totalAmt'];
        $change_amt = $_POST['change'];
        $paymentMethod = $_POST['paymentMethods'];  // Ensure it's a string
        $amt_tendered = $_POST['tenderedAmt'];
        $vat_amt = $_POST['vatValue'];
        $referenceNumber = $_POST['referenceNumber'];
        $subtotalAmt = $_POST['subtotalAmt'];
        $discountValue = $_POST['discountValue'];
        $discountType = $_POST['discountType'];
        $status = "Complete";
        session_start(); // Ensure session is started
        $user_id = isset($_SESSION['UID']) ? $_SESSION['UID'] : null;
        $db_arr = [
            'customer_id' => $customer_id,
            'user_id' => $user_id,
            'sub_total' => $subtotalAmt,
            'discounttype' => $discountType,
            'discount' => $discountValue,
            'vat' => $vat_amt,
            'total_amount' => $total_amount,
            'amount_tendered' => $amt_tendered,
            'change_amt' => $change_amt,
            'payment_method' => $paymentMethod,
            'status' => $status,
            'reference_number' => $referenceNumber
        ];
        
        // Log sales data before execution
        error_log('Sales Data: ' . print_r($db_arr, true));

        $stmt = $this->con->prepare($sql);
        $stmt->execute($db_arr);
        $sales_id = $this->con->lastInsertId();

        // Log sales_id for debugging
        error_log("Inserted Sales ID: $sales_id");

        // Insert each product in the sales_item table with NOW() for date fields
        foreach ($data as $product_id => $order_item) {
            $sql = "INSERT INTO sales_item(sales_id, product_id, quantity, unit_price, sub_total, date_created, date_updated)
                    VALUES (:sales_id, :product_id, :quantity, :unit_price, :sub_total, NOW(), NOW())";
            $db_arr = [
                'sales_id' => $sales_id,
                'product_id' => $product_id,
                'quantity' => $order_item['orderQty'],
                'unit_price' => $order_item['price'],
                'sub_total' => $order_item['amount']
            ];
            $stmt = $this->con->prepare($sql);
            $stmt->execute($db_arr);
        }
        
        return [
            'success' => true,
            'message' => 'Data inserted successfully.',
            'sales_id' => $sales_id
        ];

    } catch (PDOException $e) {
        // Log the error message
        error_log('Error: ' . $e->getMessage());
        return json_encode([
            'success' => false,
            'message' => 'Failed: ' . $e->getMessage()
        ]);
    }
}



public function getSales(){
    $stmt = $this->con->prepare("
        SELECT * FROM sales WHERE sales.date >=
        ");
    $stmt->execute();
    $rows=$stmt->fetchAll(PDO::FETCH_ASSOC);

    //get customer data

    return $rows;
}

public function totalRowCountsales(){
    $sql = "SELECT COUNT(*) FROM sales";
    $stmt = $this->con->prepare($sql);
    $stmt->execute();
    return $stmt;
}

public function totalsalesrevenue(){
    $sql = "SELECT SUM(total_amount) FROM sales";
    $stmt = $this->con->prepare($sql);
    $stmt->execute();
    return $stmt;
}

public function totalRowCountcustomer(){
    $sql = "SELECT COUNT(*) FROM customer";
    $stmt = $this->con->prepare($sql);
    $stmt->execute();
    return $stmt;
}

public function getSaleCustomer($customer_id){
    $stmt= $this->con->prepare("
            SELECT * FROM customer
                where id = $customer_id;
    ");
    $stmt->execute();
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);
    return $customer;
}

public function getOrderItems($id){
    $stmt= $this->con->prepare("SELECT * FROM sales_item WHERE sales_id=$id");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $rows;
}
public function getSale($sale_id){
    $stmt = $this->con->prepare("
        SELECT * FROM sales WHERE sales.id = :sale_id
    ");
    $stmt->bindParam(':sale_id', $sale_id, PDO::PARAM_INT);
    $stmt->execute();
    $sale = $stmt->fetch(PDO::FETCH_ASSOC);
    
    //get customer
    $customer_data = $this->getSaleCustomer($sale['customer_id']);
    
    // Fetch user name based on user_id
    $stmt = $this->con->prepare("
        SELECT name FROM user WHERE userID = :user_id
    ");
    $stmt->bindParam(':user_id', $sale['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    //get order items
    $items = $this->getOrderItems($sale['id']);
    $items_data = [];
    foreach($items as $item){
        $pid = $item['product_id'];
        $stmt = $this->con->prepare("SELECT product.brandID FROM product WHERE productID = :pid");
        $stmt->bindParam(':pid', $pid, PDO::PARAM_INT);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        $items_data[$item['id']] = $item;
        $items_data[$item['id']]['product'] = $product['brandID'];
        // Use this code for detailed output
    }
    
    return [
        'sales' => $sale,
        'items' => $items_data,
        'customer' => $customer_data,
        'user_name' => $user['name'] // Add the user's name
    ];
}


public function getSaleCustomerhistory($customer_id){
    $stmt= $this->con->prepare("
            SELECT * FROM customer
                where id = $customer_id;
    ");
    $stmt->execute();
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);
    return $customer;
}

public function getOrderItemshistory($id){
    $stmt= $this->con->prepare("SELECT * FROM sales_item WHERE sales_id=$id");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $rows;
}
public function getSalehistory($sale_id){
    $stmt = $this->con->prepare("
            SELECT * FROM sales WHERE sales.id=$sale_id
    ");
    $stmt->execute();
    $sale = $stmt->fetch(PDO::FETCH_ASSOC);
    
    //get customer
    $customer_data = $this->getSaleCustomerhistory($sale['customer_id']);
    //get order items
    $items = $this->getOrderItemshistory($sale['id']);
    $items_data = [];
    foreach($items as $item){
        $pid = $item['product_id'];
        $stmt = $this->con->prepare("SELECT product.brandID FROM product WHERE productID = $pid");
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        $items_data[$item['id']] = $item;
        $items_data[$item['id']]['product'] = $product['brandID'];
        // Use this code for detailed output

    }
    return [
        'sales' => $sale,
        'items' =>$items_data,
        'customer' => $customer_data
    ];

}
public function getSalesSummary($startDate, $endDate) {
    // Prepare the query
    $stmt = $this->con->prepare("
        SELECT 
            COUNT(CASE WHEN status = 'Complete' THEN id END) AS total_sales,
            SUM(CASE WHEN status = 'Complete' THEN total_amount ELSE 0 END) AS total_revenue,
            COUNT(CASE WHEN status = 'Void' THEN id END) AS total_voids,
            SUM(CASE WHEN status = 'Void' THEN total_amount ELSE 0 END) AS total_void_amount
        FROM sales
        WHERE DATE(date_created) BETWEEN :startDate AND :endDate
    ");

    // Execute the query with provided date range
    $stmt->execute(['startDate' => $startDate, 'endDate' => $endDate]);

    // Fetch the results
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Debugging: Uncomment this to check the results
    // print_r($result);

    // Return the summary data
    return [
        'total_sales' => $result['total_sales'],         // Number of completed sales
        'total_revenue' => $result['total_revenue'],     // Revenue from completed sales
        'total_voids' => $result['total_voids'],         // Number of voided transactions
        'total_void_amount' => $result['total_void_amount'] // Amount voided
    ];
}

public function getItemSales($startDate, $endDate) {
    $query = "
        SELECT 
    p.brandID AS name,  -- Ensure this matches your database schema
    SUM(si.quantity) AS sold,
    SUM(si.sub_total) AS total,
    COUNT(CASE WHEN s.status = 'Void' THEN 1 END) AS refunded,
    SUM(si.sub_total) - SUM(CASE WHEN s.status = 'Void' THEN si.sub_total ELSE 0 END) AS balance
FROM 
    sales_item si
JOIN 
    sales s ON si.sales_id = s.id
JOIN 
    product p ON si.product_id = p.productID
WHERE 
    s.date_created BETWEEN :startDate AND :endDate
GROUP BY 
    p.productID";
    
    $stmt = $this->con->prepare($query);
    $stmt->bindParam(':startDate', $startDate);
    $stmt->bindParam(':endDate', $endDate);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}


?>