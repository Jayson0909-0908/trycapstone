<?php
include('header.php');
include('sidebar.php');
include('footer.php');
?>

<body>

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Brand <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addcategory" style="float:right">Insert New Brand</button></h1>
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
            <div class="card-body" id="showBrand" name="showBrand">
              <h5 class="card-title">Brand</h5>
       
            </div>
          </div>

        </div>
      </div>
    </section>
    <style>
    /* Scoped CSS */
    #showBrand .dataTables_filter label {
        display: flex;
        align-items: center;
        gap: 10px; /* Adjust space between 'Search' and input */
    }
    

    #showBrand .dataTables_filter {
        float: right; /* Align the search bar to the right */
        text-align: right;
    }

    #showBrand .dataTables_length {
        float: left; /* Align the "Show entries" dropdown to the left */
    }

    #showBrand .dataTables_wrapper .row {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
</style>

  </main><!-- End #main -->



  <!-- addmodal -->
  <div class="modal fade" id="addcategory" tabindex="-1">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                    <h4 class="modal-title">Add Brand</h4>
                    </div>
                    <div class="modal-body">
                    <form action="" method="post" id="form-data">

                    <div class="form-group">
                    <label for="brandname">Brand Name</label>
                    <input type="text" name="brandname" id="brandname" class="form-control" autocomplete="off" required>
                </div> 
                
            <div class="modal-footer">

            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
            <div class="field">        
            <input type="submit" class="btn btn-primary" id="insertbrand" name="insertbrand" value="Save" style="float:right" required>
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
                    <h4 class="modal-title">Edit brand</h4>
                    </div>
                    <div class="modal-body">
                    <form action="" method="post" id="edit-form-data">
                    <input type="hidden" name="id" id="id">
                    <div class="form-group">
                    <label for="editbrandname">Brand Name</label>
                    <input type="text" name="editbrandname" id="editbrandname" class="form-control" autocomplete="off" required>
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
            <input type="submit" class="btn btn-primary" id="updatebrand" name="updatebrand" value="Save" style="float:right" required>
          </div>
        </div>
      </form>
       </div>
                    
      </div>
       </div>
      </div><!-- End add Modal-->

  <script type="text/javascript">
  $(document).ready(function(){
  
  ShowAllBrand();
  function ShowAllBrand(){
    $.ajax({
      url: "action.php",
      type: "POST",
      data: {action:"viewbrand"},
      success:function(response){
    //    console.log(response);
    $('#showBrand').html(response);
    $("table").DataTable({
      order: [0, 'desc'],
      initComplete: function () {
                    $('#showBrand .dataTables_filter').css({ 'float': 'right', 'text-align': 'right' });
                    $('#showBrand .dataTables_length').css('float', 'left');
                    $('#showBrand .dataTables_wrapper .row').css({
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
$("#insertbrand").click(function(e) {
    if ($("#form-data")[0].checkValidity()) {
        e.preventDefault();
        $.ajax({
            url: "action.php",
            type: "POST",
            data: $("#form-data").serialize() + "&action=insertbrand",
            success: function(response) {
                response = JSON.parse(response);
                if (response.status === 'error') {
                    Swal.fire({
                        title: 'Error',
                        text: response.message,
                        icon: 'error'
                    });
                } else if (response.status === 'success') {
                    Swal.fire({
                        title: 'Success',
                        text: response.message,
                        icon: 'success'
                    });
                    $("#addcategory").modal('hide');
                    $("#form-data")[0].reset();
                    ShowAllBrand();
                }
            }
        });
    }
});

$("body").on("click", ".editBtn", function (e) {
    e.preventDefault();
    let edit_idbrand = $(this).attr('id');
    $.ajax({
        url: "action.php",
        type: "POST",
        data: { action: "edit", edit_idbrand: edit_idbrand },
        success: function (response) {
            try {
                let data = JSON.parse(response);
                if (data.status === 'error') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message,
                    });
                } else {
                    $("#id").val(data.brandID);
                    $("#editbrandname").val(data.brandname);
                    $("#editisactive").val(data.isActive);
                    $("#editcategory").modal('show');
                }
            } catch (error) {
                console.error("Invalid JSON response:", response);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Unexpected response from server.',
                });
            }
        },
        error: function () {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to communicate with the server.',
            });
        },
    });
});


//update
$("#updatebrand").click(function(e){
    if($("#edit-form-data")[0].checkValidity()){
      e.preventDefault();
      $.ajax({
      url: "action.php",
      type: "POST",
      data: $("#edit-form-data").serialize()+"&action=updatebrand",
      success:function(response){
       console.log(response);
       Swal.fire({  
       title: 'Brand updated successfully!',
       type: 'success'
       })
       $("#editcategory").modal('hide');
       $("#edit-form-data")[0].reset();
       ShowAllBrand();
      }
      });
    }
  });


  // delete //
  $("body").on("click", ".deletebtn", function(e){
    e.preventDefault();
    var tr =  $(this).closest('tr');
    del_idbrand = $(this).attr('id');
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
      data:{del_idbrand:del_idbrand},
      success:function(response){
        tr.css('background-color','#ff6666');
        Swal.fire(
          'Deleted',
          'Brand deleted successfully',
          'success'
        )
        ShowAllBrand();
      }
    });
  }
    });


    });
  });





</script>


</body>

</html>