<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('statuses', function (Blueprint $table) {
            $table->id('id_status');
            $table->string('descripcion');
            $table->timestamps();
        });

        // Insertar los valores iniciales en la tabla
        DB::table('statuses')->insert([
            ['descripcion' => 'pendiente', 'created_at' => now(), 'updated_at' => now()],
            ['descripcion' => 'completada', 'created_at' => now(), 'updated_at' => now()],
            ['descripcion' => 'eliminada', 'created_at' => now(), 'updated_at' => now()]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('statuses');
    }
};
