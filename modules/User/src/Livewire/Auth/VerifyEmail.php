<?php

namespace Modules\User\Livewire\Auth;

use Livewire\Component;
use Modules\User\Contracts\Services\AuthService;
use Modules\User\Models\User;
use Modules\User\Traits\Auth\RedirectsUsers;

class VerifyEmail extends Component
{
    use RedirectsUsers;

    public $id;
    public $hash;

    protected $authService;

    public function mount($id, $hash)
    {
        $this->id = $id;
        $this->hash = $hash;
    }

    public function boot(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function verify()
    {
        if (! auth()->check()) {
            // If the user is not authenticated, log them in
            $user = User::findOrFail($this->id);
            auth()->login($user);
        }

        if ($this->authService->verifyEmail($this->id, $this->hash)) {
            session()->flash('status', 'Your email has been verified!');
            return redirect()->intended($this->redirectPath());
        }

        session()->flash('error', 'Invalid verification link or email already verified.');
        return redirect()->route('verification.notice');
    }

    public function resend()
    {
        if (auth()->check() && ! auth()->user()->hasVerifiedEmail()) {
            $this->authService->resendVerificationEmail(auth()->user());
            session()->flash('status', 'A fresh verification link has been sent to your email address.');
        } else {
            session()->flash('error', 'You are already verified or not logged in.');
        }

        return redirect()->back();
    }

    public function render()
    {
        return view('user::livewire.auth.verify-email');
    }
}
