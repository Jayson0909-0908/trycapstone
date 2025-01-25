<?php
session_start();
require_once 'connection.php';
$db = new database();
    $uname = htmlspecialchars(trim($_POST['username']));
    $password = sha1(trim($_POST['password']));
    $count = $db->loginuser($uname,$password);
    if($count->rowCount()==0){
       echo "error";
    }
    else{
          $row=$count->fetch();
          $status = $row['position']=='Admin'?"1":"0";
          if($status == 1){
            $_SESSION['UID']=$row['userID'];
            $_SESSION['username']=$uname;
            $_SESSION['name']=$row['name'];
            $_SESSION['position']=$row['position'];
            $_SESSION['image']=$row['image'];
            echo "successs";
          }
          else{
            $_SESSION['UID']=$row['userID'];
            $_SESSION['username']=$uname;
            $_SESSION['name']=$row['name'];
            $_SESSION['position']=$row['position'];
            $_SESSION['image']=$row['image'];
            echo "success";
          }
    }
  

?>
