<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{
    protected $fillable = ['name'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function credentialRequestForms(): HasMany
    {
        return $this->hasMany(CredentialRequestForm::class);
    }

    public function aplicativos(): HasMany
    {
        return $this->hasMany(Aplicativo::class);
    }
}
