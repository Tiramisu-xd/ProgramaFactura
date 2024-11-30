<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Clientes;
use App\Models\Items;
use App\Models\Encabezado_facturas;
use App\Models\Detalle_facturas;

class paginappal extends Controller
{
    public function validarCedula(Request $request)
    {
        $request->validate([
            'cedula' => 'required|numeric',
        ]);

        $cliente = Clientes::where('cc', $request->cedula)->first();

        if (!$cliente) {
            return response()->json(['error' => 'La cédula no está registrada en nuestros registros.'], 404);
        }
        return response()->json(['success' => 'Cédula registrada correctamente.'], 200);
    }

    public function obtenerProductos()
    {
        $productos = Items::all(); 
        return response()->json($productos);
    }

    public function generarNumeroFactura()
    {
        $ultimoFactura = Encabezado_facturas::latest('codigo_factura')->first();

        if ($ultimoFactura) {
            $ultimoNumero = (int) substr($ultimoFactura->codigo_factura, -4); 
            $nuevoNumero = str_pad($ultimoNumero + 1, 4, '0', STR_PAD_LEFT); 
        } else {
            $nuevoNumero = '0001'; // Si no hay facturas, comenzamos con 0001
        }

        $numeroFactura = 'F001-' . $nuevoNumero;

        return response()->json(['numero_factura' => $numeroFactura]); 
    }

    public function eliminarFactura($codigo_factura)
    {
        try {
            // Eliminar registros relacionados en detalle_facturas
            Detalle_facturas::where('codigo_factura', $codigo_factura)->delete();

            // Eliminar la factura en encabezado_facturas
            Encabezado_facturas::where('codigo_factura', $codigo_factura)->delete();

            return response()->json(['success' => true, 'message' => 'Factura eliminada correctamente.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al eliminar la factura.']);
        }
    }

    public function obtenerFactura($codigo_factura)
    {
        $factura = Encabezado_facturas::where('codigo_factura', $codigo_factura)->first();

        if ($factura) {
            return response()->json($factura);
        } else {
            return response()->json(['message' => 'Factura no encontrada.'], 404);
        }
    }

    public function actualizarFactura(Request $request, $codigo_factura)
    {
        try {
            $factura = Encabezado_facturas::where('codigo_factura', $codigo_factura)->first();

            if ($factura) {
                $factura->cc = $request->input('codigo_factura');
                $factura->save();

                // Actualizar detalles
                $detalle = Detalle_facturas::where('codigo_factura', $codigo_factura)->first();
                if ($detalle) {
                    $detalle->producto = $request->input('producto');
                    $detalle->cantidad = $request->input('cantidad');
                    $detalle->total = $request->input('total');
                    $detalle->save();
                }

                return response()->json(['success' => true, 'message' => 'Factura actualizada correctamente.']);
            }

            return response()->json(['success' => false, 'message' => 'Factura no encontrada.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al actualizar la factura.']);
        }
    }

    public function obtenerFacturaEditar($codigo_factura)
    {
        $factura = Encabezado_facturas::where('codigo_factura', $codigo_factura)->first();
    
        if (!$factura) {
            return response()->json(['error' => 'Factura no encontrada'], 404);
        }
    
        return response()->json([
            'cedula' => $factura->cc,
            'id_factura' => $factura->codigo_factura,
            'producto' => $factura->productos->map(function ($producto) {
                return [
                    'cedula' => $producto->cc,
                    'id_factura' => $producto->codigo_factura,
                    'producto' => $producto->descripcion_producto,
                    'cantidad' => $producto->pivot->unidad_producto, 
                ];
            }),
        ]);
    }

    public function obtenerFacturaVer($codigo_factura)
    {
        try {
            $factura = Encabezado_facturas::where('codigo_factura', $codigo_factura)->first();
    
            if (!$factura) {
                return response()->json(['success' => false, 'message' => 'Factura no encontrada.']);
            }
    
            $detalles = Detalle_facturas::where('codigo_factura', $codigo_factura)
                ->join('productos', 'productos.id', '=', 'detalle_facturas.producto')
                ->select(
                    'productos.descripcion_producto',
                    'detalle_facturas.cantidad',
                    'productos.precio_unitario',
                    DB::raw('detalle_facturas.cantidad * productos.precio_unitario as total')
                )
                ->get();
    
            $cliente = Clientes::where('cc', $factura->cc)->first();
    
            return response()->json([
                'success' => true,
                'factura' => $factura,
                'cliente' => $cliente ? $cliente->nombre : 'Sin nombre',
                'detalles' => $detalles
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al obtener la factura.', 'error' => $e->getMessage()]);
        }
    }

    public function guardarFactura(Request $request)
    {
        try {
            $request->validate([
                'cedula' => 'required|exists:clientes,cc',
                'producto' => 'required|array',
                'producto.*' => 'exists:items,codigo_producto',
                'cantidad' => 'required|array',
                'cantidad.*' => 'integer|min:1',
            ]);

            $encabezado = new Encabezado_facturas();
            $encabezado->codigo_factura=$request->numero_factura;
            $encabezado->cc = $request->cedula;
            $encabezado->fecha_factura = now();
            $encabezado->precio_total = $request->total;
            $encabezado->save();

            foreach ($request->producto as $index => $productoId) {
                $cantidad = $request->cantidad[$index];
                $producto = \DB::table('items')->where('codigo_producto', $productoId)->first();

                $detalle = new Detalle_facturas();
                $detalle->codigo_factura = $encabezado->codigo_factura;
                $detalle->codigo_producto = $producto->codigo_producto;
                $detalle->unidad_producto = $cantidad;
                $detalle->precio_unitario = $producto->precio_producto;
                $detalle->save();

            }

            return redirect('/')->with('completed', 'Movimiento creado!');
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al guardar la factura: ' . $e->getMessage()]);
        }
    }

}

