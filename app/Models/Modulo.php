<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Modulo extends Model
{
    protected $table = 'modulos';

    protected $fillable = ['aplicativo_id', 'nombre_modulo', 'tipo', 'activo'];

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }

    public function aplicativo(): BelongsTo
    {
        return $this->belongsTo(Aplicativo::class);
    }
}
