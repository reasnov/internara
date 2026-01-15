<?php

declare(strict_types=1);

namespace Modules\User\Livewire;

use Illuminate\Validation\Rule as ValidationRule;
use Livewire\Form;

class UserForm extends Form
{
    public string|int|null $id = null;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                ValidationRule::unique('users', 'email')->ignore($this->id),
            ],
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ];
    }
}
