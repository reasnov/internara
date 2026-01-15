<?php

declare(strict_types=1);

namespace Modules\Internship\Services\Contracts;

use Modules\Shared\Services\Contracts\EloquentQuery;

/**
 * @template TModel of \Modules\Internship\Models\Internship
 *
 * @extends EloquentQuery<TModel>
 */
interface InternshipService extends EloquentQuery
{
    //
}
