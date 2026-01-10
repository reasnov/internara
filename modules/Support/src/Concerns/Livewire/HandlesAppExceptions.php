<?php

namespace Modules\Support\Concerns\Livewire;

use Exception;
use Modules\Shared\Exceptions\AppException;

trait HandlesAppExceptions
{
    protected function handleAppException(Exception $e, callable $stopPropagation): void
    {
        if (! ($e instanceof AppException)) {
            return;
        }

        session()->flash('error', $e->getUserMessage());

        report($e);

        $stopPropagation();
    }
}
