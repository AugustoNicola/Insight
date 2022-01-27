@extends("app")

@section("titulo", "{$publicacion->titulo}")

@section('contenido')

<main class="mx-2 mt-4 lg:max-w-screen-md lg:mx-auto">
	<h1 class="lg:mb-3 font-titulo font-bold text-center text-negro text-2xl lg:text-3xl">{{$publicacion->titulo}}</h1>
	
	<div class="flex flex-row flex-wrap justify-start items-center gap-2">
		@foreach($publicacion->categorias()->get() as $categoria)
		<a class="font-cuerpo text-grisoscuro hover:text-negrohover text-lg lg:text-xl" href="/categorias/{{$categoria->id}}">#{{$categoria->nombre}}</a>
		@endforeach
	</div>
	
	<div class="informacion-adicional">
		<div class="my-2 flex flex-row flex-nowrap justify-start items-center">
            <div class="w-8 h-8 md:w-10 md:h-10">
                <img src="{{$publicacion->autore()->first()->imagen != null ? "/storage/usuaries/" . $publicacion->autore()->first()->imagen : "/assets/usuariedefault.png"}}" alt="{{$publicacion->autore()->first()->nombre}}" class="rounded-full ring-offset-2 ring-4 ring-grisoscuro" >
            </div>
            <p class="ml-3 font-cuerpo text-negro text-lg md:text-xl">por {{$publicacion->autore()->first()->nombre}}</p>
        </div>
		
		<p class="font-ui italic text-grisoscuro text-base">{{$publicacion->fecha_creacion->format("d/m/Y")}}</p>
	</div>
	
	<div class="flex flex-row flex-nowrap justify-between items-center">
		<div class="flex flex-col flex-nowrap">
			<p class="inline font-ui text-negro text-lg" id="contador-megusta" data-cantidad="{{$publicacion->cantidad_me_gusta}}">{{$publicacion->cantidad_me_gusta}} me gusta</p>
			<p class="inline font-ui text-negro text-lg" id="contador-comentarios" data-cantidad="{{$publicacion->comentarios_count}}">{{$publicacion->comentarios_count}} {{$publicacion->comentarios_count == 1 ? "comentario" : "comentarios"}}</p>
		</div>
		<div class="mr-4 flex flex-row flex-nowrap justify-around items-center gap-3">
			@csrf
			<input type="hidden" name="publicacion-id" id="publicacion-id" value="{{$publicacion->id}}">
			<button id="boton-megusta" class="{{$dadoMeGusta ? "activado" : ""}}">
				<i class="bx text-4xl cursor-pointer hover:text-primario {{$dadoMeGusta ? "text-primario bxs-heart" : "text-negro bx-heart"}}"></i>
			</button>
			<a href="#comentarios">
				<i class="bx bx-comment text-4xl text-negro cursor-pointer hover:text-primario"></i>
			</a>
			<button id="boton-guardar" class="{{$dadoGuardar ? "activado" : ""}}">
				<i class="bx text-4xl text-negro cursor-pointer hover:text-primario {{$dadoGuardar ? "text-primario bxs-bookmark" : "text-negro bx-bookmark"}}"></i>
			</button>
		</div>
	</div>
	
	<div class="max-w-screen-sm mx-auto mt-4 flex justify-center">
		<img src="{{$publicacion->portada != null ? "/storage/publicaciones/" . $publicacion->portada : "/assets/sinportada.png"}}" alt="Imagen de publicación" class="{{$publicacion->portada != null ? "" : "hidden"}} rounded-xl">
	</div>
	
	<div class="mt-4 flex flex-col gap-4">
	<?php $parrafos = explode("\n", $publicacion->cuerpo) ?>
		@foreach($parrafos as $parrafo)
		@if(trim($parrafo) != "")
		<p class="font-cuerpo font-normal text-negro text-lg">{{$parrafo}}</p>
		@endif
		@endforeach
	</div>
	
	<h2 class="mt-6 mb-3 font-ui font-semibold text-2xl text-center lg:text-left">Comentarios ({{$publicacion->comentarios_count}})</h2>
	
	<div id="comentarios">
		<form action="/comentario" method="POST" class="mb-4">
			<div class="flex flex-row flex-nowrap justify-between items-center">
				@if (Auth::check())	
				<img src="{{App\Models\Usuarie::Find(Auth::id())->imagen != null ? "/storage/usuaries/" . App\Models\Usuarie::Find(Auth::id())->imagen : "/assets/usuariedefault.png"}}" alt="Imagen de usuarie" class="mr-4 w-10 h-10 rounded-full ring-offset-2 ring-4 ring-primario">
				@else()
				<img src="/assets/sinusuarie.png" alt="Sin usuarie" class="mr-4 w-10 h-10 rounded-full ring-offset-2 ring-4 ring-primario">
				@endif
				
				@csrf
				<input type="hidden" name="id" value="{{$publicacion->id}}" />
				<textarea name="cuerpo" id="cuerpo" rows="5" placeholder="Escribí tu comentario" class="basis-full font-ui text-xl bg-blanco border-[3px] border-gris rounded-lg"></textarea>
			</div>
			
			<div class="flex flex-row flex-nowrap justify-end items-center">
				<button type="submit" class="mt-3 px-6 py-2 font-ui font-medium text-xl bg-gris hover:bg-grisoscuro rounded-md">Publicar</button>
			</div>
		</form>
		
		@if(count($publicacion->comentarios) > 0)
		
		<div class="mb-6 flex flex-col flex-nowrap gap-6">
			@foreach($publicacion->comentarios as $comentario)
			<div class="flex flex-row flex-nowrap justify-between items-start">
				<div class="mr-4 w-10 h-10 md:w-10 md:h-10">
					<img src="{{$comentario->usuarie->imagen != null ? "/storage/usuaries/" . $comentario->usuarie->imagen : "/assets/usuariedefault.png"}}" alt="{{$comentario->usuarie->nombre}}" class="rounded-full ring-offset-2 ring-4 ring-grisoscuro" >
				</div>
				
				<div class="basis-full">
						<h4 class="inline font-cuerpo font-medium text-negro text-2xl">{{$comentario->usuarie->nombre}}</h4>
						<span class="inline font-ui italic text-grisoscuro text-base">{{$comentario->fecha_creacion->format("d/m/Y")}}</span>
					
					<div class="flex flex-col gap-2">
						<?php $parrafos = explode("\n", $comentario->cuerpo) ?>
							@foreach($parrafos as $parrafo)
							@if(trim($parrafo) != "")
							<p class="font-cuerpo font-normal text-negro text-lg">{{$parrafo}}</p>
							@endif
							@endforeach
					</div>
				</div>
			</div>
			@endforeach
		</div>
		
		@else
		<h3 class="my-6 font-ui font-medium text-center text-grisoscuro text-xl lg:text-2xl">No se han encontrado comentarios de esta publicación.</h2>
		@endif
	</div>
	
	
</main>

<script src="{{ asset('js/publicacion.js') }}"></script>

@endsection

