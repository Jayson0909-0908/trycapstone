<?php
include('header.php');
include('sidebar.php');
include('footer.php');
?>

<body>
  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Category<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addcategory" style="float:right">Insert New Category</button></h1>
      <nav>
        <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="index.html">Home</a></li>
              <li class="breadcrumb-item">Tables</li>
              <li class="breadcrumb-item active">Category</li>
            </ol>
          </nav>
        </div><!-- End Page Title -->

        <section class="section">
          <div class="row">
            <div class="col-lg-12">

              <div class="card">
                <div class="card-body" id="showCategory" name="showCategory">
                  <h5 class="card-title">Data tables</h5>
          
                </div>
              </div>

        </div>
      </div>
    </section>

  </main><!-- End #main -->
  <style>
    /* Scoped CSS */
    #showCategory .dataTables_filter label {
        display: flex;
        align-items: center;
        gap: 5px; /* Adjust space between 'Search' and input */
    }

    #showCategory .dataTables_filter {
        float: right; /* Align the search bar to the right */
        text-align: right;
    }

    #showCategory .dataTables_length {
        float: left; /* Align the "Show entries" dropdown to the left */
    }

    #showCategory .dataTables_wrapper .row {
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

  <!-- addmodal -->
  <div class="modal fade" id="addcategory" tabindex="-1">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                    <h4 class="modal-title">Add Category</h4>
                    </div>
                    <div class="modal-body">
                    <form action="" id="form-data" enctype="multipart/form-data">

                    <div class="form-group">
                    <label for="categName">Category Name</label>
                    <input type="text" name="categName" id="categName" class="form-control" autocomplete="off" required>
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
                
            <div class="modal-footer">

            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
            <div class="field">        
            <input type="submit" class="btn btn-primary" id="insertcategory" name="insertcategory" value="Save" style="float:right" required>
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
                    <h4 class="modal-title">Edit Category</h4>
                    </div>
                    <div class="modal-body">
                    <form action="" method="post" id="edit-form-data" enctype="multipart/form-data">
                    <input type="hidden" name="id" id="id">
                    <div class="form-group">
                    <label for="editcategName">Category Name</label>
                    <input type="text" name="editcategName" id="editcategName" class="form-control" autocomplete="off" required>
                </div> 
                <div class="row mb-3">
                        <label for="fileToUpload">Upload Image</label>
                        <div class="col-4">
                            <img class="preview_img" src="assets/img/noimg.jpg" id="edit-preview-img" />
                        </div>
                        <div class="col-8">
                            <div class="file-upload text-secondary">
                                <input type="file" class="image" name="image" id="image" accept="image/*">
                                <span class="fs-2 fw-1">Choose file...</span>
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
            <input type="submit" class="btn btn-primary" id="updatecategory" name="updatecategory" value="Save" style="float:right" required>
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
  ShowAllCategory();
  function ShowAllCategory() {
    $.ajax({
        url: "action.php",
        type: "POST",
        data: { action: "view" },
        success: function (response) {
            $('#showCategory').html(response);
            $("table").DataTable({
                order: [0, 'desc'],
                initComplete: function () {
                    $('#showCategory .dataTables_filter').css({ 'float': 'right', 'text-align': 'right' });
                    $('#showCategory .dataTables_length').css('float', 'left');
                    $('#showCategory .dataTables_wrapper .row').css({
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
$("#insertcategory").click(function (e) {
    e.preventDefault();
    var formData = new FormData($("#form-data")[0]);
    formData.append('action', 'insertcategory');

    $.ajax({
        url: 'upload_image.php', // PHP file that handles the upload
        type: 'POST',
        data: formData,
        contentType: false, // Prevent jQuery from overriding content type
        processData: false, // Prevent jQuery from processing the data
        success: function (response) {
            console.log("Server response:", response);
            try {
                var data = JSON.parse(response); // Parse the JSON response

                if (data.status === "success") {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: data.message
                    });
                    $('#addcategory').modal('hide'); // Hide modal after successful upload
                    $('#form-data')[0].reset(); // Reset form fields
                    ShowAllCategory(); // Refresh category list
                } else if (data.status === "error") {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message
                    });
                }
            } catch (e) {
                console.error("Error parsing response:", e);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Unexpected error occurred. Check console for details.'
                });
            }
        },
        error: function () {
            Swal.fire({
                icon: 'error',
                title: 'Upload Failed',
                text: 'There was an error uploading the category.'
            });
        }
    });
});


// Edit //
  $("body").on("click", ".editBtn", function(e){
    e.preventDefault();
    var edit_id = $(this).attr('id');
    $.ajax({
      url:"action.php",
      type:"POST",
      data:{edit_id:edit_id},
      success:function(response){ 
        console.log(response);
        var data = JSON.parse(response);
        $("#id").val(data.categID);
        $("#editcategName").val(data.categname);
        $("#editisactive").val(data.isActive);
        $("#editisdeleted").val(data.isDeleted);
        if (data.image) {
                    $("#edit-preview-img").attr("src", "uploads/categoryimage/" + data.image);
                } else {
                    $("#edit-preview-img").attr("src", "assets/img/noimg.jpg");
                }
      }
    });
  });

    // Handle the update form submission
    $("#updatecategory").click(function(e) {
        e.preventDefault();
        var formData = new FormData($("#edit-form-data")[0]);
        formData.append('action', 'updatecategory');

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
                ShowAllCategory();
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


  // delete //
  $("body").on("click", ".deletebtn", function(e){
    e.preventDefault();
    var tr =  $(this).closest('tr');
    del_id = $(this).attr('id');
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
      data:{del_id:del_id},
      success:function(response){
        tr.css('background-color','#ff6666');
        Swal.fire(
          'Deleted',
          'Category deleted successfully',
          'success'
        )
        ShowAllCategory();
      }
    });
  }
    });


    });
  });





</script>


</body>

</html>