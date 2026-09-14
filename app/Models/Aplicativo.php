<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Aplicativo extends Model
{
    protected $table = 'aplicativos';

    protected $fillable = ['organization_id', 'nombre_aplicativo', 'activo'];

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function modulos(): HasMany
    {
        return $this->hasMany(Modulo::class);
    }
}
