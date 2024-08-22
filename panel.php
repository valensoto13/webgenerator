<?php 

session_start();
if (!isset($_SESSION['usuario'])) {
    header("location:login.php");
}

$submit = false;
$error=""; 
$creado="";
$data = "mysql:host=localhost;dbname=webgenerator";
$conexion = new PDO($data, 'adm_webgenerator', 'webgenerator2024');


if ($_SESSION['usuario'] == 'admin' ) {
    $sql = "SELECT * FROM webs1 INNER JOIN Usuarios2 ON webs1.idUsuario = Usuarios2.idUsuario";
    $consulta = $conexion->prepare($sql);
    $consulta->execute();
    $webs = $consulta->fetchAll(PDO::FETCH_ASSOC);
}else{
    $sql = "SELECT dominio FROM webs1 WHERE idUsuario = :usuario ";
    $consulta = $conexion->prepare($sql);
    $consulta->execute(array( 'usuario' => $_SESSION['usuario']));
    $webs = $consulta->fetchAll(PDO::FETCH_ASSOC);
}



if (isset($_POST['submit'])) {
    $submit = $_POST['submit'];
}

if ($submit) {

    $consulta = $conexion->prepare("SELECT idUsuario,dominio FROM webs1 WHERE dominio = :dominio ");
    $consulta->execute(array( 'dominio' => $_SESSION['usuario'].$_POST['dominio']));
    $usuario = $consulta->fetch(PDO::FETCH_ASSOC);

    if (!isset($usuario['dominio'])) {
        $consulta = $conexion->prepare("INSERT INTO `webs1` (`idWeb`, `idUsuario`, `dominio`, `fechaCreacion`) VALUES (NULL, :idUsuario, :dominio, :date)");
        $dominio= $_SESSION['usuario'].$_POST['dominio'];
        $consulta->execute(array( 
            'idUsuario' => $_SESSION['usuario'],
            'dominio' => $dominio,
            'date' => date('Y-m-d')));
        $exec = shell_exec('./wix.sh '.$dominio);
        $creado = "El dominio ".$dominio." fue creado";
        header("location:panel.php");
    }else{
        $error = "Dominio ya existente";
    }
}


?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel</title>
</head>
<body>

    <h1>Bievenido a tu panel</h1>
    <a href="logout.php">Cerrar sesion de <?= $_SESSION['usuario']?></a>
    <?php
    if ($_SESSION['usuario'] != 'admin') {
        echo '<form method="post">
        <h3>Generar web de :</h3>
        Nombre: <input type="text" name="dominio" placeholder="Nombre de nueva web"><br>
        <input type="submit" name="submit" value="Generar">'.$error.$creado.'</form>
    <h2>Mis Webs</h2>';
    }else{
        echo "<h2> Todas las webs </h2>";
    }
    ?>
    
    <table border="1">
        
        <?php
        if ($_SESSION['usuario'] != 'admin') {
            if (isset($webs[0])) {
                echo "<tr>
                <td>Dominio</td>
                <td> - </td>
                <td> - </td>
                </tr>";
                foreach ($webs as $key => $value) {
                    $dominio= $value['dominio'];
                    shell_exec("zip -r webs/$dominio/".$dominio.".zip "."webs/$dominio");
                        echo "<tr> <td><a href='webs/".$value['dominio']."/index.php'>".$value['dominio']."</a></td> <td><a href='webs/".$value['dominio']."/".$value['dominio'].".zip'>Descargar</a></td> <td><a href='eliminar.php?dominio=".$value['dominio']."'>Eliminar</a></td></tr>";
                }
            }
        }else{
            if (isset($webs[0])) {
                echo "<tr>
                <td> Dominio</td>
                <td> Dueño </td>
                <td> Fecha de Creacion </td>
                </tr>";
                foreach ($webs as $key => $value) {
                        echo "<tr> <td><a href='webs/".$value['dominio']."/index.php'>".$value['dominio']."</a></td> <td>".$value['email']."</td> <td>".$value['fechaCreacion']."</td></tr>";
                }
            }
        }
        ?>
    </table>

</body>
</html>

 
