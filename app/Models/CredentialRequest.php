<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CredentialRequest extends Model
{
    protected $fillable = ['credential_request_form_id', 'user_id', 'answers', 'status'];

    protected function casts(): array
    {
        return ['answers' => 'array'];
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(CredentialRequestForm::class, 'credential_request_form_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
