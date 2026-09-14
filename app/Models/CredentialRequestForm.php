<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CredentialRequestForm extends Model
{
    protected $fillable = ['organization_id', 'name', 'description', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function fields(): HasMany
    {
        return $this->hasMany(CredentialRequestField::class)->orderBy('position');
    }

    public function requests(): HasMany
    {
        return $this->hasMany(CredentialRequest::class);
    }
}
