@extends("app")

@section("titulo", "Publicaciones Guardadas")

@section('contenido')
<main>
	<h1>Tus Publicaciones Guardadas</h1>
	@if ($autenticade == true)
		
		@if (count($publicaciones) > 0)
		
			@foreach($publicaciones as $publicacion)
				<x-publicacion tipo="guardable" :publicacion="$publicacion" />
			@endforeach
		
		@else
			<div class="sin-guardados">
				<h3>Todavía no guardaste ninguna publicación.</h3>
				<a href="/publicaciones">Ver Publicaciones</a>
			</div>
		@endif
		
	@else
		
		<div class="sin-perfil">
			<h3>Para ver tus publicaciones guardadas primero es necesario iniciar sesión.</h3>
			<a href="/entrar">Entrar a tu Cuenta</a>
		</div>
		
	@endif
</main>
@endsection

