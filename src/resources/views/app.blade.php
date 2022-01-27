<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Info -->
    <title>@yield("titulo") — Insight</title>
    <meta
    name="description"
    content="Tu blog online de noticias con las últimas novedades y las mejores publicaciones."
    />
    <link rel="icon" href="{{ asset("assets/favicon.ico") }}" />
    <meta name="theme-color" content="#075E52" />    
    <link rel="apple-touch-icon" href="{{ asset("assets/logo192.png") }}" />
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fuentes -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Domine:wght@400;500;600;700&family=Libre+Franklin:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Noticia+Text:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet"> 

    <!-- Estilos -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    
    <!-- Iconos -->
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
</head>
    <body class="bg-fondo overflow-x-hidden min-h-screen m-0 flex flex-row flex-wrap justify-center"> 
    @include("utilidades.header")
    
    
    @include("utilidades.errores")
    
    <div class="w-full">
        @yield("contenido")
    </div>
    
    @include("utilidades.footer")
    </body>
</html>