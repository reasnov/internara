<?php

namespace Modules\Internship\Services;

use Modules\Internship\Contracts\Services\InternshipService as InternshipServiceContract;
use Modules\Internship\Models\Internship;
use Modules\Shared\Concerns\EloquentQuery;

class InternshipService implements InternshipServiceContract
{
    use EloquentQuery;

    public function __construct(Internship $internship)
    {
        $this->setModel($internship);
    }
}
