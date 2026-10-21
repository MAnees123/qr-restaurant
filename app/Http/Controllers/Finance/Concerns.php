<?php

namespace App\Http\Controllers\Finance;

use Illuminate\Database\Eloquent\Model;

trait Concerns
{
    protected function rid(): int
    {
        return (int) auth()->user()->restaurant_id;
    }

    /** 404 for records that belong to another restaurant. */
    protected function own(Model $model): Model
    {
        abort_unless((int) $model->restaurant_id === $this->rid(), 404);
        return $model;
    }
}
