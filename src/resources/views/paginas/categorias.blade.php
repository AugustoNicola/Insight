@extends("app")

@section("titulo", "Categorías")

@section('contenido')
<main>
	<h1>Explorar Categorías</h1>
	
	@if(count($categorias) > 0)
	
	<div class="categorias">
		
		@foreach($categorias as $categoria)
		<div class="categoria">
			<h3 class="nombre">{{$categoria->nombre}}</h3>
			
			@if($categoria->publicaciones_count >= 1)
			<span class="publicaciones">{{$categoria->publicaciones_count}} {{$categoria->publicaciones_count != 1 ? "publicaciones" : "publicación"}}</span>
			@else
			<span class="publicaciones">Sin publicaciones</span>
			@endif
			
			<p class="descripcion">{{$categoria->descripcion}}</p>
		</div>
		@endforeach
		
	</div>
	
	@else
	<h2>No se han encontrado categorías.</h2>
	@endif
</main>
@endsection

