<?php
include('header.php');
include('sidebar.php');
include('footer.php');
?>

<body>

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Users<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addcategory" style="float:right">Insert New User</button></h1>
      <nav>
        <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="index.php">Home</a></li>
              <li class="breadcrumb-item">Tables</li>
              <li class="breadcrumb-item active">Data</li>
            </ol>
          </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body" id="showUser" name="showUser">
       
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
                    <h4 class="modal-title">Add User</h4>
                    </div>
                    <div class="modal-body">
                    <form action="" id="form-data" enctype="multipart/form-data">
                    <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" class="form-control" autocomplete="off" required />
                </div> 
                    <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" class="form-control" autocomplete="off" required>
                </div> 
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" class="form-control" minlength="8" autocomplete="off" required>
                </div> 
                <div class="form-group">
                    <label for="contact">Contact Number</label>
                    <input type="tel" name="contact" id="contact" class="form-control" pattern="09[0-9]{9}" placeholder="09XX-XXX-XXXX" maxlength="11" autocomplete="off" required>
                    <small id="contactError" style="color: red; display: none;">Contact number must be 11 digits and start with 09.</small>
                </div>
                <div class="form-group">
                    <label for="position">Position</label>
                   <select name="position" id="position" class="form-control" required>
                <option value="Admin">Admin</option>
                <option value="Cashier">Cashier</option>
                    </select>
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
            <input type="submit" class="btn btn-primary" id="insertuser" name="insertuser" value="Add" style="float:right" required>
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
                    <h4 class="modal-title">Edit User</h4>
                    </div>
                    <div class="modal-body">
                    <form action="" method="post" id="edit-form-data" enctype="multipart/form-data">
                    <input type="hidden" name="id" id="id">
                    <div class="form-group">
                    <label for="editname">Full name</label>
                    <input type="text" id="editname" name="editname" class="form-control" autocomplete="off" required />
                </div> 
                    <div class="form-group">
                    <label for="editusername">Username</label>
                    <input type="email" name="editusername" id="editusername" class="form-control" autocomplete="off" required>
                </div> 
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="editpassword" id="editpassword" class="form-control" minlength="8" autocomplete="off" required>
                </div> 
                <div class="form-group">
                <label for="contact">Contact Number</label>
                <input type="tel" name="editcontact" id="editcontact" class="form-control" pattern="09[0-9]{9}" placeholder="09XX-XXX-XXXX" maxlength="11" autocomplete="off" required>
                <small id="contactError" style="color: red; display: none;">Contact number must be 11 digits and start with 09.</small>
                </div>
                <div class="form-group">
                    <label for="position">Position</label>
                   <select name="editposition" id="editposition" class="form-control" required>
                <option value="Admin">Admin</option>
                <option value="Cashier">Cashier</option>
                    </select>
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
                   <label for="editisactive">Status</label>
                   <select name="editisactive" id="editisactive" class="form-control" required>
                   <option value="1">Active</option>
                   <option value="0">Inactive</option>
                   </select>
                    </div>
            <div class="modal-footer">

            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
            <div class="field">        
            <input type="submit" class="btn btn-primary" id="updateuser" name="updateuser" value="Update" style="float:right" required>
          </div>
        </div>
      </form>
       </div>
                    
      </div>
       </div>
      </div><!-- End add Modal-->
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
  <script type="text/javascript">
  $(document).ready(function(){
  
  $("input.image").change(function() {
  var file = this.files[0];
  var url = URL.createObjectURL(file);
  $(this).closest(".row").find(".preview_img").attr("src", url);
});
  ShowAllUser();
  function ShowAllUser(){
    $.ajax({
      url: "action.php",
      type: "POST",
      data: {action:"viewuser"},
      success:function(response){
    //    console.log(response);
    $('#showUser').html(response);
    $("table").DataTable({
      order: [0, 'desc']
    });
      }
    });
  }
// insert //
 // Real-time validation for contact input
document.getElementById("contact").addEventListener("input", function () {
    const contactInput = this;
    const errorElement = document.getElementById("contactError");
    const regex = /^09\d{9}$/; // Must start with "09" and have 11 digits total

    if (contactInput.value.length === 11 && !regex.test(contactInput.value)) {
        errorElement.style.display = "block";
    } else if (contactInput.value.length !== 11) {
        errorElement.style.display = "none"; // Only show errors after 11 characters are entered
    } else {
        errorElement.style.display = "none";
    }
});

// Prevent form submission if invalid when the modal form is submitted
// Insert User Form Submission
$("#insertuser").click(function (e) {
    e.preventDefault(); // Prevent default form submission

    // Validation fields
    const contactInput = document.getElementById("contact");
    const nameInput = document.getElementById("name");
    const usernameInput = document.getElementById("username");
    const passwordInput = document.getElementById("password");
    const positionInput = document.getElementById("position");

    const regex = /^09\d{9}$/; // Contact number validation
    const passwordRegex = /^(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}$/; // Password validation

    // Validate form inputs
    if (!nameInput.value || !usernameInput.value || !passwordInput.value || !positionInput.value || !contactInput.value) {
        Swal.fire({
            icon: 'error',
            title: 'Missing Fields',
            text: 'All fields are required. Please fill out the form completely.'
        });
        return;
    }

    if (!regex.test(contactInput.value)) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid Contact Number',
            text: 'Please provide a valid contact number starting with 09 and 11 digits long.'
        });
        return;
    }

    if (!passwordRegex.test(passwordInput.value)) {
        Swal.fire({
            icon: 'error',
            title: 'Weak Password',
            text: 'Password must have at least 8 characters, including one uppercase letter and one number.'
        });
        return;
    }

    // Proceed with AJAX if validation is successful
    var formData = new FormData($("#form-data")[0]);
    formData.append('action', 'insertuser');

    $.ajax({
        url: 'upload_image.php', // File handling the PHP logic
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
            response = JSON.parse(response);
            if (response.status === 'error') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message
                });
            } else if (response.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'User Added',
                    text: response.message
                });
                $("#addcategory").modal('hide');
                $("#form-data")[0].reset();
                ShowAllUser();
            }
        },
        error: function () {
            Swal.fire({
                icon: 'error',
                title: 'Server Error',
                text: 'There was a problem processing your request. Please try again later.'
            });
        }
    });
});


$("body").on("click", ".editBtn", function(e) {
    e.preventDefault();
    var edituser_id = $(this).attr('id');

    if (!edituser_id) {
        console.error("Edit user ID is missing.");
        return;
    }

    $.ajax({
        url: "action.php",
        type: "POST",
        data: { edituser_id: edituser_id },
        success: function(response) {
            console.log("Raw response:", response);
            try {
                var data = JSON.parse(response);
                console.log("Parsed data:", data);

                if (data.error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.error,
                    });
                    return;
                }

                $("#id").val(data.userID);
                $("#editname").val(data.name);
                $("#editusername").val(data.username);
                $("#editpassword").val(data.pw);
                $("#editcontact").val(data.contact);
                $("#editposition").val(data.position);
                $("#editisactive").val(data.isActive);
                $("#editisdeleted").val(data.isDeleted);

                if (data.image) {
                    $("#edit-preview-img").attr("src", "uploads/userimage/" + data.image);
                } else {
                    $("#edit-preview-img").attr("src", "assets/img/noimg.jpg");
                }
            } catch (e) {
                console.error("Error parsing JSON:", e);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Invalid response from server. Check console for details.',
                });
            }
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error:", status, error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to fetch user details. Check console for more info.',
            });
        }
    });
});


   // Handle the update form submission
  // Handle the update form submission with validation
// Update User Form Submission
$("#updateuser").click(function (e) {
    e.preventDefault(); // Prevent the default form submission

    const nameInput = $("#editname");
    const usernameInput = $("#editusername");
    const passwordInput = $("#editpassword");
    const positionInput = $("#editposition");
    const contactInput = $("#editcontact");
    const errorElement = $("#contactError"); // Error element for contact validation
    const userId = $("#id").val(); // Hidden input for user ID

    const regex = /^09\d{9}$/; // Validation rule for contact number
    const passwordRegex = /^(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}$/; // Password with at least one capital letter, one number, and 8+ characters

    // Check if all required fields are filled
    if (!nameInput.val() || !usernameInput.val() || !passwordInput.val() || !positionInput.val() || !contactInput.val()) {
        Swal.fire({
            icon: 'error',
            title: 'All fields are required',
            text: 'Please fill in all the required fields before updating.'
        });
        return;
    }

    // Check if contact number is valid
    if (!regex.test(contactInput.val())) {
        errorElement.show();
        contactInput.focus();
        return;
    } else {
        errorElement.hide();
    }

    // Check if password meets the requirements
    if (!passwordRegex.test(passwordInput.val())) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid Password',
            text: 'Password must be at least 8 characters long, contain at least one capital letter, and one number.'
        });
        passwordInput.focus();
        return;
    }

    // Check if the username already exists
    $.ajax({
        url: 'upload_image.php',
        type: 'POST',
        data: { action: 'checkUsername', username: usernameInput.val(), id: userId },
        success: function (response) {
            console.log("Raw response:", response); // Debug the response
            try {
                const res = JSON.parse(response);

                if (res.status === 'error') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Username already exists',
                        text: res.message
                    });
                    usernameInput.focus();
                } else {
                    // Proceed with AJAX submission if username is unique
                    var formData = new FormData($("#edit-form-data")[0]);
                    formData.append('action', 'updateuser');

                    $.ajax({
                        url: 'upload_image.php',
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function (response) {
                            try {
                                const res = JSON.parse(response);
                                if (res.status === 'success') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'User updated successfully!',
                                        text: res.message
                                    });
                                    $('#editcategory').modal('hide');
                                    $('#edit-form-data')[0].reset();
                                    ShowAllUser();
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Update Failed',
                                        text: res.message
                                    });
                                }
                            } catch (e) {
                                console.error("JSON parse error:", e);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Invalid server response. Please try again.'
                                });
                            }
                        },
                        error: function () {
                            Swal.fire({
                                icon: 'error',
                                title: 'Update Failed',
                                text: 'There was an error updating the user.'
                            });
                        }
                    });
                }
            } catch (e) {
                console.error("Invalid JSON response", e);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Invalid server response. Please try again.'
                });
            }
        },
        error: function () {
            Swal.fire({
                icon: 'error',
                title: 'Validation Failed',
                text: 'There was an error validating the username.'
            });
        }
    });
});



  // delete //
  $("body").on("click", ".deletebtn", function(e){
    e.preventDefault();
    var tr =  $(this).closest('tr');
    deluser_id = $(this).attr('id');
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
      data:{deluser_id:deluser_id},
      success:function(response){
        console.log(response);
        tr.css('background-color','#ff6666');
        Swal.fire(
          'Deleted',
          'User deleted successfully',
          'success'
        )
        ShowAllUser();
      }
    });
  }
    });


    });
  });





</script>


</body>

</html>