<?php
session_start();
require_once 'connection.php';
$db = new database();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>K30 Cakes and Pastries && Bakery Supply Trading</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="assets/img/faviconn.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="assets/css/customstyle.css" rel="stylesheet">

  <!-- =======================================================
  * Template Name: NiceAdmin
  * Updated: Jan 29 2024 with Bootstrap v5.3.2
  * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body>

  <main>
    <div class="container">

      <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

              <div class="d-flex justify-content-center py-4">
                <a href="#" class="logo d-flex align-items-center w-auto">
                  <img src="assets/img/img/logo.png" alt="">
                  <span class="d-none d-lg-block">K30 Login</span>
                </a>
              </div><!-- End Logo -->

              <div class="card mb-3">

                <div class="card-body">

                  <div class="pt-4 pb-2">
                    <h5 class="card-title text-center pb-0 fs-4">Login to Your Account</h5>
                    <p class="text-center small">Enter your username & password to login</p>
                  </div>

                  <form class="row g-3 needs-validation" method="post" id="loginForm">

                    <div class="col-12">
                    <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" class="form-control" autocomplete="off" required>
                </div> 
                    </div>

                    <div class="col-12">
                    <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" class="form-control" autocomplete="off" required>
                    </div> 
                    </div>

                    <div class="col-12">
                      <button class="btn btn-primary w-100" type="submit" id="submit" name="submit">Login</button>
                    </div>
                  </form>

                </div>
                <?php
//}
                ?>
              </div>

            </div>
          </div>
        
        </div>

      </section>

    </div>
  </main><!-- End #main -->

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
  $(document).ready(function() {
    $('#loginForm').on('submit', function(e) {
        e.preventDefault();  // Prevent the default form submission

        var username = $('#username').val();
        var password = $('#password').val();
        console.log("Username:", username);
console.log("Password:", password);

        $.ajax({
            url: 'logincode.php',  // The PHP file that processes the login
            type: 'POST',
            data: { username: username, password: password },
            success: function(response) {
    console.log('Server Response:', response); // Log it here
    if (response.trim() === 'successs') { 
        window.location.href = 'index.php';
    } else if (response.trim() === 'success') {
        window.location.href = 'transactionproduct.php';
    } else {
        Swal.fire({
            title: 'Login Failed!',
            text: 'Incorrect username or password.',
            icon: 'error',
            confirmButtonText: 'Try Again'
        });
    }
},

        });
    });
});

</script>
</body>


</html>