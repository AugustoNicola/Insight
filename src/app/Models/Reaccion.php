<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;


class Reaccion extends Pivot
{ 
    protected $table = "reacciones";
    public $timestamps = false;
}
