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
        <p class="reacciones">
            <?=
			array_reduce(
                $publicacion
                ->reacciones()->get() //lista con todas las reacciones
                ->pluck("pivot.relacion")->toArray() // filtramos solo a tipo de reaccion ("me gusta" | "guardar")
                , function($valorPrevio, $reaccion) {
                    // contamos cuantos "me_gusta" hay
                    return $reaccion == "me_gusta" ? $valorPrevio + 1 : $valorPrevio;
                }, 0)
                ?> me gusta
        </p>
    </div>
</div>