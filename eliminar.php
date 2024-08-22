<?php
    session_start();
    $data = "mysql:host=localhost;dbname=6846";
    $conexion = new PDO($data, 'adm_webgenerator', 'webgenerator2024');
    $consulta = $conexion->prepare("DELETE FROM webs1 WHERE dominio = :dominio");
    $consulta->execute(array( 'dominio' => $_GET['dominio']));
    shell_exec("rm -r webs/".$_GET['dominio']);
    header("location:panel.php"); 
?> 
