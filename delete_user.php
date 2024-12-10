<?php
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
   page_require_level(1);

   $request = $_SERVER['REQUEST_URI'];
if (substr($request, -4) == '.php') {
    $new_url = substr($request, 0, -4);
    header("Location: $new_url", true, 301);
    exit();
}
?>
<?php
  $delete_id = delete_by_id('users',(int)$_GET['id']);
  if($delete_id){
      $session->msg("s","User deleted.");
      redirect('users.php');
  } else {
      $session->msg("d","User deletion failed Or Missing Prm.");
      redirect('users.php');
  }
?>
