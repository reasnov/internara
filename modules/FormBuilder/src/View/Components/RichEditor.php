<?php

namespace Modules\FormBuilder\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class RichEditor extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
    }

    /**
     * Get the view/contents that represent the component.
     */
    public function render(): View|string
    {
        return view('formbuilder::components.rich-editor');
    }
}
