<?php

namespace App\View\Components;

use Illuminate\View\Component;

class PublicacionEditable extends Component
{
    public $publicacion;

    public function __construct($publicacion)
    {
        $this->publicacion = $publicacion;
    }

    public function render()
    {
        return view('components.publicacion-editable');
    }
}
