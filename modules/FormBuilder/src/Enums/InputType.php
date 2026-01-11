<?php

namespace Modules\FormBuilder\Enums;

enum InputType: string
{
    case INPUT = 'input';

    case SELECT = 'select';

    case RADIO = 'radio';

    case CHECKBOX = 'checkbox';

    case TEXTAREA = 'textarea';

    case FILE = 'file';

    case EDITOR = 'editor';
}
