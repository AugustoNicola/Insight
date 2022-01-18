@extends("app")

@section("titulo", "Editar Perfil")

@section('contenido')
<form method="POST" action="/perfil" enctype="multipart/form-data">
	<h1>Editar Información de Cuenta</h1>
	
	<label for="nombre">nombre</label>
	<input type="text" name="nombre" id="nombre" value={{$usuarie->nombre}}>
	
	<label for="contrasena">Contraseña</label>
	<input type="text" name="contrasena" id="contrasena">
	
	<label for="contrasena_confirmation">Repetir contraseña</label>
	<input type="password" name="contrasena_confirmation" id="contrasena_confirmation">
	
	<div class="contenedor-imagen">
		<label for="imagen">Imagen de cuenta</label>
		<input type="file" name="imagen" id="imagen" />
		<img id="preview-imagen" />
	</div>
	
	@method('PUT')
	@csrf
	<button type="submit">Actualizar</button>
</form>
@endsection
