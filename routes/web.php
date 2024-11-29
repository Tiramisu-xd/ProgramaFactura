<?php

use App\Http\Controllers\buscar_fac;
use App\Http\Controllers\paginappal;
use Illuminate\Support\Facades\Route;
use App\Models\Items;


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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/facturas', [buscar_fac::class, 'obtenerFacturas']);
Route::get('/buscar', [buscar_fac::class, 'buscar']);
Route::post('/buscar', [buscar_fac::class, 'buscar']);

Route::delete('/eliminar-factura/{codigo_factura}', [paginappal::class, 'eliminarFactura']);
Route::get('/factura/{codigo_factura}', [paginappal::class, 'obtenerFactura']);
Route::put('/factura/{codigo_factura}', [paginappal::class, 'actualizarFactura']);
Route::put('/factura/{codigo_factura}', [paginappal::class, 'obtenerFacturaEditar']);
Route::get('/factura/{id}/ver', [paginappal::class, 'verFactura'])->name('factura.ver');
Route::post('/validarCedula', [paginappal::class, 'validarCedula'])->name('validarCedula');
Route::get('/productos', function () {
    return response()->json(Items::all());
});
Route::get('/productos', [paginappal::class, 'obtenerProductos']);
Route::get('/numeroFactura', [paginappal::class, 'generarNumeroFactura']);
Route::post('/calcularTotal', [paginappal::class, 'calcularTotal']);


