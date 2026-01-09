<div class="container mx-auto mt-10">
    <div class="max-w-md mx-auto bg-white p-8 rounded-lg shadow-md">
        <h2 class="text-2xl font-semibold mb-6 text-gray-800">Verify Your Email Address</h2>

        @if (session('status'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('status') }}</span>
            </div>
        @endif

        <p class="text-gray-600 mb-6">
            Before proceeding, please check your email for a verification link.
        </p>
        <p class="text-gray-600 mb-6">
            If you did not receive the email,
            <a href="#" wire:click.prevent="resend" class="text-blue-500 hover:text-blue-700 font-bold focus:outline-none">
                click here to request another
            </a>.
        </p>
    </div>
</div>
