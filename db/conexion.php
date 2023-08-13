<?php 

function conexion(){
      $user = "root";
      $password = "";

      $mbd = new PDO('mysql:host=localhost;dbname=challange', $user, $password);

      return $mbd;
}


?>