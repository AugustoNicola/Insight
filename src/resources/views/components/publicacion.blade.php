<div class="publicacion">
    <div class="imagen">
        {{-- TODO imagen portada --}}
    </div>
    <div class="informacion">
        <a class="titulo" href="/publicaciones/{{$publicacion->id}}">{{$publicacion->titulo}}</a>
        <div class="categorias">
            @foreach($publicacion->categorias()->get() as $categoria)
            <a class="categoria" href="/categorias/{{$categoria->id}}">#{{$categoria->nombre}}</a>
            @endforeach
        </div>
        <div class="autore">
            <div class="imagen-autore"> {{-- TODO imagen autore--}}</div>
            <p>por {{$publicacion->autore()->first()->nombre}}</p>
        </div>
        <p class="reacciones">{{$publicacion->withCount("meGusta")->where("id", $publicacion->id)->first()->me_gusta_count}} me gusta</p>
    </div>
    @switch($tipo)
    @case("editable")
    <div class="acciones">
        <a href="/publicaciones/{{$publicacion->id}}/editar" class="editar">Ed</a>
        <form action="/publicaciones/{{$publicacion->id}}" method="POST">
            @method('DELETE')
            @csrf
            <button type="submit" class="eliminar">El</button>
        </form>
    </div>
    @break
    
    @case("guardable")
    <div class="acciones">
        <button id="boton-desguardar">Dg</button>
    </div>
    @break
    
    @case("normal")
    @default
    {{-- nada --}}
    @endswitch
    
</div>