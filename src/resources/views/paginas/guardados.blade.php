@extends("app")

@section("titulo", "Publicaciones Guardadas")

@section('contenido')
<h1 class="my-4 font-titulo font-bold text-3xl text-center text-negro">Tus Publicaciones Guardadas</h1>

<main class="px-3 md:max-w-screen-lg md:mx-auto">
	@if ($autenticade == true)
		
		@if (count($publicaciones) > 0)
			<div class="mb-8 lg:grid lg:grid-cols-2 lg:gap-x-4 lg:gap-y-4">
				@foreach($publicaciones as $publicacion)
				<x-publicacion tipo="guardable" :publicacion="$publicacion" />
				@endforeach
			</div>
		@else
			<div>
				<h3 class="my-3 font-ui font-medium text-grisoscuro text-center text-xl">Todavía no guardaste ninguna publicación.</h3>
				<div class="flex flex-row flex-nowrap justify-center items-center">
					<a href="/publicaciones" class="my-4 mx-auto px-6 py-2 font-ui font-medium text-blanco text-2xl bg-primario hover:bg-primariohover rounded-md">Ver Publicaciones</a>
				</div>
			</div>
		@endif
		
	@else
		
		<div>
			<h3 class="my-3 font-ui font-medium text-grisoscuro text-center text-xl">Para ver tus publicaciones guardadas primero es necesario iniciar sesión.</h3>
			<div class="flex flex-row flex-nowrap justify-center items-center">
				<a href="/entrar" class="my-4 mx-auto px-6 py-2 font-ui font-medium text-blanco text-2xl bg-primario hover:bg-primariohover rounded-md">Iniciar Sesión</a>
			</div>
		</div>
	@endif
</main>

<script src="{{ asset('js/guardados.js') }}"></script>

@endsection

