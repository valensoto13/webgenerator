<?php 



$submit = false;
if (isset($_SESSION['usuario'])) {
		header("location:panel.php");
	}
if (isset($_POST['submit'])) {
	$submit = $_POST['submit'];
}
session_start();
$error = ""; 
if ($submit) {
	if ($_POST['password'] == $_POST['passRepetir']) {
    	$data = "mysql:host=localhost;dbname=webgenerator";
    	$conexion = new PDO($data, 'adm_webgenerator', 'webgenerator2024');
		$consulta = $conexion->prepare("SELECT idUsuario,email,password FROM Usuarios2 WHERE email = :email ");
		$consulta->execute(array( 'email' => $_POST['email']));
		$usuario = $consulta->fetch(PDO::FETCH_ASSOC);
		if (!isset($usuario['email'])) {
			$consulta = $conexion->prepare("INSERT INTO `Usuarios2` (`idUsuario`, `email`, `password`, `fechaRegistro`) VALUES (NULL, :email, :pass, :date)");
			$consulta->execute(array( 
				'email' => $_POST['email'],
				'pass' => $_POST['password'],
				'date' => date('Y-m-d')));
			
			header('location:login.php');
		}else{
			$error = "Email Registrado intente iniciando sesion";
		}
	}else{
		$error = "Las contraseñas deben coincidir";
	}
}




?>
<!DOCTYPE html>
<html lang="en"
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registrarte es simple.</title>
</head>
<body>
    <h1>Registrarte es simple</h1>
   <h1>Registrarse es simple.</h1>
	<form method="post">
		Email:<input type="text" name="email"><br>
		Contraseña: <input type="password" name="password"><br>
		Repetir Contraseña:<input type="password" name="passRepetir"><br>
		<input type="submit" name="submit" value="Registrarse"><a href="login.php">Loguearse</a>
		<?= $error ?>
	</form>

</body>
</html>

