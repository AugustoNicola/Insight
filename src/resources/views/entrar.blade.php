@extends("app")

@section("titulo", "Iniciar Sesión")

@section('contenido')
<form method="POST">
	<h1 class="text-3xl font-bold underline">
		Hello world!
	</h1>
	
	@csrf
	<label for="nombre">nombre</label>
	<input type="text" name="nombre" id="nombre" value={{old("nombre")}}>
	
	<label for="contrasena">Contraseña</label>
	<input type="text" name="contrasena" id="contrasena" value={{old("contrasena")}}>
	
	<button type="submit">Enviar!</button>
</form>
@endsection
