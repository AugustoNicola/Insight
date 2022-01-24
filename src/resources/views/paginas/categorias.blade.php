@extends("app")

@section("titulo", "Categorías")

@section('contenido')
<main class="px-3 md:max-w-screen-lg md:mx-auto">
	<h1 class="my-4 font-titulo font-bold text-3xl lg:text-4xl text-center text-negro">Explorar Categorías</h1>
	
	@if(count($categorias) > 0)
	
	<div class="lg:grid lg:grid-cols-2 lg:gap-x-4 lg:gap-y-4">
		
		@foreach($categorias as $categoria)
		<div class="my-4 px-3 py-2 lg:my-0 flex flex-col flex-nowrap gap-2 rounded-lg bg-primariopastel">
			<a href="/categorias/{{$categoria->id}}" class="font-titulo font-bold text-negro hover:text-negrohover text-2xl">#{{$categoria->nombre}}</a>
			
			@if($categoria->publicaciones_count >= 1)
			<span class="font-ui text-grisoscuro text-lg lg:text-xl">{{$categoria->publicaciones_count}} {{$categoria->publicaciones_count != 1 ? "publicaciones" : "publicación"}}</span>
			@else
			<span class="font-ui text-grisoscuro text-lg lg:text-xl">Sin publicaciones</span>
			@endif
			
			<p class="font-cuerpo text-negro text-lg lg:text-xl">{{$categoria->descripcion}}</p>
		</div>
		@endforeach
		
	</div>
	
	@else
	<h2 class="mt-6 font-ui font-medium text-center text-grisoscuro text-2xl">No se han encontrado categorías.</h2>
	@endif
</main>
@endsection

