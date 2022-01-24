@extends("app")

@section("titulo", "Editar Perfil")

@section('contenido')
<h1 class="my-6 font-titulo font-bold text-center text-negro text-3xl">Editar Cuenta</h1>

<form method="POST" action="/perfil" enctype="multipart/form-data" class="lg:max-w-screen-sm lg:mx-auto px-4 pb-6 flex flex-col flex-nowrap gap-10">
	<input type="text" name="nombre" id="nombre" placeholder="Nombre de Cuenta" value={{$usuarie->nombre}} class="bg-fondo font-ui text-2xl border-b-[3px] border-gris">
	<input type="password" name="contrasena" id="contrasena" placeholder="Nueva contraseña" class="bg-fondo font-ui text-2xl border-b-[3px] border-gris">
	<input type="password" name="contrasena_confirmation" id="contrasena_confirmation" placeholder="Repetir nueva contraseña" class="bg-fondo font-ui text-2xl border-b-[3px] border-gris">
	
	<div class="flex flex-col lg:flex-row flex-wrap lg:flex-nowrap justify-center items-center gap-4 lg:gap-10">
		<p class="font-ui font-semibold text-center text-xl text-negro">Imagen de cuenta</p>
		<label for="inputImagen">
			<img src="{{App\Models\Usuarie::Find(Auth::id())->imagen != null ? "/storage/usuaries/" . App\Models\Usuarie::Find(Auth::id())->imagen : "/assets/usuariedefault.png"}}" id="previewImagen" class="w-24 h-24 rounded-full ring-offset-2 ring-4 ring-primario cursor-pointer bg-grisoscuro"/>
		</label>
		<input type="file" name="imagen" id="inputImagen" class="hidden"/>
	</div>
	
	@method('PUT')
	@csrf
	<div class="flex flex-row flex-nowrap justify-end items-center">
		<button type="submit" class="w-fit mt-3 px-6 py-2 font-ui font-medium text-blanco text-2xl bg-primario hover:bg-primariohover rounded-md">Actualizar</button>
	</div>
</form>

<script src="{{ asset('js/formularioUsuarie.js') }}"></script>
@endsection
