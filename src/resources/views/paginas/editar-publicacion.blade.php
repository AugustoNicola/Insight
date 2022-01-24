@extends("app")

@section("titulo", "Editar")

@section('contenido')
<h1 class="my-6 font-titulo font-bold text-3xl lg:text-4xl text-center text-negro">Edita tu Publicación</h1>

<form method="POST" action="/publicaciones/{{$publicacion->id}}" enctype="multipart/form-data" class="px-3 md:max-w-screen-sm md:mx-auto">
	@method('PUT')
	
	<input type="text" name="titulo" id="titulo" placeholder="Título de tu publicación" value="{{$publicacion->titulo}}" class="w-full font-titulo text-2xl border-[3px] border-gris">
	
	<div class="mt-3 px-8">
		<p class="font-ui font-semibold text-center text-xl text-negro">Portada de publicación</p>
		<label for="inputImagen">
			<img src="{{$publicacion->portada != null ? "/storage/publicaciones/" . $publicacion->portada : ""}}" id="previewImagen" class="mx-auto w-full max-w-md aspect-video rounded-md border-2 border-negro cursor-pointer bg-grisoscuro"/>
		</label>
		<input type="file" name="imagen" id="inputImagen" class="hidden"/>
	</div>
	
	<textarea name="cuerpo" id="cuerpo" rows="8" placeholder="Escribí tu publicación" class="mt-6 w-full font-cuerpo text-xl bg-blanco border-[3px] border-gris rounded-lg">{{$publicacion->cuerpo}}</textarea>
	
	<h3 class="mb-2 font-ui font-semibold text-2xl text-center">Categorías</h3>
	
	<div class="py-3 flex flex-row flex-nowrap overflow-x-scroll [scrollbar-width:none] lg:[scrollbar-width:auto] gap-3">
		@foreach ($categorias as $categoria)
		<div>
			<input type="checkbox" name="categorias[]" id="categoria{{$categoria->id}}" value="{{$categoria->id}}" {{$publicacion->categorias()->where("categoria_id", $categoria->id)->exists() ? "checked" : ""}} class="peer hidden" />
			<label for="categoria{{$categoria->id}}" class="px-3 py-1 font-cuerpo font-medium text-2xl text-negro cursor-pointer hover:bg-gris rounded-xl peer-checked:bg-primario peer-checked:hover:bg-primariohover peer-checked:text-blanco">#{{$categoria->nombre}}</label>
		</div>
		@endforeach
	</div>
	
	@csrf
	<div class="flex flex-row flex-nowrap justify-center items-center">
		<button type="submit" class="w-fit my-6 px-6 py-2 font-ui font-medium text-blanco text-2xl bg-primario hover:bg-primariohover rounded-md">Editar</button>
	</div>
</form>

<script src="{{ asset('js/formularioPublicacion.js') }}"></script>
@endsection
