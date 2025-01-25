<?php
include('header.php');
include('sidebar.php');
include('footer.php');
?>

<body>

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Unit<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addcategory" style="float:right">Insert New Unit</button></h1>
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
            <div class="card-body" id="showUnit" name="showUnit">
              <h5 class="card-title">Unit</h5>
       
            </div>
          </div>

        </div>
      </div>
    </section>

    <style>
    /* Scoped CSS */
    #showUnit .dataTables_filter label {
        display: flex;
        align-items: center;
        gap: 5px; /* Adjust space between 'Search' and input */
    }

    #showUnit .dataTables_filter {
        float: right; /* Align the search bar to the right */
        text-align: right;
    }

    #showUnit .dataTables_length {
        float: left; /* Align the "Show entries" dropdown to the left */
    }

    #showUnit .dataTables_wrapper .row {
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
                    <h4 classUnittitle">Add Unit</h4>
                    </div>
                    <div class="modal-body">
                    <form action="" method="post" id="form-data">

                    <div class="form-group">
                    <label for="unitName">Unit Name</label>
                    <input type="text" name="unitName" id="unitName" class="form-control" autocomplete="off" required>
                </div> 
                
            <div class="modal-footer">

            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
            <div class="field">        
            <input type="submit" class="btn btn-primary" id="insertunit" name="insertunit" value="Add" style="float:right" required>
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
                    <h4 class="modal-title">Edit Unit</h4>
                    </div>
                    <div class="modal-body">
                    <form action="" method="post" id="edit-form-data">
                    <input type="hidden" name="id" id="id">
                    <div class="form-group">
                    <label for="editunitName">Unit Name</label>
                    <input type="text" name="editunitName" id="editunitName" class="form-control" autocomplete="off" required>
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
            <input type="submit" class="btn btn-primary" id="updateunit" name="updateunit" value="Save" style="float:right" required>
          </div>
        </div>
      </form>
       </div>
                    
      </div>
       </div>
      </div><!-- End add Modal-->

  <script type="text/javascript">
  $(document).ready(function(){
  
  ShowAllUnit();
  function ShowAllUnit(){
    $.ajax({
      url: "action.php",
      type: "POST",
      data: {action:"viewunit"},
      success:function(response){
    //    console.log(response);
    $('#showUnit').html(response);
    $("table").DataTable({
      order: [0, 'desc'],
      initComplete: function () {
                    $('#showUnit .dataTables_filter').css({ 'float': 'right', 'text-align': 'right' });
                    $('#showUnit .dataTables_length').css('float', 'left');
                    $('#showUnit .dataTables_wrapper .row').css({
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
  $("#insertunit").click(function(e){
    if($("#form-data")[0].checkValidity()){
      e.preventDefault();
      $.ajax({
      url: "action.php",
      type: "POST",
      data: $("#form-data").serialize()+"&action=insertunit",
      success:function(response){
      console.log(response);
       Swal.fire({  
       title: 'Unit added successfully!',
       type: 'success'
       })
       $("#addcategory").modal('hide');
       $("#form-data")[0].reset();
       ShowAllUnit();
      }
      });
    }
  });
// Edit //
  $("body").on("click", ".editBtn", function(e){
    e.preventDefault();
    editunit_id = $(this).attr('id');
    $.ajax({
      url:"action.php",
      type:"POST",
      data:{editunit_id:editunit_id},
      success:function(response){
        data = JSON.parse(response);
        console.log(data);
        $("#id").val(data.unitID);
        $("#editunitName").val(data.unitname);
        $("#editisactive").val(data.isActive);
        $("#editisdeleted").val(data.isDeleted);
      }
    });
  });

//update
$("#updateunit").click(function(e){
    if($("#edit-form-data")[0].checkValidity()){
      e.preventDefault();
      $.ajax({
      url: "action.php",
      type: "POST",
      data: $("#edit-form-data").serialize()+"&action=updateunit",
      success:function(response){
      // console.log(response);
       Swal.fire({  
       title: 'Unit updated successfully!',
       type: 'success'
       })
       $("#editcategory").modal('hide');
       $("#edit-form-data")[0].reset();
       ShowAllUnit();
      }
      });
    }
  });


  // delete //
  $("body").on("click", ".deletebtn", function(e){
    e.preventDefault();
    var tr =  $(this).closest('tr');
    delunit_id = $(this).attr('id');
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
      data:{delunit_id:delunit_id},
      success:function(response){
        tr.css('background-color','#ff6666');
        Swal.fire(
          'Deleted',
          'Unit deleted successfully',
          'success'
        )
        ShowAllUnit();
      }
    });
  }
    });


    });
  });





</script>


</body>

</html>