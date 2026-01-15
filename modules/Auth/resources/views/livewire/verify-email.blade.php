<div class="container mx-auto mt-10">
    <div class="max-w-md mx-auto bg-white p-8 rounded-lg shadow-md">
        <h2 class="text-2xl font-semibold mb-6 text-gray-800">Email Verification</h2>

        @if (session('status'))
            <div
                class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                role="alert"
            >
                <span class="block sm:inline">{{ session('status') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div
                class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4"
                role="alert"
            >
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <p class="text-gray-600 mb-6">
            Please click the button below to verify your email address.
        </p>

        <form wire:submit.prevent="verify">
            <button
                type="submit"
                class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
            >
                Verify Email
            </button>
        </form>

        <p class="text-center text-gray-600 text-sm mt-6">
            Didn't receive the email?
            <button
                wire:click="resend"
                class="text-blue-500 hover:text-blue-700 font-bold focus:outline-none"
            >
                Click here to resend
            </button>
            .
        </p>
    </div>
</div>
