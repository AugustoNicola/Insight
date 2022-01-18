@extends("app")

@section("titulo", "Publicaciones")

@section('contenido')
<aside class="filtros">
	<form method="GET">
		<label for="titulo">Buscar por título:</label>
		<input type="text" name="titulo" id="titulo" class="titulo">
		
		<button type="submit">Aplicar filtro</button>
	</form>
</aside>

<main>
	<h1>Explorar Publicaciones</h1>
	
	@if(count($publicaciones) > 0)
	
	<div class="publicaciones">
		@foreach($publicaciones as $publicacion)
			<x-publicacion tipo="normal" :publicacion="$publicacion" />
		@endforeach
	</div>
	
	@else
	<h2>No se han encontrado publicaciones.</h2>
	@endif
</main>

<aside class="categorias-destacadas">
	<h2>Categorías destacadas</h2>
	
	@if(count($categoriasDestacadas) > 0)
	@foreach($categoriasDestacadas as $categoria)
	<a class="categoria" href="/categorias/{{$categoria->id}}">#{{$categoria->nombre}}</a>
	@endforeach
	
	@else
	<h3>No se han encontrado categorías.</h2>
	@endif
</aside>
@endsection

