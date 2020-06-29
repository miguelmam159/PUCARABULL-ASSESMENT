<?php
include "config.php";
include "utils.php";


$dbConn =  connect($db);

/*
  listar todos los assesment o solo uno
 */
if ($_SERVER['REQUEST_METHOD'] == 'GET')
{
    if (isset($_GET['id']))
    {
      //Mostrar un assesment
      $sql = $dbConn->prepare("SELECT * FROM assesment where id=:id");
      $sql->bindValue(':id', $_GET['id']);
      $sql->execute();
      header("HTTP/1.1 200 OK");
      echo json_encode(  $sql->fetch(PDO::FETCH_ASSOC)  );
      exit();
	  }
    else {
      //Mostrar lista de assesment
      $sql = $dbConn->prepare("SELECT * FROM assesment");
      $sql->execute();
      //$sql->setFetchMode(PDO::FETCH_ASSOC);
      header("HTTP/1.1 200 OK");
      echo json_encode( $sql->fetchAll(PDO::FETCH_ASSOC)  );
      exit();
	}
}

// Crear un nuevo assesment
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $input = $_POST;
    $sql = "INSERT INTO assesment
          (nombre_usuario, comentario)
          VALUES
          (:nombre_usuario, :comentario)";
		  
   
    try {
		$statement = $dbConn->prepare($sql);
        bindAllValues($statement, $input);
		$statement->execute();
		$postId = $dbConn->lastInsertId();
		if($postId)
		{
		  $input['id'] = $postId;
		  header("HTTP/1.1 200 OK");
		  echo json_encode($input);
		  exit();
		 }
    } catch (PDOException $exception) {
         echo "Error Insert: " . $exception->getMessage();
    }		 
}

//Borrar assesment --------- hoy 29-06-2020
if ($_SERVER['REQUEST_METHOD'] == 'DELETE')
{
  $id = $_GET['id'];
  $statement = $dbConn->prepare("DELETE FROM assesment where id=:id");
  $statement->bindValue(':id', $id);
  $statement->execute();
  
  $cant =  $statement->rowCount();
	
  if ($cant > 0 ) {
	 header("HTTP/1.1 200 OK"); 
	 echo "ok ...";
  } else {
         echo "Error 400: Registro no encontrado";
		header("HTTP/1.1 400 No OK"); 
  }
	
  //header("HTTP/1.1 200 OK");
  exit();
}

//Actualizar assesment
if ($_SERVER['REQUEST_METHOD'] == 'PUT')
{
    $input = $_GET;
    $postId = $input['id'];
    $fields = getParams($input);
    
	echo "Input   <br>";
	print_r ($input);
    
	$sql = "
          UPDATE assesment
          SET $fields
          WHERE id='$postId'
           ";

    $statement = $dbConn->prepare($sql);
    bindAllValues($statement, $input);
	
	$statement->execute();
	
	$cant =  $statement->rowCount();
	
    if ($cant > 0 ) {
	   header("HTTP/1.1 200 OK");
	   echo "ok ...";
	} else {
         echo "Error 400: Registro no encontrado";
		header("HTTP/1.1 400 No OK"); 
	}
	
    //header("HTTP/1.1 200 OK");
    exit();
}

?>