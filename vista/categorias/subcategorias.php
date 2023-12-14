<?php
   require_once('../../modelo/load.php');
   $page_title = 'Lista de subcategorías';
   // Checkin What level user has permission to view this page
   page_require_level(1);
  
   $idCategoria = isset($_POST['idCategoria']) ? $_POST['idCategoria']:'';
   $catId = isset($_GET['idCat']) ? $_GET['idCat']:'';

   if(isset($_POST['agregar'])){
      $categoriaId = remove_junk($db->escape($_POST['categoriaId']));
      $req_fields = array('subCategoria');
      validate_fields($req_fields);
      if(empty($errors)){

         $subCategoria = remove_junk($db->escape($_POST['subCategoria']));
         $idCat = remove_junk($db->escape($_POST['categoriaId']));

         $subCatMax = buscaValorMaximo('subcategorias','idSubcategoria','idCategoria',$idCat);

         if ($subCatMax['valorMax'] == null)
            $idSubCategoria = 1;
         else
            $idSubCategoria = $subCatMax['valorMax'] + 1;

         $resultado = altaSubCategoria($idCat,$idSubCategoria,$subCategoria);

         if($resultado){
            $session->msg('s',"Subcategoría agregada exitosamente.");
            redirect('subcategorias.php?idCat='.$idCat, false);
         }else{
            $session->msg('d','Lo siento, falló el registro.');
            redirect('subcategorias.php?idCat='.$idCat, false);
         }
      }else{
         $idCategoria = $categoriaId;
         $session->msg("d", $errors);
         redirect('subcategorias.php?idCat='.$categoriaId,false);
      }
   }
 
   if ($idCategoria != ""){
      $subCategorias = buscaRegsPorCampo('subcategorias','idCategoria',$idCategoria);
      $categoria = find_by_id('categories',$idCategoria);
   }else{
      $subCategorias = buscaRegsPorCampo('subcategorias','idCategoria',$catId);
      $categoria = find_by_id('categories',$catId);
      $idCategoria = $catId;
   }
   $nomCategoria = $categoria['name'];
?>
<?php include_once('../layouts/header.php'); ?>

<script language="Javascript">

function foco(){
  document.form1.subcategoria.focus();
}

function subcategorias(){
  document.form1.action = "subcategorias.php";
  document.form1.submit();
}

</script>   

<!DOCTYPE html>
<html>
<head>
<title>Lista de subcategorías</title>
</head>

<body onload="foco();">
  <form name="form1" method="post" action="subcategorias.php">
     <br>
     <div class="row">
        <div class="col-md-9">
           <?php echo display_msg($msg); ?>
        </div>
        <div class="col-md-9">
           <div class="panel panel-default">
              <div class="panel-heading clearfix">
                 <div class="pull-right">
                    <div class="form-group">
                       <div class="col-md-6">
                          <div class="input-group">
                             <span class="input-group-addon">
                                <i class="glyphicon glyphicon-barcode"></i>
                             </span>
                             <input type="text" class="form-control" name="subCategoria" long="50" placeholder="Subcategoría">
                          </div>
                       </div>  
                       <a href="#" onclick="subcategorias();" class="btn btn-primary">Buscar</a> 
                       <button type="submit" name="agregar" class="btn btn-primary">Agregar subcategoría</button>
                       <div class="pull-right">
                          <strong>
                             <span class="glyphicon glyphicon-th"></span>
                             <span>Categoría:</span>
                             <?php echo $nomCategoria; ?>
                          </strong>
                       </div>
                    </div>   
                 </div>   
              </div>
           </div>
           <div class="panel-body">
              <table class="table table-bordered">
              <thead>
                 <tr>
                    <th class="text-center" style="width: 3%;">#</th>
                    <th class="text-center" style="width: 72%;">Nombre</th>
                    <th class="text-center" style="width: 5%;">Acciones</th>
                 </tr>
              </thead>
              <tbody>
                 <?php foreach ($subCategorias as $subCategoria):?>
                 <tr>
                    <td class="text-center"><?php echo count_id();?></td>
                    <td> <?php echo utf8_decode($subCategoria['nombre']); ?></td>
                    <td class="text-center">
                       <div class="btn-group">
                          <a href="editarSubCategoria.php?id=<?php echo (int)$subCategoria['id'];?>&idCat=<?php echo(int)$subCategoria['idCategoria']; ?>" class="btn btn-info btn-xs" title="Editar" data-toggle="tooltip">
                          <span class="glyphicon glyphicon-edit"></span>
                          </a>
                          <a href="borrarSubcategoria.php?id=<?php echo (int)$subCategoria['id'];?>&idCat=<?php echo(int)$subCategoria['idCategoria']; ?>" class="btn btn-danger btn-xs" title="Eliminar" data-toggle="tooltip">
                          <span class="glyphicon glyphicon-trash"></span>
                          </a>
                       </div>
                    </td>
                 </tr>
                 <?php endforeach; ?>
              </tbody>
              </table>
           </div>
        </div>
     </div>
     <input type="hidden" class="form-control" name="categoriaId" value="<?php echo $idCategoria ?>">
  </form>
</body>
</html>
<?php include_once('../layouts/footer.php'); ?>
