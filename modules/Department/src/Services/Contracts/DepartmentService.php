<?php

namespace Modules\Department\Services\Contracts;

use Modules\Shared\Services\Contracts\EloquentQuery;

/**
 * @template TModel of \Modules\Department\Models\Department
 *
 * @extends EloquentQuery<TModel>
 */
interface DepartmentService extends EloquentQuery
{
    //
}
