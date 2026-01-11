<?php

namespace Modules\FormBuilder\Concerns\Livewire;

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
