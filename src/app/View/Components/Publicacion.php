<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Publicacion extends Component
{
    public $publicacion;
    public $tipo = "normal";

    public function __construct($publicacion, $tipo)
    {
        $this->publicacion = $publicacion;
        $this->tipo = $tipo;
    }

    public function render()
    {
        return view('components.publicacion');
    }
}
