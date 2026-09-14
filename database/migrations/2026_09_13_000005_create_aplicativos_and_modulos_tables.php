<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aplicativos', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('nombre_aplicativo');
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->unique(['organization_id', 'nombre_aplicativo']);
        });

        Schema::create('modulos', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('aplicativo_id')->constrained('aplicativos')->cascadeOnDelete();
            $table->string('nombre_modulo');
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->unique(['aplicativo_id', 'nombre_modulo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modulos');
        Schema::dropIfExists('aplicativos');
    }
};
