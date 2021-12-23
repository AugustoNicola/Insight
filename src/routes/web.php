<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ControladorUsuarie;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get("/", function () {
    return view('welcome');
});

Route::post("/registrarse", [ControladorUsuarie::class, "registrarse"]);
Route::get("/entrar", [ControladorUsuarie::class, "formularioEntrar"]);
Route::post("/entrar", [ControladorUsuarie::class, "entrar"]);
