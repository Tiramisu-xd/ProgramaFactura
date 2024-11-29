<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clientes extends Model
{
    use HasFactory;

    protected $table = 'clientes';
    protected $fillable = ['cc', 'nombre', 'fecha_nacimiento'];

    public function facturas()
    {
        return $this->hasMany(Encabezado_facturas::class, 'cc', 'cc'); // Relación 1-N con EncabezadoFactura
    }
}
