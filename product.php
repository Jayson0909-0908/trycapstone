<?php
include('header.php');
include('sidebar.php');
include('footer.php');
require_once 'connection.php';
$db = new database();
?>

<body>
<main id="main" class="main">

<div class="pagetitle">
  <h1>Product<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addcategory" style="float:right">Insert New Product</button></h1>
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
        <div class="card-body" id="showProduct" name="showProduct">
          <h5 class="card-title">Products</h5>
   
        </div>
      </div>

    </div>
  </div>
</section>

<style>
    /* Scoped CSS */
    #showProduct .dataTables_filter label {
        display: flex;
        align-items: center;
        gap: 5px; /* Adjust space between 'Search' and input */
    }

    #showProduct .dataTables_filter {
        float: right; /* Align the search bar to the right */
        text-align: right;
    }

    #showProduct .dataTables_length {
        float: left; /* Align the "Show entries" dropdown to the left */
    }

    #showProduct .dataTables_wrapper .row {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
</style>

<style>
  .file-upload {
  margin-top: 50px;
  position: relative;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  height: 50px; /* Adjust height if necessary */
  padding: 10px;
  border: 1px dashed silver;
  border-radius: 8px;
  gap: 10px; /* Adds spacing between the image and text */
}

.file-upload input {
  position: absolute;
  top: 0;
  left: 0;
  height: 100%;
  width: 100%;
  cursor: pointer;
  opacity: 0;
}

.preview_img {
  height: 150px; /* Size of the preview image */
  width: 150px;
  border: 4px solid silver;
  border-radius: 100%;
  object-fit: cover;
}
</style>

</main><!-- End #main -->
 <!-- addmodal -->
  <div class="modal fade" id="addcategory" tabindex="-1">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                    <h4 class="modal-title">Add Products</h4>
                    </div>
                    <div class="modal-body">
                    <form action="" id="form-data" enctype="multipart/form-data">

                    <div class="form-group">
                    <label for="addbrand">Brand</label>
                    <select class="form-select" name="addbrand" id="addbrand" autocomplete="off" required>
                      <?php
                                    $brands = $db->brandselection();
                                    foreach ($brands as $row) {
                                        echo '<option value="'.$row['brandname'].'">'.$row['brandname'].'</option>';
                                    }
?>        
                    </select>
                  </div>
                <div class="form-group">
                <label for="adddesc">Description</label>
                <textarea class="form-control" id="adddesc" name="adddesc" rows="2" ></textarea>
                </div>
                
                <div class="row mb-3">
                <label for="fileToUpload">Upload Image</label>
                <div class="col-4">
                  <img class="preview_img" src="assets/img/noimg.jpg">
                </div>
                <div class="col-8">
                  <div class="file-upload text-secondary">
                    <input type="file" class="image" name="image" accept="image/*">
                    <span class="fs-2 fw-1">Choose File....</span>
                </div>
                </div>
                </div>

                <div class="form-group">
                  <label for="addcategory">Category</label>
                  <select class="form-select" name="addcategory" id="addcategory" autocomplete="off" required>
                      <?php
                                    $product = $db->categoryselection();
                                    while($row = $product->fetch(PDO::FETCH_ASSOC)){
                                    echo '<option value="'.$row['categname'].'">'.$row['categname'].'</option>';
                                    }
?>        
                    </select>
                </div>
                <div class="form-group">
                  <label for="addunit">Unit</label>
                  <select class="form-select" name="addunit" id="addunit" autocomplete="off" required>
                      <?php
                                    $product = $db->unitselection();
                                    while($row = $product->fetch(PDO::FETCH_ASSOC)){
                                    echo '<option value="'.$row['unitname'].'">'.$row['unitname'].'</option>';
                                    }
?>        
                    </select>
                </div>
                <div class="row mb-3">
                <div class="col-sm-6">
                <div class="form-group">
                <label for="addprice">Price</label>
                    <input type="number" class="form-control" name="addprice" id="addprice" autocomplete="off" style="text-align: right;" required>
                  </div>
                </div>
                  <div class="col-sm-6">
                  <div class="form-group">
                  <label for="addisvatable">Vatable</label>
                  <select name="addisvatable" id="addisvatable" class="form-control" required>
                      <option value="1">Vat</option>
                      <option value="0">Non-Vat</option>
                  </select>
              </div>    
                  </div>
                </div>
            <div class="modal-footer">

            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
            <div class="field">        
            <input type="submit" class="btn btn-primary" id="insertproduct" name="insertproduct" value="Save" style="float:right" required>
          </div>
        </div>
      </form>
       </div>
                    
      </div>
       </div>
      </div><!-- End add Modal-->


<!-- editmodal -->
<div class="modal fade" id="editcategory" tabindex="-1">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                    <h4 class="modal-title">Edit Products</h4>
                    </div>
                    <div class="modal-body">
                    <form action="" method="post" id="edit-form-data" enctype="multipart/form-data">
                    <input type="hidden" id="id" name="id">
                    <div class="form-group">
                    <label for="editbrand">Brand</label>
                    <select class="form-select" name="editbrand" id="editbrand" autocomplete="off" required>
                      <?php
                      $brands = $db->brandselection();
                      foreach ($brands as $row) {
                          echo '<option value="'.$row['brandname'].'">'.$row['brandname'].'</option>';
                      }

?>        
                    </select>
                  </div>
                <div class="form-group">
                <label for="editdesc">Description</label>
                <textarea class="form-control" id="editdesc" name="editdesc" rows="2" ></textarea>
                </div>

                <div class="row mb-3">
                        <label for="fileToUpload">Upload Image</label>
                        <div class="col-4">
                            <img class="preview_img" src="assets/img/noimg.jpg" id="edit-preview-img" />
                        </div>
                        <div class="col-8">
                            <div class="file-upload text-secondary">
                                <input type="file" class="image" name="image" id="image" accept="image/*">
                                <span class="fs-2 fw-1">Choose File...</span>
                            </div>
                        </div>
                    </div>
                
                <div class="form-group">
                  <label for="editcategory">Category</label>
                  <select class="form-select" name="editcategory" id="editcategory" autocomplete="off" required>
                      <?php
                                    $product = $db->categoryselection();
                                    while($row = $product->fetch(PDO::FETCH_ASSOC)){
                                    echo '<option value="'.$row['categname'].'">'.$row['categname'].'</option>';
                                    }
?>        
                    </select>
                </div>
                <div class="form-group">
                  <label for="editunit">Unit</label>
                  <select class="form-select" name="editunit" id="editunit" autocomplete="off" required>
                      <?php
                                    $product = $db->unitselection();
                                    while($row = $product->fetch(PDO::FETCH_ASSOC)){
                                    echo '<option value="'.$row['unitname'].'">'.$row['unitname'].'</option>';
                                    }
?>        
                    </select>
                </div>
                <div class="row mb-3">
                <div class="col-sm-6">
                <div class="form-group">
                <label for="editprice">Price</label>
                    <input type="number" class="form-control" name="editprice" id="editprice" autocomplete="off" style="text-align: right;" required>
                  </div>
                </div>
                  <div class="col-sm-6">
                  <div class="form-group">
                  <label for="editisvatable">Vatable</label>
                  <select name="editisvatable" id="editisvatable" class="form-control" required>
                      <option value="1">Vat</option>
                      <option value="0">Non-Vat</option>
                  </select>
              </div>    
                  </div>
                </div>
                <div class="form-group">
                <label for="editisactive">Status</label>
                <select name="editisactive" id="editisactive" class="form-control" required>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
            <div class="modal-footer">

            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
            <div class="field">        
            <input type="submit" class="btn btn-primary" id="updateproduct" name="updateproduct" value="Save" style="float:right" required>
          </div>
        </div>
      </form>
       </div>
                    
      </div>
       </div>
      </div><!-- End add Modal-->

<script type="text/javascript">
  $(document).ready(function(){

    $("input.image").change(function() {
    var file = this.files[0];
    var url = URL.createObjectURL(file);
    $(this).closest(".row").find(".preview_img").attr("src", url);
  });
  
  ShowAllProduct();
  function ShowAllProduct(){
    $.ajax({
      url: "action.php",
      type: "POST",
      data: {action:"viewproduct"},
      success:function(response){
      //console.log(response);
    $('#showProduct').html(response);
    $("table").DataTable({
      order: [0, 'desc'],
      initComplete: function () {
                    $('#showProduct .dataTables_filter').css({ 'float': 'right', 'text-align': 'right' });
                    $('#showProduct .dataTables_length').css('float', 'left');
                    $('#showProduct .dataTables_wrapper .row').css({
                        'display': 'flex',
                        'justify-content': 'space-between',
                        'align-items': 'center',
                    });
                }
    });
      }
    });
  }

// insert //
$("#insertproduct").click(function(e){
    e.preventDefault();
     var formData = new FormData($("#form-data")[0]);
     formData.append('action', 'insertproduct');
    $.ajax({
            url: 'upload_image.php', // PHP file that handles the upload
            type: 'POST',
            data: formData,
            contentType: false, // Prevent jQuery from overriding content type
            processData: false, // Prevent jQuery from processing the data
            success: function(response) {
              console.log(response);
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: response
                });
                $('#addcategory').modal('hide'); // Hide modal after successful upload
                $('#form-data')[0].reset(); // Reset form fields
                ShowAllProduct();
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Upload Failed',
                    text: 'There was an error uploading the image.'
                });
            }
        });
      });

    // Fetch product details and populate the edit modal
    $("body").on("click", ".editBtn", function(e) {
        e.preventDefault();
        var editt_id = $(this).attr('id');
        $.ajax({
            url: "action.php",
            type: "POST",
            data: { editt_id: editt_id },
            success: function(response) {
                var data = JSON.parse(response);
                $("#id").val(data.productID);
                $("#editbrand").val(data.brandID);
                $("#editdesc").val(data.productdesc);
                $("#editcategory").val(data.catID);
                $("#editunit").val(data.unitID);
                $("#editprice").val(data.unitPrice);
                $("#editisvatable").val(data.isVatable);
                $("#editisactive").val(data.isActive);

                // Set the current image preview
                if (data.image) {
                    $("#edit-preview-img").attr("src", "uploads/image/" + data.image);
                } else {
                    $("#edit-preview-img").attr("src", "assets/img/noimg.jpg");
                }
            }
        });
    });

    // Handle the update form submission
    $("#updateproduct").click(function(e) {
        e.preventDefault();
        var formData = new FormData($("#edit-form-data")[0]);
        formData.append('action', 'updateproduct');

        $.ajax({
            url: 'upload_image.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: response
                });
                $('#editcategory').modal('hide');
                $('#edit-form-data')[0].reset();
                ShowAllProduct();
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Update Failed',
                    text: 'There was an error updating the product.'
                });
            }
        });
    });

//delete
  // delete //
  $("body").on("click", ".deletebtn", function(e){
    e.preventDefault();
    var tr =  $(this).closest('tr');
    dell_id = $(this).attr('id');
    Swal.fire({
      title: 'Are you sure you want to delete this data',
      type: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelBuutonColor: '#d33',
      confirmButtonText: 'Yes, Delete it!'
    }).then((result) => {
      if (result.value){
        $.ajax({
      url:"action.php",
      type:"POST",
      data:{dell_id:dell_id},
      success:function(response){
        tr.css('background-color','#ff6666');
        Swal.fire(
          'Deleted',
          'Product deleted successfully',
          'success'
        )
        ShowAllProduct();
      }
    });
  }
    });


    });
});
</script>
</body>