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
        // Validar que la cédula esté presente
        $request->validate([
            'cedula' => 'required|numeric',
        ]);

        // Buscar la cédula en la tabla de clientes
        $cliente = Clientes::where('cc', $request->cedula)->first();

        // Si el cliente no existe, retornar un mensaje de error
        if (!$cliente) {
            return response()->json(['error' => 'La cédula no está registrada en nuestros registros.'], 404);
        }

        // Si el cliente existe, devolver un mensaje de éxito
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

    public function verFactura($codigo_factura)
    {
        $factura = Encabezado_facturas::where('codigo_factura', $codigo_factura)->first();
    
        if (!$factura) {
            return response()->json(['error' => 'Factura no encontrada'], 404);
        }
    
        return response()->json([
            'codigo_factura' => $factura->codigo_factura,
            'nombre_cliente' => $factura->cliente->nombre, 
            'cc' => $factura->cc,
            'fecha_factura' => $factura->fecha_factura,
            'precio_total' => $factura->precio_total,
            'productos' => $factura->productos->map(function ($producto) {
                return [
                    'nombre' => $producto->descripcion_producto,
                    'descripcion' => $producto->descripcion,
                    'cantidad' => $producto->pivot->cantidad,
                    'precio_unitario' => $producto->precio_unitario,
                ];
            }),
        ]);
    }

    public function calcularTotal(Request $request)
    {
        $item_id = $request->input('numero_detalle');
        $numero_detalle = $request->input('detalle_factura_id');
        
        $item = Items::find($item_id);

        $detalleFactura = Detalle_facturas::find($numero_detalle);

        if (!$item || !$detalleFactura) {
            return response()->json(['error' => 'Item o detalle de factura no encontrado.'], 404);
        }

        $total = $item->precio_producto * $detalleFactura->unidad_producto;

        return response()->json(['total' => $total]);
    }

}

