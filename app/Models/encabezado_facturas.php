<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Encabezado_facturas extends Model
{
    use HasFactory;

    protected $table = 'encabezado_facturas';
    protected $fillable = ['codigo_factura', 'cc', 'fecha_factura', 'precio_total'];

    public function clientes()
    {
        return $this->belongsTo(Clientes::class, 'cc', 'cc');
    }
}