<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('encabezado_facturas', function (Blueprint $table) {
            $table->string('codigo_factura', 30)->primary(); 
            $table->string('cc'); 
            $table->date('fecha_factura'); 
            $table->decimal('precio_total', 10, 2); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('encabezado_facturas');
    }
};
