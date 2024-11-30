<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clientes extends Model
{
    use HasFactory;

    protected $table = 'clientes';
    protected $fillable = ['cc', 'nombre', 'fecha_nacimiento'];

    public function factura()
    {
        return $this->belongsTo(Encabezado_facturas::class, 'cc', 'cc');
    }
}
