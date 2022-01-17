@extends("app")

@section("titulo", "Escribir")

@section('contenido')
<form method="POST" action="/publicaciones" enctype="multipart/form-data">
	<h1>Escribe tu Publicación</h1>
	
	<label for="titulo">Título de tu publicación</label>
	<input type="text" name="titulo" id="titulo" value={{old("titulo")}}>
	
	<div class="contenedor-imagen">
		<label for="imagen">Imagen de portada</label>
		<input type="file" name="imagen" id="imagen" />
		<img id="preview-imagen" />
	</div>
	
	<label for="cuerpo">Escribe aquí tu publicación...</label>
	<textarea name="cuerpo" id="cuerpo">{{old("cuerpo")}}</textarea>
	
	<h3>Categorías</h3>
	
	@foreach ($categorias as $categoria)
	<input type="checkbox" name="categorias[]" value="{{$categoria->id}}" />#{{$categoria->nombre}}<br />
	@endforeach
	
	@csrf
	<button type="submit">Publicar</button>
</form>
@endsection
