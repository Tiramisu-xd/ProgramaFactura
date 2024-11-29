<?php

namespace App\Http\Controllers;

use App\Models\Clientes;
use App\Models\Encabezado_facturas;
use Illuminate\Http\Request;

class buscar_fac extends Controller
{
    public function obtenerFacturas()
    {
        $facturas = Encabezado_facturas::all();
        return response()->json($facturas);
    }

    public function buscar(Request $request)
    {
        // Validar los parámetros de búsqueda
        $request->validate([
            'termino' => 'required|string|max:255',
        ]);

        // Obtener el término de búsqueda
        $termino = $request->input('termino');

        // Buscar por cédula en la tabla de clientes
        $resultadosClientes = Clientes::with('factura')
            ->where('cc', 'LIKE', "%$termino%")
            ->get();

        // Buscar por número de factura en la tabla de encabezado_facturas
        $resultadosFacturas = Encabezado_facturas::with('clientes')
            ->where('codigo_factura', 'LIKE', "%$termino%")
            ->get();

        //División en páginas en caso de más de 10 registros
        $resultadosFacturas = Encabezado_facturas::with('clientes')
        ->where('codigo_factura', 'LIKE', "%$termino%")
        ->paginate(10);

        $resultados = $resultadosClientes->merge($resultadosFacturas);

        return response()->json($resultados);
    }
}
