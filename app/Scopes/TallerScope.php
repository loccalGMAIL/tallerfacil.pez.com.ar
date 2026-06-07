<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TallerScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (app()->bound('taller.actual')) {
            $builder->where($model->getTable() . '.taller_id', app('taller.actual')->id);
        }
    }
}
