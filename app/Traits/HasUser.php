<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasUser
{
    public static function bootHasUser(): void
    {
        self::creating(function (Model $model) {
            $model->getAttribute('user_id') ?? $model->setAttribute('user_id', auth()->id());
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
