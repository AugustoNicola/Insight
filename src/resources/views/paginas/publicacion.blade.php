@extends("app")

@section("titulo", "{$publicacion->titulo}")

@section('contenido')

<aside class="interacciones">
	@csrf
	<input type="hidden" name="publicacion-id" id="publicacion-id" value="{{$publicacion->id}}">
	<button id="boton-megusta" class="{{$dadoMeGusta ? "activado" : ""}}">Me gusta</button>
	<a href="#comentarios">Comentar</a>
	<button id="boton-guardar" class="{{$dadoGuardar ? "activado" : ""}}">Guardar</button>
</aside>

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
		
		<p class="fecha">{{$publicacion->fecha_creacion->format("d/m/Y")}}</p>
	</div>
	
	<p id="contador-megusta" data-cantidad="{{$publicacion->cantidad_me_gusta}}">{{$publicacion->cantidad_me_gusta}} me gusta</p>
	<p id="contador-comentarios" data-cantidad="{{$publicacion->comentarios_count}}">{{$publicacion->comentarios_count}} {{$publicacion->comentarios_count == 1 ? "comentario" : "comentarios"}}</p>
	
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
	
	<div id="comentarios">
		<div class="formulario-comentario">
			<div class="imagen-usuarie">
				{{-- TODO imagen usuarie --}}
			</div>
			
			<form action="/comentario" method="POST">
				@csrf
				<input type="hidden" name="id" value="{{$publicacion->id}}" />
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
				<span class="fecha">{{$comentario->fecha_creacion->format("d/m/Y")}}</span>
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

<script src="{{ asset('js/publicacion.js') }}"></script>

@endsection

