<?php

declare(strict_types=1);

namespace Modules\FormBuilder\Concerns;

use Modules\FormBuilder\Schema;

trait ManagesFormSchemas
{
    public array $formAttributes = [];

    public function schema(): Schema
    {
        return Schema::configure(function (Schema $schema) {
            return $schema->header('Example Title', 'Example Subtitle')->form([
                //
            ]);
        });
    }

    public function buildForm()
    {
        $this->formAttributes = $this->schema()->getFormAttributes();
    }
}
