<?php

namespace Modules\Internship\Services;

use Modules\Internship\Models\Internship;
use Modules\Shared\Services\EloquentQuery;

class InternshipService extends EloquentQuery implements Contracts\InternshipService
{
    public function __construct(Internship $internship)
    {
        $this->setModel($internship);
    }
}
