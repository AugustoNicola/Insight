<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ControladorUsuarie;
use App\Http\Controllers\ControladorCategoria;
use App\Http\Controllers\ControladorPublicacion;
use App\Http\Controllers\ControladorComentario;
use App\Http\Controllers\ControladorReaccion;

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

//* =========== Usuaries ===========
Route::get("/registrarse", [ControladorUsuarie::class, "vistaRegistrarse"])->name("registrarse");
Route::post("/registrarse", [ControladorUsuarie::class, "registrarse"]);

Route::get("/entrar", [ControladorUsuarie::class, "vistaEntrar"])->name("entrar");
Route::post("/entrar", [ControladorUsuarie::class, "entrar"]);

//* =========== Categorias ===========
Route::get("/categorias", [ControladorCategoria::class, "listarCategorias"])->name("categorias");
Route::get("/categorias/{id}", [ControladorCategoria::class, "informacionCategoria"])->name("categoria");

//* =========== Publicaciones ===========
Route::get("/publicaciones", [ControladorPublicacion::class, "listarPublicaciones"])->name("publicaciones");
Route::get("/publicaciones/{id}", [ControladorPublicacion::class, "informacionPublicacion"])->name("publicacion");
Route::get("/escribir", [ControladorPublicacion::class, "vistaPublicarPublicacion"])->name("escribir");
Route::post("/publicaciones", [ControladorPublicacion::class, "publicarPublicacion"])->name("publicarPublicacion");
Route::get("/publicaciones/{id}/editar", [ControladorPublicacion::class, "vistaEditarPublicacion"])->name("editarPublicacion");
Route::put("/publicaciones/{id}", [ControladorPublicacion::class, "editarPublicacion"]);
Route::delete("/publicaciones/{id}", [ControladorPublicacion::class, "eliminarPublicacion"]);

//* =========== Comentarios ===========
Route::post("/comentario", [ControladorComentario::class, "publicarComentario"])->name("comentario");

//* =========== API ===========
Route::post("/api/reacciones", [ControladorReaccion::class, "publicarReaccion"])->name("publicarReaccion");

Route::delete("/api/reacciones", [ControladorReaccion::class, "eliminarReaccion"])->name("eliminarReaccion");
