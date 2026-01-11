<?php

namespace Modules\Records\Concerns\Livewire;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Records\Contracts\Services\Eloquent\EloquentService;

/**
 * @mixin \Livewire\Component
 */
trait ManagesRecords
{
    protected EloquentService $service;
}
