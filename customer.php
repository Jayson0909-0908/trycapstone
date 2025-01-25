<?php
include('header.php');
include('sidebar.php');
include('footer.php');
?>

<body>
  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Customer Details</h1>
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
            <div class="card-body" id="showCustomer" name="showCustomer">
              <h5 class="card-title">Sales table</h5>
       
            </div>
          </div>

        </div>
      </div>
    </section>

  </main><!-- End #main -->

  <!-- addmodal -->
  <div class="modal fade" id="addcategory" tabindex="-1">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                    <h4 class="modal-title">Add category</h4>
                    </div>
                    <div class="modal-body">
                    <form action="" method="post" id="form-data">

                    <div class="form-group">
                    <label for="categName">Category Name</label>
                    <input type="text" name="categName" id="categName" class="form-control" autocomplete="off" required>
                </div> 
                
            <div class="modal-footer">

            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
            <div class="field">        
            <input type="submit" class="btn btn-primary" id="insert" name="insert" value="Add" style="float:right" required>
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
                    <h4 class="modal-title">Edit category</h4>
                    </div>
                    <div class="modal-body">
                    <form action="" method="post" id="edit-form-data">
                    <input type="hidden" name="id" id="id">
                    <div class="form-group">
                    <label for="editcategName">Category Name</label>
                    <input type="text" name="editcategName" id="editcategName" class="form-control" autocomplete="off" required>
                </div> 
                <div class="form-group">
                    <label for="editisactive">IsActive</label>
                    <input type="text" name="editisactive" id="editisactive" class="form-control" autocomplete="off" required>
                    </div>
                    <div class="form-group">
                    <label for="editisdeleted">isDeleted</label>
                    <input type="text" name="editisdeleted" id="editisdeleted" class="form-control" autocomplete="off" required>
              </div>
            <div class="modal-footer">

            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
            <div class="field">        
            <input type="submit" class="btn btn-primary" id="update" name="update" value="Update" style="float:right" required>
          </div>
        </div>
      </form>
       </div>
                    
      </div>
       </div>
      </div><!-- End add Modal-->

  <script type="text/javascript">
  $(document).ready(function(){
  
  ShowAllCategory();
  function ShowAllCategory(){
    $.ajax({
      url: "action.php",
      type: "POST",
      data: {action:"viewcustomer"},
      success:function(response){
    //    console.log(response);
    $('#showCustomer').html(response);
    $("table").DataTable({
      order: [1, 'desc']
    });
      }
    });
  }
// insert //
  $("#insert").click(function(e){
    if($("#form-data")[0].checkValidity()){
      e.preventDefault();
      $.ajax({
      url: "action.php",
      type: "POST",
      data: $("#form-data").serialize()+"&action=insert",
      success:function(response){
      console.log(response);
       Swal.fire({  
       title: 'Category added successfully!',
       type: 'success'
       })
       $("#addcategory").modal('hide');
       $("#form-data")[0].reset();
       ShowAllCategory();
      }
      });
    }
  });
// Edit //
  $("body").on("click", ".editBtn", function(e){
    e.preventDefault();
    edit_id = $(this).attr('id');
    $.ajax({
      url:"action.php",
      type:"POST",
      data:{edit_id:edit_id},
      success:function(response){ 
        console.log(response);
        data = JSON.parse(response);
        console.log(data);
        $("#id").val(data.categID);
        $("#editcategName").val(data.categname);
        $("#editisactive").val(data.isActive);
        $("#editisdeleted").val(data.isDeleted);
      }
    });
  });

//update
$("#update").click(function(e){
    if($("#edit-form-data")[0].checkValidity()){
      e.preventDefault();
      $.ajax({
      url: "action.php",
      type: "POST",
      data: $("#edit-form-data").serialize()+"&action=update",
      success:function(response){
       console.log(response);
       Swal.fire({  
       title: 'Category updated successfully!',
       type: 'success'
       })
       $("#editcategory").modal('hide');
       $("#edit-form-data")[0].reset();
       ShowAllCategory();
      }
      });
    }
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