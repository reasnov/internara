<?php

namespace Modules\Internship\Services;

use Modules\Internship\Models\Internship;
use Modules\Shared\Services\Concerns\EloquentQuery;

class InternshipService implements Contracts\InternshipService
{
    use EloquentQuery;

    public function __construct(Internship $internship)
    {
        $this->setModel($internship);
    }
}
