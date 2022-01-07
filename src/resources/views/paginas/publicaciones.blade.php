@extends("app")

@section("titulo", "Publicaciones")

@section('contenido')
<main>
	<h1>Explorar Publicaciones</h1>
	
	@if(count($publicaciones) > 0)
	
	<div class="publicaciones">
		
		@foreach($publicaciones as $publicacion)
		<div class="publicacion">
			<h4 class="titulo">{{$publicacion->titulo}}</h4>
			<div class="categorias">
				@foreach($publicacion->categorias()->get() as $categoria)
				<p class="categoria">#{{$categoria->nombre}}</p>
				@endforeach
			</div>
			<div class="autore">por {{$publicacion->autore()->first()->nombre}}</div>
			<p class="reacciones">
				<?=
					array_reduce(
						$publicacion
							->reacciones()->get() //lista con todas las reacciones
							->pluck("pivot.relacion")->toArray() // filtramos solo a tipo de reaccion ("me gusta" | "guardar")
					, function($valorPrevio, $reaccion) {
						// contamos cuantos "me_gusta" hay
						return $reaccion == "me_gusta" ? $valorPrevio + 1 : $valorPrevio;
					}, 0)
				?> me gusta
			</p>
		</div>
		@endforeach
		
	</div>
	
	@else
	<h2>No se han encontrado publicaciones.</h2>
	@endif
</main>
@endsection

