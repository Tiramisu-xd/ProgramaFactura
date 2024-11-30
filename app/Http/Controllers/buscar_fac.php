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
        $request->validate([
            'termino' => 'required|string|max:255',
        ]);

        $termino = $request->input('termino');

        $resultadosClientes = Clientes::with('factura')
            ->where('cc', 'LIKE', "%$termino%")
            ->get();

        $resultadosFacturas = Encabezado_facturas::with('clientes')
            ->where('codigo_factura', 'LIKE', "%$termino%")
            ->get();

        $resultadosFacturas = Encabezado_facturas::with('clientes')
        ->where('codigo_factura', 'LIKE', "%$termino%")
        ->paginate(10);

        $resultados = $resultadosClientes->merge($resultadosFacturas);

        return response()->json($resultados);
    }
}
