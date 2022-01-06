@extends("app")

@section("titulo", "Iniciar Sesión")

@section('contenido')
<form method="POST">
	<h1>Crear Cuenta</h1>
	
	<input type="text" name="nombre" id="nombre" placeholder="Nombre de Cuenta">
	<input type="password" name="contrasena" id="contrasena" placeholder="Contraseña">
	<input type="password" name="contrasena_confirmation" id="contrasena_confirmation" placeholder="Repetir Contraseña">
	
	<div class="contenedor-imagen">
		<label for="imagen">Imagen de cuenta</label>
		<input type="file" accept="image/*" name="imagen" id="imagen">
		<img id="preview-imagen" />
	</div>
	
	@csrf
	<button type="submit">Registrate</button>
</form>
	@endsection
