<?php

namespace Modules\User\Livewire\Auth;

use Livewire\Component;
use Modules\Shared\Exceptions\AppException;
use Modules\User\Contracts\Services\AuthService;
use Modules\User\Traits\Auth\RedirectsUsers;

class VerificationNotice extends Component
{
    use RedirectsUsers;

    protected AuthService $authService;

    public function boot(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function mount()
    {
        if (auth()->check() && auth()->user()->hasVerifiedEmail()) {
            return redirect()->intended($this->redirectPath());
        }
    }

    public function resend()
    {
        if (! auth()->check()) {
            session()->flash('error', 'You must be logged in to resend the verification email.');
            return;
        }

        try {
            $this->authService->resendVerificationEmail(auth()->user());
            session()->flash('status', 'A fresh verification link has been sent to your email address.');
        } catch (AppException $e) {
            session()->flash('error', $e->getUserMessage());
        }
    }

    public function render()
    {
        return view('user::livewire.auth.verification-notice');
    }
}
