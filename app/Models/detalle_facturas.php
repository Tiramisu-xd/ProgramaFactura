<?php

namespace App\Models;

use CreateEncabezado_facturasTable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detalle_facturas extends Model
{
    use HasFactory;

    protected $table = 'detalle_facturas';

    protected $fillable = ['codigo_factura', 'codigo_producto', 'unidad_producto', 'precio_unitario'];

    public function encabezado_facturas()
    {
        return $this->belongsTo(encabezado_facturas::class, 'codigo_factura', 'codigo_factura');
    }

    public function items()
    {
        return $this->belongsTo(Items::class, 'codigo_producto', 'codigo_producto');
    }
}