<?php
  $page_title = 'Home Page';
  require_once('../../modelo/load.php');
  if (!$session->isUserLoggedIn(true)) { redirect('../../index.php', false);}
?>
<?php include_once('../layouts/header.php'); ?>
<div class="row">
   <div class="col-md-12">
      <?php echo display_msg($msg); ?>
   </div>
   <div class="col-md-12">
      <div class="panel">
         <div class="jumbotron text-center">
            <h1>Bienvenido a Pakán</h1>
            <!--img src="uploads/products/LONA.jpg" height="500" width="650" alt=""-->         
         </div>
      </div>
   </div>
</div>
<?php include_once('../layouts/footer.php'); ?>
