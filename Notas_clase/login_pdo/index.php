<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Iniciar Sesión (PDO)</title>
</head>
<body>
	<h2>Iniciar Sesión (PDO)</h2>
	<form action="validar.php" method="post">
	  <label>Usuario:</label>
	  <input type="text" name="username" required><br>
	  <label>Contraseña:</label>
	  <input type="password" name="password" required><br>
	  <button type="submit">Ingresar</button>
	</form>
</body>
</html>