
 <!-- ======= Sidebar ======= -->
 <aside id="sidebar" class="sidebar">


 <?php 
  $status = $_SESSION['position']=='Admin'?"1":"0";
  if($status == 1){
    echo "<ul class='sidebar-nav' id='sidebar-nav'>

  <li class='nav-item'>
    <a class='nav-link collapsed ' href='index.php'>
      <i class='bi bi-grid'></i>
      <span>Dashboard</span>
    </a>
  </li><!-- End Dashboard Nav -->
  
  <li class='nav-item'>
    <a class='nav-link collapsed' data-bs-target='#components-nav' data-bs-toggle='collapse' href='#'>
      <i class='bi bi-menu-button-wide'></i><span>Items</span><i class='bi bi-chevron-down ms-auto'></i>
    </a>
    <ul id='components-nav' class='nav-content collapse ' data-bs-parent='#sidebar-nav'>
    <li>
        <a href='brand.php'>
          <i class='bi bi-circle'></i><span>Brand</span>
        </a>
      </li>
      <li>
        <a href='category.php'>
          <i class='bi bi-circle'></i><span>Category</span>
        </a>
      </li>
      <li>
        <a href='product.php'>
          <i class='bi bi-circle'></i><span>Product</span>
        </a>
      </li>
      <li>
        <a href='unit.php'>
          <i class='bi bi-circle'></i><span>Unit</span>
        </a>
      </li>
    </ul>
  </li><!-- End items Nav -->


  <li class='nav-item'>
    <a class='nav-link collapsed' href='transactionproduct.php'>
      <i class='bi bi-pc-display-horizontal'></i>
      <span>POS</span>
    </a>
  </li><!-- End Profile Page Nav -->
  
  <li class='nav-item'>
    <a class='nav-link collapsed' href='sales.php'>
      <i class='bi bi-receipt'></i>
      <span>Sales</span>
    </a>
  </li><!-- End Profile Page Nav -->


  
    <li class='nav-item'>
    <a class='nav-link collapsed' href='reports.php'>
      <i class='bi bi-card-list'></i>
      <span>Reports</span>
    </a>
  </li><!-- End Profile Page Nav -->
  
   <!-- invoices -->
  <!-- <li class='nav-item'>
    <a class='nav-link collapsed' href='users-profile.html'>
      <i class='bi bi-person'></i>
      <span>Invoices</span>
    </a>
  </li> -->

  <li class='nav-item'>
    <a class='nav-link collapsed' href='customer.php'>
      <i class='bi bi-person'></i>
      <span>Customer</span>
    </a>
  </li><!-- End Profile Page Nav -->

  <li class='nav-item'>
    <a class='nav-link collapsed' data-bs-target='#forms-nav' data-bs-toggle='collapse' href='#'>
    <i class='bi bi-sliders2'></i><span>Settings  </span><i class='bi bi-chevron-down ms-auto'></i>
    </a>
    <ul id='forms-nav' class='nav-content collapse ' data-bs-parent='#sidebar-nav'>
      <li>
        <a href='user.php'>
          <i class='bi bi-circle'></i><span>Staff and Admins</span>
        </a>
      </li>
    
    </ul>";
  }
  else{
    echo "<ul class='sidebar-nav' id='sidebar-nav'>

  <li class='nav-item'>
    <a class='nav-link collapsed ' href='index.php'>
      <i class='bi bi-grid'></i>
      <span>Dashboard</span>
    </a>
  </li><!-- End Dashboard Nav -->

  <li class='nav-item'>
    <a class='nav-link collapsed' href='transactionproduct.php'>
      <i class='bi bi-pc-display-horizontal'></i>
      <span>POS</span>
    </a>
  </li><!-- End Profile Page Nav -->
  
  <li class='nav-item'>
    <a class='nav-link collapsed' href='sales.php'>
      <i class='bi bi-receipt'></i>
      <span>Sales</span>
    </a>
  </li><!-- End Profile Page Nav -->";

  }

  ?>
</aside>


<!-- End Sidebar-->