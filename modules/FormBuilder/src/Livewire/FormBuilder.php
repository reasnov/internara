<?php

declare(strict_types=1);

namespace Modules\FormBuilder\Livewire;

use Illuminate\View\View;
use Livewire\Component;
use Modules\FormBuilder\Concerns\ManagesFormSchemas;
use Modules\FormBuilder\Livewire\Concerns\HandlesInitialize;

class FormBuilder extends Component
{
    use HandlesInitialize;
    use ManagesFormSchemas;

    public function mount(array $data = []): void
    {
        $this->initialize($data);
    }

    public function render(): View
    {
        return view('formbuilder::livewire.form-builder', $this->formAttributes);
    }
}
