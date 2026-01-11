<?php

namespace Modules\FormBuilder\Livewire;

use Illuminate\View\View;
use Livewire\Component;
use Modules\FormBuilder\Concerns\Livewire\HandlesInitialize;
use Modules\FormBuilder\Concerns\ManagesFormSchemas;

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
