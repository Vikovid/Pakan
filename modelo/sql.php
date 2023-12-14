<?php
require_once(LIB_PATH_INC.DS."load.php");

/*--------------------------------------------------------------*/
/* Login with the data provided in $_POST,
/* coming from the login form.
/*--------------------------------------------------------------*/
function authenticate($username='', $password='') {
   global $db;
   $username = $db->escape($username);
   $password = $db->escape($password);

   $sql  = sprintf("SELECT id,username,password,user_level FROM users WHERE username ='%s' LIMIT 1", $username);

   $result = $db->query($sql);

   if($db->num_rows($result)){
     $user = $db->fetch_assoc($result);
     $password_request = sha1($password);
     if($password_request === $user['password'] ){
        return $user['id'];
     }
   }
   return false;
}

/*--------------------------------------------------------------*/
/* Function to update the last log in of a user
/*--------------------------------------------------------------*/
function updateLastLogIn($user_id){
    global $db;

    $date = make_date();
    $sql = "UPDATE users SET last_login='{$date}' WHERE id ='{$user_id}' LIMIT 1";

    $result = $db->query($sql);

    return ($result && $db->affected_rows() === 1 ? true : false);
}

function cambiaContrasenia($contrasenia,$id){
    global $db;

    $sql = "UPDATE users SET password ='$contrasenia' WHERE id='$id'";

    $result = $db->query($sql);

    return ($result && $db->affected_rows() === 1 ? true : false);
}

function cambiaDatosUsuario($nombre,$nombreUsuario,$id){
    global $db;

    $sql = "UPDATE users SET name ='$nombre',username ='$nombreUsuario' WHERE id='$id'";

    $result = $db->query($sql);

    return ($result && $db->affected_rows() === 1 ? true : false);
}

function messages($fecha){
  global $db;

  $sql = "SELECT * FROM  mensaje WHERE fecha = '{$fecha}'";

  $result = $db->query($sql);
  if ($db->num_rows($result)){
     $mens = $db->fetch_assoc($result);
     return $mens['mensaje'];
  }
  return false;
}

/*--------------------------------------------------------------*/
/*  Function for Find data from table by id
/*--------------------------------------------------------------*/
function find_by_id($table,$id){
   global $db;

   $id = (int)$id;
   if(tableExists($table)){
      $sql = $db->query("SELECT * FROM {$db->escape($table)} WHERE id='{$db->escape($id)}' LIMIT 1");
  
      if($result = $db->fetch_assoc($sql))
         return $result;
      else
         return null;
   }
}

/*--------------------------------------------------------------*/
/* Find current log in user by session id
/*--------------------------------------------------------------*/
function current_user(){
   static $current_user;
   global $db;

   if(!$current_user){
      if(isset($_SESSION['user_id'])):
         $user_id = intval($_SESSION['user_id']);
         $current_user = find_by_id('users',$user_id);
      endif;
   }
   return $current_user;
}

/*--------------------------------------------------------------*/
/* Determine if database table exists
/*--------------------------------------------------------------*/
function tableExists($table){
   global $db;
 
   $table_exit = $db->query('SHOW TABLES FROM '.DB_NAME.' LIKE "'.$db->escape($table).'"');
  
   if($table_exit) {
      if($db->num_rows($table_exit) > 0)
         return true;
      else
         return false;
   }
}

/*--------------------------------------------------------------*/
/* Find group level
/*--------------------------------------------------------------*/
function find_by_groupLevel($level){
   global $db;

   $sql = "SELECT group_level FROM user_groups WHERE group_level = '{$db->escape($level)}' LIMIT 1 ";
    
   $result = $db->query($sql);

   return($db->num_rows($result) === 0 ? true : false);
}

function actUsuario($nombre,$usuario,$nivel,$status,$sucursal,$id){
   global $db;

   $sql  = "UPDATE users SET name='{$nombre}',username='{$usuario}',user_level='{$nivel}',";
   $sql .= "status='{$status}',idSucursal='{$sucursal}' WHERE id='$id'";

   $db->query($sql);
   
   return($db->affected_rows() === 1 ? true : false);
}

/*--------------------------------------------------------------*/
/* Function to update the last log in of a user
/*--------------------------------------------------------------*/
function actContrasenia($pass,$id){
    global $db;

    $sql = "UPDATE users SET password='{$pass}' WHERE id ='$id'";

    $result = $db->query($sql);

    return ($result && $db->affected_rows() === 1 ? true : false);
}

/*--------------------------------------------------------------*/
/* Function for cheaking which user level has access to page
/*--------------------------------------------------------------*/
function page_require_level($require_level){
   global $session;
   $current_user = current_user();
   $login_level = find_by_groupLevel($current_user['user_level']);
   //if user not login
   if (!$session->isUserLoggedIn(true)):
      $session->msg('d','Por favor Iniciar sesión...');
      redirect('../../index.php', false);
      //if Group status Deactive
   elseif($login_level === true):
      $session->msg('d','Este nivel de usuario está inactivo!');
      redirect('../login/home.php',false);
      //cheackin log in User level and Require level is Less than or equal to
   elseif($current_user['user_level'] <= (int)$require_level):
      return true;
   else:
      $session->msg("d", "¡Lo siento!  no tienes permiso para ver la página.");
      redirect('../login/home.php', false);
   endif;
}

function find_all_user(){
   global $db;

   $sql  = "SELECT u.id,u.name,u.username,u.user_level,u.status,u.last_login,";
   $sql .="g.group_name,s.nom_sucursal ";
   $sql .="FROM users u, user_groups g, sucursal s ";
   $sql .="WHERE g.group_level=u.user_level AND s.idSucursal=u.idSucursal ORDER BY u.name ASC";

   $result = find_by_sql($sql);

   return $result;
}

/*--------------------------------------------------------------*/
/* Function for Perform queries
/*--------------------------------------------------------------*/
function find_by_sql($sql)
{
  global $db;

  $result = $db->query($sql);
  $result_set = $db->while_loop($result);

  return $result_set;
}

/*--------------------------------------------------------------*/
/* Function for find all database table rows by table name
/*--------------------------------------------------------------*/
function find_all($table) {
   global $db;

   if(tableExists($table)){
      return find_by_sql("SELECT * FROM ".$db->escape($table));
   }
}

/*--------------------------------------------------------------*/
/* Function for Delete data from table by id
/*--------------------------------------------------------------*/
function delete_by_id($table,$id){
   global $db;

   if(tableExists($table)){
      $sql  = "DELETE FROM ".$db->escape($table);
      $sql .= " WHERE id=". $db->escape($id);
      $sql .= " LIMIT 1";
      $db->query($sql);
    
      return ($db->affected_rows() === 1) ? true : false;
   }
}

function consultaCampos($campos,$tabla){
   global $db;

   if(tableExists($tabla)){
      $sql = "SELECT ".$campos." FROM ".$tabla;

      $result = $db->query($sql);

      return $result;
   }
}

function altaUsuario($nombre,$usuario,$pass,$nivel,$sucursal){
   global $db;

   $sql  = "INSERT INTO users (";
   $sql .="id,name,username,password,user_level,status,idSucursal";
   $sql .=") VALUES (";
   $sql .=" '','{$nombre}','{$usuario}','{$pass}','{$nivel}','1','{$sucursal}')";

   $db->query($sql);
 
   return ($db->affected_rows() === 1) ? true : false;
}

/*Consulta de cliente*/
function join_cliente_table(){
   global $db;
   
   $sql  ="SELECT SUM(a.price/100) AS venta ,b.nom_cliente,b.dir_cliente,b.tel_cliente,b.idcredencial ";
   $sql .="FROM cliente b LEFT JOIN sales a ON a.idCliente = b.idcredencial and a.descuentos = '0' ";
   $sql .="GROUP BY b.idcredencial";

   return find_by_sql($sql);
}

function join_cliente_table1a($codigo){
   global $db;

   $sql  ="SELECT SUM(a.price/100) AS venta,b.nom_cliente,b.dir_cliente,b.tel_cliente,b.idcredencial ";
   $sql .="FROM cliente b ";
   $sql .="LEFT JOIN sales a ON a.idCliente = b.idcredencial and a.descuentos = '0' ";
   $sql .="WHERE b.idcredencial = $codigo ";
   $sql .="GROUP BY b.idcredencial ";

   return find_by_sql($sql);
}

function join_cliente_table2a($codigo){
   global $db;

   $sql  ="SELECT SUM(a.price/100) AS venta,b.nom_cliente,b.dir_cliente,b.tel_cliente,b.idcredencial ";
   $sql .="FROM cliente b ";
   $sql .="LEFT JOIN sales a ON a.idCliente = b.idcredencial and a.descuentos = '0' ";
   $sql .="WHERE b.nom_cliente like '%$codigo%' ";
   $sql .="GROUP BY b.idcredencial";

   return find_by_sql($sql);
}

function buscaRegistroMaximo($tabla,$campo){
   global $db;
   
   if(tableExists($tabla)){
      $sql = "SELECT * FROM {$db->escape($tabla)} WHERE $campo=(SELECT MAX($campo) from $tabla) LIMIT 1";
 
      $query = $db->query($sql);

      if($result = $db->fetch_assoc($query))
         return $result;
      else
         return null;
   }
}

function buscaRegistroPorCampo($tabla,$campo,$valor){
   global $db;

   if(tableExists($tabla)){
      $sql = "SELECT * FROM {$db->escape($tabla)} WHERE $campo='{$db->escape($valor)}' LIMIT 1";

      $query = $db->query($sql);

      if($result = $db->fetch_assoc($query))
         return $result;
      else
         return null;
   }
}

function altaCliente($nombre,$direccion,$telefono,$correo,$credencial,$fechaNac,$alergias,$padecimientos,$foto,$sexo){
   global $db;

   $sql  = "INSERT INTO cliente (";
   $sql .=" nom_cliente,dir_cliente,tel_cliente,correo,idcredencial,alergias,fechaNac,padecimientos,foto,sexo";
   $sql .=") VALUES (";
   $sql .="'{$nombre}','{$direccion}','{$telefono}','{$correo}','{$credencial}','{$alergias}','{$fechaNac}','{$padecimientos}','{$foto}',";
   $sql .="'{$sexo}')";

   $db->query($sql);
 
   return ($db->affected_rows() === 1) ? true : false;
}

function actCliente($nombre,$direccion,$telefono,$correo,$idCliente,$fechaNac,$alergias,$padecimientos,$foto){
   global $db;

   $sql  ="UPDATE cliente set nom_cliente = '{$nombre}',dir_cliente = '{$direccion}',";
   $sql .="tel_cliente = '{$telefono}',correo = '{$correo}',fechaNac = '{$fechaNac}',alergias = '{$alergias}', ";
   $sql .="padecimientos = '{$padecimientos}',foto = '{$foto}' WHERE idcredencial = '$idCliente'";

   $db->query($sql);
   
   return($db->affected_rows() === 1 ? true : false);
}

/*--------------------------------------------------------------*/
/* Function for Delete data from table by id
/*--------------------------------------------------------------*/
function borraRegistroPorCampo($tabla,$campo,$valor){
   global $db;

   if(tableExists($tabla)){

      $sql  = "DELETE FROM ".$db->escape($tabla);
      $sql .= " WHERE $campo = '$valor'";

      $db->query($sql);
    
      return ($db->affected_rows() === 1) ? true : false;
   }
}

function join_historico_table(){
   global $db;
   
   $sql  ="SELECT H.idHistorico,M.movimiento,P.name,S.nom_sucursal,H.comentario,H.qtyin,";
   $sql .="H.qtyfinal,H.usuario,H.vendedor,H.fechaMov,H.horaMov ";
   $sql .="FROM historico H,movimiento M,products P,sucursal S ";
   $sql .="WHERE M.id_movimiento = H.id_movimiento AND P.id = H.id_producto ";
   $sql .="AND S.idSucursal = H.idSucursal ORDER BY H.idHistorico DESC";

   return find_by_sql($sql);
}   

function join_his_table1($codigo,$p_scu){
   global $db;

   $sql  ="SELECT H.idHistorico,M.movimiento,P.name,S.nom_sucursal,H.comentario,H.qtyin,";
   $sql .="H.qtyfinal,H.usuario,H.vendedor,H.fechaMov,H.horaMov ";
   $sql .="FROM historico H,movimiento M,products P,sucursal S ";
   $sql .="WHERE M.id_movimiento = H.id_movimiento AND P.id = H.id_producto ";
   $sql .="AND P.Codigo = $codigo AND H.idSucursal = $p_scu ORDER BY H.idHistorico DESC";

   return find_by_sql($sql);
}

function join_his_table2($codigo,$p_scu){
   global $db;

   $sql  ="SELECT H.idHistorico,M.movimiento,P.name,S.nom_sucursal,H.comentario,H.qtyin,";
   $sql .="H.qtyfinal,H.usuario,H.vendedor,H.fechaMov,H.horaMov ";
   $sql .="FROM historico H,movimiento M,products P,sucursal S ";
   $sql .="WHERE M.id_movimiento = H.id_movimiento AND P.id = H.id_producto ";
   $sql .="AND P.name like '%$codigo%' and H.idSucursal = $p_scu ORDER BY H.idHistorico DESC";

   return find_by_sql($sql);
}

function join_his_table3($p_scu){
   global $db;

   $sql  ="SELECT H.idHistorico,M.movimiento,P.name,S.nom_sucursal,H.comentario,H.qtyin,";
   $sql .="H.qtyfinal,H.usuario,H.vendedor,H.fechaMov,H.horaMov ";
   $sql .="FROM historico H,movimiento M,products P,sucursal S ";
   $sql .="WHERE M.id_movimiento = H.id_movimiento and P.id = H.id_producto ";
   $sql .="AND H.idSucursal = $p_scu ORDER BY H.idHistorico DESC ";

   return find_by_sql($sql);
}

function join_his_table1a($codigo){
   global $db;

   $sql  ="SELECT H.idHistorico,M.movimiento,P.name,S.nom_sucursal,H.comentario,H.qtyin,";
   $sql .="H.qtyfinal,H.usuario,H.vendedor,H.fechaMov,H.horaMov ";
   $sql .="FROM historico H,movimiento M,products P,sucursal S ";
   $sql .="WHERE M.id_movimiento = H.id_movimiento and P.id = H.id_producto AND S.idSucursal = H.idSucursal ";
   $sql .="AND P.Codigo = $codigo ORDER BY H.idHistorico DESC";

   return find_by_sql($sql);
}

function join_his_table2a($codigo){
   global $db;

   $sql  ="SELECT H.idHistorico,M.movimiento,P.name,S.nom_sucursal,H.comentario,H.qtyin,";
   $sql .="H.qtyfinal,H.usuario,H.vendedor,H.fechaMov,H.horaMov ";
   $sql .="FROM historico H,movimiento M,products P,sucursal S ";
   $sql .="WHERE M.id_movimiento = H.id_movimiento and P.id = H.id_producto AND S.idSucursal = H.idSucursal ";
   $sql .="AND P.name like '%$codigo%' ORDER BY H.idHistorico DESC";

   return find_by_sql($sql);
}

function histEfecUsuSuc($p_usu,$p_suc){
   global $db;

   $sql  ="SELECT A.idHistEfectivo,M.movimiento,A.cantIni,A.cantFinal,D.nom_sucursal,E.username,";
   $sql .="A.vendedor,A.fechaMov,A.horaMov ";
   $sql .="FROM histefectivo A,sucursal D, users E, movimiento M ";
   $sql .="WHERE A.idSucursal = D.idSucursal and E.id = A.usuario and A.usuario = $p_usu ";
   $sql .="and A.idSucursal = $p_suc and M.id_movimiento = A.id_movimiento ORDER BY A.idHistEfectivo DESC";

   return find_by_sql($sql);
}

function histEfecSuc($p_suc){
   global $db;

   $sql  ="SELECT A.idHistEfectivo,M.movimiento,A.cantIni,A.cantFinal,D.nom_sucursal,E.username,";
   $sql .="A.vendedor,A.fechaMov,A.horaMov ";
   $sql .="FROM histefectivo A, sucursal D, users E, movimiento M ";
   $sql .="WHERE A.idSucursal = D.idSucursal and E.id = A.usuario and A.idSucursal = $p_suc ";
   $sql .="and M.id_movimiento = A.id_movimiento ORDER BY A.idHistEfectivo DESC";

   return find_by_sql($sql);
}

function histEfecUsu($p_usu){
   global $db;

   $sql  ="SELECT A.idHistEfectivo,M.movimiento,A.cantIni,A.cantFinal,D.nom_sucursal,E.username,";
   $sql .="A.vendedor,A.fechaMov,A.horaMov ";
   $sql .="FROM histefectivo A, sucursal D, users E, movimiento M ";
   $sql .="WHERE A.idSucursal = D.idSucursal and E.id = A.usuario and A.usuario = $p_usu ";
   $sql .="and M.id_movimiento = A.id_movimiento ORDER BY A.idHistEfectivo DESC";
    
   return find_by_sql($sql);
}

function histEfectivo(){
   global $db;

   $sql  ="SELECT A.idHistEfectivo,M.movimiento,A.cantIni,A.cantFinal,D.nom_sucursal,E.username,";
   $sql .="A.vendedor,A.fechaMov,A.horaMov ";
   $sql .="FROM histefectivo A, sucursal D, users E, movimiento M ";
   $sql .="WHERE A.idSucursal = D.idSucursal and E.id = A.usuario and M.id_movimiento = A.id_movimiento ";
   $sql .="ORDER BY A.idHistEfectivo DESC LIMIT 10";

   return find_by_sql($sql);
}   

function registrarEfectivo($movimiento,$montoActual,$montoFinal,$idSucursal,$usuario,$vendedor,$fecha,$hora){
   global $db;

   $sql  ="INSERT INTO histefectivo (";
   $sql .="idHistEfectivo,id_movimiento,cantIni,cantFinal,idSucursal,usuario,vendedor,fechaMov,horaMov) "; 
   $sql .="VALUES ('','$movimiento','{$montoActual}','{$montoFinal}','{$idSucursal}','{$usuario}',";
   $sql .="'{$vendedor}','{$fecha}','{$hora}')";

   $db->query($sql);
 
   return ($db->affected_rows() === 1) ? true : false;
}

function actCaja($monto,$fecha,$idCaja){
   global $db;

   $sql ="UPDATE caja SET monto = '$monto',fecha = '$fecha' WHERE id = '$idCaja'";

   $db->query($sql);
   
   return($db->affected_rows() === 1 ? true : false);
}

function cancelaciones(){
   global $db;

   $sql  ="SELECT a.idcancelacion,b.name,c.nom_sucursal,a.usuario,a.date,a.mensaje ";
   $sql .="FROM cancelacion a,sucursal c,products b ";
   $sql .="WHERE a.idproducto = b.id and c.idSucursal = a.idsucursal ";
   $sql .="ORDER BY a.idCancelacion DESC";

   return find_by_sql($sql);
}

function cancelacionesXSuc($p_scu){
   global $db;

   $sql  ="SELECT a.idcancelacion,b.name,c.nom_sucursal,a.usuario,a.date,a.mensaje ";
   $sql .="FROM cancelacion a,sucursal c,products b ";
   $sql .="WHERE a.idproducto = b.id and a.idsucursal = $p_scu and c.idSucursal = a.idsucursal ";
   $sql .="ORDER BY a.idCancelacion DESC";

   return find_by_sql($sql);
}

function altaCategoria($nombre){
   global $db;

   $sql = "INSERT INTO categories (name) VALUES ('{$nombre}')";

   $db->query($sql);
 
   return ($db->affected_rows() === 1) ? true : false;
}

function actCategoria($nombre,$id){
   global $db;

   $sql = "UPDATE categories SET name = '{$nombre}' WHERE id = '{$id}'";

   $db->query($sql);
   
   return($db->affected_rows() === 1 ? true : false);
}

function altaProveedor($proveedor,$direccion,$telefono,$contacto){
   global $db;

   $sql  = "INSERT INTO proveedor (idProveedor,nom_proveedor,direccion,telefono,contacto";
   $sql .=") VALUES (";
   $sql .=" '','{$proveedor}','{$direccion}','{$telefono}','{$contacto}')";

   $db->query($sql);
 
   return ($db->affected_rows() === 1) ? true : false;
}

function actProveedor($proveedor,$direccion,$telefono,$contacto,$idProveedor){
   global $db;

   $sql  ="UPDATE proveedor SET ";
   $sql .="nom_proveedor = '{$proveedor}',direccion ='{$direccion}',telefono = '{$telefono}',";
   $sql .="contacto = '{$contacto}' WHERE idProveedor ='{$idProveedor}'";

   $db->query($sql);
   
   return($db->affected_rows() === 1 ? true : false);
}


function cuentaRegistros($aContar,$tabla,$campo,$valor){
  global $db;

  if(tableExists($tabla))
  {
    $sql = "SELECT COUNT($aContar) AS total FROM {$db->escape($tabla)} WHERE $campo='{$db->escape($valor)}' LIMIT 1";

    $result = $db->query($sql);
    return($db->fetch_assoc($result));
  }
}

/*--------------------------------------------------------------*/
/* Function for Finding all product name
/* JOIN with categorie  and media database table
/*--------------------------------------------------------------*/
function join_product_table(){
   global $db;

   $sql  ="SELECT p.id,p.name,p.quantity,p.buy_price,p.sale_price,p.idSucursal,p.foto,p.fechaRegistro,c.name ";
   $sql .="AS categorie,s.nom_sucursal AS sucursal ";
   $sql .="FROM products p ";
   $sql .="LEFT JOIN sucursal s ON s.idSucursal = p.idSucursal ";
   $sql .="LEFT JOIN categories c ON c.id = p.categorie_id ";
   $sql .="ORDER BY p.name ASC";

   return find_by_sql($sql);
}

/*--------------------------------------------------------------*/
/* Function for Finding product name
/* JOIN with categorie and media database table
/*--------------------------------------------------------------*/
function join_product_table1($codigo,$categoria){
   global $db;

   $sql  ="SELECT p.id,p.name,p.quantity,p.buy_price,p.idSucursal,p.sale_price,p.foto,p.fechaRegistro,c.name ";
   $sql .="AS categorie,s.nom_sucursal AS sucursal ";
   $sql .="FROM products p ";
   $sql .="LEFT JOIN categories c ON c.id = p.categorie_id ";
   $sql .="LEFT JOIN sucursal s ON s.idSucursal = p.idSucursal ";
   $sql .="WHERE p.Codigo = $codigo and c.id = $categoria ";
   $sql .="ORDER BY p.name ASC";

   return find_by_sql($sql);
}

/*--------------------------------------------------------------*/
/* Function for Finding product name
/* JOIN with categorie and media database table
/*--------------------------------------------------------------*/
function join_product_table2($codigo,$categoria){
   global $db;

   $sql  ="SELECT p.id,p.name,p.quantity,p.idSucursal,p.buy_price,p.sale_price,p.foto,p.fechaRegistro,c.name ";
   $sql .="AS categorie,s.nom_sucursal AS sucursal ";
   $sql .="FROM products p ";
   $sql .="LEFT JOIN categories c ON c.id = p.categorie_id ";
   $sql .="LEFT JOIN sucursal s ON s.idSucursal = p.idSucursal ";
   $sql .="WHERE p.name like '%$codigo%' AND c.id = $categoria ";
   $sql .="ORDER BY p.name ASC";
 
   return find_by_sql($sql);
}

function join_select_categories($categoria){
   global $db;

   $sql  ="SELECT p.id,p.name,p.quantity,p.buy_price,p.sale_price,p.idSucursal,p.foto,p.fechaRegistro,c.name ";
   $sql .="AS categorie,s.nom_sucursal AS sucursal ";
   $sql .="FROM products p ";
   $sql .="LEFT JOIN sucursal s ON s.idSucursal = p.idSucursal ";
   $sql .="LEFT JOIN categories c ON c.id = p.categorie_id ";
   $sql .="WHERE c.id = '$categoria' ";
   $sql .="ORDER BY p.name ASC";

   return find_by_sql($sql);
}

function join_product_table1a($codigo){
   global $db;

   $sql  ="SELECT p.id,p.name,p.quantity,p.buy_price,p.idSucursal,p.sale_price,p.foto,p.fechaRegistro,c.name ";
   $sql .="AS categorie,s.nom_sucursal AS sucursal ";
   $sql .="FROM products p ";
   $sql .="LEFT JOIN categories c ON c.id = p.categorie_id ";
   $sql .="LEFT JOIN sucursal s ON s.idSucursal = p.idSucursal ";
   $sql .="WHERE p.Codigo = $codigo ";
   $sql .="ORDER BY p.name ASC";

   return find_by_sql($sql);
}

function join_product_table2a($codigo){
   global $db;

   $sql  ="SELECT p.id,p.name,p.quantity,p.idSucursal,p.buy_price,p.sale_price,p.foto,p.fechaRegistro,c.name ";
   $sql .="AS categorie,s.nom_sucursal AS sucursal ";
   $sql .="FROM products p ";
   $sql .="LEFT JOIN categories c ON c.id = p.categorie_id ";
   $sql .="LEFT JOIN sucursal s ON s.idSucursal = p.idSucursal ";
   $sql .="WHERE p.name like '%$codigo%' ";
   $sql .="ORDER BY p.name ASC";

   return find_by_sql($sql);
}

function totalesProductos(){
   global $db;

   $sql  ="SELECT SUM(quantity) as cantidadTotal,SUM(buy_price * quantity) as totalPrecio,";
   $sql .="SUM(sale_price * quantity) as totalVenta FROM products";

   $query = $db->query($sql);

   if($result = $db->fetch_assoc($query))
      return $result;
   else
      return null;
}

function totalesProductosCod($codigo){
   global $db;

   $sql  ="SELECT SUM(p.buy_price * p.quantity) AS totalPrecio,SUM(p.quantity) As cantidadTotal,";
   $sql .="SUM(sale_price * quantity) as totalVenta FROM products p ";
   $sql .="LEFT JOIN sucursal s ON s.idSucursal = p.idSucursal ";
   $sql .="LEFT JOIN categories c ON c.id = p.categorie_id ";
   $sql .="WHERE p.name like '%$codigo%'";

   $query = $db->query($sql);

   if($result = $db->fetch_assoc($query))
      return $result;
   else
      return null;
}

function totalesProductosCat($categoria){
   global $db;

   $sql  ="SELECT SUM(p.buy_price * p.quantity) AS totalPrecio,SUM(p.quantity) As cantidadTotal,";
   $sql .="SUM(sale_price * quantity) as totalVenta FROM products p ";
   $sql .="LEFT JOIN sucursal s ON s.idSucursal = p.idSucursal ";
   $sql .="LEFT JOIN categories c ON c.id = p.categorie_id ";
   $sql .="WHERE c.id = '$categoria' ";

   $query = $db->query($sql);

   if($result = $db->fetch_assoc($query))
      return $result;
   else
      return null;
}

function totalesProductosCodCat($codigo,$categoria){
   global $db;

   $sql  ="SELECT SUM(p.buy_price * p.quantity) AS totalPrecio,SUM(p.quantity) As cantidadTotal,";
   $sql .="SUM(sale_price * quantity) as totalVenta FROM products p ";
   $sql .="LEFT JOIN sucursal s ON s.idSucursal = p.idSucursal ";
   $sql .="LEFT JOIN categories c ON c.id = p.categorie_id ";
   $sql .="WHERE p.name like '%$codigo%' AND c.id = '$categoria' ";

   $query = $db->query($sql);

   if($result = $db->fetch_assoc($query))
      return $result;
   else
      return null;
}

function productosPDF(){
   global $db;

   $sql  ="SELECT p.id,p.name,p.quantity,p.buy_price,p.sale_price,p.idSucursal,p.fechaRegistro,";
   $sql .="s.nom_sucursal,s.idsucursal,p.Codigo,c.name as categories ";
   $sql .="FROM products p ";
   $sql .="LEFT JOIN sucursal s ON s.idSucursal = p.idSucursal ";
   $sql .="LEFT JOIN categories c ON c.id = p.categorie_id ";
   $sql .="ORDER BY p.name ASC";

   return find_by_sql($sql);
}

function productosCodCatPDF($codigo,$categoria){
   global $db;

   $sql  ="SELECT p.id,p.name,p.quantity,p.buy_price,p.sale_price,p.idSucursal,p.fechaRegistro,";
   $sql .="s.nom_sucursal,s.idsucursal,p.Codigo,c.name as categories ";
   $sql .="FROM products p ";
   $sql .="LEFT JOIN sucursal s ON s.idSucursal = p.idSucursal ";
   $sql .="LEFT JOIN categories c ON c.id = p.categorie_id ";
   $sql .="WHERE p.name like '%$codigo%' AND c.id = '$categoria' ";
   $sql .="ORDER BY p.name ASC";

   return find_by_sql($sql);
}

function productosCatPDF($categoria){
   global $db;

   $sql  ="SELECT p.id,p.name,p.quantity,p.buy_price,p.sale_price,p.idSucursal,p.fechaRegistro,";
   $sql .="s.nom_sucursal,s.idsucursal,p.Codigo,c.name as categories ";
   $sql .="FROM products p ";
   $sql .="LEFT JOIN sucursal s ON s.idSucursal = p.idSucursal ";
   $sql .="LEFT JOIN categories c ON c.id = p.categorie_id ";
   $sql .="WHERE c.id = '$categoria' ORDER BY p.name ASC";

   return find_by_sql($sql);
}

function productosCodPDF($codigo){
   global $db;

   $sql  ="SELECT p.id,p.name,p.quantity,p.buy_price,p.sale_price,p.idSucursal,p.fechaRegistro,";
   $sql .="s.nom_sucursal,s.idsucursal,p.Codigo,c.name as categories ";
   $sql .="FROM products p ";
   $sql .="LEFT JOIN sucursal s ON s.idSucursal = p.idSucursal ";
   $sql .="LEFT JOIN categories c ON c.id = p.categorie_id ";
   $sql .="WHERE p.name like '%$codigo%' ORDER BY p.name ASC";

   return find_by_sql($sql);
}

function productosExcel(){
   global $db;

   $sql  ="SELECT p.id,p.name,p.quantity,p.buy_price,p.sale_price,p.idSucursal,";
   $sql .="DATE_FORMAT(p.fechaRegistro,'%d-%m-%Y %r') AS date,s.nom_sucursal,s.idsucursal,p.Codigo,";
   $sql .="c.name AS categories ";
   $sql .="FROM products p ";
   $sql .="LEFT JOIN sucursal s ON s.idSucursal = p.idSucursal ";
   $sql .="LEFT JOIN categories c ON c.id = p.categorie_id ORDER BY p.name ASC";

   return find_by_sql($sql);
}

function productosCodCatExcel($codigo,$categoria){
   global $db;

   $sql  ="SELECT p.id,p.name,p.quantity,p.buy_price,p.sale_price,p.idSucursal,";
   $sql .="DATE_FORMAT(p.fechaRegistro,'%d-%m-%Y %r') AS date,s.nom_sucursal,s.idsucursal,p.Codigo,";
   $sql .="c.name AS categories ";
   $sql .="FROM products p ";
   $sql .="LEFT JOIN sucursal s ON s.idSucursal = p.idSucursal ";
   $sql .="LEFT JOIN categories c ON c.id = p.categorie_id ";
   $sql .="WHERE p.name like '%$codigo%' AND c.id = '$categoria' ORDER BY p.name ASC";

   return find_by_sql($sql);
}

function productosCatExcel($categoria){
   global $db;

   $sql  ="SELECT p.id,p.name,p.quantity,p.buy_price,p.sale_price,p.idSucursal,";
   $sql .="DATE_FORMAT(p.fechaRegistro,'%d-%m-%Y %r') AS date,s.nom_sucursal,s.idsucursal,p.Codigo,";
   $sql .="c.name AS categories ";
   $sql .="FROM products p ";
   $sql .="LEFT JOIN sucursal s ON s.idSucursal = p.idSucursal ";
   $sql .="LEFT JOIN categories c ON c.id = p.categorie_id ";
   $sql .="WHERE c.id = '$categoria' ORDER BY p.name ASC";

   return find_by_sql($sql);
}

function productosCodExcel($codigo){
   global $db;

   $sql  ="SELECT p.id,p.name,p.quantity,p.buy_price,p.sale_price,p.idSucursal,";
   $sql .="DATE_FORMAT(p.fechaRegistro,'%d-%m-%Y %r') AS date,s.nom_sucursal,s.idsucursal,p.Codigo,";
   $sql .="c.name AS categories ";
   $sql .="FROM products p ";
   $sql .="LEFT JOIN sucursal s ON s.idSucursal = p.idSucursal ";
   $sql .="LEFT JOIN categories c ON c.id = p.categorie_id ";
   $sql .="WHERE p.name like '%$codigo%' ORDER BY p.name ASC";

   return find_by_sql($sql);
}

function altaProducto($name,$cantidad,$pCompra,$pVenta,$categoria,$foto,$fechaReg,$codigo,$proveedor,$sucursal,$fecCad,$cantCaja,$precioCaja,$fechaMod,$subcategoria){
   global $db;

   $sql  = "INSERT INTO products (";
   $sql .="name,quantity,buy_price,sale_price,categorie_id,foto,fechaRegistro,Codigo,idProveedor,";
   $sql .="idSucursal,fecha_caducidad,cantidadCaja,precioCaja,fechaMod,idSubcategoria";
   $sql .=") VALUES (";
   $sql .="'{$name}','{$cantidad}','{$pCompra}','{$pVenta}','{$categoria}','{$foto}','{$fechaReg}',";
   $sql .="'{$codigo}','{$proveedor}','{$sucursal}','{$fecCad}','{$cantCaja}','{$precioCaja}',";
   $sql .="'{$fechaMod}','{$subcategoria}') ON DUPLICATE KEY UPDATE name='{$name}'";

   $db->query($sql);
 
   return ($db->affected_rows() === 1) ? true : false;
}

function altaHistorico($movimiento,$idProducto,$cantIni,$cantFin,$comentario,$sucursal,$usuario,$vendedor,$fecha,$hora){
   global $db;

   $sql  ="INSERT INTO historico (idHistorico,id_movimiento,id_producto,qtyin,qtyfinal,comentario,";
   $sql .="idSucursal,usuario,vendedor,fechaMov,horaMov) ";
   $sql .="VALUES ('','{$movimiento}','{$idProducto}','{$cantIni}','{$cantFin}','{$comentario}',";
   $sql .="'{$sucursal}','{$usuario}','{$vendedor}','{$fecha}','{$hora}')";

   $db->query($sql);
 
   return ($db->affected_rows() === 1) ? true : false;
}

function actStockProducto($cantidad,$fecha,$idProducto,$foto){
   global $db;

   $sql  ="UPDATE products SET quantity = '$cantidad',fechaMod = '$fecha'";

   if ($foto != "")
      $sql .=",foto = '$foto' WHERE id = '$idProducto'";
   else
      $sql .=" WHERE id = '$idProducto'";

   $db->query($sql);
   
   return($db->affected_rows() === 1 ? true : false);
}

function actProducto($nombre,$cantidad,$pCompra,$pVenta,$categoria,$subcategoria,$codigo,$sucursal,$proveedor,$fecCad,$cantCaja,$precioCaja,$fecha,$foto,$idproducto){
   global $db;

   $sql  ="UPDATE products SET ";
   $sql .="name ='{$nombre}',quantity ='{$cantidad}',buy_price = '{$pCompra}',sale_price = '{$pVenta}',";
   $sql .="categorie_id = '{$categoria}',Codigo = '{$codigo}',idSucursal= '{$sucursal}',";
   $sql .="idProveedor = '{$proveedor}',fecha_caducidad = '{$fecCad}',cantidadCaja = '{$cantCaja}',";
   $sql .="precioCaja = '{$precioCaja}',fechaMod = '{$fecha}',idSubcategoria = '{$subcategoria}'";
   
   if ($foto != "")
      $sql .=",foto = '{$foto}' WHERE id ='{$idproducto}'";
   else
      $sql .="WHERE id ='{$idproducto}'";

   $db->query($sql);
   
   return($db->affected_rows() === 1 ? true : false);
}

function buscaProductosCod($codigo,$usuario,$sucursal){
   global $db;

   $sql  ="SELECT p.id,p.name,p.sale_price,p.Codigo from products p,users u where p.Codigo = '$codigo' ";
   $sql .="or p.name like '%{$codigo}%' and p.quantity > 0 and u.id = '{$usuario}' ";
   $sql .="and p.idSucursal = '{$sucursal}' and u.idSucursal = '{$sucursal}' group by p.name";

   return find_by_sql($sql);  
}

function buscaProducto($usuario,$sucursal){
   global $db;

   $sql  ="SELECT p.id,p.name,p.sale_price,p.Codigo from products p,users u where p.quantity > 0 ";
   $sql .="and u.id = '{$usuario}' and p.idSucursal = '{$sucursal}' and u.idSucursal = '{$sucursal}' ";
   $sql .="LIMIT 0,3";   

   return find_by_sql($sql);     
}

function buscaProdsTempEntregas($usuario){
   global $db;

   $sql  ="SELECT a.cve_temporal,a.cantidad,a.precio,b.sale_price,b.quantity,b.name,b.cantidadCaja,";
   $sql .="b.porcentajeMayoreo,b.id from tempentregas a,products b where a.product_id=b.id ";
   $sql .="and a.usuario='$usuario'";

   return find_by_sql($sql);     
}

function sumaCampo($aSumar,$tabla,$campo,$valor){
   global $db;

   $sql = "SELECT SUM($aSumar) as total FROM $tabla WHERE $campo = '$valor'";   

   $query = $db->query($sql);

   if($result = $db->fetch_assoc($query))
      return $result;
   else
      return null;
}

function actCantidad($tabla,$campo,$multiplo,$precio,$clave){
   global $db;

   $sql  ="UPDATE $tabla SET $campo = '$multiplo',precio='$precio' ";
   $sql .="WHERE cve_temporal = '$clave'";

   $db->query($sql);
   
   return($db->affected_rows() === 1 ? true : false);
}

function join_gastos_table2(){
   global $db;

   $sql  =" SELECT g.id,g.descripcion,g.monto,g.fecha,g.categoria,g.iva,g.total,p.nom_proveedor,";
   $sql .="p.idProveedor,a.tipo_pago,a.id_pago,c.name ";
   $sql .="FROM gastos g,proveedor p,tipo_pago a,categories c WHERE g.tipo_pago=a.id_pago ";
   $sql .="and g.idProveedor = p.idProveedor and g.categoria = c.id ORDER BY g.fecha DESC";

   return find_by_sql($sql);
}   

function altaGasto($descripcion,$precioCompra,$fecha,$proveedor,$sucursal,$tipoPago,$categoria,$iva,$total,$subcategoria){
   global $db;

   $sql  = "INSERT INTO gastos (";
   $sql .=" id,descripcion,monto,fecha,idProveedor,idSucursal,tipo_pago,categoria,iva,total,subCategoria";
   $sql .=") VALUES (";
   $sql .=" '','{$descripcion}','{$precioCompra}','{$fecha}','{$proveedor}','{$sucursal}',";
   $sql .="'{$tipoPago}','{$categoria}','{$iva}','{$total}','{$subcategoria}'";
   $sql .=")";

   $db->query($sql);
 
   return ($db->affected_rows() === 1) ? true : false;
}

function altaHisEfectivo($movimiento,$montoActual,$montoFinal,$idSucursal,$usuario,$vendedor,$fechaActual,$horaActual){
   global $db;

   $sql  ="INSERT INTO histefectivo (idHistEfectivo,id_movimiento,cantIni,cantFinal,idSucursal,usuario,";
   $sql .="vendedor,fechaMov,horaMov) VALUES ('','{$movimiento}','{$montoActual}','{$montoFinal}',";
   $sql .="'{$idSucursal}','{$usuario}','{$vendedor}','{$fechaActual}','{$horaActual}')";

   $db->query($sql);
 
   return ($db->affected_rows() === 1) ? true : false;
}

function actGasto($descripcion,$precioCompra,$proveedor,$categoria,$subcategoria,$tipoPago,$fecha,$iva,$total,$idGasto){
   global $db;

   $sql  ="UPDATE gastos SET ";
   $sql .="descripcion ='{$descripcion}',monto ='{$precioCompra}',idProveedor = '{$proveedor}',";
   $sql .="categoria = '{$categoria}',tipo_pago = '{$tipoPago}',fecha = '{$fecha}',";
   $sql .="iva = '{$iva}',total = '{$total}',subCategoria = '{$subcategoria}' ";
   $sql .="WHERE id ='{$idGasto}'";

   $db->query($sql);
   
   return($db->affected_rows() === 1 ? true : false);
}

function gastosMAP($prove,$fechaIni,$fechaFin){
   global $db;
  
   $sql  = "SELECT g.fecha,g.descripcion,g.total,p.nom_proveedor,tp.tipo_pago ";
   $sql .= "FROM gastos g ";
   $sql .= "INNER JOIN proveedor p ON g.idProveedor = p.idProveedor ";
   $sql .= "INNER JOIN tipo_pago tp ON g.tipo_pago = tp.id_pago ";
   $sql .= "WHERE g.idProveedor =$prove AND g.fecha BETWEEN '{$fechaIni}' AND '{$fechaFin}' ";
   $sql .= "ORDER BY g.fecha DESC";
   
   return $db->query($sql);
}

function gastosMesAnio($fechaIni,$fechaFin){
   global $db;

   $sql  = "SELECT g.fecha,g.descripcion,g.total,p.nom_proveedor,tp.tipo_pago,c.name ";
   $sql .= "FROM gastos g ";
   $sql .= "INNER JOIN proveedor p ON g.idProveedor = p.idProveedor ";
   $sql .= "INNER JOIN tipo_pago tp ON g.tipo_pago = tp.id_pago ";
   $sql .= "INNER JOIN categories c ON g.categoria = c.id ";
   $sql .= "WHERE g.fecha BETWEEN '{$fechaIni}' AND '{$fechaFin}' ";
   $sql .= "ORDER BY g.fecha DESC";

   return $db->query($sql);
}

function gastosMAC($categ,$fechaIni,$fechaFin){
   global $db;

   $sql  = " SELECT g.fecha,g.descripcion,g.total,p.nom_proveedor,tp.tipo_pago,c.name ";
   $sql .= "FROM gastos g ";
   $sql .= "INNER JOIN proveedor p ON g.idProveedor = p.idProveedor ";
   $sql .= "INNER JOIN tipo_pago tp ON g.tipo_pago = tp.id_pago ";
   $sql .= "INNER JOIN categories c ON g.categoria = c.id ";
   $sql .= "WHERE c.id =$categ AND g.fecha BETWEEN '{$fechaIni}' AND '{$fechaFin}' ";
   $sql .= "ORDER BY g.fecha DESC";
  
   return $db->query($sql);
}

function buscaProdsTempVentas($usuario){
   global $db;

   $sql  ="SELECT t.cve_temporal,t.product_id,p.name,t.qty,t.precio,p.sale_price,p.quantity,p.name, ";
   $sql .="p.cantidadCaja,p.precioCaja,p.id from temporal t,products p ";
   $sql .="where product_id = id and usuario = '$usuario'";

   return $db->query($sql);
}

function altaTemporal($idProducto,$cantidad,$precio,$fecha,$usuario,$idSucursal){
   global $db;

   $sql  ="INSERT INTO temporal (cve_temporal,product_id,qty,precio,fecha,usuario,idSucursal) ";
   $sql .="VALUES ('','$idProducto','$cantidad','$precio','$fecha','$usuario','$idSucursal')";

   $db->query($sql);
 
   return ($db->affected_rows() === 1) ? true : false;
}

function altaCancelacion($idProducto,$idSucursal,$usuario,$fecha,$elimina){
   global $db;

   $sql  ="INSERT INTO cancelacion (idcancelacion,idproducto,idsucursal,usuario,date,mensaje) ";
   $sql .="VALUES ('','{$idProducto}','{$idSucursal}','{$usuario}','{$fecha}','{$elimina}')";

   $db->query($sql);
 
   return ($db->affected_rows() === 1) ? true : false;
}

function borraProdTemporal($cveTemporal,$idSucursal){
   global $db;

   $sql ="DELETE FROM temporal WHERE cve_temporal = '$cveTemporal' and idSucursal = '$idSucursal'";

   $db->query($sql);
    
   return ($db->affected_rows() === 1) ? true : false;
}

function buscaProductosVentas($usuario,$idSucursal){
   global $db;

   $sql  ="SELECT a.cve_temporal,a.product_id,SUM(a.qty) AS qty,SUM(a.precio) AS precio,SUM(a.qty * b.buy_price) AS pCompra, ";
   $sql .="a.fecha,a.usuario,a.idSucursal,b.quantity,b.name FROM temporal a,products b WHERE usuario = '$usuario' ";
   $sql .="and b.id = a.product_id and a.idSucursal = '$idSucursal' and b.idSucursal = '$idSucursal' ";
   $sql .="and qty > 0 GROUP BY a.product_id";

   return $db->query($sql);
}

function obtenPuntos($idCliente){
   global $db;

   $sql  ="SELECT SUM(a.price/100) AS venta ,b.nom_cliente FROM cliente b,sales a ";
   $sql .="WHERE a.idCliente = b.idCredencial and a.descuentos = '0' and b.IdCredencial = '$idCliente' ";
   $sql .="GROUP BY b.idCredencial";

   $query = $db->query($sql);

   if($result = $db->fetch_assoc($query))
      return $result;
   else
      return null;
}

function cuentaRegsTemporal($usuario,$idSucursal){
   global $db;

   $sql  ="SELECT COUNT(precio) AS numRegs FROM temporal WHERE usuario = '$usuario' ";
   $sql .="and idSucursal = '$idSucursal' and qty > 0";

   $query = $db->query($sql);

   if($result = $db->fetch_assoc($query))
      return $result;
   else
      return null;
}

function actProdsVentas($resta,$fecha,$idProducto,$idSucursal){
   global $db;

   $sql  ="UPDATE products SET quantity = '$resta',fechaMod = '$fecha' WHERE id = '$idProducto' ";
   $sql .="and idSucursal = '$idSucursal'";
   
   $db->query($sql);
   
   return($db->affected_rows() === 1 ? true : false);
}

function altaVenta($id,$idProducto,$cantidad,$precio,$fecha,$usuario,$idSucursal,$vendedor,$idCliente,$descuentos,$tipoPago,$idTicket,$idCredito,$montoVenta,$pCompra,$categoria,$subcategoria){
   global $db;

   $sql  ="INSERT INTO sales(id,product_id,qty,price,date,usuario,idSucursal,vendedor,idCliente,";
   $sql .="descuentos,tipo_pago,id_ticket,idCredito,montoVenta,precioCompra,idCategoria,idSubcategoria) VALUES ('{$id}','{$idProducto}','{$cantidad}',";
   $sql .="'{$precio}','{$fecha}','{$usuario}','{$idSucursal}','{$vendedor}','{$idCliente}',";
   $sql .="'{$descuentos}','{$tipoPago}','{$idTicket}','{$idCredito}','{$montoVenta}','{$pCompra}','{$categoria}','{$subcategoria}')";

   $db->query($sql);
 
   return ($db->affected_rows() === 1) ? true : false;
}

function altaTicket($idTicket,$nomProducto,$precio,$cantidad,$totalDesc,$descPorc,$idVenta){
   global $db;

   $sql  = "INSERT INTO tickets(id,id_ticket,nomProducto,precio,cantidad,descPuntos,descPorc,idVenta) ";
   $sql .= "VALUES ('','{$idTicket}','{$nomProducto}','{$precio}','{$cantidad}','{$totalDesc}',";
   $sql .= "'{$descPorc}','{$idVenta}')";

   $db->query($sql);
 
   return ($db->affected_rows() === 1) ? true : false;
}

function actDescuentos($idCliente){
   global $db;

   $sql = "UPDATE sales SET descuentos = '1' WHERE idCliente = '$idCliente'";

   $db->query($sql);
   
   return($db->affected_rows() === 1 ? true : false);
}

function altaPago($idTicket,$cantidad,$tipoPago,$fecha,$idSucursal,$credito){
   global $db;

   $sql  ="INSERT INTO pagos(id_pago,id_ticket,cantidad,id_tipo,fecha,id_sucursal,credito) ";
   $sql .="VALUES ('','{$idTicket}','{$cantidad}','{$tipoPago}','{$fecha}','{$idSucursal}','{$credito}')";

   $db->query($sql);
 
   return ($db->affected_rows() === 1) ? true : false;
}

function altaFolio($idTicket){
   global $db;

   $sql = "INSERT INTO folio(id_folio,dato) VALUES ('','$idTicket')";

   $db->query($sql);
 
   return ($db->affected_rows() === 1) ? true : false;
}

function buscaProdsTicket($usuario,$idSucursal){
   global $db;

   $sql  ="SELECT @i := @i + 1 as contador,t.cve_temporal,t.product_id,p.name,SUM(t.qty) AS qty,";
   $sql .="SUM(t.precio) AS precio,t.usuario,t.precio AS PU from temporal t,";
   $sql .="products p cross join (select @i := 0) p where t.product_id = p.id and t.usuario = '$usuario' ";
   $sql .="and t.idSucursal = '$idSucursal' and p.idSucursal = '$idSucursal' GROUP BY t.product_id ";
   $sql .="ORDER BY cve_temporal";

   return $db->query($sql);
}

function venta($fechaInicio,$fechaFinal){
   global $db;

   $sql  = "SELECT s.id,s.qty,s.price,s.date,p.name,s.vendedor,s.id_ticket,s.tipo_pago,s.idCliente ";
   $sql .= "FROM sales s ";
   $sql .= "LEFT JOIN products p ON s.product_id = p.id ";
   $sql .= "WHERE s.date BETWEEN '{$fechaInicio}' AND '{$fechaFinal}' ";
   $sql .= "ORDER BY s.date DESC,s.id DESC";

   return find_by_sql($sql);
}

function venta3($encargado,$fechaInicio,$fechaFinal){
   global $db;

   $sql  = "SELECT s.id,s.qty,s.price,s.date,p.name,s.vendedor,s.id_ticket,s.tipo_pago,s.idCliente ";
   $sql .= "FROM sales s ";
   $sql .= "LEFT JOIN products p ON s.product_id = p.id ";
   $sql .= "WHERE s.vendedor = '$encargado' AND s.date BETWEEN '{$fechaInicio}' AND '{$fechaFinal}' ";
   $sql .= "ORDER BY s.date DESC,s.id DESC";

   return find_by_sql($sql);
}

function ventas($ticket){
   global $db;
   
   $sql  ="SELECT s.id,s.qty,s.price,s.tipo_pago,p.name ";
   $sql .="FROM sales s,products p WHERE id_ticket = '$ticket' ";
   $sql .="AND s.product_id = p.id ";
   $sql .="ORDER BY id DESC";
   
   return find_by_sql($sql);
}

function tipoPago($ticket){
   global $db;

   $sql  ="SELECT p.cantidad,t.tipo_pago ";
   $sql .="FROM pagos p,tipo_pago t WHERE p.id_ticket = '$ticket' AND p.id_tipo = t.id_pago ";
   $sql .="ORDER BY p.id_tipo";
   
   return find_by_sql($sql);
}

function buscaClienteTicket($idTicket){
   global $db;

   $sql ="SELECT date,usuario,idCliente FROM sales WHERE id_ticket = '$idTicket' LIMIT 1";

   $query = $db->query($sql);

   if($result = $db->fetch_assoc($query))
      return $result;
   else
      return null;
}

function buscaProductosTicket($idTicket){
  global $db;

  $sql  ="SELECT @i := @i + 1 as contador,t.nomProducto,t.precio,t.cantidad,t.descPuntos,t.descPorc ";
  $sql .="FROM tickets t cross join (select @i := 0) t where t.id_ticket = '$idTicket'";

  return find_by_sql($sql);
}

function buscaPagosSucursal($idTicket,$idSucursal){
   global $db;

   $sql = "SELECT * FROM pagos WHERE id_ticket = '$idTicket' and id_sucursal = '$idSucursal'";
 
   return find_by_sql($sql); 
}

function tipoPagoTTP($ticket,$tipo){
  global $db;

  $query  ="SELECT p.cantidad,t.tipo_pago,p.id_pago,p.cantidad ";
  $query .="FROM pagos p,tipo_pago t WHERE p.id_ticket = '$ticket' AND p.id_tipo = '$tipo' ";
  $query .="AND t.id_pago = '$tipo' ";

  $sql = $db->query($query);

  if($result = $db->fetch_assoc($sql))
     return $result;
  else
     return null;
}

function actVentaPrecioFecha($precio,$fecha,$id){
   global $db;

   $sql = "UPDATE sales SET price = '{$precio}',date = '{$fecha}' WHERE id ='{$id}'";

   $db->query($sql);
   
   return($db->affected_rows() === 1 ? true : false);
}

function actRegistroPorCampo($tabla,$aActualizar,$nuevoValor,$campo,$valor){
   global $db;

   $sql = "UPDATE $tabla SET $aActualizar = '$nuevoValor' WHERE $campo = '$valor'";   

   $db->query($sql);
   
   return($db->affected_rows() === 1 ? true : false);
}

function actCantFechaPagos($cantidad,$fecha,$idTicket,$idTipo){
   global $db;

   $sql  ="UPDATE pagos SET cantidad = '{$cantidad}',fecha = '{$fecha}' ";
   $sql .="WHERE id_ticket = '{$idTicket}' AND id_tipo = '{$idTipo}'";

   $db->query($sql);
   
   return($db->affected_rows() === 1 ? true : false);
}

function borraPagoTicketTipo($idTicket,$idTipo){
   global $db;

   $sql = "DELETE FROM pagos WHERE id_ticket = '$idTicket' AND id_tipo = '$idTipo'";   

   $db->query($sql);
    
   return ($db->affected_rows() === 1) ? true : false;
}

function actProdIdSucursal($cantidad,$fecha,$idProducto,$idSucursal){
   global $db;

   $sql  ="UPDATE products SET quantity = '$cantidad',fechaMod = '$fecha' WHERE id = '$idProducto' ";
   $sql .="and idSucursal = '$idSucursal'";

   $db->query($sql);
   
   return($db->affected_rows() === 1 ? true : false);
}

function altaCuenta($nomCliente,$total,$idCliente,$idProducto,$cantidad,$totalVenta,$idCredito,$pCompra,$fecha,$hora){
   global $db;

   $sql  ="INSERT INTO cuenta(id,cliente,total,idCredencial,productId,cantidad,totalVenta,idCredito,precioCompra,";
   $sql .="fecha,hora) VALUES ('','{$nomCliente}','{$total}','{$idCliente}','{$idProducto}','{$cantidad}',";
   $sql .="'{$totalVenta}','{$idCredito}','{$pCompra}','{$fecha}','{$hora}')";

   $db->query($sql);
 
   return ($db->affected_rows() === 1) ? true : false;
}

function apartadosCliente(){
  global $db;

  $sql  ="SELECT SUM(total) AS monto,cliente,idCredencial,fecha ";
  $sql .="FROM cuenta WHERE total > 0 and pagado = 0 ";
  $sql .="GROUP BY idCredencial ";
  $sql .="ORDER BY cliente ASC";

  return $db->query($sql);
}

function sumApartadosXCliente($idCredencial){
  global $db;

  $sql  ="SELECT SUM(total) AS monto,cliente,idCredencial ";
  $sql .="FROM cuenta WHERE idCredencial = '$idCredencial' AND pagado = 0 ";
  $sql .="GROUP BY idCredencial ";

  $result = $db->query($sql);

  if($db->num_rows($result)){
    $apartado = $db->fetch_assoc($result);
    return $apartado;
  }
    return false;
}

function apartadosXCliente($idCredencial){
   global $db;

   $sql ="SELECT * FROM cuenta WHERE idCredencial = '$idCredencial' AND pagado = 0";

   return $db->query($sql);
}

function buscaProdsCredito($idSucursal,$idCredencial){
   global $db;

   $sql  ="SELECT @i := @i + 1 as contador, c.total,p.name from cuenta c,products p ";
   $sql .="cross join (select @i := 0) p where c.productId = p.id and p.idSucursal = '$idSucursal' ";
   $sql .="and c.idCredencial = '$idCredencial' and c.pagado = '0' and c.total > 0";

   return $db->query($sql);
}

function actCuenta($total,$pagado,$idCuenta){
   global $db;

   $sql ="UPDATE cuenta SET total = '$total',pagado = '$pagado' WHERE id = '$idCuenta'";

   $db->query($sql);
   
   return($db->affected_rows() === 1 ? true : false);
}

function altaHisCredito($idCliente,$abono,$idSucursal,$nomCliente,$fecha,$hora,$pagado,$idTicket){
   global $db;

   $sql  ="INSERT INTO histcredito(idHistCredito,idCliente,pago,idSucursal,cliente,fechaPago,horaPago,";
   $sql .="pagado,id_ticket) VALUES ('','{$idCliente}','{$abono}','{$idSucursal}','{$nomCliente}',";
   $sql .="'{$fecha}','{$hora}','{$pagado}','{$idTicket}')";

   $db->query($sql);
 
   return ($db->affected_rows() === 1) ? true : false;
}

function detApartadoXCliente($idCredencial){
   global $db;
  
   $sql  ="SELECT c.id,c.total,c.cantidad,c.totalVenta,p.name FROM cuenta c,products p ";
   $sql .="WHERE p.id = c.productId AND c.idCredencial = '$idCredencial' AND pagado = 0";
  
   return $db->query($sql);
}

function histCredito($idCliente){
   global $db;

   $sql ="SELECT * FROM histcredito WHERE idCliente = $idCliente AND pagado = '0'";
    
   return find_by_sql($sql);
}

function buscaPagoCredito($idTicket,$idSucursal){
   global $db;

   $sql = "SELECT * FROM pagos WHERE id_ticket = '$idTicket' and id_sucursal = '$idSucursal'";
 
   $query = $db->query($sql);

   if($result = $db->fetch_assoc($query))
      return $result;
   else
      return null;
}

function buscaRegsPorCampo($tabla,$campo,$valor){
   global $db;

   $sql = "SELECT * FROM $tabla WHERE $campo = $valor";

   $result = $db->query($sql);

   return $result;
}

function buscaCredito($idProducto,$idCredito,$idCliente){
   global $db;

   $sql  ="SELECT * FROM cuenta WHERE productId = '$idProducto' AND idCredito = '$idCredito' ";
   $sql .="AND idCredencial = '$idCliente'";

   $query = $db->query($sql);

   if($result = $db->fetch_assoc($query))
      return $result;
   else
      return null;
}

/*--------------------------------------------------------------*/
/* Function for Generate sales report by two dates
/*--------------------------------------------------------------*/
function find_sale_by_dates($start_date,$end_date){
   global $db;
  
   $start_date  = date("Y-m-d", strtotime($start_date));
   $end_date    = date("Y-m-d", strtotime($end_date));

   $sql  ="SELECT s.date, p.name,p.sale_price,p.buy_price,";
   $sql .="COUNT(s.product_id) AS total_records,";
   $sql .="SUM(s.qty) AS total_sales,";
   $sql .="SUM(p.sale_price * s.qty) AS total_saleing_price,";
   $sql .="SUM(p.buy_price * s.qty) AS total_buying_price ";
   $sql .="FROM sales s ";
   $sql .="LEFT JOIN products p ON s.product_id = p.id";
   $sql .=" WHERE s.date BETWEEN '{$start_date}' AND '{$end_date}'";
   $sql .=" GROUP BY DATE(s.date),p.name";
   $sql .=" ORDER BY DATE(s.date) DESC";
  
   return $db->query($sql);
}

/*--------------------------------------------------------------*/
/* Function for Generate sales report by two dates
/*--------------------------------------------------------------*/
function find_sale_by_dates_suc($start_date,$end_date,$sucursal){
   global $db;
   
   $start_date  = date("Y-m-d", strtotime($start_date));
   $end_date    = date("Y-m-d", strtotime($end_date));
   
   $sql  ="SELECT s.date, p.name,p.sale_price,p.buy_price,";
   $sql .="COUNT(s.product_id) AS total_records,";
   $sql .="SUM(s.qty) AS total_sales,";
   $sql .="SUM(p.sale_price * s.qty) AS total_saleing_price,";
   $sql .="SUM(p.buy_price * s.qty) AS total_buying_price ";
   $sql .="FROM sales s ";
   $sql .="LEFT JOIN products p ON s.product_id = p.id";
   $sql .=" WHERE s.idSucursal='$sucursal' and s.date BETWEEN '{$start_date}' AND '{$end_date}'";
   $sql .=" GROUP BY DATE(s.date),p.name";
   $sql .=" ORDER BY DATE(s.date) DESC";
   
   return $db->query($sql);
}

function ventasCatTotal($categ,$fechaIni,$fechaFin){
   global $db;

   $sql  ="SELECT SUM(s.price) as total,SUM(s.qty) as cantidad ";
   $sql .="FROM sales s,products p,categories c ";
   $sql .="WHERE p.categorie_id = $categ AND p.categorie_id = c.id AND s.product_id = p.id ";
   $sql .="AND s.date BETWEEN '{$fechaIni}' AND '{$fechaFin}' ";

   $query = $db->query($sql);
   
   if($result = $db->fetch_assoc($query))
      return $result;
   else
      return null;
}

function gastosCatTotal($categ,$fechaIni,$fechaFin){
  global $db;

  $query  ="SELECT SUM(total) AS total FROM gastos WHERE categoria = $categ ";
  $query .="AND fecha BETWEEN '{$fechaIni}' AND '{$fechaFin}'";
  
  $sql = $db->query($query);

  if($result = $db->fetch_assoc($sql))
     return $result;
  else
     return null;
}

function monthlycatsuc($sucursal,$fechaIni,$fechaFin){
  global $db;

  $sql  ="SELECT a.name, ";
  $sql .="SUM(c.qty) as cantidad,";
  $sql .="SUM(c.price-b.buy_price * c.qty) AS ganancia, ";
  $sql .="SUM(b.sale_price * c.qty) AS precio_total  ";
  $sql .="FROM categories a, products b, sales c ";
  $sql .="WHERE b.categorie_id = a.id AND c.product_id= b.id ";
  $sql .="AND b.idSucursal = '$sucursal' AND c.idSucursal = '$sucursal' ";
  $sql .="AND c.date BETWEEN '{$fechaIni}' AND '{$fechaFin}' ";
  $sql .="GROUP BY (a.name)";

  return $db->query($sql);
}

function monthlycat1($fechaIni,$fechaFin){
  global $db;

  $sql  ="SELECT a.name, ";
  $sql .="SUM(c.qty) as cantidad,";
  $sql .="SUM(c.price-b.buy_price * c.qty) AS ganancia, ";
  $sql .="SUM(b.sale_price * c.qty) AS precio_total  ";
  $sql .="FROM categories a, products b, sales c ";
  $sql .="WHERE b.categorie_id = a.id AND c.product_id= b.id ";
  $sql .="AND c.date BETWEEN '{$fechaIni}' AND '{$fechaFin}' ";
  $sql .="GROUP BY (a.name)";

  return $db->query($sql);
}

function ventasPeriodoSuc($sucursal,$fechaIni,$fechaFin){
   global $db;

   $query  ="SELECT SUM(v.price) AS totalVentas,s.nom_sucursal FROM sales v,sucursal s ";
   $query .="WHERE v.idSucursal = '$sucursal' AND v.idSucursal = s.idSucursal ";
   $query .="AND v.date BETWEEN '$fechaIni' AND '$fechaFin'";

   $sql = $db->query($query);

   if($result = $db->fetch_assoc($sql))
      return $result;
   else
      return null;
}

function gastosPeriodoSuc($sucursal,$fechaIni,$fechaFin){
   global $db;

   $query  ="SELECT SUM(g.total) AS total,s.nom_sucursal FROM gastos g,sucursal s ";
   $query .="WHERE g.idSucursal = '$sucursal' AND g.idSucursal = s.idSucursal ";
   $query .="AND g.fecha BETWEEN '$fechaIni' AND '$fechaFin'";

   $sql = $db->query($query);

   if($result = $db->fetch_assoc($sql))
      return $result;
   else
      return null;
}

function ventasPeriodo($fechaIni,$fechaFin){
   global $db;

   $query  = "SELECT SUM(v.price) AS totalVentas,s.nom_sucursal FROM sales v,sucursal s ";
   $query .="WHERE v.idSucursal = s.idSucursal AND v.date BETWEEN '$fechaIni' AND '$fechaFin'";

   $sql = $db->query($query);

   if($result = $db->fetch_assoc($sql))
      return $result;
   else
      return null;
}

function gastosPeriodo($fechaIni,$fechaFin){
   global $db;

   $query  ="SELECT SUM(g.total) AS total,s.nom_sucursal FROM gastos g,sucursal s ";
   $query .="WHERE g.idSucursal = s.idSucursal AND g.fecha BETWEEN '$fechaIni' AND '$fechaFin'";

   $sql = $db->query($query);

   if($result = $db->fetch_assoc($sql))
      return $result;
   else
      return null;
}

function ySalesSucFecha($sucursal,$fechaIni,$fechaFin){
  global $db;

  $sql  = "SELECT a.qty, a.price, a.date,c.nom_sucursal,";
  $sql .= "SUM(a.price) AS total_ventas ";
  $sql .= "FROM sales a, sucursal c ";
  $sql .= "WHERE a.idSucursal = '$sucursal' AND c.idsucursal='$sucursal' ";
  $sql .= "AND a.date BETWEEN '{$fechaIni}' AND '{$fechaFin}'";  
  $sql .= "GROUP BY month(a.date) ";
  $sql .= "ORDER BY a.date ASC";
  
  return $db->query($sql);
}

function ySalesFecha($fechaIni,$fechaFin){
  global $db;

  $sql = "SELECT a.qty, a.price, a.date,c.nom_sucursal, ";
  $sql .= "SUM(a.price) AS total_ventas ";
  $sql .= "FROM sales a,sucursal c ";
  $sql .= "WHERE a.idsucursal = c.idsucursal AND a.date BETWEEN '{$fechaIni}' AND '{$fechaFin}' ";
  $sql .= "GROUP BY month(a.date) ";
  $sql .= "ORDER BY a.date ASC";

  return $db->query($sql);
}

/*--------------------------------------------------------------*/
/* Function for Generate Daily sales report
/*--------------------------------------------------------------*/
function dailySales($fecha){
   global $db;
 
   $fecha = date("Y/m/d", strtotime($fecha));

   $sql  ="SELECT b.name, a.qty, a.price, a.date, ";
   $sql .="SUM(a.qty) AS total_ventas, ";
   $sql .="SUM(a.price-b.buy_price * a.qty) AS ganancia, ";
   $sql .="SUM(a.price - 0 * a.qty) AS precio_total ";
   $sql .="FROM sales a, products b ";
   $sql .="WHERE a.product_id = b.id AND a.date='$fecha' ";
   $sql .="GROUP BY DATE(a.date),b.name";  
   
   return $db->query($sql);
}

/*--------------------------------------------------------------*/
/* Function for Generate Daily sales report
/*--------------------------------------------------------------*/
function dailySalesSuc($fecha,$sucursal){
   global $db;
 
   $fecha = date("Y/m/d", strtotime($fecha));

   $sql  ="SELECT b.name, a.qty, a.price, a.date, ";
   $sql .="SUM(a.qty) AS total_ventas, ";
   $sql .="SUM(a.price-b.buy_price * a.qty) AS ganancia, ";
   $sql .="SUM(a.price - 0 * a.qty) AS precio_total ";
   $sql .="FROM sales a, products b ";
   $sql .="WHERE a.product_id = b.id AND a.date='$fecha' ";
   $sql .="AND a.idSucursal = '$sucursal' AND b.idSucursal = '$sucursal' ";
   $sql .="GROUP BY DATE(a.date),b.name";  
  
   return $db->query($sql);
}

function ventaDiaSuc($fecha,$idSucursal){
   global $db;

   $sql  ="SELECT SUM(price) as venta FROM sales WHERE date = '$fecha' and idSucursal = '$idSucursal'";

   $query = $db->query($sql);
  
   if($result = $db->fetch_assoc($query))
      return $result;
   else
      return null;
}

function ventaDia($fecha){
   global $db;

   $sql ="SELECT SUM(price) as venta FROM sales WHERE date='$fecha'";

   $query = $db->query($sql);
  
   if($result = $db->fetch_assoc($query))
      return $result;
   else
      return null;
}

function gananciaDiaSuc($fecha,$idsucursal){
   global $db;

   $sql  ="SELECT SUM(b.price-(b.qty*a.buy_price)) as ganancia FROM products a,sales b ";
   $sql .="WHERE b.product_id = a.id and b.date = '$fecha' and a.idSucursal = '$idsucursal' ";
   $sql .="and b.idSucursal = '$idsucursal'";

   $query = $db->query($sql);
  
   if($result = $db->fetch_assoc($query))
      return $result;
   else
      return null;
}

function ganaciaDia($fecha){
   global $db;

   $sql  ="SELECT SUM(b.price-(b.qty*a.buy_price)) as ganancia FROM products a,sales b ";
   $sql .="WHERE b.product_id = a.id and b.date = '$fecha'";

   $query = $db->query($sql);
  
   if($result = $db->fetch_assoc($query))
      return $result;
   else
      return null;
}

function corteVendedor($vendedor){
   global $db;

   $sql  ="SELECT SUM(a.price) AS venta,SUM(a.montoVenta - a.precioCompra) AS ganancia,a.vendedor,a.date,b.nom_sucursal ";
   $sql .="FROM sales a,sucursal b,products p  ";
   $sql .="WHERE b.idSucursal = a.idSucursal and a.product_id = p.id and a.vendedor = '$vendedor' Group BY a.vendedor,a.date ";
   $sql .="ORDER BY a.date DESC";

   return find_by_sql($sql);
}

function corte(){
   global $db;

   $sql  ="SELECT SUM(a.price) AS venta,SUM(a.montoVenta - a.precioCompra) AS ganancia,a.vendedor,a.date,b.nom_sucursal ";
   $sql .="FROM sales a,sucursal b,products p ";
   $sql .="WHERE b.idSucursal = a.idSucursal and a.product_id = p.id Group BY a.vendedor,a.date ";
   $sql .="ORDER BY a.date DESC ";

   return find_by_sql($sql);
}

function corteDiaVendedor($vendedor,$fecha){
   global $db;

   $sql  ="SELECT SUM(a.price) AS venta,SUM(a.montoVenta - a.precioCompra) AS ganancia,a.vendedor,a.date,b.nom_sucursal ";
   $sql .="FROM sales a,sucursal b,products p ";
   $sql .="WHERE b.idSucursal = a.idSucursal and a.product_id = p.id and a.vendedor = '$vendedor' and a.date = '$fecha' ";
   $sql .="Group BY a.vendedor,a.date ORDER BY a.date DESC";

   return find_by_sql($sql);
}

function corteDia($fecha){
   global $db;

   $sql  ="SELECT SUM(a.price) AS venta,SUM(a.montoVenta - a.precioCompra) AS ganancia,a.vendedor,a.date,b.nom_sucursal ";
   $sql .="FROM sales a,sucursal b,products p ";
   $sql .="WHERE b.idSucursal = a.idSucursal and a.product_id = p.id and a.date = '$fecha' Group BY a.vendedor ";
   $sql .="ORDER BY a.vendedor";

   return find_by_sql($sql);
}

function cortePeriodoVen($encargado,$fechaInicio,$fechaFinal){
   global $db;

   $sql  ="SELECT SUM(a.price) AS venta,SUM(a.montoVenta - a.precioCompra) AS ganancia,a.vendedor,a.date,b.nom_sucursal ";
   $sql .="FROM sales a,sucursal b,products p ";
   $sql .="WHERE b.idSucursal = a.idSucursal and a.product_id = p.id and a.vendedor = '$encargado' ";
   $sql .="and a.date BETWEEN '{$fechaInicio}' AND '{$fechaFinal}' Group BY a.vendedor ";
   $sql .="ORDER BY a.date DESC";

   return find_by_sql($sql);
}

function cortePeriodo($fechaInicio,$fechaFinal){
   global $db;

   $sql  ="SELECT SUM(a.price) AS venta,SUM(a.montoVenta - a.precioCompra) AS ganancia,a.vendedor,a.date,b.nom_sucursal ";
   $sql .="FROM sales a,sucursal b,products p ";
   $sql .="WHERE b.idSucursal = a.idSucursal and a.product_id = p.id and a.date BETWEEN '{$fechaInicio}' AND '{$fechaFinal}' ";
   $sql .="Group BY a.vendedor ORDER BY a.vendedor";

   return find_by_sql($sql);
}

function alertaProductos($idSucursal){
   global $db;

   $sql  ="SELECT a.name,a.quantity,b.nom_sucursal FROM products a,sucursal b ";
   $sql .="WHERE a.idSucursal = '$idSucursal' and b.idSucursal = '$idSucursal' and a.quantity <= '1' ";
   $sql .="order by a.quantity";

   return find_by_sql($sql);
}

/*--------------------------------------------------------------*/
/* Function for Count id  By table name
/*--------------------------------------------------------------*/
function count_by_id($table){
   global $db;

   if(tableExists($table)){
      $sql ="SELECT COUNT(id) AS total FROM ".$db->escape($table);
      
      $result = $db->query($sql);
      
      return($db->fetch_assoc($result));
   }
}

function count_su_id($table){
   global $db;
  
   if(tableExists($table)){
      $sql ="SELECT COUNT(idSucursal) AS total FROM ".$db->escape($table);

      $result = $db->query($sql);

      return($db->fetch_assoc($result));
   }
}

function saldoEfectivoDia($fecha,$idSucursal,$movimiento){
   global $db;

   $sql  ="SELECT SUM(cantIni-cantFinal) as total FROM histefectivo WHERE id_movimiento = '$movimiento' ";
   $sql .="AND fechaMov = '$fecha' AND idsucursal = '$idSucursal'";

   $query = $db->query($sql);
  
   if($result = $db->fetch_assoc($query))
      return $result;
   else
      return null;
}

function pagoPeriodoPortipo($idSucursal,$tipoPago,$fechaIni,$fechaFin){
   global $db;

   $sql  ="SELECT SUM(cantidad) as total FROM pagos WHERE id_tipo = '$tipoPago' ";
   $sql .="and id_sucursal = '$idSucursal' and credito = '0' and fecha BETWEEN '{$fechaIni}' AND '{$fechaFin}'";

   $query = $db->query($sql);
  
   if($result = $db->fetch_assoc($query))
      return $result;
   else
      return null;
}   

function gastoPeriodoPortipo($idSucursal,$tipoPago,$fechaIni,$fechaFin){
   global $db;

   $sql  ="SELECT SUM(total) AS total from gastos where tipo_pago = '$tipoPago' ";
   $sql .="and idSucursal = '$idSucursal' and fecha BETWEEN '{$fechaIni}' AND '{$fechaFin}'";

   $query = $db->query($sql);
  
   if($result = $db->fetch_assoc($query))
      return $result;
   else
      return null;
}   

function mejoresClientes($idSucursal){
   global $db;

   $sql  ="SELECT b.nom_cliente,a.idCliente,SUM(a.price) AS venta,SUM(a.price/100) AS puntos ";
   $sql .="FROM sales a,cliente b WHERE a.IdCliente = b.IdCredencial and a.idSucursal = '$idSucursal' ";
   $sql .="GROUP BY a.idCliente ORDER BY venta DESC LIMIT 10";

   return find_by_sql($sql);
}

function nuevosClientes(){
   global $db;

   $sql  ="SELECT a.nom_cliente,a.idcredencial,a.correo FROM cliente a GROUP BY a.idcredencial ";
   $sql .="ORDER BY a.date DESC LIMIT 10";

   return find_by_sql($sql);
}

function buscaConsultas($idCliente){
  global $db;

  $sql  ="SELECT consulta,idconsulta,diagnostico,problema,peso,fecha FROM consulta ";
  $sql .="WHERE idCredencial = $idCliente order by fecha DESC";

  return find_by_sql($sql);
}

function buscaEstudios($idCliente){
   global $db;

   $sql ="SELECT * FROM files WHERE idCredencial = $idCliente order by fecha DESC LIMIT 10";

   return find_by_sql($sql);  
}

function buscaConsultasAsc($idCliente){
   global $db;

   $sql  ="SELECT consulta,diagnostico,problema,peso,temperatura,fecha FROM consulta ";
   $sql .="WHERE idCredencial = $idCliente order by fecha ASC";

   return $db->query($sql);
}

function altaConsulta($receta,$diagnostico,$problema,$peso,$temperatura,$idCliente,$fecha,$costo,$usuario,$Nota,$saturacion,$talla,$fc,$fr,$pa){
   global $db;

   $sql  ="INSERT INTO consulta (idconsulta,consulta,diagnostico,problema,peso,temperatura,idCredencial,";
   $sql .="fecha,costo,responsable,nota,saturacion,talla,FC,FR,PA) VALUES ('','{$receta}','{$diagnostico}','{$problema}','{$peso}',";
   $sql .="'{$temperatura}','{$idCliente}','{$fecha}','{$costo}','{$usuario}','{$Nota}','{$saturacion}','{$talla}','{$fc}','{$fr}','{$pa}')";

   $db->query($sql);
 
   return ($db->affected_rows() === 1) ? true : false;
}

function altaEstudio($nombre,$descripcion,$nombreArchivo,$idCliente,$fecha){
   global $db;

   $sql  ="INSERT INTO files(nombre,descripcion,url,idCredencial,fecha) VALUES ('{$nombre}','{$descripcion}',";
   $sql .="'{$nombreArchivo}','{$idCliente}','{$fecha}')";

   $db->query($sql);
 
   return ($db->affected_rows() === 1) ? true : false;
}

function buscaCita($responsable,$fecha,$hora){
   global $db;

   $sql  ="SELECT id FROM cita WHERE responsable = '$responsable' AND fecha_cita = '$fecha'";
   $sql .="AND hora = '$hora'";   

   $query = $db->query($sql);
  
   if($result = $db->fetch_assoc($query))
      return $result;
   else
      return null;
}

function altaCita($idCliente,$responsable,$fechaCita,$horaCita,$nota,$fechaActual,$idSucursal){
   global $db;

   $sql  ="INSERT INTO cita (id,idCredencial,responsable,fecha_cita,hora,nota,fecha_solicitud,idSucursal) ";
   $sql .="VALUES ('','{$idCliente}','{$responsable}','{$fechaCita}','{$horaCita}','{$nota}',";
   $sql .="'{$fechaActual}','{$idSucursal}')";

   $db->query($sql);
 
   return ($db->affected_rows() === 1) ? true : false;
}

function actConsulta($receta,$problema,$temperatura,$peso,$diagnostico,$nota,$fecha,$idConsulta,$saturacion,$talla,$fc,$fr,$pa){
   global $db;

   $sql  ="UPDATE consulta SET consulta = '{$receta}',problema = '{$problema}',";
   $sql .="temperatura = '{$temperatura}',peso = '{$peso}',diagnostico = '{$diagnostico}',";
   $sql .="nota = '{$nota}',fecha = '{$fecha}',saturacion = '{$saturacion}',talla = '{$talla}',";
   $sql .="FC = '{$fc}',FR = '{$fr}',PA = '{$pa}' WHERE idconsulta = '{$idConsulta}'";

   $db->query($sql);
   
   return($db->affected_rows() === 1 ? true : false);
}

function citasSucFecha($sucursal,$fechaIni,$fechaFin){
   global $db;

   $sql  ="SELECT p.nom_cliente,c.responsable,c.fecha_cita,c.hora,c.nota,c.id ";
   $sql .="FROM cliente p, cita c ";
   $sql .="WHERE p.idcredencial = c.idCredencial AND c.idSucursal='$sucursal' ";
   $sql .="AND c.fecha_cita BETWEEN '{$fechaIni}' AND '{$fechaFin}' ";  
   $sql .="ORDER BY c.fecha_cita ASC";
  
   return $db->query($sql);
}

function citasFecha($fechaIni,$fechaFin){
   global $db;

   $sql  ="SELECT p.nom_cliente,c.responsable,c.fecha_cita,c.hora,c.nota,c.id ";
   $sql .="FROM cliente p, cita c ";
   $sql .="WHERE p.idcredencial = c.idCredencial ";
   $sql .="AND c.fecha_cita BETWEEN '{$fechaIni}' AND '{$fechaFin}' ";  
   $sql .="ORDER BY c.fecha_cita ASC";

   return $db->query($sql);
}

function actCita($responsable,$fechaCita,$nota,$hora,$fechaActual,$idCita){
   global $db;

   $sql  ="UPDATE cita SET responsable = '{$responsable}',fecha_cita = '{$fechaCita}',nota = '{$nota}',";
   $sql .="hora = '{$hora}',fecha_solicitud = '{$fechaActual}' WHERE id = '{$idCita}'";

   $db->query($sql);
   
   return($db->affected_rows() === 1 ? true : false);
}

function buscaConsultasXResp($fechaIni,$fechaFin){
   global $db;

   $sql  ="SELECT responsable,count(responsable) as numero from consulta where fecha BETWEEN '{$fechaIni}' ";
   $sql .="AND '{$fechaFin}' group by responsable order by numero desc";

   return find_by_sql($sql);
}

function actDatosProducto($nombre,$nuevoStock,$fechaAct,$categoria,$precCompra,$precVenta,$idProducto){
   global $db;

   $sql  ="UPDATE products SET name = '{$nombre}',quantity = '{$nuevoStock}',fechaMod = '{$fechaAct}',";
   $sql .="categorie_id = '{$categoria}',buy_price = '{$precCompra}',sale_price = '{$precVenta}' ";
   $sql .="WHERE id ='{$idProducto}'";

   $db->query($sql);
   
   return($db->affected_rows() === 1 ? true : false);
}

function pagoCreditoDia($idSucursal,$fecha){
   global $db;

   $sql  ="SELECT SUM(cantidad) as total FROM pagos WHERE credito = '1' and id_tipo = '1' ";
   $sql .="and id_sucursal = '$idSucursal' and fecha = '{$fecha}'";

   $query = $db->query($sql);
  
   if($result = $db->fetch_assoc($query))
      return $result;
   else
      return null;
}   

function saldoCajaDia($fecha,$idSucursal,$movimiento){
   global $db;

   $sql  ="SELECT SUM(cantFinal-cantIni) as total FROM histefectivo WHERE id_movimiento = '$movimiento'";
   $sql .="AND fechaMov = '$fecha' AND idsucursal = '$idSucursal'";

   $query = $db->query($sql);
  
   if($result = $db->fetch_assoc($query))
      return $result;
   else
      return null;
}

function borraRegistrosPorCampo($tabla,$campo,$valor){
   global $db;

   if(tableExists($tabla)){

      $sql  = "DELETE FROM ".$db->escape($tabla);
      $sql .= " WHERE $campo = '$valor'";

      $result = $db->query($sql);
    
      return $result;
   }
}

function sumaCampoTemp($aSumar,$tabla,$campo,$valor,$usuario){
   global $db;

   $sql = "SELECT SUM($aSumar) as total FROM $tabla WHERE $campo = '$valor' AND usuario = '$usuario'";   

   $query = $db->query($sql);

   if($result = $db->fetch_assoc($query))
      return $result;
   else
      return null;
}

function categoria($nombre){
   global $db;

   $sql ="SELECT * FROM categories WHERE name like '%$nombre%' LIMIT 5";

   return find_by_sql($sql);
}

function buscaValorMaximo($tabla,$aBuscar,$campo,$valor){
   global $db;

   $sql = "SELECT MAX($aBuscar) as valorMax FROM $tabla WHERE $campo = '$valor'";   

   $query = $db->query($sql);

   if($result = $db->fetch_assoc($query))
      return $result;
   else
      return null;
}

function altaSubCategoria($idCategoria,$idSubcategoria,$nombre){
   global $db;

   $sql  = "INSERT INTO subcategorias (";
   $sql .="id,idCategoria,idSubCategoria,nombre";
   $sql .=") VALUES (";
   $sql .="'','{$idCategoria}','{$idSubcategoria}','{$nombre}')";

   $db->query($sql);
 
   return ($db->affected_rows() === 1) ? true : false;
}

function actSubcategoria($nombre,$id){
   global $db;

   $sql = "UPDATE subcategorias SET nombre = '{$nombre}' WHERE id = '{$id}'";

   $db->query($sql);
   
   return($db->affected_rows() === 1 ? true : false);
}

function depurarBD(){
  global $db;

  $contProductos = 0;

  $sqlProds = "SELECT id,fechaMod,quantity FROM products";
  $productos = $db->query($sqlProds);

  foreach ($productos as $producto):

     $fechaMod = date("Y-m-d", strtotime ($producto['fechaMod']));

     $dia = date("d", strtotime ($fechaMod));
     $mes = date("m", strtotime ($fechaMod));
     $anio = date("Y", strtotime ($fechaMod)); 

     $fechaUltmod = date("Y-m-d", mktime(0,0,0, $mes,$dia,$anio));

     $fechaIniPer = new DateTime($fechaUltmod);
     $fecha_actual = new DateTime(date('Y-m-d',time()));

     $difActual = date_diff($fecha_actual,$fechaIniPer);

     $meses = $difActual->m;
     $anios = $difActual->y;
     $dias = $difActual->d;

     if ($dias > 0 && $meses == "0" && $anios == "1"){
        if ($producto['quantity'] == "0.00"){
           $sqlBorrarProd = "DELETE FROM products WHERE id = '{$producto['id']}'";
           $db->query($sqlBorrarProd);
        }
     }

     $contProductos++;     
  endforeach;

  if ($contProductos > 0){
     $sqlSuc = "SELECT idSucursal FROM sucursal WHERE idSucursal NOT IN (SELECT idSucursal FROM products)";
     $sucursales = $db->query($sqlSuc);
  
     foreach ($sucursales as $sucursal):

        $sqlBorrarSuc = "DELETE FROM sucursal WHERE idSucursal = '{$sucursal['idSucursal']}'";
        $db->query($sqlBorrarSuc);
  
     endforeach;
  }

  return true;
}

?>
