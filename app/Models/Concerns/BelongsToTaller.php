<?php

namespace App\Models\Concerns;

use App\Models\Taller;
use App\Scopes\TallerScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToTaller
{
    public static function bootBelongsToTaller(): void
    {
        static::addGlobalScope(new TallerScope);

        static::creating(function ($model) {
            if (empty($model->taller_id) && app()->bound('taller.actual')) {
                $model->taller_id = app('taller.actual')->id;
            }
        });
    }

    public function taller(): BelongsTo
    {
        return $this->belongsTo(Taller::class);
    }
}
