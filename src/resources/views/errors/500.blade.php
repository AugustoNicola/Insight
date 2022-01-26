@extends("app")

@section("titulo", "Error 500")

@section('contenido')

<h1 class="px-4 my-4 font-ui font-extrabold text-center text-primario text-5xl">Error 500</h1>
<h3 class="px-4 font-ui font-medium text-center text-negro text-3xl">Ha ocurrido un error inesperado.</h3>
<div class="px-4 mt-6 flex flex-row flex-nowrap justify-center items-center">
    <a href="/" class="w-fit mx-auto px-6 py-2 font-ui font-medium text-blanco text-2xl bg-primario hover:bg-primariohover rounded-md">Volver al inicio</a>
</div>

@endsection