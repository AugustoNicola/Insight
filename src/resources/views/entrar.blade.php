<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Entrar Test</title>
</head>
<body>
	<form method="POST">
		@csrf
		<label for="nombre">nombre</label>
		<input type="text" name="nombre" id="nombre">
		
		<label for="contrasena">Contraseña</label>
		<input type="text" name="contrasena" id="contrasena">
		
		<button type="submit">Enviar!</button>
	</form>
</body>
</html>