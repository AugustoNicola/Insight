@extends("app")

@section("titulo", "{$categoria->nombre}")

@section('contenido')
<main class="px-3 md:max-w-screen-lg md:mx-auto">
	<div class="md:max-w-screen-md md:mx-auto">
		<h1 class="my-4 font-titulo font-bold text-3xl lg:text-4xl text-center text-negro">#{{$categoria->nombre}}</h1>
		
		@if($categoria->publicaciones_count >= 1)
		<span class="font-ui text-grisoscuro text-lg lg:text-xl">{{$categoria->publicaciones_count}} {{$categoria->publicaciones_count > 1 ? "publicaciones" : "publicación"}}</span>
		@else
		<span class="font-ui text-grisoscuro text-lg lg:text-xl">Sin publicaciones</span>
		@endif
		<p class="mb-6 font-cuerpo text-negro text-lg lg:text-xl">{{$categoria->descripcion}}</p>
	</div>
	
	@if(count($categoria->publicaciones) > 0)
	
	<div class="mb-8 lg:grid lg:grid-cols-2 lg:gap-x-4 lg:gap-y-4">
		@foreach($categoria->publicaciones as $publicacion)
			<x-publicacion tipo="normal" :publicacion="$publicacion" />
		@endforeach
	</div>
	
	@else
	<h2 class="mt-6 font-ui font-medium text-center text-grisoscuro text-xl">No se han encontrado publicaciones de esta categoría.</h2>
	@endif
</main>
@endsection

