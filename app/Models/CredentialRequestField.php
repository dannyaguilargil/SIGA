<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CredentialRequestField extends Model
{
    protected $fillable = ['label', 'type', 'options', 'is_required', 'position'];

    protected function casts(): array
    {
        return ['options' => 'array', 'is_required' => 'boolean'];
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(CredentialRequestForm::class, 'credential_request_form_id');
    }
}
