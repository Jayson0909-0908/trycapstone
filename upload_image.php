<?php
require_once 'connection.php';

if(isset($_POST['action']) && $_POST['action'] == "insertproduct") {
    $db = new database();
    $con = $db->con; // Use the connection from the database class

    $brandID = $_POST['addbrand'];
    $desc = $_POST['adddesc']; 
    $catID = $_POST['addcategory'];
    $unitID = $_POST['addunit'];
    $unitPrice = $_POST['addprice'];
    $isvatable = $_POST['addisvatable'];
    $isactive = 1;
    $isdeleted = 0;

    try {
        // Prepare SQL query to insert product
        $sql = "INSERT INTO product (brandID, productdesc, catID, unitID, unitPrice, isVatable, isActive, isDeleted) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $data = [$brandID, $desc, $catID, $unitID, $unitPrice, $isvatable, $isactive, $isdeleted];
        $stmt = $con->prepare($sql);
        $stmt->execute($data);

        // Handle image upload
        $folder = "uploads/image/";
        $newName = $con->lastInsertId(); // Get the ID of the newly inserted user

        if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
            // If an image is uploaded, process and save it
            $fname = $_FILES['image'];
            UploadOne($fname, $newName, $folder, $con);

            // Extract file extension
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            updateImage($newName, $ext, $con);
        } else {
            // If no image is uploaded, set the default image
            updateImage($newName, 'default.jpg', $con);
        }

        echo "Product added successfully";
    } catch (PDOException $th) {
        echo "Error: " . $th->getMessage();
    }
}

if(isset($_POST['action']) && $_POST['action'] == "updateproduct") {
    $db = new database();
    $con = $db->con;

    $id = $_POST['id'];
    $brandID = $_POST['editbrand'];
    $desc = $_POST['editdesc']; 
    $catID = $_POST['editcategory'];
    $unitID = $_POST['editunit'];
    $unitPrice = $_POST['editprice'];
    $isvatable = $_POST['editisvatable'];
    $isactive = $_POST['editisactive'];

        // Update product details
        $sql = "UPDATE product SET brandID=?, productdesc=?, catID=?, unitID=?, unitPrice=?, isVatable=?, isActive=? WHERE productID=?";
        $data = [$brandID, $desc, $catID, $unitID, $unitPrice, $isvatable, $isactive, $id];
        $stmt = $con->prepare($sql);
        $stmt->execute($data);

       // Check if a new image is uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        // Remove old image if it exists
        $oldImageQuery = "SELECT image FROM product WHERE productID = ?";
        $oldImageStmt = $con->prepare($oldImageQuery);
        $oldImageStmt->execute([$id]);
        $oldImage = $oldImageStmt->fetchColumn();

        if ($oldImage && file_exists("uploads/image/".$oldImage)) {
            unlink("uploads/image/".$oldImage); // Remove old image
        }

        // Upload new image
        $folder = "uploads/image/";
        $fname = $_FILES['image'];
        $newName = $id; // Use userID or any unique identifier
        UploadOne($fname, $newName, $folder, $con);
        $name = explode(".", $_FILES['image']['name']);
        $ext = $name[1];
        updateImageuser($newName, $ext, $con); // Update image path in database
    }
}


function UploadOne($fname, $newName, $folder, $con) {
    $pangalan = $fname['name'];
    $ext = explode('.', $pangalan);
    if (is_uploaded_file($fname['tmp_name'])) {
        $filname = basename($fname['name']);
        $uploadfile = $folder . $newName . "." . $ext[1];

        if (move_uploaded_file($fname['tmp_name'], $uploadfile)) {
            return "File " . $filname . " was successfully uploaded and stored.<br>";
        } else {
            return "Could not move " . $fname['tmp_name'] . " to " . $uploadfile . "<br>";
        }
    } else {
        return "File " . $fname['name'] . " failed to upload";
    }
}

function updateImage($userID, $imageName, $con) {
    $finalImageName = ($imageName === 'default.jpg') ? $imageName : $userID . '.' . $imageName;

    $sql = "UPDATE product SET image = ? WHERE productID = ?";
    $stmt = $con->prepare($sql);
    $stmt->execute([$finalImageName, $userID]);
}



if (isset($_POST['action']) && $_POST['action'] == "insertcategory") {
    $db = new database();
    $con = $db->con; // Use the connection from the database class
    $categName = $_POST['categName'];
    $isactive = 1;
    $isdeleted = 0;

    try {
        // Check if the category already exists
        $checkQuery = "SELECT * FROM category WHERE categname = ? AND isDeleted = 0";
        $checkStmt = $con->prepare($checkQuery);
        $checkStmt->execute([$categName]);
        $existingCategory = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if ($existingCategory) {
            echo json_encode(['status' => 'error', 'message' => 'Category already exists']);
        } else {
            // Insert the category if it doesn't exist
            $sql = "INSERT INTO category (categname, isActive, isDeleted) VALUES (?, ?, ?)";
            $data = [$categName, $isactive, $isdeleted];
            $stmt = $con->prepare($sql);
            $stmt->execute($data);

            $folder = "uploads/categoryimage/";
            $newName = $con->lastInsertId(); // Get the ID of the newly inserted category

            if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
                // Process and save the uploaded image
                $fname = $_FILES['image'];
                UploadOne($fname, $newName, $folder, $con);

                // Extract file extension
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                updateImagecategory($newName, $ext, $con);
            } else {
                // Set a default image if none is uploaded
                updateImagecategory($newName, 'default.jpg', $con);
            }

            echo json_encode(['status' => 'success', 'message' => 'Category added successfully']);
        }
    } catch (PDOException $th) {
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $th->getMessage()]);
    }
}


if(isset($_POST['action']) && $_POST['action'] == "updatecategory"){
    $db = new database();
    $con = $db->con;
    $id = $_POST['id'];
    $categName = $_POST['editcategName'];
    $isactive = $_POST['editisactive'];

    $sql = "UPDATE category SET categname= ?,isActive= ? WHERE categID=?";
    $data = [$categName,$isactive, $id];
    $stmt = $con->prepare($sql);
    $stmt->execute($data);

    // Check if a new image is uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        // Remove old image if it exists
        $oldImageQuery = "SELECT image FROM user WHERE userID = ?";
        $oldImageStmt = $con->prepare($oldImageQuery);
        $oldImageStmt->execute([$id]);
        $oldImage = $oldImageStmt->fetchColumn();

        if ($oldImage && file_exists("uploads/categoryimage/".$oldImage)) {
            unlink("uploads/categoryimage/".$oldImage); // Remove old image
        }

        // Upload new image
        $folder = "uploads/categoryimage/";
        $fname = $_FILES['image'];
        $newName = $id; // Use userID or any unique identifier
        UploadOne($fname, $newName, $folder, $con);
        $name = explode(".", $_FILES['image']['name']);
        $ext = $name[1];
        updateImagecategory($newName, $ext, $con); // Update image path in database
    }
}

function updateImagecategory($userID, $imageName, $con) {
        $finalImageName = ($imageName === 'default.jpg') ? $imageName : $userID . '.' . $imageName;
    
        $sql = "UPDATE category SET image = ? WHERE categID = ?";
        $stmt = $con->prepare($sql);
        $stmt->execute([$finalImageName, $userID]);
    }
    


    if (isset($_POST['action']) && $_POST['action'] == "insertuser") {
        $db = new database();
        $con = $db->con;
    
        $username = htmlspecialchars(trim($_POST['username']));
        $password = sha1(trim($_POST['password'])); // Encrypt password
        $name = htmlspecialchars(trim($_POST['name']));
        $contact = htmlspecialchars(trim($_POST['contact']));
        $position = htmlspecialchars(trim($_POST['position']));
        $isactive = 1;
        $isdeleted = 0;
    
        // Check if the username already exists
        $existingUser = $db->checkUserExists($username);
        if ($existingUser) {
            echo json_encode(['status' => 'error', 'message' => 'Username already exists.']);
            exit;
        }
    
        try {
            // Insert user details into the database
            $sql = "INSERT INTO user (name, username, pw, contact, position, isActive, isDeleted) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $con->prepare($sql);
            $stmt->execute([$name, $username, $password, $contact, $position, $isactive, $isdeleted]);
    
            $newUserId = $con->lastInsertId();
            $folder = "uploads/userimage/";
    
            // Handle image upload if provided
            if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
                $file = $_FILES['image'];
                UploadOne($file, $newUserId, $folder, $con);
    
                // Update the user record with the uploaded image
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                updateImageuser($newUserId, $ext, $con);
            } else {
                // Set default image if no upload
                updateImageuser($newUserId, 'default.jpg', $con);
            }
    
            echo json_encode(['status' => 'success', 'message' => 'User added successfully.']);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
        }
    }
    
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_POST['action'])) {
    $db = new database();
    $con = $db->con;

    // Check if username already exists
    if ($_POST['action'] == 'checkUsername') {
        $username = htmlspecialchars(trim($_POST['username']));
        $id = $_POST['id'];

        $query = "SELECT * FROM user WHERE username = ? AND userID != ?";
        $stmt = $con->prepare($query);
        $stmt->execute([$username, $id]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Username already exists.']);
        } else {
            echo json_encode(['status' => 'success', 'message' => 'Username is available.']);
        }
        exit;
    }

    // Update user details
    if ($_POST['action'] == 'updateuser') {
        $id = $_POST['id'];
        $username = htmlspecialchars(trim($_POST['editusername']));
        $password = sha1(trim($_POST['editpassword']));
        $isactive = $_POST['editisactive'];
        $contact = $_POST['editcontact'];
        $position = $_POST['editposition'];
        $name = $_POST['editname'];

        // Check if username exists before updating
        $query = "SELECT * FROM user WHERE username = ? AND userID != ?";
        $stmt = $con->prepare($query);
        $stmt->execute([$username, $id]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Username already exists.']);
            exit;
        }

        // Proceed with updating user data
        $sql = "UPDATE user SET name=?, username=?, pw=?, contact=?, position=?, isActive=? WHERE userID=?";
        $data = [$name, $username, $password, $contact, $position, $isactive, $id];
        $stmt = $con->prepare($sql);
        $stmt->execute($data);

        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
            $oldImageQuery = "SELECT image FROM user WHERE userID = ?";
            $oldImageStmt = $con->prepare($oldImageQuery);
            $oldImageStmt->execute([$id]);
            $oldImage = $oldImageStmt->fetchColumn();

            if ($oldImage && file_exists("uploads/userimage/" . $oldImage)) {
                unlink("uploads/userimage/" . $oldImage);
            }

            $folder = "uploads/userimage/";
            $fname = $_FILES['image'];
            $newName = $id;
            UploadOne($fname, $newName, $folder, $con);
            $name = explode(".", $_FILES['image']['name']);
            $ext = $name[1];
            updateImageuser($newName, $ext, $con);
        }

        echo json_encode(['status' => 'success', 'message' => 'User updated successfully.']);
        exit;
    }
}

    

function updateImageuser($userID, $imageName, $con) {
    $finalImageName = ($imageName === 'default.jpg') ? $imageName : $userID . '.' . $imageName;

    $sql = "UPDATE user SET image = ? WHERE userID = ?";
    $stmt = $con->prepare($sql);
    $stmt->execute([$finalImageName, $userID]);
}


?>