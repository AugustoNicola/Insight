@extends("app")

@section("titulo", "{$publicacion->titulo}")

@section('contenido')

<main class="publicacion">
	<h1>{{$publicacion->titulo}}</h1>
	
	<div class="categorias">
		@foreach($publicacion->categorias()->get() as $categoria)
		<a class="categoria" href="/categorias/{{$categoria->id}}">#{{$categoria->nombre}}</a>
		@endforeach
	</div>
	
	<div class="informacion-adicional">
		
		<div class="autore">
			<div class="imagen-autore"> {{-- TODO imagen autore--}}</div>
			<p>Por {{$publicacion->autore->nombre}}</p>
		</div>
		
		<p class="fecha">{{$publicacion->fecha_creacion->format("d/m/y")}}</p>
	</div>
	
	<p class="reacciones">{{$publicacion->cantidad_me_gusta}} me gusta * {{$publicacion->comentarios_count}} {{$publicacion->comentarios_count == 1 ? "comentario" : "comentarios"}}</p>
	
	<div class="portada">
		{{-- TODO imagen portada --}}
	</div>
	
	<div class="cuerpo">
	<?php $parrafos = explode("\n", $publicacion->cuerpo) ?>
		@foreach($parrafos as $parrafo)
		@if(trim($parrafo) != "")
		<p class="parrafo">{{$parrafo}}</p>
		@endif
		@endforeach
	</div>
	
	<h2>Comentarios ({{$publicacion->comentarios_count}})</h2>
	
	<div class="comentarios">
		<div class="formulario-comentario">
			<div class="imagen-usuarie">
				{{-- TODO imagen usuarie --}}
			</div>
			
			<form action="/comentarios" method="POST">
				@csrf
				<input type="hidden" name="publicacion" value="{{$publicacion->id}}">
				<textarea name="cuerpo" id="cuerpo" placeholder="Escribí tu comentario"></textarea>
				<button type="submit">Publicar</button>
			</form>
		</div>
		
		@if(count($publicacion->comentarios) > 0)
		
		@foreach($publicacion->comentarios as $comentario)
		
		<div class="comentario">
			<div class="informacion">
				<div class="imagen-usuarie">
					{{-- TODO imagen usuarie --}}
				</div>
				<h4 class="nombre">{{$comentario->usuarie->nombre}}</h4>
				<span class="fecha">{{$comentario->fecha_creacion->format("d/m/y")}}</span>
			</div>
			
			<div class="cuerpo">
			<?php $parrafos = explode("\n", $comentario->cuerpo) ?>
				@foreach($parrafos as $parrafo)
				@if(trim($parrafo) != "")
				<p class="parrafo">{{$parrafo}}</p>
				@endif
				@endforeach
			</div>
		</div>
		
		@endforeach
		
		@else
		<h3>No se han encontrado comentarios de esta publicación.</h2>
		@endif
	</div>
	
	
</main>

@endsection

