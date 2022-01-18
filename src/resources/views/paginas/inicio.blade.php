@extends("app")

@section("titulo", "Inicio")

@section('contenido')

<div class="hero">
	<h1>Hero Banner</h1>
	<button class="cta">Botón!</button>
</div>

<main class="publicaciones-destacadas">
	<h2>Publicaciones más leídas</h1>
	
	@if(count($publicacionesDestacadas) > 0)
	
	<div class="publicaciones">
		@foreach($publicacionesDestacadas as $publicacion)
			<x-publicacion tipo="normal" :publicacion="$publicacion" />
		@endforeach
	</div>
	
	@else
	<h4>No se han encontrado publicaciones.</h2>
	@endif
</main>

<aside class="categorias-destacadas">
	<h2>Categorías populares</h2>
	
	@if(count($categoriasDestacadas) > 0)
	
	<div class="categorias">
		@foreach($categoriasDestacadas as $categoria)
			<a class="categoria" href="/categorias/{{$categoria->id}}">#{{$categoria->nombre}}</a>
			<span>{{$categoria->publicaciones_count}} {{$categoria->publicaciones_count != 1 ? "publicaciones" : "publicación"}}</span>
		@endforeach
	</div>
	
	@else
	<h3>No se han encontrado categorías.</h2>
	@endif
</aside>
@endsection

