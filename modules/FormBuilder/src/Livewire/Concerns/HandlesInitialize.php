<?php

namespace Modules\FormBuilder\Livewire\Concerns;

trait HandlesInitialize
{
    public bool $isInitialized = false;

    public array $data = [];

    public function initialize(array $data = []): void
    {
        $this->data = $data;
        $this->isInitialized = true;
    }
}
