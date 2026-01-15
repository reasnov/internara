<?php

declare(strict_types=1);

namespace Modules\User\Livewire\Users;

use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\Exception\Concerns\HandlesAppException;
use Modules\Permission\Models\Role;
use Modules\User\Models\User;
use Modules\User\Services\Contracts\UserService;

/**
 * Class Form
 *
 * Handles creating and updating users.
 */
class Form extends Component
{
    use HandlesAppException;
    use WithFileUploads;

    /**
     * The user instance being edited (null for create).
     */
    public ?User $user = null;

    /**
     * Form properties.
     */
    public string $name = '';
    public string $email = '';
    public string $username = '';
    public string $password = '';
    public string $status = 'active';
    public array $selectedRoles = [];
    public $avatar;

    /**
     * Data for selection.
     */
    public Collection $roles;

    /**
     * The user service instance.
     */
    protected UserService $userService;

    /**
     * Boot the component.
     */
    public function boot(UserService $userService): void
    {
        $this->userService = $userService;
    }

    /**
     * Initialize the component.
     */
    public function mount(?string $id = null): void
    {
        $this->roles = Role::where('name', '!=', 'super-admin')->get();

        if ($id) {
            $this->user = User::findOrFail($id);
            $this->name = $this->user->name;
            $this->email = $this->user->email;
            $this->username = $this->user->username;
            $this->status = $this->user->latestStatus()?->name ?? 'active';
            $this->selectedRoles = $this->user->roles->pluck('name')->toArray();
        }
    }

    /**
     * Save the user data.
     */
    public function save(): void
    {
        $rules = [
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email,' . ($this->user?->id ?? 'NULL'),
            'username'      => 'required|string|max:255|unique:users,username,' . ($this->user?->id ?? 'NULL'),
            'password'      => ($this->user ? 'nullable' : 'required') . '|string|min:8',
            'status'        => 'required|string|in:active,inactive,pending',
            'selectedRoles' => 'required|array|min:1',
            'avatar'        => 'nullable|image|max:1024',
        ];

        $data = $this->validate($rules);
        $data['roles'] = $this->selectedRoles;
        $data['status'] = $this->status;
        $data['avatar_file'] = $this->avatar;

        try {
            if ($this->user) {
                $this->userService->update($this->user->id, $data);
                $message = __('User updated successfully.');
            } else {
                $this->userService->create($data);
                $message = __('User created successfully.');
            }

            $this->dispatch('success', message: $message);
            $this->redirect('/users', navigate: true);
        } catch (\Throwable $e) {
            $this->handleAppExceptionInLivewire($e);
        }
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('user::livewire.users.form');
    }
}
