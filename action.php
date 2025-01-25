<?php
require_once 'connection.php';
$db = new database();

// categoriesss
if(isset($_POST['action']) && $_POST['action'] == "view"){
    $output = '';
    $data = $db->read();
    if($db->totalRowCountcategory()>0){
        $output .= '
        <h5 class="card-title">Category Table</h5>
              
              <table class="table datatable">
        <thead>
          <tr>
            <th>
              <b>ID
            </th>
            <th>Category Name</th>
            <th>Image</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>';

        foreach ($data as $row){
          $status = $row['isActive']==1?"Active":"Inactive";
            $output .='<tr>
                        <td>'.$row['categID'].'</td>
                        <td>'.$row['categname'].'</td>
                        <td><img src="uploads/categoryimage/' . htmlspecialchars($row['image']) . '" alt="Product Image" style="width: 100px; height: auto;"></td>
                        <td>'.$status.'</td>
                        <td>
                        <a href="#" title="Edit" class="text-primary editBtn" data-bs-toggle="modal" data-bs-target="#editcategory" id="'.$row['categID'].'"><i class="fas fa-edit fa-lg"></i></a>&nbsp;
                        <a href="#" title="Delete" class="text-danger deletebtn" data-bs-toggle="modal" data-bs-target="#" id="'.$row['categID'].'"><i class="fas fa-trash-alt fa-lg"></i></a>&nbsp;
                        </td></tr>
            ';
        }
        $output .= '</tbody></table>';
        echo $output;
    }
    else{
        echo '<h3 class="text-center">No categories found</h3>';
    }
    }

    
    if(isset($_POST['action']) && $_POST['action'] == "insertcategory"){
        $categName = $_POST['categName'];
        $isactive = 1;
        $isdeleted = 0;

        $db->insert($categName,$isactive,$isdeleted);
    }

    if(isset($_POST['edit_id'])){
        $id = $_POST['edit_id'];

        $row = $db->getUserById($id);
        echo json_encode($row);
    }

    if(isset($_POST['action']) && $_POST['action'] == "update"){
      $id = $_POST['id'];
      $categName = $_POST['editcategName'];
      $isactive = $_POST['editisactive'];
      $isdeleted = $_POST['editisdeleted'];

      $db->update($id,$categName,$isactive,$isdeleted);
  }

  if(isset($_POST['del_id'])){
    $id = $_POST['del_id'];

    $row = $db->delete($id);
}



//sales
if(isset($_POST['action']) && $_POST['action'] == "viewsales"){
  $output = '';
  $data = $db->readsales();
  if($db->totalnumberofsales()>0){
      $output .= '
      <h5 class="card-title">Sales Table</h5>
           
            <table class="table datatable">
      <thead>
        <tr>
          <th>
            <b>ID
          </th>
          <th>Customer</th>
          <th>User</th>
          <th>Amount</th>
          <th>Tendered</th>
          <th>Change</th>
          <th>Payment</th> 
          <th>Reference</th>
          <th>Date and Time</th>
          <th>Status</th>
          <th>View</th>
        </tr>
      </thead>
      <tbody>';

      foreach ($data as $row){
        $date = $row['date_created'];
        $badgeClass = $row['status'] === 'Complete' ? 'bg-success' : 'bg-danger';
          $output .='<tr>
                      <td>'.$row['id'].'</td>
                      <td>'.$row['customer_name'].'</td>
                      <td>'.$row['user_name'].'</td>
                      <td>'.number_format($row['total_amount'], 2).'</td>
                      <td>'.number_format($row['amount_tendered'], 2).'</td>
                      <td>'.number_format($row['change_amt'], 2).'</td>
                      <td>'.$row['payment_method'].'</td>
                      <td>'.$row['reference_number'].'</td>
                      <td>'.$row['date_updated'].'</td>
                      <td class="badge ' . $badgeClass . ' ms-2" style="color: white;">' . $row['status'] . '</td>
                      <td>
                      <a href="#" title="Edit" class="text-primary editBtn" data-bs-toggle="modal" data-bs-target="#editcategory" id="'.$row['id'].'"><i class="fa fa-eye" aria-hidden="true"></i></a>&nbsp;
                      </td></tr>
          ';
      }
      $output .= '</tbody></table>';
      echo $output;
  }
  else{
      echo '<h3 class="text-center">No sales found</h3>';
  }
  }
    // deletesalesbutton
  // <a href="#" title="Delete" class="text-danger deletebtn" data-bs-toggle="modal" data-bs-target="#" id="'.$row['id'].'"><i class="fas fa-trash-alt fa-lg"></i></a>&nbsp;
  if (isset($_POST['action']) && $_POST['action'] == 'getSaleData') {
    $sale_id = $_POST['sale_id'];

    $sales_data = $db->getSalehistory($sale_id);
    
    // Send the response as a JSON object
    echo json_encode($sales_data);
}

if (isset($_POST['action']) && $_POST['action'] == 'updateStatus') {
  $sale_id = $_POST['sale_id'];
  $status = $_POST['status'];

  try {
      // Call the updateStatus method
      $db->updateStatus($sale_id, $status);
      echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
  } catch (Exception $e) {
      echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
  }
}



  if(isset($_POST['del_idsales'])){
    $id = $_POST['del_idsales'];

    $row = $db->deleteSales($id);
}

  //customer



  if (isset($_POST['action']) && $_POST['action'] == "viewcustomer") {
    $output = '';
    $data = $db->readcustomer();

    // Sort the data by transaction count in descending order
    usort($data, function($a, $b) {
        return $b['transaction_count'] - $a['transaction_count'];  // Compare in descending order
    });

    if (count($data) > 0) {
        $output .= '
        <h5 class="card-title">Customer Table</h5>
        <table class="table datatable">
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th># Transactions</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>';
        
        foreach ($data as $row) {
            $transactionCount = $row['transaction_count'];
            $output .= '<tr>
                        <td>' . $row['full_name'] . '</td>
                        <td>' . $transactionCount . '</td>
                        <td>
                            <a href="#" title="Delete" class="text-danger deletebtn" data-bs-toggle="modal" data-bs-target="#" id="' . $row['full_name'] . '">
                                <i class="fas fa-trash-alt fa-lg"></i>
                            </a>&nbsp;
                        </td>
                    </tr>';
        }
        
        $output .= '</tbody></table>';
        echo $output;
    } else {
        echo '<h3 class="text-center">No customers found</h3>';
    }
}


//product 




if(isset($_POST['action']) && $_POST['action'] == "viewproduct"){
  $outputproduct = '';
  $dataproduct = $db->readproduct();
  if($db->product()>0){
    $outputproduct .= '
      <h5 class="card-title">Product Table</h5>

            <table class="table datatable">
      <thead>
        <tr>
          <th>
            <b>ID
          </th>
          <th>Brand Name</th>
          <th>Image</th>
          <th>Description</th>
          <th>Category</th>
          <th>Unit</th>
          <th>Price</th>
          <th>Vatable</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>';
      foreach ($dataproduct as $row){
        $status = $row['isActive']==1?"Active":"Inactive";
        $vatable = $row['isVatable']==1?"Vat":"Non-Vat";
        $outputproduct .='<tr>
                      <td>'.$row['productID'].'</td>
                      <td>'.$row['brandID'].'</td>
                      <td><img src="uploads/image/' . htmlspecialchars($row['image']) . '" alt="Product Image" style="width: 100px; height: auto;"></td>
                      <td>'.$row['productdesc'].'</td>
                      <td>'.$row['catID'].'</td>
                      <td>'.$row['unitID'].'</td>
                      <td>'.$row['unitPrice'].'</td>
                      <td>'.$vatable.'</td>
                      <td>'.$status.'</td>
                      <td>
                      <a href="#" title="Edit" class="text-primary editBtn" data-bs-toggle="modal" data-bs-target="#editcategory" id="'.$row['productID'].'"><i class="fas fa-edit fa-lg"></i></a>&nbsp;
                      <a href="#" title="Delete" class="text-danger deletebtn" data-bs-toggle="modal" data-bs-target="#" id="'.$row['productID'].'"><i class="fas fa-trash-alt fa-lg"></i></a>&nbsp;
                      </td></tr>
          ';
      }
      $outputproduct .= '</tbody></table>';
      echo $outputproduct;
  }
  else{
      echo '<h3 class="text-center">No product found</h3>';
  }
}

  if(isset($_POST['action']) && $_POST['action'] == "insertproduct"){
    $brandID = $_POST['addbrand'];
    $desc = $_POST['adddesc']; 
    $catID = $_POST['addcategory'];
    $unitID = $_POST['addunit'];
    $unitPrice = $_POST['addprice'];
    $isvatable = $_POST['isvatable'];
    $isactive = 1;
    $isdeleted = 0;
    $db->insertproduct($brandID,$desc,$catID,$unitID,$unitPrice,$isvatable,$isactive,$isdeleted);

}

if(isset($_POST['editt_id'])){
  $id = $_POST['editt_id'];

  $row = $db->getproductById($id);
  echo json_encode($row);
}

if(isset($_POST['action']) && $_POST['action'] == "updateproduct"){
  $id = $_POST['id'];
  $brandID = $_POST['editbrand'];
  $desc = $_POST['editdesc']; 
  $catID = $_POST['editcategory'];
  $unitID = $_POST['editunit'];
  $unitPrice = $_POST['editprice'];
  $isvatable = $_POST['editisvatable'];
  $isactive = $_POST['editisactive'];
  $isdeleted = $_POST['editisdeleted'];

  $db->updateproduct($id,$brandID,$desc,$catID,$unitID,$unitPrice,$isvatable,$isactive,$isdeleted);
}

if(isset($_POST['dell_id'])){
  $id = $_POST['dell_id'];

  $row = $db->deleteproduct($id);
}
  
// unittt
if(isset($_POST['action']) && $_POST['action'] == "viewunit"){
  $output = '';
  $data = $db->readunit();
  if($db->totalRowCountunit()>0){
      $output .= '
      <h5 class="card-title">Unit Table</h5>

            <table class="table datatable">
      <thead>
        <tr>
          <th>
            <b>ID
          </th>
          <th>Unit</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>';
      foreach ($data as $row){
        $status = $row['isActive']==1?"Active":"Inactive";
          $output .='<tr>
                      <td>'.$row['unitID'].'</td>
                      <td>'.$row['unitname'].'</td>
                      <td>'.$status.'</td>
                      <td>
                      <a href="#" title="Edit" class="text-primary editBtn" data-bs-toggle="modal" data-bs-target="#editcategory" id="'.$row['unitID'].'"><i class="fas fa-edit fa-lg"></i></a>&nbsp;
                      <a href="#" title="Delete" class="text-danger deletebtn" data-bs-toggle="modal" data-bs-target="#" id="'.$row['unitID'].'"><i class="fas fa-trash-alt fa-lg"></i></a>&nbsp;
                      </td></tr>
          ';
      }
      $output .= '</tbody></table>';
      echo $output;
  }
  else{
      echo '<h3 class="text-center">No categories found</h3>';
  }
  }

  
  if(isset($_POST['action']) && $_POST['action'] == "insertunit"){
      $unitName = $_POST['unitName'];
      $isactive = 1;
      $isdeleted = 0;

      $db->insertunit($unitName,$isactive,$isdeleted);
  }

  if(isset($_POST['editunit_id'])){
      $id = $_POST['editunit_id'];

      $row = $db->getUnitById($id);
      echo json_encode($row);
  }

  if(isset($_POST['action']) && $_POST['action'] == "updateunit"){
    $id = $_POST['id'];
    $unitName = $_POST['editunitName'];
    $isactive = $_POST['editisactive'];

    $db->updateunit($id,$unitName,$isactive);
}

if(isset($_POST['delunit_id'])){
  $id = $_POST['delunit_id'];

  $row = $db->deleteunit($id);
}

// users
if(isset($_POST['action']) && $_POST['action'] == "viewuser"){
  $output = '';
  $data = $db->readusers();
  if($db->totalRowCountUsers()>0){
      $output .= '
      <h5 class="card-title">User List</h5>
 
            <table class="table datatable">
      <thead>
        <tr>
          <th>
            <b>ID
          </th>
          <th>Full Names</th>
          <th>Username</th>
          <th>Contact Number</th>
          <th>Position</th>
          <th>Image</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>';
      foreach ($data as $row){
        $status = $row['isActive']==1?"Active":"Inactive";
        $password = $row['pw'];
          $output .='<tr>
                      <td>'.$row['userID'].'</td>
                      <td>'.$row['name'].'</td>
                      <td>'.$row['username'].'</td>
                      <td>'.$row['contact'].'</td>
                      <td>'.$row['position'].'</td>
                      <td><img src="uploads/userimage/' . htmlspecialchars($row['image']) . '" alt="User Image" style="width: 100px; height: auto;"></td>
                      <td>'.$status.'</td>
                      <td>
                      <a href="#" title="Edit" class="text-primary editBtn" data-bs-toggle="modal" data-bs-target="#editcategory" id="'.$row['userID'].'"><i class="fas fa-edit fa-lg"></i></a>&nbsp;
                      <a href="#" title="Delete" class="text-danger deletebtn" data-bs-toggle="modal" data-bs-target="#" id="'.$row['userID'].'"><i class="fas fa-trash-alt fa-lg"></i></a>&nbsp;
                      </td></tr>
          ';
      }
      $output .= '</tbody></table>';
      echo $output;
  }
  else{
      echo '<h3 class="text-center">No users found</h3>';
  }
  }


if (isset($_POST['action']) && $_POST['action'] == "checkUsername") {
    $username = $_POST['username'];

    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "exists";
    } else {
        echo "available";
    }
    exit;
}
  
  if(isset($_POST['action']) && $_POST['action'] == "insertuser"){
    $username = htmlspecialchars(trim($_POST['username']));
    $password = sha1(trim($_POST['password']));
    $isactive = 1;
    $isdeleted = 0;
    $contact = $_POST['contact'];
    $position =$_POST['position'];
    $name = $_POST['name'];
    
    $db->insertusers($username,$password,$isactive,$isdeleted,$contact,$position,$name);
    // $fname = $_FILES['files'];
    // $folder="upload/image/";
    // $newName = $db->lastInsertId();
    // $db->UploadOne($fname,$folder,$newName);
    // $name=explode(".",$_FILES['file']['name']);
    // $ext=$name[1];
    // $db->updateImage($name,$ext);

}

  if(isset($_POST['edituser_id'])){
      $id = $_POST['edituser_id'];

      $row = $db->getUsersById($id);
      echo json_encode($row);
  }

  if(isset($_POST['action']) && $_POST['action'] == "updateuser"){
    $id = $_POST['id'];
    $username = htmlspecialchars($_POST['editusername']);
    $password = sha1($_POST['editpassword']);
    $isactive = $_POST['editisactive'];
    $isdeleted = $_POST['editisdeleted'];
    $contact = $_POST['editcontact'];
    $position =$_POST['editposition'];
    $name = $_POST['editname'];

    $db->updateusers($id,$username,$password,$isactive,$isdeleted,$contact,$position,$name);
}

if(isset($_POST['deluser_id'])){
  $id = $_POST['deluser_id'];

  $row = $db->deleteusers($id);
}


// Brandd
if(isset($_POST['action']) && $_POST['action'] == "viewbrand"){
  $output = '';
  $data = $db->readbrand();
  if($db->totalRowCountbrand()>0){
      $output .= '
      <h5 class="card-title">Brand Table</h5>

            <table class="table datatable">
      <thead>
        <tr>
          <th>
            <b>ID
          </th>
          <th>Brand Name</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>';

      foreach ($data as $row){
        $status = $row['isActive']==1?"Active":"Inactive";
        
          $output .='<tr>
                      <td>'.$row['brandID'].'</td>
                      <td>'.$row['brandname'].'</td>
                      <td>'.$status.'</td>
                      <td>
                      <a href="#" title="Edit" class="text-primary editBtn" data-bs-toggle="modal" data-bs-target="#editcategory" id="'.$row['brandID'].'"><i class="fas fa-edit fa-lg"></i></a>&nbsp;
                      <a href="#" title="Delete" class="text-danger deletebtn" data-bs-toggle="modal" data-bs-target="#" id="'.$row['brandID'].'"><i class="fas fa-trash-alt fa-lg"></i></a>&nbsp;
                      </td></tr>
          ';
      }
      $output .= '</tbody></table>';
      echo $output;
  }
  else{
      echo '<h3 class="text-center">No Brand found</h3>';
  }
  }

  
  if(isset($_POST['action']) && $_POST['action'] == "insertbrand") {
    $brand = $_POST['brandname'];
    $isactive = 1;
    $isdeleted = 0;

    // Check if the brand already exists
    $existingBrand = $db->checkBrandExists($brand);
    if ($existingBrand) {
        echo json_encode(['status' => 'error', 'message' => 'Brand already exists']);
    } else {
        // Insert the brand if not exists
        $db->insertbrand($brand, $isactive, $isdeleted);
        echo json_encode(['status' => 'success', 'message' => 'Brand added successfully']);
    }
}


  if(isset($_POST['edit_idbrand'])){
      $id = $_POST['edit_idbrand'];

      $row = $db->getBrandById($id);
      echo json_encode($row);
  }

  if(isset($_POST['action']) && $_POST['action'] == "updatebrand"){
    $id = $_POST['id'];
    $categName = $_POST['editbrandname'];
    $isactive = $_POST['editisactive'];

    $db->updateBrand($id,$categName,$isactive);
}

if(isset($_POST['del_idbrand'])){
  $id = $_POST['del_idbrand'];

  $row = $db->deleteBrand($id);
}

// transaction

$action = isset($_GET['action']) ? $_GET['action'] : '';
if($action === 'checkout'){
    $result = $db->saveProducts();
    error_log("Save Products Result: " . print_r($result, true));
    $products = $db->getProducts();
error_log("Fetched Products: " . print_r($products, true)); 
echo json_encode([
    'success' => true,
    'id' => $result['sales_id'],
    'message' => 'Order successful!',
    'product' => $products
]);

}








?>