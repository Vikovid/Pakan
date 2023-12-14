<!--GASTOS PAKAN-->
<!-- LOAD -->
<?php
  // ARCHIVO LOAD
  require_once('../../modelo/load.php');
  page_require_level(1);

  // TÍTULO DE LA PÁGINA
  $page_title = 'Lista de Gastos';

  // GASTOS
  // $categorias = find_all('categories');
  $gastos = join_gastos_table2();
?>

<!-- CATEGORIA -->
<?php
  function buscaCategoria($id){
    $nom_categoria = find_by_id('categories',$id);
    return $nom_categoria['name'];
  }
?>

<!-- HEADER -->
<?php include_once('../layouts/header.php');?>

<!-- DIVS -->
<div class="row">
   <div class="col-md-12">

    <!-- MENSAJE -->
    <?php echo display_msg($msg);?>

      <div class="panel panel-default">
        
        <div class="panel-heading clearfix">
          <div class="pull-right">
              
              <a href="add_gastos.php" class="btn btn-primary">Agregar Gastos</a>
              
              <img src="../../libs/imagenes/Logo.png" height="50" width="50" alt="" align="center">
          
          </div>
          </div>
          
          <div class="panel-body">
          <table class="table table-bordered">
              
              <thead>
                  <tr>
                    <th style="width: 18%;"> Proveedor </th>
                    <th style="width: 18%;"> Descripción </th>
                    <th style="width: 15%;"> Categoría </th>
                    <th class="text-center" style="width: 8%;"> Subtotal </th>
                    <th class="text-center" style="width: 8%;"> IVA </th>
                    <th class="text-center" style="width: 8%;"> Total </th>
                    <th class="text-center" style="width: 10%;"> Forma de Pago </th>
                    <th class="text-center" style="width: 10%;"> Fecha </th>
                    <th class="text-center" style="width: 5%;"> Acciones </th>
                  </tr>
              </thead>
              
              <tbody>
                <?php foreach($gastos as $gasto):?>
                <tr>
                    
                    <td class="text-left"><?php echo remove_junk($gasto['nom_proveedor']);?></td>
                    <td class="text-left"><?php echo remove_junk($gasto['descripcion']);?></td>
                    <td class="text-left"><?php echo remove_junk(buscaCategoria($gasto['categoria']));?></td>
                    <td class="text-right"><?php echo remove_junk($gasto['monto']);?></td>
                    <td class="text-right"><?php echo remove_junk($gasto['iva']);?></td>
                    <td class="text-right"><?php echo remove_junk($gasto['total']);?></td>
                    <td class="text-center"><?php echo remove_junk($gasto['tipo_pago']);?></td>
                    <td class="text-center"><?php echo remove_junk($gasto['fecha']);?></td>
                    
                    <td class="text-center">
                      <div class="btn-group">
                          
                          <a href="edit_gasto.php?id=<?php echo (int)$gasto['id']?>&idProveedor=<?php echo (int)$gasto['idProveedor']?>&idCategoria=<?php echo (int)$gasto['categoria']?>&id_pago=<?php echo (int)$gasto['id_pago']?>" class="btn btn-info btn-xs"  title="Editar" data-toggle="tooltip">
                          <span class="glyphicon glyphicon-edit"></span></a>
                     
                        <a href="delete_gasto.php?id=<?php echo (int)$gasto['id']?>" class="btn btn-danger btn-xs"  title="Eliminar" data-toggle="tooltip">
                          <span class="glyphicon glyphicon-trash"></span></a>

                      </div>
                    </td>

                  </tr>
                  <?php endforeach;?>
               </tbody>
          </table>
         </div>

      </div>
    </div>
  </div>

<!-- FOOTER -->
<?php include_once('../layouts/footer.php');?>

