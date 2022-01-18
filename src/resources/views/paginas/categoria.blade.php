@extends("app")

@section("titulo", "#{$categoria->nombre}")

@section('contenido')
<main>
	<h1>#{{$categoria->nombre}}</h1>
	
	@if($categoria->publicaciones_count >= 1)
	<span class="publicaciones">{{$categoria->publicaciones_count}} {{$categoria->publicaciones_count > 1 ? "publicaciones" : "publicación"}}</span>
	@else
	<span class="publicaciones">Sin publicaciones</span>
	@endif
	<p>{{$categoria->descripcion}}</p>
	
	
	@if(count($categoria->publicaciones) > 0)
	
	<div class="publicaciones">
		@foreach($categoria->publicaciones as $publicacion)
			<x-publicacion tipo="normal" :publicacion="$publicacion" />
		@endforeach
	</div>
	
	@else
	<h2>No se han encontrado publicaciones de esta categoría.</h2>
	@endif
</main>
@endsection

