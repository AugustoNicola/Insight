@extends("app")

@section("titulo", "Tu Perfil")

@section('contenido')
<main>
	<h1>Tu Cuenta</h1>
	@if ($usuarie != null)
	
	<div class="usuarie">
		<div class="imagen"></div>
		<div class="informacion-usuarie">
			<h3 class="nombre">{{$usuarie->nombre}}</h3>
			<p class="fecha-unide">Te uniste el {{$usuarie->fecha_creacion->format("d/m/Y")}}</p>
			<p class="cantidad-publicaciones">{{$usuarie->publicaciones_count}} {{$usuarie->publicaciones_count == 1 ? "publicación" : "publicaciones"}}</p>
			<p class="cantidad-guardados">{{$usuarie->cantidad_guardados}} {{$usuarie->cantidad_guardados == 1 ? "publicación guardada" : "publicaciones guardadas"}}</p>
		</div>
	</div>
	
	<a href="/perfil/editar">Editar Cuenta</a>
	
	<div class="publicaciones-usuarie">
		<h2>Tus Publicaciones</h2>
		
		@if (count($usuarie->publicaciones) > 0)
		
			@foreach($usuarie->publicaciones as $publicacion)
				<x-publicacion-editable :publicacion="$publicacion" />
			@endforeach
		
		@else
			<p class="sin-publicaciones">Todavía no escribiste ninguna publicación.</p>
			<a href="/escribir" class="escribir-publicacion">Escribir</a>
		@endif
	</div>
	
	@else
	
	<div class="sin-perfil">
		<h3>Para ver tu perfil primero es necesario iniciar sesión.</h3>
		<a href="/entrar">Entrar a tu Cuenta</a>
	</div>
	
	@endif
</main>
@endsection

